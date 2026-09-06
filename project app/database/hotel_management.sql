CREATE DATABASE IF NOT EXISTS hotel_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hotel_management;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY, full_name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE, email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL, role ENUM('admin','receptionist') NOT NULL DEFAULT 'receptionist',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE room_types (
  id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(60) NOT NULL UNIQUE,
  description TEXT NULL, base_price DECIMAL(10,2) NOT NULL DEFAULT 0
);
CREATE TABLE rooms (
  id INT AUTO_INCREMENT PRIMARY KEY, room_number VARCHAR(20) NOT NULL UNIQUE,
  room_type_id INT NOT NULL, price DECIMAL(10,2) NOT NULL,
  status ENUM('Available','Booked','Under Maintenance') NOT NULL DEFAULT 'Available',
  notes VARCHAR(255) NULL, FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE RESTRICT
);
CREATE TABLE customers (
  id INT AUTO_INCREMENT PRIMARY KEY, full_name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) NOT NULL, email VARCHAR(120) NULL, national_id VARCHAR(60) NULL,
  address VARCHAR(255) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE staff (
  id INT AUTO_INCREMENT PRIMARY KEY, full_name VARCHAR(120) NOT NULL, phone VARCHAR(30) NOT NULL,
  email VARCHAR(120) NULL, job_role VARCHAR(80) NOT NULL, responsibility VARCHAR(255) NULL,
  status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY, booking_code VARCHAR(30) NOT NULL UNIQUE,
  customer_id INT NOT NULL, room_id INT NOT NULL, check_in DATE NOT NULL, check_out DATE NOT NULL,
  adults INT NOT NULL DEFAULT 1, children INT NOT NULL DEFAULT 0,
  total_amount DECIMAL(10,2) NOT NULL DEFAULT 0, status ENUM('Active','Checked In','Checked Out','Cancelled') NOT NULL DEFAULT 'Active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
  FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE RESTRICT
);
CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY, booking_id INT NOT NULL, amount DECIMAL(10,2) NOT NULL,
  payment_method ENUM('Cash','Card','Mobile Money','Bank Transfer') NOT NULL DEFAULT 'Cash',
  payment_status ENUM('Pending','Paid','Completed') NOT NULL DEFAULT 'Pending',
  paid_on DATE NULL, notes VARCHAR(255) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);
CREATE TABLE password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(120) NOT NULL, token VARCHAR(100) NOT NULL,
  expires_at DATETIME NOT NULL, used_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE hotel_settings (
  id TINYINT PRIMARY KEY, hotel_name VARCHAR(120) NOT NULL DEFAULT 'HotelMS',
  hotel_email VARCHAR(120) NULL, hotel_phone VARCHAR(30) NULL, hotel_address VARCHAR(255) NULL,
  logo_path VARCHAR(255) NULL, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO users (full_name, username, email, password, role) VALUES
('System Administrator','admin','admin@hotel.local','$2y$10$7LTCDmTrldTfYeS4xfA8.eEBQS70Ic6nsclm9JPRDdY/4F5hA3K8O','admin');
INSERT INTO room_types (name, description, base_price) VALUES
('Single','One guest, one bed',35.00),('Double','Two guests, one double bed',55.00),('Deluxe','Premium room with extra amenities',90.00);
INSERT INTO rooms (room_number, room_type_id, price, status, notes) VALUES
('101',1,35.00,'Available','Ground floor'),('102',2,55.00,'Available','City view'),('201',3,90.00,'Under Maintenance','Painting in progress');
INSERT INTO hotel_settings (id, hotel_name) VALUES (1,'HotelMS');
