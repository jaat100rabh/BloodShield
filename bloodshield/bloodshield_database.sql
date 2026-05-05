-- BloodShield Database Structure
-- MySQL Database for Blood Donation Management System

-- Create Database
CREATE DATABASE IF NOT EXISTS bloodshield;
USE bloodshield;

-- Users Table (General user information)
CREATE TABLE IF NOT EXISTS users (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    phone VARCHAR(15) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    user_type ENUM('donor', 'recipient', 'admin') DEFAULT 'recipient',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Donors Table (Specific donor information)
CREATE TABLE IF NOT EXISTS donors (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    age INT(3) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    weight DECIMAL(5,2) NOT NULL,
    last_donation_date DATE,
    total_donations INT(11) DEFAULT 0,
    is_available BOOLEAN DEFAULT TRUE,
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    pincode VARCHAR(10),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    medical_conditions TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (blood_group),
    INDEX (is_available),
    INDEX (city)
);

-- Recipients Table (Specific recipient information)
CREATE TABLE IF NOT EXISTS recipients (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    blood_group_required ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    hospital_name VARCHAR(100),
    patient_name VARCHAR(100),
    urgency_level ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'Medium',
    units_required INT(11) NOT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    required_date DATE,
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    pincode VARCHAR(10),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    contact_person VARCHAR(100),
    contact_phone VARCHAR(15),
    status ENUM('Pending', 'Matched', 'Completed', 'Cancelled') DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (blood_group_required),
    INDEX (status),
    INDEX (urgency_level),
    INDEX (city)
);

-- Blood Requests Table (Detailed blood donation requests)
CREATE TABLE IF NOT EXISTS blood_requests (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    recipient_id INT(11) NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    units_needed INT(11) NOT NULL,
    urgency_level ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'Medium',
    hospital_name VARCHAR(100),
    patient_name VARCHAR(100),
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    required_date DATE,
    location_address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    pincode VARCHAR(10),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    contact_person VARCHAR(100),
    contact_phone VARCHAR(15),
    medical_reason TEXT,
    status ENUM('Active', 'Fulfilled', 'Cancelled', 'Expired') DEFAULT 'Active',
    FOREIGN KEY (recipient_id) REFERENCES recipients(id) ON DELETE CASCADE,
    INDEX (blood_group),
    INDEX (status),
    INDEX (urgency_level),
    INDEX (city),
    INDEX (required_date)
);

-- Donations Table (Record of actual donations)
CREATE TABLE IF NOT EXISTS donations (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    donor_id INT(11) NOT NULL,
    request_id INT(11) NOT NULL,
    donation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    units_donated INT(11) NOT NULL,
    donation_center VARCHAR(100),
    health_status ENUM('Good', 'Fair', 'Poor') DEFAULT 'Good',
    hemoglobin_level DECIMAL(4,2),
    blood_pressure VARCHAR(20),
    temperature DECIMAL(4,2),
    notes TEXT,
    status ENUM('Completed', 'Pending', 'Cancelled') DEFAULT 'Completed',
    FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE,
    FOREIGN KEY (request_id) REFERENCES blood_requests(id) ON DELETE CASCADE,
    INDEX (donation_date),
    INDEX (status)
);

-- Matches Table (Donor-Request matching system)
CREATE TABLE IF NOT EXISTS matches (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    request_id INT(11) NOT NULL,
    donor_id INT(11) NOT NULL,
    match_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    distance_km DECIMAL(8,2),
    compatibility_score DECIMAL(5,2),
    status ENUM('Pending', 'Accepted', 'Rejected', 'Completed', 'Cancelled') DEFAULT 'Pending',
    response_time TIMESTAMP NULL,
    completion_date TIMESTAMP NULL,
    FOREIGN KEY (request_id) REFERENCES blood_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE,
    INDEX (status),
    INDEX (match_date),
    INDEX (distance_km)
);

-- Notifications Table (System notifications)
CREATE TABLE IF NOT EXISTS notifications (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    notification_type ENUM('New_Request', 'Match_Found', 'Donation_Reminder', 'Request_Updated', 'System') NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    action_url VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (user_id),
    INDEX (is_read),
    INDEX (notification_type),
    INDEX (created_at)
);

-- Emergency Contacts Table
CREATE TABLE IF NOT EXISTS emergency_contacts (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    contact_name VARCHAR(100) NOT NULL,
    contact_phone VARCHAR(15) NOT NULL,
    relationship VARCHAR(50),
    is_primary BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (user_id),
    INDEX (is_primary)
);

-- Blood Banks Table
CREATE TABLE IF NOT EXISTS blood_banks (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50) NOT NULL,
    pincode VARCHAR(10),
    phone VARCHAR(15),
    email VARCHAR(100),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    operating_hours VARCHAR(100),
    website VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (city),
    INDEX (is_active)
);

-- Blood Inventory Table (Blood bank stock levels)
CREATE TABLE IF NOT EXISTS blood_inventory (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    blood_bank_id INT(11) NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    units_available INT(11) DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    minimum_level INT(11) DEFAULT 5,
    FOREIGN KEY (blood_bank_id) REFERENCES blood_banks(id) ON DELETE CASCADE,
    UNIQUE KEY (blood_bank_id, blood_group),
    INDEX (blood_group)
);

