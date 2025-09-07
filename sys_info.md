## Base Description
---------
The Vendor Management System is essential for overseeing relationships with suppliers in a manufacturing company. The system helps manage vendor information, products supplied, and purchase orders.
Each vendor is recorded with a unique VendorID to distinguish them in the system. It stores crucial details such as the vendor's name, contact information, and email. This allows for streamlined communication and easy tracking of all vendors.
The system also catalogs the products supplied by each vendor, using a ProductID for unique identification. Information such as product name, price, and the VendorID linking the product to the respective vendor is stored.

The purchase order process ensures smooth operations, as every order is recorded with a unique PurchaseOrderID. It links the vendor to the order, logs the order date, and tracks the total amount spent.
Primary use cases include placing new purchase orders, viewing products supplied by vendors, and tracking purchase orders, making vendor management more organized and efficient.

---------
## Introduction to the Vendor Management System
The Vendor Management System is a crucial tool for a manufacturing company, designed to efficiently oversee and manage all interactions and relationships with its suppliers. The primary goal of this system is to streamline and organize the entire vendor lifecycle, from storing vendor information to tracking purchase orders. By centralizing all vendor-related data and processes, the system aims to improve communication, enhance transparency, and increase overall operational efficiency in the procurement process.

### What You Are Expected to Do

You are expected to develop a comprehensive Vendor Management System that incorporates the following core modules and functionalities:

**1. Vendor Information Management:**

*   **Functionality:** The system must be able to store and manage a comprehensive database of all vendors.
*   **Data to be managed:**
    *   `VendorID`: A unique identifier for each vendor.
    *   `Name`: The name of the vendor.
    *   `Contact Information`: Contact details for the vendor.
    *   `Email`: The vendor's email address.

**2. Product Catalog Management:**

*   **Functionality:** The system should maintain a detailed catalog of all products supplied by the vendors.
*   **Data to be managed:**
    *   `ProductID`: A unique identifier for each product.
    *   `Product Name`: The name of the product.
    *   `Price`: The price of the product.
    *   `VendorID`: A foreign key that links the product to the specific vendor who supplies it.

**3. Purchase Order Management:**

*   **Functionality:** The system must be able to create, record, and track all purchase orders.
*   **Data to be managed:**
    *   `PurchaseOrderID`: A unique identifier for each purchase order.
    *   `VendorID`: A foreign key to link the order to the respective vendor.
    *   `Order Date`: The date the purchase order was created.
    *   `Total Amount`: The total cost of the purchase order.

**Primary Use Cases to Implement:**

Your system should be designed to support the following key operations:

*   **Placing New Purchase Orders:** Users should be able to create and issue new purchase orders to vendors through the system.
*   **Viewing Products Supplied by Vendors:** The system must provide a way to easily view all products that a specific vendor supplies.
*   **Tracking Purchase Orders:** Users should be able to track the status and details of all purchase orders within the system.

In essence, the project requires you to build a robust and user-friendly system that makes the process of managing vendors, their products, and the associated purchasing activities more organized and efficient.

### Introduction to the Vendor Management System

The Vendor Management System (VMS) is a specialized software solution designed to streamline and optimize the oversight of supplier relationships within a manufacturing company. In the context of manufacturing operations, where timely procurement of materials and components is critical for production efficiency, the VMS serves as a centralized platform to handle all aspects of vendor interactions. This includes maintaining accurate records of vendors, cataloging the products they supply, and managing the entire purchase order lifecycle. By automating these processes, the system reduces manual errors, enhances communication, ensures compliance with procurement policies, and provides actionable insights into spending and supplier performance.

At its core, the VMS addresses the challenges of fragmented vendor data and disorganized purchasing workflows. For instance, in a manufacturing environment, companies often deal with multiple suppliers for raw materials, parts, or services. Without a dedicated system, tracking vendor details, product availability, pricing fluctuations, and order histories can become cumbersome, leading to delays, overpayments, or supply chain disruptions. The VMS mitigates these issues by creating a unified database that links vendors, their offerings, and transactional data, enabling better decision-making, cost control, and relationship building with suppliers.

The system is built around three primary entities—vendors, products, and purchase orders—each with unique identifiers and attributes to ensure data integrity and easy retrieval. This relational structure allows for efficient querying and reporting, such as identifying the best vendors for specific products or analyzing historical purchase trends. Overall, the VMS promotes organizational efficiency, scalability, and transparency, making it an indispensable tool for procurement teams in manufacturing settings.

