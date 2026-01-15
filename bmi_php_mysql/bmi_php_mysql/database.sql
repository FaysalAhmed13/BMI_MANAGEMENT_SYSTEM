-- BMI System DB
CREATE DATABASE IF NOT EXISTS bmi_system_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE bmi_system_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  auto_id INT NULL,
  first_name VARCHAR(80) NOT NULL,
  last_name  VARCHAR(80) NOT NULL,
  sex ENUM('Male','Female') NOT NULL,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  phone VARCHAR(30) NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  profile_pic VARCHAR(255) NULL,
  user_type ENUM('admin','user') NOT NULL DEFAULT 'user',
  user_status ENUM('active','not_active') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bmi_records (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  height_cm DECIMAL(8,2) NOT NULL,
  weight_kg DECIMAL(8,2) NOT NULL,
  bmi DECIMAL(6,2) NOT NULL,
  status VARCHAR(40) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS auth_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token_hash CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX(token_hash)
);

-- Default admin (username: admin, password: admin123)
-- If you import this and admin password doesn't work, create admin via register.php (user_type=admin).
-- This hash is for 'admin123' generated with password_hash (bcrypt).
INSERT INTO users(first_name,last_name,sex,username,password_hash,phone,email,profile_pic,user_type,user_status)
SELECT 'Admin','User','Male','admin',
'$2y$10$eE0f9d3v5e7qfI1jPq2b4eQy5xk7yYx6ZkzVdPqX0Gm9tD0zZcQb2',
'','admin@example.com',NULL,'admin','active'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username='admin');
