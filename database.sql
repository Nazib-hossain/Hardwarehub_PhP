-- ========================================================
-- HardwareHub Database Setup Script
-- Academic Specification: CIS324 / Normalized Relational Schema
-- Compatible with: MySQL / MariaDB (XAMPP Default)
-- ========================================================

CREATE DATABASE IF NOT EXISTS `hardwarehub_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hardwarehub_db`;

-- Drop tables in reverse dependency order to prevent FK constraints issues
DROP TABLE IF EXISTS `Orders`;
DROP TABLE IF EXISTS `Hardware_Products`;
DROP TABLE IF EXISTS `Users`;

-- 1. Users Table (Normalized)
-- Stores credentials with bcrypt hash and role-based access control (admin vs customer)
CREATE TABLE `Users` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Hardware_Products Table (Normalized)
-- Stores product inventory, pricing, stock levels, and descriptive metadata
CREATE TABLE `Hardware_Products` (
    `product_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `stock_quantity` INT NOT NULL DEFAULT 0,
    `description` TEXT,
    `image_url` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Orders Table (Normalized)
-- Stores customer orders linking to Users and Hardware_Products via Foreign Keys
CREATE TABLE `Orders` (
    `order_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `order_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) 
        REFERENCES `Users` (`user_id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT `fk_orders_product` FOREIGN KEY (`product_id`) 
        REFERENCES `Hardware_Products` (`product_id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- Seed Data
-- Passwords:
-- Admin:    admin@hardwarehub.com    / Admin@123
-- Customer: customer@hardwarehub.com / Customer@123
-- ========================================================

INSERT INTO `Users` (`user_id`, `name`, `email`, `password_hash`, `role`) VALUES
(1, 'System Administrator', 'admin@hardwarehub.com', '$2y$10$VautqabyDyjW08oV1x4kC.a/ob.t6dILS9pkkoeo4hI8xVvclNw9a', 'admin'),
(2, 'Alex Carter (Customer)', 'customer@hardwarehub.com', '$2y$10$bYKGjIBCU7Hn9Ne1pIufmOZCZA7mdkXVlC3DSibJZPR0r3XyT16F2', 'customer');

-- Seed Hardware Products (BDT Pricing, local crisp assets from Spring Boot HardwareHub)
INSERT INTO `Hardware_Products` (`product_id`, `name`, `category`, `price`, `stock_quantity`, `description`, `image_url`) VALUES
(101, 'AMD Ryzen 7 7800X3D Processor', 'Processors', 52500.00, 18, '8 cores, 16 threads, 5.0 GHz Max Boost with AMD 3D V-Cache Technology. Premier gaming CPU.', 'public/images/products/ryzen9_9950x.png'),
(102, 'NVIDIA GeForce RTX 4080 Super 16GB', 'Graphics Cards', 145000.00, 12, '16GB GDDR6X, Ada Lovelace architecture, DLSS 3.5, Full Ray Tracing capability for extreme 4K gaming.', 'public/images/products/rtx4080s.png'),
(103, 'Corsair Vengeance RGB 32GB DDR5-6000', 'Memory', 14500.00, 35, '32GB (2x16GB) DDR5 DRAM 6000MHz C36 with dynamic multi-zone RGB lighting and Intel XMP / AMD EXPO support.', 'public/images/products/vengeance_ddr5.png'),
(104, 'Samsung 990 PRO 2TB PCIe 4.0 NVMe SSD', 'Storage', 22000.00, 24, 'Sequential read speeds up to 7,450 MB/s and write speeds up to 6,900 MB/s. Thermal guard included.', 'public/images/products/wd_sn850x.png'),
(105, 'ASUS ROG Strix Z790-E Gaming WiFi II', 'Motherboards', 54000.00, 8, 'Intel LGA 1700 ATX motherboard with 18+1+2 power stages, DDR5, PCIe 5.0, WiFi 7 and onboard M.2 heatsinks.', 'public/images/products/motherboard_default.png'),
(106, 'Seasonic Focus GX-850 850W 80+ Gold', 'Power Supplies', 16800.00, 15, 'Full modular power supply, 80 PLUS Gold certified, ATX 3.0 / PCIe 5.0 ready with 135mm FDB silent fan.', 'public/images/products/psu_default.png'),
(107, 'NZXT Kraken Elite 360 RGB Liquid Cooler', 'Cooling', 34500.00, 3, '2.36 inch customizable LCD display, 3x F120 RGB Core fans, high-performance Asetek 7th gen pump (Low Stock Alert).', 'public/images/products/case_2.png'),
(108, 'Lian Li O11 Dynamic EVO Mid-Tower Case', 'Chassis', 19500.00, 0, 'Dual-chamber tempered glass case with reversible chassis design. Currently out of stock for testing real-time stock alert.', 'public/images/products/case_1.png');

-- Seed Sample Orders
INSERT INTO `Orders` (`order_id`, `user_id`, `product_id`, `quantity`, `order_date`) VALUES
(1, 2, 101, 1, '2026-11-20 10:15:00'),
(2, 2, 103, 1, '2026-11-20 10:15:00'),
(3, 2, 104, 2, '2026-11-22 14:30:00');
