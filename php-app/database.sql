-- database.sql - Script para crear la base de datos y tablas
CREATE DATABASE IF NOT EXISTS vehicle_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vehicle_db;

-- Tabla para vehículos registrados
CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license_plate VARCHAR(20) NOT NULL UNIQUE,
    owner_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para eventos registrados
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_date DATE NOT NULL,
    license_plate VARCHAR(20) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar algunos datos de ejemplo para pruebas
INSERT INTO vehicles (license_plate, owner_name) VALUES 
('ABC123', 'Juan Pérez'),
('XYZ789', 'María González'),
('DEF456', 'Carlos Rodríguez'),
('GHI789', 'Ana Martínez');

-- Crear índices para mejorar el rendimiento
CREATE INDEX idx_license_plate ON vehicles(license_plate);
CREATE INDEX idx_event_license_plate ON events(license_plate);
CREATE INDEX idx_event_date ON events(event_date);
