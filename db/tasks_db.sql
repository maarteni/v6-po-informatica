-- Create the tasks database
CREATE DATABASE IF NOT EXISTS tasks_db;

-- database gebruiken (gaat kennelijk niet standaard)
USE tasks_db;

-- taak table
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,          -- dit is allemaal best vergelijkbaar met de user db die ik eerst heb gemaakt
    user_id INT NOT NULL,                       -- key om te linken naar de user db
    title VARCHAR(255) NOT NULL,                -- taak naam
    description TEXT,                           -- beschrijving (niet vereist)
    status ENUM('pending', 'completed') DEFAULT 'pending', -- status
    due_date DATE,                              -- inleverdatum (niet vereist)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- creer datum
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- meest recente update tijd
    FOREIGN KEY (user_id) REFERENCES users_db.users(id) ON DELETE CASCADE -- taken weghalen als user is weggehaald
);
