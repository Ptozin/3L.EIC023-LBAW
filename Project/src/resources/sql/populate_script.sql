-------------------------------------
-- DROP OLD SCHEMA
-------------------------------------
-------------------------------------
-- USER DE TESTE: admin@gmail.com   1234
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

SET search_path TO sportsverse;

DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS category CASCADE;
DROP TABLE IF EXISTS sub_category CASCADE;
DROP TABLE IF EXISTS color CASCADE;
DROP TABLE IF EXISTS size CASCADE;
DROP TABLE IF EXISTS product CASCADE;
DROP TABLE IF EXISTS product_variation CASCADE;
DROP TABLE IF EXISTS product_image CASCADE;
DROP TABLE IF EXISTS review CASCADE;
DROP TABLE IF EXISTS shopping_cart CASCADE;
DROP TABLE IF EXISTS wishlist CASCADE;
DROP TABLE IF EXISTS purchase CASCADE;
DROP TABLE IF EXISTS product_purchase CASCADE;
DROP TABLE IF EXISTS expedition CASCADE;
DROP TABLE IF EXISTS notifications CASCADE;


DROP TYPE IF EXISTS expedition_status CASCADE;
DROP TYPE IF EXISTS purchase_status CASCADE;
DROP TYPE IF EXISTS notification_status CASCADE;


-------------------------------------
-- TYPES
-------------------------------------


CREATE TYPE expedition_status AS ENUM (
    'Created',
    'In Transit',
    'Delivered'
);


CREATE TYPE purchase_status AS ENUM (
    'Payment Pending',
    'Processing',
    'Shipping',
    'Canceled',
    'Concluded'
);

CREATE TYPE notification_status as ENUM (
    'Product available',
    'Product out of stock',
    'Price change'
);


-------------------------------------
-- TABLES
-------------------------------------


CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email text NOT NULL UNIQUE,
    name text NOT NULL,
    password text NOT NULL,
    birthdate date NOT NULL,
    address text NOT NULL,
    phone_number numeric NOT NULL,
    is_admin boolean DEFAULT false NOT NULL,
    blocked boolean NOT NULL DEFAULT false,
    remember_token VARCHAR, -- Laravel related
    CONSTRAINT birthdate_ck CHECK ((birthdate <= CURRENT_DATE))
);


CREATE TABLE category (
    id SERIAL PRIMARY KEY,
    name text NOT NULL
);


CREATE TABLE sub_category (
    id SERIAL PRIMARY KEY,
    name text NOT NULL,
    id_category integer NOT NULL
);


CREATE TABLE color (
    id SERIAL PRIMARY KEY,
    color text NOT NULL
);


CREATE TABLE size (
    id SERIAL PRIMARY KEY,
    size text NOT NULL
);


CREATE TABLE product (
    id SERIAL PRIMARY KEY,
    name text NOT NULL,
    short_description text NOT NULL,
    long_description text NOT NULL,
    rating numeric(2,1) NOT NULL,
    manufacturer text NOT NULL,
    id_sub_category integer NOT NULL REFERENCES sub_category (id) ON UPDATE CASCADE,
    CONSTRAINT product_rating_ck CHECK ((rating >= 0 AND rating <= 5))
);


CREATE TABLE product_variation (
    id SERIAL PRIMARY KEY,
    id_prod integer NOT NULL REFERENCES product (id) ON UPDATE CASCADE,
    stock integer NOT NULL,
    price numeric(10,2) NOT NULL,
    id_size integer NOT NULL REFERENCES size (id) ON UPDATE CASCADE,
    id_color integer NOT NULL REFERENCES color (id) ON UPDATE CASCADE,
    CONSTRAINT product_variation_price_check CHECK ((price > 0)),
    CONSTRAINT product_variation_stock_check CHECK ((stock >= 0))
);


CREATE TABLE product_image (
    id SERIAL PRIMARY KEY,
    url text NOT NULL,
    product_variation_id integer NOT NULL REFERENCES product_variation (id) ON UPDATE CASCADE
);


CREATE TABLE review (
    user_id integer NOT NULL REFERENCES users (id) ON UPDATE CASCADE,
    id_product integer NOT NULL REFERENCES product (id) ON UPDATE CASCADE,
    comment text NOT NULL,
    rating integer DEFAULT 4 NOT NULL,
    date date NOT NULL,
    CONSTRAINT Review_rating_ck CHECK ((rating >= 0 AND rating <= 5))
);


CREATE TABLE shopping_cart (
    product_variation_id integer NOT NULL REFERENCES product_variation (id) ON UPDATE CASCADE,
    user_id integer NOT NULL REFERENCES users (id) ON UPDATE CASCADE,
    quantity integer NOT NULL,
    CONSTRAINT shopping_cart_quantity_check CHECK ((quantity > 0))
);


CREATE TABLE wishlist (
    product_variation_id integer NOT NULL REFERENCES product_variation (id) ON UPDATE CASCADE,
    user_id integer NOT NULL REFERENCES users (id) ON UPDATE CASCADE
);


CREATE TABLE purchase (
    id SERIAL PRIMARY KEY,
    payment_method text NOT NULL,
    date date NOT NULL,
    user_id integer NOT NULL REFERENCES users (id) ON UPDATE CASCADE,
    price numeric(10,2) NOT NULL,
    pur_status purchase_status NOT NULL,
    CONSTRAINT purchase_price_check CHECK ((price > 0))
);


CREATE TABLE product_purchase (
    product_variation_id integer NOT NULL REFERENCES product_variation (id) ON UPDATE CASCADE,
    purchase_id integer NOT NULL REFERENCES purchase (id) ON UPDATE CASCADE,
    quantity integer NOT NULL,
    CONSTRAINT product_purchase_quantity_check CHECK ((quantity > 0))
);


CREATE TABLE expedition (
    purchase_id integer NOT NULL REFERENCES purchase (id) ON UPDATE CASCADE,
    delivery_date date NOT NULL,
    delivery_address text NOT NULL,
    shipping_cost numeric(10,2) NOT NULL,
    exp_status expedition_status NOT NULL,
    CONSTRAINT expedition_shipping_cost_check CHECK ((shipping_cost > 0))
);

