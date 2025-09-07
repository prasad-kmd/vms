-- Create the database
CREATE DATABASE IF NOT EXISTS db_vms;

-- Use the newly created database
USE db_vms;

-- Create the 'vendors' table
CREATE TABLE IF NOT EXISTS `vendors` (
    `VendorID` INT PRIMARY KEY AUTO_INCREMENT,
    `Name` VARCHAR(255) NOT NULL,
    `ContactInfo` VARCHAR(255),
    `Email` VARCHAR(255) NOT NULL UNIQUE
) ENGINE=INNODB;

-- Create the 'products' table
CREATE TABLE IF NOT EXISTS `products` (
    `ProductID` INT PRIMARY KEY AUTO_INCREMENT,
    `ProductName` VARCHAR(255) NOT NULL,
    `Price` DECIMAL(10, 2) NOT NULL,
    `VendorID` INT NOT NULL,
    FOREIGN KEY (`VendorID`) REFERENCES `vendors`(`VendorID`) ON DELETE RESTRICT
) ENGINE=INNODB;

-- Create the 'purchase_orders' table
CREATE TABLE IF NOT EXISTS `purchase_orders` (
    `PurchaseOrderID` INT PRIMARY KEY AUTO_INCREMENT,
    `VendorID` INT NOT NULL,
    `UserID` INT NULL, -- Can be NULL if the user is deleted
    `OrderDate` DATE NOT NULL,
    `TotalAmount` DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (`VendorID`) REFERENCES `vendors`(`VendorID`) ON DELETE RESTRICT,
    FOREIGN KEY (`UserID`) REFERENCES `Users`(`UserID`) ON DELETE SET NULL
) ENGINE=INNODB;

-- Create Users table for sign-in and sign-up
CREATE TABLE IF NOT EXISTS `Users` (
    `UserID` INT PRIMARY KEY AUTO_INCREMENT,
    `Username` VARCHAR(50) NOT NULL UNIQUE,
    `Password` VARCHAR(255) NOT NULL, -- Store hashed passwords
    `Email` VARCHAR(100) NOT NULL UNIQUE,
    `Role` ENUM('Admin', 'User') DEFAULT 'User' NOT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=INNODB;

-- Create the 'purchase_order_items' table
CREATE TABLE IF NOT EXISTS `purchase_order_items` (
    `OrderItemID` INT PRIMARY KEY AUTO_INCREMENT,
    `PurchaseOrderID` INT NOT NULL,
    `ProductID` INT NOT NULL,
    `Quantity` INT NOT NULL,
    `UnitPrice` DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (`PurchaseOrderID`) REFERENCES `purchase_orders`(`PurchaseOrderID`) ON DELETE CASCADE,
    FOREIGN KEY (`ProductID`) REFERENCES `products`(`ProductID`) ON DELETE RESTRICT
) ENGINE=INNODB;
