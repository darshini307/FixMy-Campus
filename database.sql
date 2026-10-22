CREATE DATABASE IF NOT EXISTS fixmycampus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fixmycampus;

CREATE TABLE IF NOT EXISTS users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS staff (
    staff_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    staff_name VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    contact_number VARCHAR(30) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS issues (
    issue_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    complaint_id VARCHAR(30) NOT NULL UNIQUE,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    category ENUM('Electrical', 'Water and Plumbing', 'Furniture', 'Infrastructure', 'Others') NOT NULL,
    location VARCHAR(150) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    status ENUM('Reported', 'Assigned', 'In Progress', 'Resolved') NOT NULL DEFAULT 'Reported',
    assigned_staff INT UNSIGNED DEFAULT NULL,
    priority ENUM('Normal', 'High') NOT NULL DEFAULT 'Normal',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_issue_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_issue_staff FOREIGN KEY (assigned_staff) REFERENCES staff(staff_id) ON DELETE SET NULL,
    INDEX idx_issue_location_category (location, category),
    INDEX idx_issue_status (status)
) ENGINE=InnoDB;

INSERT INTO staff (staff_name, department, contact_number) VALUES
('Arun Kumar', 'Electrical Maintenance', '9876543210'),
('Priya Sharma', 'Plumbing', '9876543211'),
('Rahul Singh', 'Civil and Furniture', '9876543212');

-- After registering a normal account, promote it to an administrator:
-- UPDATE users SET role = 'admin' WHERE email = 'admin@example.com';
