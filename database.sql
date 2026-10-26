-- Create Database
CREATE DATABASE IF NOT EXISTS eazy_ride_db;
USE eazy_ride_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    bike_name VARCHAR(100) NOT NULL,
    price_per_day DECIMAL(10, 2) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    license_number VARCHAR(50) NOT NULL,
    status VARCHAR(50) DEFAULT 'Confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- To create an admin account:
-- 1. Register a new user via the website (auth.php).
-- 2. Go to your database and run: UPDATE users SET role='admin' WHERE email='your_email@example.com';

-- Bikes Table
CREATE TABLE IF NOT EXISTS bikes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    cc VARCHAR(50) NOT NULL,
    type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO bikes (name, price, image_path, cc, type, description) VALUES 
('Urban Scooter', 500, 'https://images.unsplash.com/photo-1505159940484-eb2b9f2588e2?auto=format&fit=crop&w=600&q=80', '110cc', 'Auto', 'Perfect for city commuting.'),
('Harley Street', 1500, 'https://images.unsplash.com/photo-1558981285-6f0c94958bb6?auto=format&fit=crop&w=600&q=80', '750cc', 'Manual', 'A classic cruiser for long drives.'),
('Ninja Sport', 2000, 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=600&q=80', '650cc', 'Manual', 'High performance bike for thrills.'),
('Royal Classic', 1200, 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80', '350cc', 'Manual', 'Vintage style with modern power.'),
('Yamaha R15', 1000, 'https://images.unsplash.com/photo-1547873380-632909b5c3e9?auto=format&fit=crop&w=600&q=80', '155cc', 'Manual', 'Agile sports bike for enthusiasts.'),
('Honda CBR', 1800, 'https://images.unsplash.com/photo-1599819811279-d5ad9cccf838?auto=format&fit=crop&w=600&q=80', '650cc', 'Manual', 'Reliable and fast sports tourer.'),
('Vespa SXL', 700, 'https://images.unsplash.com/photo-1623079400394-f0795693b82d?auto=format&fit=crop&w=600&q=80', '150cc', 'Auto', 'Stylish Italian scooter.'),
('KTM Duke', 1600, 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=600&q=80', '390cc', 'Manual', 'Naked bike with raw power.'),
('BMW G310', 2200, 'https://images.unsplash.com/photo-1609630875171-b1321377ee65?auto=format&fit=crop&w=600&q=80', '313cc', 'Manual', 'Premium adventure experience.'),
('Ducati Monster', 3000, 'https://images.unsplash.com/photo-1558981806-ec527fa84f3d?auto=format&fit=crop&w=600&q=80', '937cc', 'Manual', 'The ultimate naked bike icon.');