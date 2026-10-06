# Tool Rental Co. — Web Programming Project

A dynamic web application for a fictional tool rental company, built by a team of three for the **CST8285 Web Programming** course (Algonquin College, summer 2025).

Customers can browse a tool inventory, search and filter it, and place, edit and delete rental orders stored in a MySQL database.

## My role

I worked on the **design side** of the project: the site map, wireframes and functional diagram, plus the HTML structure and CSS styling. My teammates built the database and testing, and the JavaScript form validation.

## Features

- **Inventory** with search by name and filters by category and location
- **Rent orders:** create, edit and delete orders; the total is calculated from the daily price, quantity and rental dates
- **Server-side validation** and a CSRF token on the order forms; database access through PDO prepared statements
- **Client-side form validation** (JavaScript and DOM manipulation) on the login and sign-up forms
- Home page, testimonials, terms and privacy, and contact pages
- Responsive layout with a single external stylesheet

## Tech stack

HTML5 · CSS3 · JavaScript · PHP · MySQL (PDO) · XAMPP

## Run it locally

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL** from the control panel.
2. Copy this project folder into `htdocs` and name the folder **`assignment2`** (lowercase). The pages link to `/assignment2/...`.
3. In phpMyAdmin (`http://localhost/phpmyadmin`), import `database/webtoolrental.sql`. It contains sample data only.
4. Create a MySQL user for the app and put its password in `Includes/database.inc.php` (the file uses the user `admin`; the password is a placeholder in this repository).
5. Open `http://localhost/assignment2/index.php`.

## Known limitations

This is a course project, so some parts were left unfinished:

- The sign-up and login pages are front-end only. They are not connected to account storage, so there is no real authentication.
- The JavaScript validation file for the order form (`scripts/order-validation.js`) is not loaded by the page; the order form is validated on the server instead.

## Project structure

```
index.php, header.php, footer.php   shared layout
pages/                              inventory, rent orders, edit order, login, sign-up, testimonials, terms, contact
css/style.css                       styles
scripts/                            client-side validation
server/                             login handler
Includes/database.inc.php           database connection
database/webtoolrental.sql          database script with sample data
```
