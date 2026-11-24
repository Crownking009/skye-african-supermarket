-- Optional: the admin creates these automatically on first sign-in.
CREATE TABLE IF NOT EXISTS admin_users(id INT AUTO_INCREMENT PRIMARY KEY,username VARCHAR(40) NOT NULL UNIQUE,pass_hash VARCHAR(255) NOT NULL,role ENUM('owner','manager','staff') NOT NULL DEFAULT 'staff',active TINYINT(1) DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS settings(k VARCHAR(40) PRIMARY KEY,v TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