CREATE TABLE notifications (
    id SERIAL PRIMARY KEY,
    user_id integer NOT NULL REFERENCES users (id) ON UPDATE CASCADE,
    product_variation_id integer NOT NULL REFERENCES product_variation (id) ON UPDATE CASCADE,
    notification_type notification_status NOT NULL
);


-------------------------------------
-- INDEXES
-------------------------------------
CREATE INDEX user_purchases ON  purchase USING hash (user_id);


CREATE INDEX product_reviews ON  review USING hash (id_product);


CREATE INDEX product_variations ON  product_variation USING btree (id_prod);
ALTER TABLE product_variation CLUSTER ON product_variations;


-------------------------------------
-- FTS INDEXES
-------------------------------------


 -- Add column to work to store computed ts_vectors.
 ALTER TABLE product
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
    ON product
    FOR EACH ROW
    EXECUTE FUNCTION search_product_update_tf();

-- Finally, create a GIN index for ts_vectors.
CREATE INDEX search_idx ON product USING GIN (tsvectors);


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
        UPDATE product
        SET rating = (SELECT AVG(rating) FROM review)
        WHERE NEW.id_product = product.id;
        RETURN NEW;
    END


$BODY$;


CREATE TRIGGER update_rating
    AFTER INSERT OR UPDATE
    ON review
    FOR EACH ROW
    EXECUTE FUNCTION update_product_rating();



CREATE OR REPLACE FUNCTION update_product_stock()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
    AS $BODY$
    BEGIN
        UPDATE product_variation SET stock=stock-NEW.quantity WHERE product_variation.id=NEW.product_variation_id;
        RETURN NEW;
    END
$BODY$;


CREATE TRIGGER update_stock
    BEFORE INSERT
    ON product_purchase
    FOR EACH ROW
    EXECUTE FUNCTION update_product_stock();



CREATE OR REPLACE FUNCTION delete_user_tf()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS $BODY$
BEGIN
    UPDATE users SET name = 'Anonymous', password = 'dummy', birthdate = '1901-01-01', address = 'Anonymous', phone_number = 0 WHERE id = OLD.id;
    IF NOT FOUND THEN RETURN NULL; 
    END IF;
    RETURN NULL;
END
$BODY$;


CREATE TRIGGER delete_user_t
    BEFORE DELETE
    ON users
    FOR EACH ROW
    EXECUTE FUNCTION delete_user_tf();


CREATE OR REPLACE FUNCTION limit_product_images()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
    AS $BODY$
    BEGIN
        IF ((SELECT COUNT(NEW.id) FROM product_image WHERE product_image.product_variation_id = NEW.product_variation_id) > 11) THEN
            RAISE EXCEPTION 'Cannot insert more than 12 images';
        ELSE
            RETURN NEW;
        END IF;
    END

$BODY$;


CREATE TRIGGER limit_product_images
    BEFORE INSERT
    ON product_image
    FOR EACH ROW
    EXECUTE FUNCTION limit_product_images();



CREATE OR REPLACE FUNCTION blocked_user_tf()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
    AS $BODY$
    BEGIN
        IF EXISTS (SELECT * FROM users WHERE id = NEW.user_id AND blocked = TRUE) THEN
            RAISE EXCEPTION 'Blocked users are not able to make a purchase';
        ELSE
            RETURN NEW;
        END IF;
    END

$BODY$;


CREATE TRIGGER blocked_user_t
    BEFORE INSERT
    ON purchase
    FOR EACH ROW
    EXECUTE FUNCTION blocked_user_tf();



CREATE OR REPLACE FUNCTION blocked_user_review_tf()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
    AS $BODY$
    BEGIN
        IF EXISTS (SELECT * FROM users WHERE id = NEW.user_id AND blocked = TRUE) THEN
            RAISE EXCEPTION 'Blocked users are not able to make a review';
        ELSE
            RETURN NEW;
        END IF;
    END

$BODY$;


CREATE TRIGGER blocked_user_review_t
    BEFORE INSERT
    ON review
    FOR EACH ROW
    EXECUTE FUNCTION blocked_user_review_tf();


-------------------------------------
-------------------------------------
-------------------------------------
-------------------------------------
-------------------------------------
-------------------------------------
-------------------------------------
-------------------------------------
-------------------------------------


INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('admin@gmail.com', 'Theo Atkinson', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', '1992-6-17', '887 Wintergreen Ave. Xenia, OH 45385', '9235285408', True);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('user@gmail.com', 'John Doe', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', '1992-6-17', '887 Wintergreen Ave. Xenia, OH 45385', '9235285408', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('williambrown@gmail.com', 'William Brown', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', '1951-2-14', '245 Glen Creek Lane Paducah, KY 42001', '9418313293', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('heathburns@gmail.com', 'Heath Burns', '2VqbSMqlOlF0b5lwenkpt.', '2002-4-1', '8385 Brook St. Davenport, IA 52804', '2203526032', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('callumkelly@gmail.com', 'Callum Kelly', 'QTfMuKEeNDq8w2QpuNgNx.', '1993-3-4', '73 Wintergreen Dr. Rockford, MI 49341', '9948291381', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('arthurtaylor@gmail.com', 'Arthur Taylor', 'RdpSHZsiMorsPWWWA9hyqe', '1951-7-27', '36 N. Hudson Road Hamtramck, MI 48212', '3591746750', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('benedictcunningham@gmail.com', 'Benedict Cunningham', 'Oe.3gf73O7Xj.xW7llE5hO', '1964-7-31', '8068 South Vernon Street Kissimmee, FL 34741', '5392137914', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('fabianhussain@gmail.com', 'Fabian Hussain', '6WD4WMbj/23TVQR.C6fdYO', '1977-1-16', '8451 Wood Dr. Grand Forks, ND 58201', '2216404894', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('ollyrees@gmail.com', 'Olly Rees', '05Re1BvvkFR2yhJq6PjrOe', '1962-2-17', '945 Pawnee St. Campbell, CA 95008', '9932688603', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('haydenmatthews@gmail.com', 'Hayden Matthews', 'ud/yUklxfJZ17f.afI0hDO', '2002-4-26', '820 Greystone Lane Buffalo Grove, IL 60089', '7243433847', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('albyholmes@gmail.com', 'Alby Holmes', '/dfHaKlvyqehcSYu1hMWpO', '1977-1-1', '176 Wall Court Geneva, IL 60134', '1983232199', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('ewanthomas@gmail.com', 'Ewan Thomas', '7bNKkso8KqqMBExjDjRthe', '1973-12-22', '815 Cleveland Street Lynnwood, WA 98037', '6502449139', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('carterhughes@gmail.com', 'Carter Hughes', 'QYD2C5Ppg1MXrwBccJsApe', '1958-2-14', '811 West Summer Road Westwood, NJ 07675', '3628804619', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('alanstevens@gmail.com', 'Alan Stevens', 'zjPOsSWbwu57StFLdlpHWu', '1948-2-14', '541 Homewood St. Central Islip, NY 11722', '3948303055', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('lochlannicholson@gmail.com', 'Lochlan Nicholson', '1sY0wOpm.l3t.d7sM2gNsu', '1995-10-2', '145 Bow Ridge Ave.West Warwick, RI 02893', '9615279289', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('sonnydavidson@gmail.com', 'Sonny Davidson', 'tmuYGs7uaphCjccWPH1Hq.', '1973-10-25', '9651 E. Talbot Court Franklin, MA 02038', '4912121707', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('marleygrant@gmail.com', 'Marley Grant', 'f7PrylUaeGCxdMz9fnOEre', '1971-6-22', '79 King Circle Prattville, AL 36067', '1664750774', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('jonasread@gmail.com', 'Jonas Read', 'XMOA4P.te7ELBs1Hf1R0M.', '1965-4-21', '7167 Queen Rd. Brentwood, NY 11717', '5706075695', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('micahking@gmail.com', 'Micah King', 'BKuEQET9tZ88kdMEuIKTBu', '1994-9-8', '28 Tallwood St. Bloomfield, NJ 07003', '5180091643', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('zacharydavis@gmail.com', 'Zachary Davis', 'M5u6iPX5qVPxxwt5G.hJeu', '1984-4-4', '239 Vale St. Medford, MA 02155', '2905441304', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('cilliandavies@gmail.com', 'Cillian Davies', 'Et7DrT8bSB.vboRHLz5Mxe', '1958-6-30', '149 Sierra St. Indian Trail, NC 28079', '7771048467', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('agnesbrown@gmail.com', 'Agnes Brown', 'JuBkYlST8kZvbzrX.Kh/tO', '1947-12-5', '6 Westport Dr. Sewell, NJ 08080', '7233280877', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('rowanmiller@gmail.com', 'Rowan Miller', 'g1TPbzwd/ZNUqo/WBijihu', '1987-12-31', '7342 53rd Dr. Buffalo, NY 14215', '6503004279', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('georginaread@gmail.com', 'Georgina Read', 'YqboTmcKAhJAVeXL8HF3uO', '1972-4-14', '252 East Leeton Ridge Street Oak Forest, IL 60452', '1398157700', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('floraturner@gmail.com', 'Flora Turner', 'InLNRa5F0kgYKJPTcC.kwe', '1978-9-18', '431 Trusel Rd. Mcallen, TX 78501', '8504973704', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('olivethompson@gmail.com', 'Olive Thompson', 'EMhdMAE7En4yEwiu6ogfue', '1948-5-10', '98 Marshall St. Twin Falls, ID 83301', '3204518709', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('sapphirehayes@gmail.com', 'Sapphire Hayes', 'myF/b0vAwHpNawrhZ.H02.', '1957-1-4', '55 Richardson Street Elgin, IL 60120', '9034278704', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('willowjohnston@gmail.com', 'Willow Johnston', 'Vg.t34mmtmSHZpnL7Cfv5e', '1994-1-12', '99 Marsh Lane Ronkonkoma, NY 11779', '2105222206', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('maryamcarr@gmail.com', 'Maryam Carr', '/.xi9/I9tMRd.lgWVaD82u', '1992-2-1', '8214 West Pennington Rd. Milton, MA 02186', '2855257176', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('poppybates@gmail.com', 'Poppy Bates', 'JYhrZatayLAIlUC8TY2q5O', '2004-4-22', '290 Cypress St. Winter Springs, FL 32708', '8368501175', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('jorgiedawson@gmail.com', 'Jorgie Dawson', 'tlEbFV0nHlWY3islu8G5b.', '1962-9-4', '35 Rock Creek Avenue Beaver Falls, PA 15010', '8601990690', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('ameliamarshall@gmail.com', 'Amelia Marshall', '5LPu0uDrwGoCKq00m7gbLe', '1940-7-13', '232 S. Sunset Ave. Culpeper, VA 22701', '7133661443', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('pippabrown@gmail.com', 'Pippa Brown', 'y6MMbNTXTJ1k.5rlsexQB.', '1975-6-7', '896 N. Surrey Avenue Hazleton, PA 18201', '9414224056', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('hannahriley@gmail.com', 'Hannah Riley', 'FtTUxXEcr7Tp8fojHnHuYO', '1945-12-22', '226 Paris Hill Ave. Stafford, VA 22554', '8626125066', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('matildajohnson@gmail.com', 'Matilda Johnson', 'pikoo8QaCMSA0fFvpsqYJO', '1989-3-9', '15 Ketch Harbour Dr. Easton, PA 18042', '6512450050', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('milanahunter@gmail.com', 'Milana Hunter', 'xXtspeqjCLVaC.eA3rTd3e', '2001-7-2', '9262 Beaver Ridge Lane El Dorado, AR 71730', '8315462855', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('remi bradley@gmail.com', 'Remi Bradley', '8hISz5jlCXcjTG9ZPkY6pe', '1940-1-26', '997 Brook Ave. Erlanger, KY 41018', '3557936700', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('jenniferwebb@gmail.com', 'Jennifer Webb', '7DsghCv7trInWJGD/HR53u', '2002-8-24', '7632 Wellington St. Quincy, MA 02169', '5208107478', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('winterblack@gmail.com', 'Winter Black', 'WlL4zwSppPhM4bOfHAUO..', '1977-7-17', '9724 Marsh St. Westland, MI 48185', '3457227375', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('darciechambers@gmail.com', 'Darcie Chambers', 'EV9u5HNp2aY1B7XH8Apor.', '1946-1-12', '8187 Princess Ave. Santa Clara, CA 95050', '4796587480', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('evieburns@gmail.com', 'Evie Burns', '8KflwPEgfRODXPo.h6I8vu', '2002-3-4', '72 Rockcrest St. Waterbury, CT 06705', '9333168847', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('jessbailey@gmail.com', 'Jess Bailey', 't7VSnrAWMFb5Cy45Sts1bu', '2004-4-19', '575 Orange St. Little Rock, AR 72209', '7155410487', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('aarenwallace@gmail.com', 'Aaren Wallace', 'GIk.nG8sIwNoToScGyOcZe', '2000-6-20', '9569 Prairie St. Billings, MT 59101', '6857280475', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('melmoore@gmail.com', 'Mel Moore', '.oYJvoUOk.MHGI2OvqnURO', '1967-6-17', '35 South Street Starkville, MS 39759', '3484990738', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('ashmorgan@gmail.com', 'Ash Morgan', 'p3t.2TKeg0/HrK1MRTyjMO', '1962-10-20', '879 Military St. West Deptford, NJ 08096', '6794040715', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('frankierichards@gmail.com', 'Frankie Richards', 'lOzw4nQWZspxDnVC7HdyXe', '1962-8-27', '31 North Street Houston, TX 77016', '4718105968', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('morganharper@gmail.com', 'Morgan Harper', 'DDVQ58/dZ5fDqAxyG5kJnO', '1980-5-27', '9680 South Logan Drive Londonderry, NH 03053', '6276569507', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('taylorharper@gmail.com', 'Taylor Harper', 'J9Rt2hHfZDdNPfayaNDva.', '1941-2-14', '985 Pumpkin Hill St. Gallatin, TN 37066', '5527857053', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('drewfletcher@gmail.com', 'Drew Fletcher', 'q.ZJtwPTq1/F7BCNoYnrcO', '1975-7-28', '6 Meadow St. Saratoga Springs, NY 12866', '9082152565', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('kaijohnston@gmail.com', 'Kai Johnston', 'XP3IDpNV56Gc.Vde2RbZ0O', '1975-1-19', '826 Sheffield St. Upper Marlboro, MD 20772', '2573860652', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('gailmurray@gmail.com', 'Gail Murray', 'Plw7M7QE6DmT5PtURpUUsu', '1987-11-14', '7516 Spruce Ave. Taylors, SC 29687', '7715898106', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('blakejames@gmail.com', 'Blake James', 'mH8i.aETC4A42WZxRlpybu', '1970-9-30', '97 Hartford Street Blackwood, NJ 08012', '2503565027', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('terryfletcher@gmail.com', 'Terry Fletcher', 'ggov5pSzrvQo2DbKhWHCge', '1977-10-30', '7756 Sugar Street Bayside, NY 11361', '1476610283', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('gailmurphy@gmail.com', 'Gail Murphy', 'KjsLMapcafGTJXGl0MZade', '1996-6-4', '7040 Coffee St. Boynton Beach, FL 33435', '3196874098', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('reedkennedy@gmail.com', 'Reed Kennedy', '3qw.LDN0VUxLOvRaWxC/LO', '1963-12-26', '475 Anderson Drive Essex, MD 21221', '8258272940', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('billyharvey@gmail.com', 'Billy Harvey', 'yFHd7qCxv7pTj9VddTAaPu', '1987-11-18', '98 Bishop Ave. Palm Beach Gardens, FL 33410', '5228392708', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('glenngallagher@gmail.com', 'Glenn Gallagher', '3.tajgGYSy3edRopolRXTe', '1991-11-7', '18 E. Hudson Court Lancaster, NY 14086', '4460418037', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('rileymatthews@gmail.com', 'Riley Matthews', 'ZBnvHL8PeYHUW/CClNBfHO', '1984-4-4', '7855 Division Rd. Youngstown, OH 44512', '4438700210', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('jadenball@gmail.com', 'Jaden Ball', '6AEWdKFdvxuUUnO1P1aCDe', '1969-7-14', '611 Sherman Court Woodbridge, VA 22191', '6468033801', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('jordancollins@gmail.com', 'Jordan Collins', 'CAE/MYrtkQpz/ZwTT1kajO', '1957-8-23', '9147 Sutor Street Barberton, OH 44203', '1694167386', False);
INSERT INTO users(email, name, password, birthdate, address, phone_number, is_admin) VALUES ('kiranyoung@gmail.com', 'Kiran Young', 'ekWeEUYkL6dGkhN0VUxGxS', '1969-1-10', '9183 Big Rock Cove Drive Sterling Heights, MI 4831', '5947928458', False);

-- Color

INSERT INTO color(color) VALUES ('NOTHING');
INSERT INTO color(color) VALUES ('White');
INSERT INTO color(color) VALUES ('Yellow');
INSERT INTO color(color) VALUES ('Blue');
INSERT INTO color(color) VALUES ('Red');
INSERT INTO color(color) VALUES ('Green');
INSERT INTO color(color) VALUES ('Black');
INSERT INTO color(color) VALUES ('Brown');
INSERT INTO color(color) VALUES ('Azure');
INSERT INTO color(color) VALUES ('Ivory');
INSERT INTO color(color) VALUES ('Teal');
INSERT INTO color(color) VALUES ('Silver');
INSERT INTO color(color) VALUES ('Purple');
INSERT INTO color(color) VALUES ('Navy blue');
INSERT INTO color(color) VALUES ('Pea green');
INSERT INTO color(color) VALUES ('Gray');
INSERT INTO color(color) VALUES ('Orange');
INSERT INTO color(color) VALUES ('Maroon');
INSERT INTO color(color) VALUES ('Charcoal');
INSERT INTO color(color) VALUES ('Aquamarine');
INSERT INTO color(color) VALUES ('Coral');
INSERT INTO color(color) VALUES ('Fuchsia');
INSERT INTO color(color) VALUES ('Wheat');
INSERT INTO color(color) VALUES ('Lime');
INSERT INTO color(color) VALUES ('Crimson');
INSERT INTO color(color) VALUES ('Khaki');
INSERT INTO color(color) VALUES ('Hot pink');
INSERT INTO color(color) VALUES ('Magenta');
INSERT INTO color(color) VALUES ('Olden');
INSERT INTO color(color) VALUES ('Plum');
INSERT INTO color(color) VALUES ('Olive');
INSERT INTO color(color) VALUES ('Cyan');

-- Size

INSERT INTO size(size) VALUES ('NOTHING');
INSERT INTO size(size) VALUES ('XXSmall');
INSERT INTO size(size) VALUES ('XSmall');
INSERT INTO size(size) VALUES ('Small');
INSERT INTO size(size) VALUES ('Medium');
INSERT INTO size(size) VALUES ('Large');
INSERT INTO size(size) VALUES ('XLarge');
INSERT INTO size(size) VALUES ('2XLarge');
INSERT INTO size(size) VALUES ('3XLarge');
INSERT INTO size(size) VALUES ('0');
INSERT INTO size(size) VALUES ('1');
INSERT INTO size(size) VALUES ('2');
INSERT INTO size(size) VALUES ('3');
INSERT INTO size(size) VALUES ('4');
INSERT INTO size(size) VALUES ('5');
INSERT INTO size(size) VALUES ('6');
INSERT INTO size(size) VALUES ('7');
INSERT INTO size(size) VALUES ('8');
INSERT INTO size(size) VALUES ('9');
INSERT INTO size(size) VALUES ('10');
INSERT INTO size(size) VALUES ('11');
INSERT INTO size(size) VALUES ('12');
INSERT INTO size(size) VALUES ('13');
INSERT INTO size(size) VALUES ('14');
INSERT INTO size(size) VALUES ('15');
INSERT INTO size(size) VALUES ('16');
INSERT INTO size(size) VALUES ('17');
INSERT INTO size(size) VALUES ('18');
INSERT INTO size(size) VALUES ('19');
INSERT INTO size(size) VALUES ('20');
INSERT INTO size(size) VALUES ('21');
INSERT INTO size(size) VALUES ('22');
INSERT INTO size(size) VALUES ('23');
INSERT INTO size(size) VALUES ('24');
INSERT INTO size(size) VALUES ('25');
INSERT INTO size(size) VALUES ('26');
INSERT INTO size(size) VALUES ('27');
INSERT INTO size(size) VALUES ('28');
INSERT INTO size(size) VALUES ('29');
INSERT INTO size(size) VALUES ('30');
INSERT INTO size(size) VALUES ('31');
INSERT INTO size(size) VALUES ('32');
INSERT INTO size(size) VALUES ('33');
INSERT INTO size(size) VALUES ('34');
INSERT INTO size(size) VALUES ('35');
INSERT INTO size(size) VALUES ('36');
INSERT INTO size(size) VALUES ('37');
INSERT INTO size(size) VALUES ('38');
INSERT INTO size(size) VALUES ('39');
INSERT INTO size(size) VALUES ('40');
INSERT INTO size(size) VALUES ('41');
INSERT INTO size(size) VALUES ('42');
INSERT INTO size(size) VALUES ('43');
INSERT INTO size(size) VALUES ('44');
INSERT INTO size(size) VALUES ('45');
INSERT INTO size(size) VALUES ('46');
INSERT INTO size(size) VALUES ('47');
INSERT INTO size(size) VALUES ('48');
INSERT INTO size(size) VALUES ('49');
INSERT INTO size(size) VALUES ('50');
INSERT INTO size(size) VALUES ('51');
INSERT INTO size(size) VALUES ('52');
INSERT INTO size(size) VALUES ('53');
INSERT INTO size(size) VALUES ('54');
INSERT INTO size(size) VALUES ('55');
INSERT INTO size(size) VALUES ('56');
INSERT INTO size(size) VALUES ('57');
INSERT INTO size(size) VALUES ('58');
INSERT INTO size(size) VALUES ('59');

-- Category

INSERT INTO category(name) VALUES ('Protein');
INSERT INTO category(name) VALUES ('Build Muscle');
INSERT INTO category(name) VALUES ('Energy & Endurance');
INSERT INTO category(name) VALUES ('Fat Burners & Muscle Definition');
INSERT INTO category(name) VALUES ('Athlete''s Health');
INSERT INTO category(name) VALUES ('Accessories Home Gym');
INSERT INTO category(name) VALUES ('Home Gym');

-- Sub category

INSERT INTO sub_category(name, id_category) VALUES ('Whey Protein', 1);
INSERT INTO sub_category(name, id_category) VALUES ('Whey Isolate & Native Whey', 1);
INSERT INTO sub_category(name, id_category) VALUES ('Whey Protein Hydrolysate', 1);
INSERT INTO sub_category(name, id_category) VALUES ('Casein & Slow-Release Protein', 1);
INSERT INTO sub_category(name, id_category) VALUES ('Egg & Beef Protein', 1);
INSERT INTO sub_category(name, id_category) VALUES ('Mass Gainers', 1);
INSERT INTO sub_category(name, id_category) VALUES ('Vegetable Protein', 1);
INSERT INTO sub_category(name, id_category) VALUES ('Collagen', 1);
INSERT INTO sub_category(name, id_category) VALUES ('Diet Protein', 1);
INSERT INTO sub_category(name, id_category) VALUES ('Protein Bars', 1);
INSERT INTO sub_category(name, id_category) VALUES ('Protein Snacks', 1);
INSERT INTO sub_category(name, id_category) VALUES ('Protein Food', 1);
INSERT INTO sub_category(name, id_category) VALUES ('Ready-to-Drink', 2);
INSERT INTO sub_category(name, id_category) VALUES ('Whey Protein', 2);
INSERT INTO sub_category(name, id_category) VALUES ('Whey Isolate & Native Whey', 2);
INSERT INTO sub_category(name, id_category) VALUES ('Mass Gainers', 2);
INSERT INTO sub_category(name, id_category) VALUES ('Carbohydrates', 3);
INSERT INTO sub_category(name, id_category) VALUES ('Pre-Workout & Nitric Oxide', 3);
INSERT INTO sub_category(name, id_category) VALUES ('Intra-Workout', 3);
INSERT INTO sub_category(name, id_category) VALUES ('Post-Workout Recovery', 3);
INSERT INTO sub_category(name, id_category) VALUES ('Creatine', 3);
INSERT INTO sub_category(name, id_category) VALUES ('BCAAs', 3);
INSERT INTO sub_category(name, id_category) VALUES ('EAAs (Essential Amino Acids)', 3);
INSERT INTO sub_category(name, id_category) VALUES ('Glutamine', 3);
INSERT INTO sub_category(name, id_category) VALUES ('Amino Acids', 3);
INSERT INTO sub_category(name, id_category) VALUES ('Testosterone Boosters', 4);
INSERT INTO sub_category(name, id_category) VALUES ('Pre-Workout & Nitric Oxide', 4);
INSERT INTO sub_category(name, id_category) VALUES ('Intra-Workout', 4);
INSERT INTO sub_category(name, id_category) VALUES ('Post-Workout Recovery', 4);
INSERT INTO sub_category(name, id_category) VALUES ('Caffeine', 4);
INSERT INTO sub_category(name, id_category) VALUES ('Creatine', 4);
INSERT INTO sub_category(name, id_category) VALUES ('BCAAs', 4);
INSERT INTO sub_category(name, id_category) VALUES ('L-Carnitine', 4);
INSERT INTO sub_category(name, id_category) VALUES ('Energy Series', 4);
INSERT INTO sub_category(name, id_category) VALUES ('Energy Bars', 4);
INSERT INTO sub_category(name, id_category) VALUES ('Energy Gels', 4);
INSERT INTO sub_category(name, id_category) VALUES ('Energy Drinks', 4);
INSERT INTO sub_category(name, id_category) VALUES ('Electrolyte & Isotonic Drinks', 4);
INSERT INTO sub_category(name, id_category) VALUES ('Carbohydrates', 5);
INSERT INTO sub_category(name, id_category) VALUES ('Thermogenics', 5);
INSERT INTO sub_category(name, id_category) VALUES ('Stimulant-Free Fat Burners', 5);
INSERT INTO sub_category(name, id_category) VALUES ('Fat-Burning Creams', 5);
INSERT INTO sub_category(name, id_category) VALUES ('CLA', 5);
INSERT INTO sub_category(name, id_category) VALUES ('L-Carnitine', 5);
INSERT INTO sub_category(name, id_category) VALUES ('Carb Blockers', 5);
INSERT INTO sub_category(name, id_category) VALUES ('Appetite Control', 5);
INSERT INTO sub_category(name, id_category) VALUES ('Diuretics & Detox', 5);
INSERT INTO sub_category(name, id_category) VALUES ('Diet Protein', 5);
INSERT INTO sub_category(name, id_category) VALUES ('Zero & Diet Products', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Vitamins & Minerals', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Joints, Cartilage & Bones', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Antioxidants & Herbs', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Omega-3 & Other Fatty Acids', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Liver & Detox', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Prohormones', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Shakers', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Bottles', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Lunch Bags', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Pillboxes & Containers', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Exercise & Yoga Mats', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Weights & Bars', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Ropes', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Training Equipment', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Sports Gloves', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Belts & Supports', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Towels', 6);
INSERT INTO sub_category(name, id_category) VALUES ('Muscle Stimulation & Relaxation', 6);

-- Product

INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('100% Real Whey Protein 1000 g', '100% Real Whey Protein 1000 g', '100% Real Whey Protein 1000 g', 4.5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('100% Whey Prime 1000 g', '100% Whey Prime 1000 g', '100% Whey Prime 1000 g', 2.5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Xtreme Whey Protein 2000 g', 'Xtreme Whey Protein 2000 g', 'Xtreme Whey Protein 2000 g', 4.9 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Zero Diet Whey 750 g', 'Zero Diet Whey 750 g', 'Zero Diet Whey 750 g', 1.5 , 'sportsverse', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Whey Protein Fusion 900 g', 'Whey Protein Fusion 900 g', 'Whey Protein Fusion 900 g', 1.5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('100% Whey Premium Protein 900 g', '100% Whey Premium Protein 900 g', '100% Whey Premium Protein 900 g', 4.4 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('100% Real Whey Protein 400 g', '100% Real Whey Protein 400 g', '100% Real Whey Protein 400 g', 1.5 , 'sportsverse', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Natural Real Whey Protein 1000 g', 'Natural Real Whey Protein 1000 g', 'Natural Real Whey Protein 1000 g', 4.5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('100% Whey Prime 400 g', '100% Whey Prime 400 g', '100% Whey Prime 400 g', 1.5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Whey Protein - Freakin Good 400 g', 'Whey Protein - Freakin Good 400 g', 'Whey Protein - Freakin Good 400 g', 4.7 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Whey + Fibre 900 g', 'Whey + Fibre 900 g', 'Whey + Fibre 900 g', 2 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Sachet 100%  Real Whey Protein 25 g', 'Sachet 100%  Real Whey Protein 25 g', 'Sachet 100%  Real Whey Protein 25 g', 3 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Protein Cappuccino 400 g', 'Protein Cappuccino 400 g', 'Protein Cappuccino 400 g', 1.2 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Protein Caramel Latte - Extra Caffeine 400 g', 'Protein Caramel Latte - Extra Caffeine 400 g', 'Protein Caramel Latte - Extra Caffeine 400 g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Protein Mochaccino - Extra Caffeine 400 g', 'Protein Mochaccino - Extra Caffeine 400 g', 'Protein Mochaccino - Extra Caffeine 400 g', 3.6 , 'sportsverse', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Protein Caramel Latte 400 g', 'Protein Caramel Latte 400 g', 'Protein Caramel Latte 400 g', 3.4 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Protein Hazelnut Latte - Extra Caffeine 400 g', 'Protein Hazelnut Latte - Extra Caffeine 400 g', 'Protein Hazelnut Latte - Extra Caffeine 400 g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('10 x 100% Whey Premium Protein 25 g', '10 x 100% Whey Premium Protein 25 g', '10 x 100% Whey Premium Protein 25 g', 2.1 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Sachet 100% Whey Prime 25 g', 'Sachet 100% Whey Prime 25 g', 'Sachet 100% Whey Prime 25 g', 3.5 , 'sportsverse', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Sachet Zero Diet Whey 21 g', 'Sachet Zero Diet Whey 21 g', 'Sachet Zero Diet Whey 21 g', 3.5 , 'sportsverse', 1);
--INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Protein Latte 400 g', 'Protein Latte 400 g', 'Protein Latte 400 g', 5 , 'XTREME', 1);
/* INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Protein Hazelnut Latte 400 g', 'Protein Hazelnut Latte 400 g', 'Protein Hazelnut Latte 400 g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Protein Latte - Extra Caffeine 400 g', 'Protein Latte - Extra Caffeine 400 g', 'Protein Latte - Extra Caffeine 400 g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Protein Mochaccino 400 g', 'Protein Mochaccino 400 g', 'Protein Mochaccino 400 g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('XCESS Thermo Whey 700 g', 'XCESS Thermo Whey 700 g', 'XCESS Thermo Whey 700 g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Sachet Xtreme Whey Protein 25 g', 'Sachet Xtreme Whey Protein 25 g', 'Sachet Xtreme Whey Protein 25 g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Sachet Natural Real Whey Protein 25 g', 'Sachet Natural Real Whey Protein 25 g', 'Sachet Natural Real Whey Protein 25 g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Sachet Whey Protein - Freakin Good 30 g', 'Sachet Whey Protein - Freakin Good 30 g', 'Sachet Whey Protein - Freakin Good 30 g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Protein Walnut Latte - Extra Caffeine 400 g', 'Protein Walnut Latte - Extra Caffeine 400 g', 'Protein Walnut Latte - Extra Caffeine 400 g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Protein Walnut Latte 400 g', 'Protein Walnut Latte 400 g', 'Protein Walnut Latte 400 g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Men&#39;s Beginner Pack', 'Men&#39;s Beginner Pack', 'Men&#39;s Beginner Pack', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('2 x Sachet Whey Protein Fusion 31 g', '2 x Sachet Whey Protein Fusion 31 g', '2 x Sachet Whey Protein Fusion 31 g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Strength Training Pack', 'Strength Training Pack', 'Strength Training Pack', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Zero Diet Whey 1500 g', 'Zero Diet Whey 1500 g', 'Zero Diet Whey 1500 g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Women&#39;s Beginner Pack', 'Women&#39;s Beginner Pack', 'Women&#39;s Beginner Pack', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('100% Real Whey Protein 2000g', '100% Real Whey Protein 2000g', '100% Real Whey Protein 2000g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Natural Real Whey Protein 2000g', 'Natural Real Whey Protein 2000g', 'Natural Real Whey Protein 2000g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('100% Whey Professional 900 g', '100% Whey Professional 900 g', '100% Whey Professional 900 g', 5 , 'XTREME', 1);
INSERT INTO product(name, short_description, long_description, rating, manufacturer, id_sub_category) VALUES ('Protein Cappuccino - Extra Caffeine 400 g', 'Protein Cappuccino - Extra Caffeine 400 g', 'Protein Cappuccino - Extra Caffeine 400 g', 5 , 'XTREME', 1); 
*/

-- Product Variation

INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (1,9,29.99,25,25);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (1,15,29.99,30,30);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (2,8,62.99,20,23);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (2,5,34.99,22,23);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (3,6,27.99,13,5);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (3,8,33.99,12,14);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (4,5,14.99,28,29);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (4,8,31.99,18,8);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (5,14,14.99,4,3);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (5,6,12.99,28,10);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (6,4,22.99,24,7);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (6,11,0.99,28,17);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (7,8,14.99,12,7);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (7,3,14.99,3,2);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (8,2,14.99,30,6);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (8,3,14.99,16,3);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (9,10,14.99,23,22);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (9,13,11.99,7,17);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (10,5,0.99,28,18);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (10,11,0.99,9,21);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (11,11,14.99,29,7);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (11,14,14.99,27,8);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (12,5,14.99,29,2);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (12,7,14.99,1,2);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (13,8,42.99,6,21);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (13,16,0.99,20,25);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (14,7,1.19,23,19);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (14,1,1.09,7,10);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (15,4,14.99,16,29);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (15,14,14.99,20,19);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (16,15,80.71,26,24);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (16,4,2.38,28,16);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (17,14,101.60,12,1);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (17,9,63.89,7,15);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (18,8,59.82,18,29);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (18,4,56.08,9,23);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (19,15,57.58,27,9);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (19,0,45.99,17,24);
INSERT INTO product_variation (id_prod, stock, price, id_size, id_color) VALUES (20,0,14.99,1,9);

-- product image

INSERT INTO product_image(product_variation_id, url) VALUES (1, '3.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (1, '4.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (1, '5.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (2, '6.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (2, '7.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (2, '8.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (3, '9.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (3, '10.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (3, '11.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (4, '12.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (4, '13.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (4, '14.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (5, '15.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (5, '16.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (5, '17.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (6, '18.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (6, '19.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (6, '20.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (7, '21.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (7, '22.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (7, '23.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (8, '24.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (8, '25.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (8, '26.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (9, '27.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (9, '28.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (9, '29.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (10, '30.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (10, '31.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (10, '32.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (11, '33.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (11, '34.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (11, '35.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (12, '36.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (12, '37.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (12, '38.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (13, '39.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (13, '40.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (13, '41.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (14, '42.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (14, '43.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (14, '44.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (15, '45.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (15, '46.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (15, '47.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (16, '48.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (16, '49.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (16, '50.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (17, '51.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (17, '52.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (17, '53.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (18, '54.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (18, '55.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (18, '56.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (19, '57.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (19, '58.jpg');
INSERT INTO product_image(product_variation_id, url) VALUES (19, '59.jpg');


-- shopping cart

INSERT INTO shopping_cart (product_variation_id, user_id, quantity) VALUES (1, 2, 2);
INSERT INTO shopping_cart (product_variation_id, user_id, quantity) VALUES (2, 3, 4);
INSERT INTO shopping_cart (product_variation_id, user_id, quantity) VALUES (3, 4, 2);
INSERT INTO shopping_cart (product_variation_id, user_id, quantity) VALUES (4, 5, 2);
INSERT INTO shopping_cart (product_variation_id, user_id, quantity) VALUES (5, 6, 2);
INSERT INTO shopping_cart (product_variation_id, user_id, quantity) VALUES (33, 7, 2);
INSERT INTO shopping_cart (product_variation_id, user_id, quantity) VALUES (34, 8, 2);
INSERT INTO shopping_cart (product_variation_id, user_id, quantity) VALUES (35, 9, 2);
INSERT INTO shopping_cart (product_variation_id, user_id, quantity) VALUES (36, 10, 2);
INSERT INTO shopping_cart (product_variation_id, user_id, quantity) VALUES (37, 11, 2);

-- wishlist

INSERT INTO wishlist (product_variation_id, user_id) VALUES (38, 6);
INSERT INTO wishlist (product_variation_id, user_id) VALUES (39, 7);
INSERT INTO wishlist (product_variation_id, user_id) VALUES (38, 8);
INSERT INTO wishlist (product_variation_id, user_id) VALUES (39, 9);
INSERT INTO wishlist (product_variation_id, user_id) VALUES (24, 10);
INSERT INTO wishlist (product_variation_id, user_id) VALUES (24, 11);
INSERT INTO wishlist (product_variation_id, user_id) VALUES (38, 12);
INSERT INTO wishlist (product_variation_id, user_id) VALUES (24, 13);
INSERT INTO wishlist (product_variation_id, user_id) VALUES (38, 14);
INSERT INTO wishlist (product_variation_id, user_id) VALUES (39, 15);

-- purchase
INSERT INTO purchase (payment_method, date, user_id, price, pur_status) VALUES ('credit card', '2022-1-10', 3, 29.99, 'Concluded');
INSERT INTO purchase (payment_method, date, user_id, price, pur_status) VALUES ('paypal', '2022-1-12', 4, 29.99, 'Shipping');
INSERT INTO purchase (payment_method, date, user_id, price, pur_status) VALUES ('bank transfer', '2022-1-11', 15, 2.97, 'Concluded');
INSERT INTO purchase (payment_method, date, user_id, price, pur_status) VALUES ('paypal', '2022-1-12', 16, 44.97, 'Shipping');
INSERT INTO purchase (payment_method, date, user_id, price, pur_status) VALUES ('credit card', '2022-1-12', 17, 29.98, 'Concluded');
INSERT INTO purchase (payment_method, date, user_id, price, pur_status) VALUES ('credit card', '2022-1-10', 1, 29.99, 'Concluded');
INSERT INTO purchase (payment_method, date, user_id, price, pur_status) VALUES ('credit card', '2022-1-11', 1, 29.99, 'Concluded');

-- product purchase

INSERT INTO product_purchase (product_variation_id, purchase_id, quantity) VALUES (1, 1, 1);
INSERT INTO product_purchase (product_variation_id, purchase_id, quantity) VALUES (2, 2, 1);
INSERT INTO product_purchase (product_variation_id, purchase_id, quantity) VALUES (24, 3, 3);
INSERT INTO product_purchase (product_variation_id, purchase_id, quantity) VALUES (20, 4, 2);
INSERT INTO product_purchase (product_variation_id, purchase_id, quantity) VALUES (17, 4, 1);
INSERT INTO product_purchase (product_variation_id, purchase_id, quantity) VALUES (13, 5, 2);
INSERT INTO product_purchase (product_variation_id, purchase_id, quantity) VALUES (1, 6, 2);
INSERT INTO product_purchase (product_variation_id, purchase_id, quantity) VALUES (2, 6, 2);
INSERT INTO product_purchase (product_variation_id, purchase_id, quantity) VALUES (1, 7, 2);
INSERT INTO product_purchase (product_variation_id, purchase_id, quantity) VALUES (2, 7, 2);
INSERT INTO product_purchase (product_variation_id, purchase_id, quantity) VALUES (3, 7, 1);


-- expedition

INSERT INTO expedition (purchase_id, delivery_date, delivery_address, shipping_cost, exp_status) VALUES (1, '2022-1-13', '245 Glen Creek Lane Paducah, KY 42001', 3.5, 'Delivered');
INSERT INTO expedition (purchase_id, delivery_date, delivery_address, shipping_cost, exp_status) VALUES (2, '2022-1-14', '8385 Brook St. Davenport, IA 52804', 3.5, 'In Transit');
INSERT INTO expedition (purchase_id, delivery_date, delivery_address, shipping_cost, exp_status) VALUES (3, '2022-1-15', '145 Bow Ridge Ave.West Warwick, RI 02893', 1, 'Created');
INSERT INTO expedition (purchase_id, delivery_date, delivery_address, shipping_cost, exp_status) VALUES (4, '2022-1-14', '9651 E. Talbot Court Franklin, MA 02038', 3.5, 'Delivered');
INSERT INTO expedition (purchase_id, delivery_date, delivery_address, shipping_cost, exp_status) VALUES (5, '2022-1-15', '79 King Circle Prattville, AL 36067', 3.5, 'In Transit');

-- Review 

INSERT INTO review (user_id, id_product, comment, date) VALUES (1, 1, 'I loved it, best flavour ever', '2022-1-15');
INSERT INTO review (user_id, id_product, comment, date) VALUES (3, 1, 'I loved it, best flavour ever', '2022-1-15');
INSERT INTO review (user_id, id_product, comment, date) VALUES (16, 1, 'Only one word, amazing', '2022-1-18');
INSERT INTO review (user_id, id_product, comment, date) VALUES (15, 1, 'I loved it, they never fail', '2022-1-18');
INSERT INTO review (user_id, id_product, comment, date) VALUES (13, 1, 'Lasted over 1 month, amazing flavour', '2022-1-18');
INSERT INTO review (user_id, id_product, comment, date) VALUES (12, 1, 'Damn, it is wonderful', '2022-1-18');

-- END