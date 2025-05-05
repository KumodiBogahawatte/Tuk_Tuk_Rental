CREATE database TukTuk;

use TukTuk;
-- Create vehicles table
CREATE TABLE IF NOT EXISTS vehicles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    brand VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    price_per_day DECIMAL(10,2) NOT NULL,
    gear_box VARCHAR(50) NOT NULL,
    fuel_type VARCHAR(50) NOT NULL,
    max_speed VARCHAR(50) NOT NULL,
    capacity INT NOT NULL,
    fuel_tank VARCHAR(50) NOT NULL,
    mileage VARCHAR(50) NOT NULL,
    main_image VARCHAR(255) NOT NULL,
    image1 VARCHAR(255),
    image2 VARCHAR(255),
    image3 VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create admin_users table for authentication
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); 