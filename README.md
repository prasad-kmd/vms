# Vendor Management System

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![License: AGPL v3](https://img.shields.io/badge/License-AGPL%20v3-blue.svg?style=for-the-badge)

A robust and user-friendly Vendor Management System (VMS) designed to streamline supplier relationships, manage product catalogs, and track purchase orders efficiently.

## 🌟 Introduction

The Vendor Management System is a crucial tool for any company, designed to efficiently oversee and manage all interactions and relationships with its suppliers. The primary goal of this system is to streamline and organize the entire vendor lifecycle, from storing vendor information to tracking purchase orders. By centralizing all vendor-related data and processes, the system aims to improve communication, enhance transparency, and increase overall operational efficiency in the procurement process.

This project provides a full-featured, modern, and responsive web application built with PHP and MySQL.

## ✨ Features

*   **Dashboard:** A comprehensive overview of key metrics, including total vendors, products, and purchase order values, along with a list of recent purchase orders.
*   **Vendor Management:** Full CRUD (Create, Read, Update, Delete) functionality for managing vendor details, including name, contact information, and email.
*   **Product Catalog:** Full CRUD functionality for managing products, including name, price, and the associated vendor.
*   **Purchase Order System:** Create dynamic purchase orders, add items, and view detailed order summaries.
*   **User Authentication:** Secure user registration and login system.
*   **Role-Based Access Control:** Simple and effective distinction between 'Admin' and 'User' roles, with different permissions for managing data.
*   **Modern & Responsive UI:** A sleek, modern user interface built with Bootstrap that is fully responsive and works on all devices.

## 💻 Technology Stack

*   **Backend:** PHP
*   **Database:** MySQL
*   **Frontend:** HTML, CSS, JavaScript
*   **Frameworks/Libraries:**
    *   [Bootstrap](https://getbootstrap.com/) for responsive UI components.
    *   [jQuery](https://jquery.com/) for JavaScript interactivity.
    *   [Font Awesome](https://fontawesome.com/) for icons.

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

You will need a local server environment to run this project. We recommend using [XAMPP](https://www.apachefriends.org/index.html) or a similar package that includes Apache, MySQL, and PHP.

*   An Apache web server
*   PHP (version 7.0 or higher)
*   A MySQL database server

### Installation

Follow these steps to set up your development environment:

1.  **Clone the repository**
    ```bash
    git clone https://github.com/your-username/your-repo-name.git
    ```
    Navigate into the cloned directory:
    ```bash
    cd your-repo-name
    ```

2.  **Database Setup**
    *   Start your Apache and MySQL services from your XAMPP/WAMP control panel.
    *   Open your web browser and navigate to `http://localhost/phpmyadmin`.
    *   Create a new database. It is recommended to name it `db_vms`.
    *   Select the newly created database and click on the **Import** tab.
    *   Click on "Choose File" and select the `database.sql` file located in the root of the project directory.
    *   Click **Go** to import the database schema and tables.

3.  **Configure the Application**
    *   Open the `config/db.php` file in your code editor.
    *   Update the database connection variables (`$db_host`, `$db_username`, `$db_password`, `$db_name`) to match your local environment's settings.
    ```php
    <?php
    $db_host = 'localhost';
    $db_username = 'root'; // Your DB username
    $db_password = '';     // Your DB password
    $db_name = 'db_vms';   // The DB name you created

    // ... rest of the file
    ```

4.  **Run the Application**
    *   Move the entire project folder into your web server's root directory (e.g., `C:/xampp/htdocs` on Windows or `/Applications/XAMPP/htdocs` on macOS).
    *   Open your web browser and navigate to:
    `http://localhost/your-project-folder-name`

##  usage

The system has two user roles with different levels of permissions:

*   **User:** Can view vendors and products, and can create and view their own purchase orders.
*   **Admin:** Has full CRUD access to all vendors, products, purchase orders, and can manage user accounts.

### Creating an Admin Account

To create an administrator account, follow these steps:
1.  Register a new user through the "Sign Up" page on the application.
2.  Open your database management tool (e.g., phpMyAdmin).
3.  Navigate to the `Users` table within your `db_vms` database.
4.  Find the user you just created and change their `Role` value from `User` to `Admin`.

## 🤝 Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1.  **Fork the Project**
2.  **Create your Feature Branch** (`git checkout -b feature/AmazingFeature`)
3.  **Commit your Changes** (`git commit -m 'Add some AmazingFeature'`)
4.  **Push to the Branch** (`git push origin feature/AmazingFeature`)
5.  **Open a Pull Request**

## 📜 License

This project is licensed under the **GNU Affero General Public License v3.0**. See the [LICENSE](LICENSE) file for more details.
