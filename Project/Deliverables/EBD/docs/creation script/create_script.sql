-------------------------------------
-- DROP OLD SCHEMA
-------------------------------------

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', 'public', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

DROP SCHEMA IF EXISTS sportsverse CASCADE;

CREATE SCHEMA sportsverse;

DROP TABLE IF EXISTS sportsverse.users CASCADE;
DROP TABLE IF EXISTS sportsverse.category CASCADE;
DROP TABLE IF EXISTS sportsverse.sub_category CASCADE;
DROP TABLE IF EXISTS sportsverse.color CASCADE;
DROP TABLE IF EXISTS sportsverse.size CASCADE;
DROP TABLE IF EXISTS sportsverse.product CASCADE;
DROP TABLE IF EXISTS sportsverse.product_variation CASCADE;
DROP TABLE IF EXISTS sportsverse.product_image CASCADE;
DROP TABLE IF EXISTS sportsverse.review CASCADE;
DROP TABLE IF EXISTS sportsverse.shopping_cart CASCADE;
DROP TABLE IF EXISTS sportsverse.wishlist CASCADE;
DROP TABLE IF EXISTS sportsverse.purchase CASCADE;
DROP TABLE IF EXISTS sportsverse.product_purchase CASCADE;
DROP TABLE IF EXISTS sportsverse.expedition CASCADE;


DROP TYPE IF EXISTS sportsverse.expedition_status CASCADE;
DROP TYPE IF EXISTS sportsverse.purchase_status CASCADE;


-------------------------------------
-- TYPES
-------------------------------------


CREATE TYPE sportsverse.expedition_status AS ENUM (
    'Created',
    'In Transit',
    'Delivered'
);


CREATE TYPE sportsverse.purchase_status AS ENUM (
    'Payment Pending',
    'Processing',
    'Shipping',
    'Canceled',
    'Concluded'
);


-------------------------------------
-- TABLES
-------------------------------------


CREATE TABLE sportsverse.users (
    id SERIAL PRIMARY KEY,
    email text NOT NULL UNIQUE,
    name text NOT NULL,
    password text NOT NULL,
    birthdate date NOT NULL,
    address text NOT NULL,
    phone_number numeric NOT NULL,
    is_admin boolean DEFAULT false NOT NULL,
    blocked boolean NOT NULL DEFAULT false,
    CONSTRAINT birthdate_ck CHECK ((birthdate <= CURRENT_DATE))
);


CREATE TABLE sportsverse.category (
    id SERIAL PRIMARY KEY,
    name text NOT NULL
);


CREATE TABLE sportsverse.sub_category (
    id SERIAL PRIMARY KEY,
    name text NOT NULL,
    id_category integer NOT NULL
);


CREATE TABLE sportsverse.color (
    id SERIAL PRIMARY KEY,
    color text NOT NULL
);


CREATE TABLE sportsverse.size (
    id SERIAL PRIMARY KEY,
    size text NOT NULL
);


CREATE TABLE sportsverse.product (
    id SERIAL PRIMARY KEY,
    name text NOT NULL,
    short_description text NOT NULL,
    long_description text NOT NULL,
    rating integer NOT NULL,
    manufacturer text NOT NULL,
    id_sub_category integer NOT NULL REFERENCES sportsverse.sub_category (id) ON UPDATE CASCADE,
    CONSTRAINT product_rating_ck CHECK ((rating >= 0 AND rating <= 5))
);


CREATE TABLE sportsverse.product_variation (
    id SERIAL PRIMARY KEY,
    id_prod integer NOT NULL REFERENCES sportsverse.product (id) ON UPDATE CASCADE,
    stock integer NOT NULL,
    price numeric(10,2) NOT NULL,
    id_size integer NOT NULL REFERENCES sportsverse.size (id) ON UPDATE CASCADE,
    id_color integer NOT NULL REFERENCES sportsverse.color (id) ON UPDATE CASCADE,
    CONSTRAINT product_variation_price_check CHECK ((price > 0)),
    CONSTRAINT product_variation_stock_check CHECK ((stock >= 0))
);


