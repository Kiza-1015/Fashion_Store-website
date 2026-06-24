# Fashion Store

A full-stack e-commerce web application for browsing and purchasing fashion & clothing, built with PHP and MySQL.

---

## Tech Stack

- **Backend:** PHP (vanilla, no framework)
- **Database:** MySQL via MySQLi
- **Frontend:** HTML, CSS, JavaScript (no frameworks)
- **Auth:** PHP sessions with `password_hash()` / `password_verify()`
- **Dev environment:** XAMPP / MAMP (localhost)

---

## Features

### Customer
- Register and log in securely
- Browse Men's and Women's collections with filtering, sorting, and pagination
- Filter by on-sale items
- View full product detail pages with average star rating
- Add items to cart with size selection
- Save products to a personal wishlist
- Checkout with shipping details
- View full order history with itemised breakdown
- Leave star ratings and reviews on products
- View and edit personal profile

### Admin Panel (`/admin`)
- Dashboard with total customers, orders, products, and revenue
- Add, edit, and delete products (with sale price and size support)
- View and update order statuses (pending → confirmed → shipped → delivered → cancelled)
- Moderate and delete customer reviews
- View all registered customers

---

## Database Schema

| Table | Description |
|---|---|
| `customers` | Customers and admins (role-based) |
| `products` | Listings with price, sale price, size, colour, category |
| `orders` | Customer orders with shipping info |
| `order_items` | Line items per order (includes size) |
| `cart` | Active cart items per customer (includes size) |
| `wishlist` | Saved products per customer |
| `reviews` | Product ratings (1–5 stars) and comments |

---

## Setup

### Requirements
- PHP 7.4+
- MySQL 5.7+
- Apache (XAMPP / MAMP / Laragon)

### Steps

1. Clone the repository into your web server's root directory (e.g. `htdocs/`)
2. Import the database schema:
   ```
   mysql -u root -p < sql/database.sql
   ```
3. Update credentials in `includes/db.php` if needed (default: `root` / no password)
4. Visit `http://localhost/fashion-store/`

### Default Admin Account
| Field | Value |
|---|---|
| Email | `admin@fashionstore.com` |
| Password | `admin123` |

---

## Project Structure

```
fashion-store/
├── admin/              # Admin panel pages
├── css/                # Stylesheet
├── includes/           # DB connection, auth helpers, header/footer
├── js/                 # Frontend JavaScript
├── sql/                # Database schema and seed data
├── index.php           # Homepage (featured men's, women's, sale items)
├── products.php        # Full product listing
├── mens.php            # Men's collection page
├── womens.php          # Women's collection page
├── product-detail.php  # Single product view with reviews
├── cart.php            # Shopping cart
├── checkout.php        # Checkout page
├── orders.php          # Customer order history
├── wishlist.php        # Saved items
├── profile.php         # Customer profile
├── login.php
├── register.php
└── logout.php
```
