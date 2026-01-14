-- Create database
CREATE DATABASE IF NOT EXISTS elevator_db;
USE elevator_db;

-- Create admin table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO admin (username, password) VALUES 
('admin', '0192023a7bbd73250516f069df18b500') 
ON DUPLICATE KEY UPDATE username=username;

-- Create service_requests table
CREATE TABLE IF NOT EXISTS service_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    location VARCHAR(255) NOT NULL,
    elevator_type VARCHAR(50) NOT NULL,
    problem TEXT NOT NULL,
    status ENUM('Pending', 'In Progress', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create modules table
CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    capacity VARCHAR(50) NOT NULL,
    speed VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample elevator modules
INSERT INTO modules (title, description, capacity, speed) VALUES 
('Passenger Lift', 'Standard passenger elevator for commercial and residential buildings', '8-20 Persons', '1.0-2.5 m/s'),
('Goods Lift', 'Heavy-duty elevator for transporting goods and materials', '500-3000 kg', '0.5-1.5 m/s'),
('Hospital Lift', 'Specialized elevator for hospitals with stretcher accommodation', '13-26 Persons', '1.0-2.0 m/s'),
('Home Lift', 'Compact residential elevator for private homes', '2-6 Persons', '0.3-0.6 m/s')
ON DUPLICATE KEY UPDATE title=title;
