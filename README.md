# Vendor Management System (VMS)

A comprehensive, PHP-based Vendor Management System designed to streamline relationships with suppliers in a manufacturing company. The system helps manage vendor information, product catalogs, and purchase orders with a robust, role-based access control system.

## About The Project

This project is a web-based application built to provide a centralized platform for all vendor-related activities. It allows procurement staff to manage vendors, track products, and create purchase orders efficiently. With a two-tier user system (Admin/User), it ensures that data is secure and that users only have access to the functionalities relevant to their roles. The system is complete with a summary dashboard for at-a-glance insights into key business metrics.

---

## Features

-   **Secure Authentication:** Users can register and log in to a secure system. Passwords are fully hashed.
-   **Role-Based Access Control (RBAC):**
    -   **Admin:** Full CRUD (Create, Read, Update, Delete) access to all modules. Can manage users and view site-wide statistics.
    -   **User:** Can view vendors and products. Can create purchase orders and view their own, but cannot modify or delete them.
-   **Dashboard:** A dynamic dashboard shows key statistics based on the user's role. Admins see site-wide data, while Users see their own personalized stats.
-   **Vendor Management (Admin):** Admins can add, edit, and delete vendor information.
-   **Product Management (Admin):** Admins can manage the product catalog, assigning products to specific vendors.
-   **Purchase Order Management:**
    -   Users and Admins can create new purchase orders with multiple line items.
    -   A dynamic form uses JavaScript to fetch products based on the selected vendor.
    -   Users can view their own purchase orders; Admins can view all purchase orders.
-   **User & Profile Management:**
    -   Users can update their own email and password on a dedicated profile page.
    -   Admins have a User Management module to edit other users' roles and information.
-   **Dynamic Filtering:** The product and purchase order lists can be filtered by vendor and/or product, making it easy to find information.

---

## Technology Stack

This project is built with a classic, robust stack suitable for web applications:

-   **Backend:** PHP
-   **Database:** MySQL
-   **Frontend:** HTML, Bootstrap CSS, JavaScript (for dynamic forms)
-   **Server:** Designed to run on a local server environment like WAMP, XAMPP, or MAMP.

---

## Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

You will need a local web server environment with PHP and MySQL. The most common choices are:
-   [XAMPP](https://www.apachefriends.org/index.html) (for Windows, macOS, Linux)
-   [WAMP](https://www.wampserver.com/en/) (for Windows)
-   [MAMP](https://www.mamp.info/en/windows/) (for macOS, Windows)

### Installation Guide

**Step 1: Clone the Repository**
Clone this repository to a folder on your local machine.

```bash
git clone <repository_url>
```

**Step 2: Place Project in Web Root**
Move the cloned project folder into the web root of your local server.
-   For XAMPP, this is typically the `htdocs` folder inside your XAMPP installation directory.
-   For WAMP, this is the `www` folder.

**Step 3: Set Up the Database**
1.  Start your local server and open **phpMyAdmin**.
2.  Create a new database named `db_vms`.
3.  Select the new `db_vms` database.
4.  Click on the **Import** tab.
5.  Click "Choose File" and select the `database.sql` file located in the root of this project.
6.  Click "Go" to run the import. This will create all the necessary tables (`users`, `vendors`, `products`, etc.).

**Step 4: Configure Database Connection**
1.  Open the project in your code editor.
2.  Navigate to the `config/` directory and open the `db.php` file.
3.  If your MySQL database credentials are different from the default, update the `DB_USERNAME` and `DB_PASSWORD` values. The default is set to:
    -   `DB_USERNAME`: 'root'
    -   `DB_PASSWORD`: '' (empty)

**Step 5: Access the Application**
1.  Open your web browser and navigate to the project folder. For example:
    -   `http://localhost/vms` (if you named the project folder `vms`)
2.  You should be redirected to the login page.

---

## How to Use

### Creating the First Admin User

The system has no default admin account. To get full access, follow these steps:

1.  **Register a New User:** Go to the application's registration page and create a new account.
2.  **Manually Update Role in Database:**
    -   Open **phpMyAdmin** and select the `db_vms` database.
    -   Open the `Users` table.
    -   Find the user you just created.
    -   Click "Edit".
    -   Change the value in the `Role` column from `User` to `Admin`.
    -   Click "Go" to save the change.
3.  **Log In as Admin:** You can now log in with that user's credentials to access all the administrative features of the application.

### General Usage

-   **Admin Role:** As an Admin, you will see "User Management" in the navigation bar and "Add", "Edit", and "Delete" buttons throughout the application. You can manage all vendors, products, users, and purchase orders.
-   **User Role:** As a User, you will have a more limited view. You can view data and create purchase orders, but you cannot modify or delete most records.

---

## Project Structure

Here is a brief overview of the project's file structure:

```
/
├── assets/                 # Contains CSS and JS files (Bootstrap)
├── config/
│   └── db.php              # Database connection and configuration
├── database.sql            # The master SQL schema for the database
├── index.php               # The main dashboard page
├── login.php               # User login page
├── register.php            # User registration page
├── profile.php             # User's own profile management page
├── users.php               # Admin: List all users
├── edit_user.php           # Admin: Edit a user's role/info
├── vendors.php             # List vendors
├── add_vendor.php          # Admin: Add a new vendor
├── edit_vendor.php         # Admin: Edit a vendor
├── delete_vendor.php       # Admin: Delete a vendor
├── products.php            # List products
├── add_product.php         # Admin: Add a new product
...and so on for all other feature pages.
```
