-- Metro Ticket Booking System Database Schema

CREATE DATABASE IF NOT EXISTS metro_db;
USE metro_db;

-- Cities Table
CREATE TABLE IF NOT EXISTS cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mobile VARCHAR(15) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    city_id INT,
    otp_code VARCHAR(6),
    otp_expiry DATETIME,
    is_verified BOOLEAN DEFAULT FALSE,
    role ENUM('admin', 'passenger') DEFAULT 'passenger',
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    profile_photo VARCHAR(255) DEFAULT 'default.png',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES cities(id)
);

-- Stations Table
CREATE TABLE IF NOT EXISTS stations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    city_id INT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    zone VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES cities(id)
);

-- Routes Table
CREATE TABLE IF NOT EXISTS routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    line_color VARCHAR(20),
    start_station_id INT,
    end_station_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (start_station_id) REFERENCES stations(id),
    FOREIGN KEY (end_station_id) REFERENCES stations(id)
);

-- Route Stations Mapping
CREATE TABLE IF NOT EXISTS route_stations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT,
    station_id INT,
    sequence INT,
    distance_from_prev DECIMAL(5, 2), -- Distance in KM from the previous station in sequence
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (route_id) REFERENCES routes(id),
    FOREIGN KEY (station_id) REFERENCES stations(id)
);

-- Train Schedules Table
CREATE TABLE IF NOT EXISTS train_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT,
    train_name VARCHAR(50),
    departure_time TIME,
    arrival_time TIME,
    frequency_minutes INT,
    status ENUM('on_time', 'delayed', 'cancelled') DEFAULT 'on_time',
    delay_minutes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (route_id) REFERENCES routes(id)
);

-- Fare Rules Table
CREATE TABLE IF NOT EXISTS fares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    min_distance DECIMAL(5, 2),
    max_distance DECIMAL(5, 2),
    amount DECIMAL(10, 2),
    pass_type ENUM('single', 'return', 'student', 'senior', 'monthly') DEFAULT 'single',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    source_station_id INT,
    destination_station_id INT,
    ticket_type ENUM('single', 'return') DEFAULT 'single',
    passenger_count INT DEFAULT 1,
    total_fare DECIMAL(10, 2),
    status ENUM('pending', 'paid', 'cancelled', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (source_station_id) REFERENCES stations(id),
    FOREIGN KEY (destination_station_id) REFERENCES stations(id)
);

-- Tickets Table
CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    ticket_id VARCHAR(50) UNIQUE NOT NULL,
    qr_code_path VARCHAR(255),
    status ENUM('valid', 'used', 'expired') DEFAULT 'valid',
    expiry_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    transaction_id VARCHAR(100) UNIQUE,
    payment_method ENUM('upi', 'card', 'wallet'),
    amount DECIMAL(10, 2),
    status ENUM('success', 'failed', 'refunded') DEFAULT 'success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- Refunds Table
CREATE TABLE IF NOT EXISTS refunds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    refund_amount DECIMAL(10, 2),
    reason TEXT,
    status ENUM('pending', 'processed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- Login Activity Table
CREATE TABLE IF NOT EXISTS login_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Multi-language Translations (Example structure)
CREATE TABLE IF NOT EXISTS translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lang_code VARCHAR(5),
    keyword VARCHAR(100),
    value TEXT,
    UNIQUE KEY (lang_code, keyword)
);

-- Insert Cities
INSERT IGNORE INTO cities (name) VALUES ('Bangalore'), ('Delhi');

-- Insert Sample Data
-- Bangalore Stations (city_id = 1)
INSERT IGNORE INTO stations (name, city_id, latitude, longitude, zone) VALUES 
('Majestic', 1, 12.9767, 77.5713, 'Purple Line'),
('Indiranagar', 1, 12.9784, 77.6385, 'Purple Line'),
('MG Road', 1, 12.9755, 77.6067, 'Purple Line'),
('Baiyappanahalli', 1, 12.9907, 77.6525, 'Purple Line'),
('Yelachenahalli', 1, 12.8966, 77.5715, 'Green Line'),
('Jayanagar', 1, 12.9304, 77.5804, 'Green Line'),
('Banashankari', 1, 12.9154, 77.5736, 'Green Line'),
('JP Nagar', 1, 12.9074, 77.5736, 'Green Line'),
('Silk Institute', 1, 12.8624, 77.5456, 'Green Line');

-- Delhi Stations (city_id = 2)
INSERT IGNORE INTO stations (name, city_id, latitude, longitude, zone) VALUES 
('Rajiv Chowk', 2, 28.6328, 77.2197, 'Yellow Line'),
('Hauz Khas', 2, 28.5431, 77.2065, 'Yellow Line'),
('New Delhi', 2, 28.6431, 77.2223, 'Yellow Line'),
('Chandni Chowk', 2, 28.6578, 77.2301, 'Yellow Line'),
('Kashmere Gate', 2, 28.6675, 77.2281, 'Red Line'),
('Central Secretariat', 2, 28.6146, 77.2115, 'Yellow Line'),
('INA', 2, 28.5755, 77.2091, 'Yellow Line'),
('AIIMS', 2, 28.5684, 77.2069, 'Yellow Line'),
('Saket', 2, 28.5205, 77.2005, 'Yellow Line'),
('Qutub Minar', 2, 28.5126, 77.1858, 'Yellow Line');

INSERT IGNORE INTO routes (name, line_color, start_station_id, end_station_id) VALUES 
('Purple Line', 'Purple', 1, 4);

INSERT IGNORE INTO route_stations (route_id, station_id, sequence, distance_from_prev) VALUES 
(1, 1, 1, 0.00),
(1, 2, 2, 1.20),
(1, 3, 3, 1.10),
(1, 4, 4, 1.30);

INSERT IGNORE INTO fares (min_distance, max_distance, amount, pass_type) VALUES 
(0, 2, 10.00, 'single'),
(2.01, 5, 20.00, 'single'),
(5.01, 10, 30.00, 'single'),
(10.01, 100, 50.00, 'single');
