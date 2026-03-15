-- Use the database
USE metro_db;

-- Add new cities if they don't exist
INSERT IGNORE INTO cities (name) VALUES ('Mumbai'), ('Chennai');

-- Get IDs for the cities
SET @mumbai_id = (SELECT id FROM cities WHERE name = 'Mumbai');
SET @chennai_id = (SELECT id FROM cities WHERE name = 'Chennai');

-- Add stations for Mumbai
INSERT IGNORE INTO stations (name, city_id, latitude, longitude, zone) VALUES 
('CSMT', @mumbai_id, 18.9398, 72.8355, 'Central Line'),
('Dadar', @mumbai_id, 19.0178, 72.8478, 'Central Line'),
('Kurla', @mumbai_id, 19.0650, 72.8797, 'Central Line'),
('Andheri', @mumbai_id, 19.1136, 72.8697, 'Western Line'),
('Borivali', @mumbai_id, 19.2290, 72.8573, 'Western Line');

-- Add stations for Chennai
INSERT IGNORE INTO stations (name, city_id, latitude, longitude, zone) VALUES 
('Chennai Central', @chennai_id, 13.0827, 80.2707, 'Blue Line'),
('Egmore', @chennai_id, 13.0782, 80.2575, 'Blue Line'),
('Guindy', @chennai_id, 13.0067, 80.2206, 'Blue Line'),
('Tambaram', @chennai_id, 12.9249, 80.1277, 'Green Line'),
('Koyambedu', @chennai_id, 13.0732, 80.1912, 'Green Line');

-- Ensure all existing stations have a city assigned (defaulting to Bangalore/1)
UPDATE stations SET city_id = 1 WHERE city_id IS NULL;