CREATE TABLE sportsverse.product_image (
    id SERIAL PRIMARY KEY,
    url text NOT NULL,
    id_prod_var integer NOT NULL REFERENCES sportsverse.product_variation (id) ON UPDATE CASCADE
);


CREATE TABLE sportsverse.review (
    id_user integer NOT NULL REFERENCES sportsverse.users (id) ON UPDATE CASCADE,
    id_product integer NOT NULL REFERENCES sportsverse.product (id) ON UPDATE CASCADE,
    comment text NOT NULL,
    rating integer NOT NULL,
    date date NOT NULL,
    CONSTRAINT Review_rating_ck CHECK ((rating >= 0 AND rating <= 5))
);


CREATE TABLE sportsverse.shopping_cart (
    id_prod_var integer NOT NULL REFERENCES sportsverse.product_variation (id) ON UPDATE CASCADE,
    id_user integer NOT NULL REFERENCES sportsverse.users (id) ON UPDATE CASCADE,
    quantity integer NOT NULL,
    CONSTRAINT shopping_cart_quantity_check CHECK ((quantity > 0))
);


CREATE TABLE sportsverse.wishlist (
    id_prod_var integer NOT NULL REFERENCES sportsverse.product_variation (id) ON UPDATE CASCADE,
    id_user integer NOT NULL REFERENCES sportsverse.users (id) ON UPDATE CASCADE
);


CREATE TABLE sportsverse.purchase (
    id SERIAL PRIMARY KEY,
    payment_method text NOT NULL,
    date date NOT NULL,
    id_user integer NOT NULL REFERENCES sportsverse.users (id) ON UPDATE CASCADE,
    price numeric(10,2) NOT NULL,
    pur_status sportsverse.purchase_status NOT NULL,
    CONSTRAINT purchase_price_check CHECK ((price > 0))
);


CREATE TABLE sportsverse.product_purchase (
    id_prod_var integer NOT NULL REFERENCES sportsverse.product_variation (id) ON UPDATE CASCADE,
    id_purchase integer NOT NULL REFERENCES sportsverse.purchase (id) ON UPDATE CASCADE,
    quantity integer NOT NULL,
    CONSTRAINT product_purchase_quantity_check CHECK ((quantity > 0))
);


CREATE TABLE sportsverse.expedition (
    id_purchase integer NOT NULL REFERENCES sportsverse.purchase (id) ON UPDATE CASCADE,
    delivery_date date NOT NULL,
    delivery_address text NOT NULL,
    shipping_cost numeric(10,2) NOT NULL,
    exp_status sportsverse.expedition_status NOT NULL,
    CONSTRAINT expedition_shipping_cost_check CHECK ((shipping_cost > 0))
);


-------------------------------------
-- INDEXES
-------------------------------------
CREATE INDEX user_purchases ON  sportsverse.purchase USING hash (id_user);


CREATE INDEX product_reviews ON  sportsverse.review USING hash (id_product);


CREATE INDEX product_variations ON  sportsverse.product_variation USING btree (id_prod);
ALTER TABLE sportsverse.product_variation CLUSTER ON product_variations;


-------------------------------------
-- FTS INDEXES
-------------------------------------


 -- Add column to work to store computed ts_vectors.
 ALTER TABLE sportsverse.product
 ADD COLUMN tsvectors TSVECTOR;
 
 
 -- Create a function to automatically update ts_vectors
CREATE OR REPLACE FUNCTION search_product_update_tf()
 RETURNS trigger
 LANGUAGE 'plpgsql'
 COST 100
 VOLATILE NOT LEAKPROOF
 AS $BODY$
 BEGIN
    IF (TG_OP = 'INSERT') THEN
        NEW.tsvectors = (
        setweight(to_tsvector('english', NEW.name), 'A') ||
        setweight(to_tsvector('english', NEW.short_description), 'B')
        );
    END IF;
    IF (TG_OP = 'UPDATE') THEN
        IF (NEW.name <> OLD.name OR NEW.short_description <> OLD.short_description)
        THEN NEW.tsvectors = (
            setweight(to_tsvector('english', NEW.name), 'A') ||
            setweight(to_tsvector('english', NEW.short_description), 'B')
        );
        END IF;
    END IF;
 RETURN NEW;
 END
 $BODY$;
 
 
 -- Create Trigger before insert or update on product
 CREATE TRIGGER search_product_update_t
    BEFORE INSERT OR UPDATE
    ON sportsverse.product
    FOR EACH ROW
    EXECUTE FUNCTION search_product_update_tf();

