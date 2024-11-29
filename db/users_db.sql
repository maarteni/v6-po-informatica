-- Create the users database
CREATE DATABASE IF NOT EXISTS users_db;

-- database gebruiken (best wel obvious tbh)
USE users_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,          -- (uniek) user id
    username VARCHAR(50) NOT NULL UNIQUE,       -- inlog naam (kan niet 2x hetzelfde zijn) (stackovervlow)
    password VARCHAR(255) NOT NULL,             -- gekopieerd van stackovervlow ik heb werkelijkwaar geen idee
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- account datum
);