-- Login Table (Existing login system - keeping for compatibility)
CREATE TABLE IF NOT EXISTS login (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    phone VARCHAR(15) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('donor', 'recipient', 'admin') DEFAULT 'recipient',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Insert sample data for testing
INSERT INTO blood_banks (name, address, city, state, pincode, phone, email) VALUES 
('AIIMS Blood Bank', 'AIIMS Hospital, Ansari Nagar', 'New Delhi', 'Delhi', '110029', '01126588500', 'bloodbank@aiims.edu'),
('PGIMER Blood Bank', 'PGIMER, Sector 12', 'Chandigarh', 'Chandigarh', '160012', '01722747581', 'bloodbank@pgimer.edu.in'),
('KGMU Blood Bank', 'KGMU Campus, Chowk', 'Lucknow', 'Uttar Pradesh', '226003', '05222575300', 'bloodbank@kgmu.edu.in');

-- Insert sample blood inventory
INSERT INTO blood_inventory (blood_bank_id, blood_group, units_available, minimum_level) VALUES 
(1, 'A+', 25, 10), (1, 'A-', 15, 5), (1, 'B+', 20, 8), (1, 'B-', 12, 5),
(1, 'AB+', 18, 6), (1, 'AB-', 8, 3), (1, 'O+', 30, 12), (1, 'O-', 22, 8),
(2, 'A+', 22, 10), (2, 'A-', 18, 5), (2, 'B+', 25, 8), (2, 'B-', 14, 5),
(2, 'AB+', 20, 6), (2, 'AB-', 10, 3), (2, 'O+', 28, 12), (2, 'O-', 20, 8),
(3, 'A+', 20, 10), (3, 'A-', 12, 5), (3, 'B+', 18, 8), (3, 'B-', 10, 5),
(3, 'AB+', 15, 6), (3, 'AB-', 6, 3), (3, 'O+', 25, 12), (3, 'O-', 18, 8);

-- Create indexes for better performance
CREATE INDEX idx_users_phone ON users(phone);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_donors_blood_group_city ON donors(blood_group, city);
CREATE INDEX idx_donors_location ON donors(latitude, longitude);
CREATE INDEX idx_recipients_blood_group ON recipients(blood_group_required);
CREATE INDEX idx_requests_blood_group_urgency ON blood_requests(blood_group, urgency_level);
CREATE INDEX idx_requests_location ON blood_requests(latitude, longitude);
CREATE INDEX idx_donations_donor_date ON donations(donor_id, donation_date);
CREATE INDEX idx_matches_status_date ON matches(status, match_date);

-- Create view for active donors with availability
CREATE VIEW active_donors AS
SELECT d.*, u.full_name, u.phone, u.email
FROM donors d
JOIN users u ON d.user_id = u.id
WHERE d.is_available = TRUE 
AND u.is_active = TRUE
AND (d.last_donation_date IS NULL OR d.last_donation_date < DATE_SUB(CURDATE(), INTERVAL 90 DAY));

-- Create view for urgent blood requests
CREATE VIEW urgent_requests AS
SELECT br.*, r.patient_name, r.hospital_name, r.contact_phone, u.full_name as recipient_name
FROM blood_requests br
JOIN recipients r ON br.recipient_id = r.id
JOIN users u ON r.user_id = u.id
WHERE br.status = 'Active' 
AND br.required_date >= CURDATE()
AND br.urgency_level IN ('High', 'Critical');

-- Stored procedure for finding compatible donors
DELIMITER //
CREATE PROCEDURE FindCompatibleDonors(
    IN p_blood_group VARCHAR(3),
    IN p_city VARCHAR(50),
    IN p_max_distance DECIMAL(8,2),
    IN p_units_needed INT
)
BEGIN
    SELECT 
        d.id,
        d.blood_group,
        d.age,
        d.gender,
        u.full_name,
        u.phone,
        u.email,
        d.city,
        d.last_donation_date,
        d.total_donations,
        (
            6371 * acos(
                cos(radians(p_latitude)) * 
                cos(radians(d.latitude)) * 
                cos(radians(d.longitude) - radians(p_longitude)) + 
                sin(radians(p_latitude)) * 
                sin(radians(d.latitude))
            )
        ) AS distance_km
    FROM donors d
    JOIN users u ON d.user_id = u.id
    WHERE d.is_available = TRUE
    AND u.is_active = TRUE
    AND d.blood_group = p_blood_group
    AND (d.city = p_city OR p_city IS NULL)
    AND (d.last_donation_date IS NULL OR d.last_donation_date < DATE_SUB(CURDATE(), INTERVAL 90 DAY))
    HAVING distance_km <= p_max_distance OR p_max_distance IS NULL
    ORDER BY distance_km ASC, d.total_donations DESC
    LIMIT p_units_needed;
END //
DELIMITER ;

-- Triggers for maintaining data integrity
DELIMITER //
CREATE TRIGGER update_user_timestamp 
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END //

CREATE TRIGGER update_inventory_timestamp 
BEFORE UPDATE ON blood_inventory
FOR EACH ROW
BEGIN
    SET NEW.last_updated = CURRENT_TIMESTAMP;
END //
DELIMITER ;

-- Create admin user (default password: admin123)
INSERT INTO users (phone, email, password, full_name, user_type) VALUES 
('9999999999', 'admin@bloodshield.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');

INSERT INTO login (phone, password, user_type) VALUES 
('9999999999', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Database configuration complete
SELECT 'BloodShield Database Created Successfully!' as status;
