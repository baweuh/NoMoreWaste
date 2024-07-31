CREATE DATABASE fight_food_waste;

USE fight_food_waste;

CREATE TABLE Commercants (
    merchant_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    phone VARCHAR(15),
    email VARCHAR(100),
    membership_start_date DATE,
    membership_end_date DATE,
    renewal_reminder_sent BOOLEAN DEFAULT FALSE
);

CREATE TABLE Collectes (
    collection_id INT AUTO_INCREMENT PRIMARY KEY,
    merchant_id INT NOT NULL,
    collection_date DATE NOT NULL,
    total_items INT,
    status VARCHAR(50),
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (merchant_id) REFERENCES Commercants(merchant_id)
);

CREATE TABLE Produits (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    barcode VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    quantity INT,
    expiry_date DATE,
    collection_id INT NOT NULL,
    FOREIGN KEY (collection_id) REFERENCES Collectes(collection_id)
);

CREATE TABLE Stocks (
    stock_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT,
    location VARCHAR(255),
    FOREIGN KEY (product_id) REFERENCES Produits(product_id)
);

CREATE TABLE Clients (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(15)
);

CREATE TABLE Tournees (
    delivery_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    delivery_date DATE NOT NULL,
    recipient_type VARCHAR(50),
    recipient_id INT,
    status VARCHAR(50),
    pdf_report BLOB,
    FOREIGN KEY (customer_id) REFERENCES Clients(customer_id)
);

CREATE TABLE Services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT
);

CREATE TABLE Benevoles (
    volunteer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(15),
    skills TEXT,
    status VARCHAR(50)
);

CREATE TABLE Benevoles_Services (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    volunteer_id INT NOT NULL,
    service_id INT NOT NULL,
    task VARCHAR(255),
    date DATE,
    status VARCHAR(50),
    FOREIGN KEY (volunteer_id) REFERENCES Benevoles(volunteer_id),
    FOREIGN KEY (service_id) REFERENCES Services(service_id)
);

CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL
);