---
title: EAP component
author: João Paulo Moreira Araújo
date: 21/11/2022
---

# EAP: Architecture Specification and Prototype

SportsVerse! The only place you need for a healthy and active lifestyle

---

## A7: Web Resources Specification

This artifact presents the documentation for SportsVerse's web application to be developed, indicating the catalogue of resources, the properties of each resource, as well as the format JSON responses. It also includes the CRUD (create, read, update, delete) operation for each resource.

### Overview

<table>
    <tr>
        <td>M01: Authentication and Individual Profile</td>
        <td>Web resources associated with user authentication and individual profile management. Includes the following system features: login/logout, registration, credential recovery, view and edit of personal profile information.</td>
    </tr>
    <tr>
        <td>M02: Products and Categories</td>
        <td>Web resources associated with the search, filtering and listing of the products available to the user.
</td>
    </tr>
    <tr>
        <td>M03: Management Area</td>
        <td>Web resources associated with website and user management, specifically: view and edit purchases; view, edit, add and delete categories, sub categories and products; view and search users; delete or block users; view and change user information.</td>
    </tr>
    <tr>
        <td>M04: Product Reviews</td>
        <td>Web resources associated with product. Includes the following system features: add review, list reviews, edit and delete reviews.</td>
    </tr>
    <tr>
        <td>M05: Static pages</td>
        <td>Web resources with static content are associated with this module: about, contact, services and faq.</td>
    </tr>
    <tr>
        <td>M06: Cart and Wishlist</td>
        <td>Web resources associated with the user cart and wishlist.</td>
    </tr>
</table>

### Permissions

<table>
    <tr>
        <td>PUB</td>
        <td>Public</td>
        <td>Group of users without privileges.</td>
    </tr>
    <tr>
        <td>USR</td>
        <td>User</td>
        <td>Authenticated user.</td>
    </tr>
    <tr>
        <td>ADM</td>
        <td>Administrator</td>
        <td>System administrators</td>
    </tr>
</table>

### OpenAPI Specification

[Link to the a7_openapi.yaml](https://git.fe.up.pt/lbaw/lbaw2223/lbaw2223/-/blob/9c057ed8d42c700d9c8d8dcd9ba2bfc4f95b312f/Components/EAP/docs/openapi/a7_openapi.yaml).

```openapi: 3.0.0
openapi: 3.0.0

...

```

---

## A8: Vertical prototype

### Implemented Features

### 1.1 Implemented User Stories

 | Identifier | Name | Priority | Description |
 | --- | --- |--- | ---|
 | US101 | View Products List | high | As a User, I want to be able to see the products list, so that I can view what the shop has to offer |
 | US102 | View Product Details | high | As a User, I want to view the product details, so that I can know more about it |
 | US103 | Add Product to Shopping Cart | high | As a User, I want to add products to my shopping cart, so that I can list the products I want to buy |
 | US104 | Manage Shopping Cart | high | As a User, I want to manage my shopping cart, so that I can change the list of products I want to buy|
 | US105 | Search Products | high | As a User, I want to search the platform keywords, so that I can quickly find the items that I am looking for |
 | US106 | See Home | high | As a User, I want to access the home page, so that I can see a brief presentation of the website |
 | US107 | Browse Product Categories | high | As a User, I want to browse through product categories, so that I can view a specific selection of products |
 | US108 | See About | high | As a User, I want to access the about page, so that I can see a complete description of the website and its creators  |
 | US109 | Consult Services | high | As a User, I want to be able to access the services information, so that I can see the website's services |
 | US110 | Consult FAQ | high | As a User, I want to access the FAQ, so that I can get quick answers to common questions |
 | US111 | Consult Contacts | high | As a User, I want to access contacts, so that I can come in touch with the platform creators |
 | US112 | View Product Reviews | high | As a User, I want to view product reviews, so that I can know what other users think about those products |
 |US201|sign in|high|As a Visitor, I want to authenticate into the system, so that I can access privileged information
 |US202|Sign-up|high|As a Visitor, I want to register myself into the system, so that I can authenticate myself into the system
 |US301|Manage Account Information|high|As an Authenticated User, I want to be able to manage my account information, so that I can change it at any time
 |US302|Checkout|high|As an Authenticated User, I want to be able to checkout my order, so that I can buy it
 |US303|Log out|high|As an Authenticated User, I want to be able to log out from my account, so that I can protect my account
 |US304|View Purchase History|high|As an Authenticated User, I want to be able to view my purchase history, so that I can see what I have bought
 |US401|Review Purchased Product|high|As a Buyer, I want to be able to give a review to a product that I have bought, so that I can share my opinion of it to the platform
 |US501|Edit Review|high|As a Product Reviewer, I want to be able to edit my review, so that I can share my new opinion on the product
 |US502|Remove Review|high|As a Product Reviewer, I want to be able to remove my review, so that it disappears from the platform

### 1.2. Implemented Web Resources

#### 'M01: Authentication and Individual Profile'

 | Web Resource Reference | URL |
 | ---------------------- | --- |
 | R101: Login Form | GET /login |
 | R102: Login Action | POST /login |
 | R103: Logout Action | GET /logout |
 | R104: Register Form | GET /register |
 | R105: Register Action | POST /login |
 | R106: View user profile | GET /profile |
 | R107: View the users edit form | GET /profile/edit |
 | R108: Edit user profile Action | POST /profile/edit |

#### 'M02: Products and Categories'

| Web Resource Reference | URL |
| ---------------------- | --- |
| R201: Homepage | GET /homepage |
| R102: R202: Product categories | GET /category |
| R203: Product subcategories | GET /products/category/{category_id} |
| R204: Products | GET /products/subcategory/{subcategory_id} |
| R204: Products | GET /products/subcategory/{subcategory_id} |
| R206: Search Product Mechanism | GET /search?keyword={input} |

#### 'M03: Management Area'

| Web Resource Reference | URL |
| ---------------------- | --- |
| R301: View a specific user profile | GET /user/{id}n |
| R302: View the specific user edit form | GET /user/{id}/edit |
| R303: Edit a specific user profile Action | POST /user/{id}/edit |

#### 'M05: Static pages'

| Web Resource Reference | URL |
| ---------------------- | --- |
| R501: About Page | GET /about |
| R502: Consult Services | GET /services |
| R503: Consult FAQ | GET /faq |
| R504: See Contacts available | GET /contacts |

#### 'M06: Cart and Wishlist'

| Web Resource Reference | URL |
| ---------------------- | --- |
| R601: View Products from cart | GET /shopping_cart |
| R602: Add product to cart | POST /shopping_cart |
| R603: Remove product from cart | DELETE /shopping_cart |
| R604: Update product quantity in cart | PUT /shopping_cart |
| R605: Checkout | POST /checkout |

### 2. Prototype

Prototype is available at [this link](https://lbaw2223.lbaw.fe.up.pt/).

Credentials:

- admin user: admin@gmail.com/1234
- regular user: heathburns@gmail.com/1234

## Revision history

Changes made to the first submission:

---

GROUP2223, 21/10/2022

* Bárbara Ema Pinto Rodrigues, up202007163@fe.up.pt
* Henrique Oliveira Silva, up202007242@fe.up.pt
* João Paulo Moreira Araújo, up202004293@fe.up.pt
* Tiago Nunes Moreira Branquinho, up202005567@fe.up.pt