### Expected Development and Functionalities

From the project description, you are expected to develop a fully functional Vendor Management System that implements the outlined features and use cases. This involves creating a software application (a web-based) using appropriate technologies such as databases (MySQL for storing vendor, product, and order data), programming languages (PHP), and user interfaces (HTML,CSS,JS) to make it accessible and intuitive.

The primary expectations for the system include:

1. **Core Data Management**:
   - Implement CRUD (Create, Read, Update, Delete) operations for vendors, products, and purchase orders.
   - Ensure data validation, such as unique IDs, required fields (e.g., email for vendors), and relational integrity (e.g., a product must be linked to an existing vendor).
   - Use a database schema that reflects the entities: e.g., tables for Vendors (VendorID, Name, ContactInfo, Email), Products (ProductID, Name, Price, VendorID), and PurchaseOrders (PurchaseOrderID, VendorID, OrderDate, TotalAmount).

2. **Key Use Cases and Features**:
   - **Placing New Purchase Orders**: Allow users to select a vendor, choose products from their catalog, calculate totals (based on product prices and quantities), and record the order with a generated PurchaseOrderID and current date. Include options for order confirmation and notifications (e.g., email to the vendor).
   - **Viewing Products Supplied by Vendors**: Provide search and filter functionalities to display products associated with a specific vendor, including details like names and prices. This could include reporting tools, such as generating lists or charts of top products per vendor.
   - **Tracking Purchase Orders**: Enable users to view, search, and filter orders by criteria like vendor, date range, or total amount. Features might include status updates (e.g., pending, fulfilled, canceled), historical logs, and analytics (e.g., total spend per vendor over time).
   - Additional implied features: User authentication for secure access, export options (e.g., CSV reports), and error handling for scenarios like invalid vendor links or duplicate IDs.

3. **Overall System Goals**:
   - Make vendor management more organized and efficient by reducing manual processes and providing real-time insights.
   - Ensure scalability for growing manufacturing operations, such as handling hundreds of vendors and thousands of orders.
   - Focus on user experience: The interface should be intuitive for procurement staff, with dashboards for quick overviews and detailed views for in-depth analysis.
   - Incorporate best practices like data security (e.g., encryption for sensitive contact info), backups, and possibly integration points for future expansions (e.g., linking to inventory or accounting systems).

To successfully develop this project, start by designing the database schema, then build the backend logic for data operations, followed by the frontend for user interactions. Test thoroughly with sample data to verify the use cases. since this is for an academic assignment, consider documenting your design choices in a separate file, such as why certain technologies were selected or how relationships between entities are handled.

```SQL
CREATE DATABASE db_vms;
-- Use the newly created database
USE db_vms;

-- Create the 'vendors' table
CREATE TABLE vendors (
    VendorID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(255) NOT NULL,
    ContactInfo VARCHAR(255),
    Email VARCHAR(255) NOT NULL UNIQUE
)ENGINE=INNODB;

-- Create the 'products' table
CREATE TABLE products (
    ProductID INT PRIMARY KEY AUTO_INCREMENT,
    ProductName VARCHAR(255) NOT NULL,
    Price DECIMAL(10, 2) NOT NULL,
    VendorID INT NOT NULL,
    FOREIGN KEY (VendorID) REFERENCES vendors(VendorID) ON DELETE RESTRICT
)ENGINE=INNODB;

-- Create the 'purchase_orders' table
CREATE TABLE purchase_orders (
    PurchaseOrderID INT PRIMARY KEY AUTO_INCREMENT,
    VendorID INT NOT NULL,
    OrderDate DATE NOT NULL,
    TotalAmount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (VendorID) REFERENCES vendors(VendorID) ON DELETE RESTRICT
)ENGINE=INNODB;

-- Create Users table for sign-in and sign-up
CREATE TABLE Users (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL, -- Store hashed passwords
    Email VARCHAR(100) NOT NULL UNIQUE,
    Role ENUM('Admin', 'User') DEFAULT 'User' NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)ENGINE=INNODB;
```

I wanted to implement this system as I mentioned before with minimum number of source code files.
- Design a system using PHP,HTML,CSS(Bootstrap) and JS. for me i installed wampserver for local deployment.
- also implement signin/up mechanism where users can login or register. admins also should register as a user then his role can be changed in database.