-- Finally, create a GIN index for ts_vectors.
CREATE INDEX search_idx ON sportsverse.product USING GIN (tsvectors);


-------------------------------------
-- TRIGGERS AND UDFs
-------------------------------------


CREATE OR REPLACE FUNCTION update_product_rating()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
    AS $BODY$
    BEGIN
        UPDATE sportsverse.product
        SET rating = (SELECT AVG(rating) FROM sportsverse.review)
        WHERE NEW.id_product = product.id;
        RETURN NEW;
    END


$BODY$;


CREATE TRIGGER update_rating
    AFTER INSERT OR UPDATE
    ON sportsverse.review
    FOR EACH ROW
    EXECUTE FUNCTION update_product_rating();



CREATE OR REPLACE FUNCTION update_product_stock()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
    AS $BODY$
    BEGIN
        UPDATE sportsverse.product_variation SET stock=stock-NEW.quantity WHERE sportsverse.product_variation.id=NEW.id_prod_var;
        RETURN NEW;
    END
$BODY$;


CREATE TRIGGER update_stock
    BEFORE INSERT
    ON sportsverse.product_purchase
    FOR EACH ROW
    EXECUTE FUNCTION update_product_stock();



CREATE OR REPLACE FUNCTION delete_user_tf()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS $BODY$
BEGIN
    UPDATE sportsverse.users SET name = 'Anonymous', password = 'dummy', birthdate = '1901-01-01', address = 'Anonymous', phone_number = 0 WHERE id = OLD.id;
    IF NOT FOUND THEN RETURN NULL; 
    END IF;
    RETURN NULL;
END
$BODY$;


CREATE TRIGGER delete_user_t
    BEFORE DELETE
    ON sportsverse.users
    FOR EACH ROW
    EXECUTE FUNCTION delete_user_tf();


CREATE OR REPLACE FUNCTION limit_product_images()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
    AS $BODY$
    BEGIN
        IF ((SELECT COUNT(NEW.id) FROM sportsverse.product_image WHERE sportsverse.product_image.id_prod_var = NEW.id_prod_var) > 11) THEN
            RAISE EXCEPTION 'Cannot insert more than 12 images';
        ELSE
            RETURN NEW;
        END IF;
    END

$BODY$;


CREATE TRIGGER limit_product_images
    BEFORE INSERT
    ON sportsverse.product_image
    FOR EACH ROW
    EXECUTE FUNCTION limit_product_images();



CREATE OR REPLACE FUNCTION blocked_user_tf()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
    AS $BODY$
    BEGIN
        IF EXISTS (SELECT * FROM sportsverse.users WHERE id = NEW.id_user AND blocked = TRUE) THEN
            RAISE EXCEPTION 'Blocked users are not able to make a purchase';
        ELSE
            RETURN NEW;
        END IF;
    END

$BODY$;


CREATE TRIGGER blocked_user_t
    BEFORE INSERT
    ON sportsverse.purchase
    FOR EACH ROW
    EXECUTE FUNCTION blocked_user_tf();



CREATE OR REPLACE FUNCTION blocked_user_review_tf()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
    AS $BODY$
    BEGIN
        IF EXISTS (SELECT * FROM sportsverse.users WHERE id = NEW.id_user AND blocked = TRUE) THEN
            RAISE EXCEPTION 'Blocked users are not able to make a review';
        ELSE
            RETURN NEW;
        END IF;
    END

$BODY$;


CREATE TRIGGER blocked_user_review_t
    BEFORE INSERT
    ON sportsverse.review
    FOR EACH ROW
    EXECUTE FUNCTION blocked_user_review_tf();

-------------------------------------
-- END
-------------------------------------
