-- Run once after schema_phase6.sql. Adds payment/refund tracking,
-- admin recovery email/reset storage, and newsletter consent/unsubscribe fields.
ALTER TABLE orders
 ADD COLUMN payment_status ENUM('not_applicable','unpaid','customer_marked_paid','verified','failed') NOT NULL DEFAULT 'unpaid',
 ADD COLUMN payment_done_at DATETIME NULL,
 ADD COLUMN refund_status ENUM('none','requested','processing','completed','rejected') NOT NULL DEFAULT 'none',
 ADD COLUMN refund_amount DECIMAL(8,2) NOT NULL DEFAULT 0,
 ADD COLUMN refund_note VARCHAR(500) NULL,
 ADD COLUMN refunded_at DATETIME NULL,
 ADD KEY idx_payment_status(payment_status),
 ADD KEY idx_refund_status(refund_status);

ALTER TABLE admin_users ADD COLUMN email VARCHAR(190) NULL UNIQUE;

CREATE TABLE request_limits(
 bucket CHAR(64) PRIMARY KEY,
 hits INT NOT NULL DEFAULT 0,
 window_start BIGINT NOT NULL,
 blocked_until BIGINT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE admin_password_resets(
 id INT AUTO_INCREMENT PRIMARY KEY,
 admin_id INT NOT NULL,
 token_hash CHAR(64) NOT NULL UNIQUE,
 expires_at DATETIME NOT NULL,
 used_at DATETIME NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY(admin_id),
 FOREIGN KEY(admin_id) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE subscribers
 ADD COLUMN consent_at DATETIME NULL,
 ADD COLUMN confirmed_at DATETIME NULL,
 ADD COLUMN confirmation_token CHAR(64) NULL,
 ADD COLUMN unsubscribed_at DATETIME NULL,
 ADD COLUMN unsubscribe_token CHAR(64) NULL UNIQUE;

