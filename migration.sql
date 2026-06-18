-- ============================================
-- Gym Management System — Database Migration
-- ============================================
-- Run this script against the 'loginsystem' database
-- before deploying the modernized application.
--
-- This migration is ADDITIVE — it does NOT drop or rename
-- any existing tables/columns, ensuring backward compatibility.
-- ============================================

USE loginsystem;

-- 1. Create MembershipProgram table
--    Links Members (doctorapp) to Packages with date tracking
CREATE TABLE IF NOT EXISTS MembershipProgram (
    membership_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id VARCHAR(40) NOT NULL COMMENT 'References doctorapp.contact (Member ID)',
    package_id VARCHAR(40) NOT NULL COMMENT 'References Package.Package_id',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE' COMMENT 'ACTIVE, EXPIRED, or PENDING',
    deleted_at DATETIME DEFAULT NULL COMMENT 'Soft delete timestamp, NULL means active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_member_id (member_id),
    INDEX idx_package_id (package_id),
    INDEX idx_status (status),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Fix Payment table: add missing customer_name column
--    The existing payment form collects customer_name but the column
--    was missing from the original schema, causing INSERT failures.
ALTER TABLE Payment
    ADD COLUMN IF NOT EXISTS customer_name VARCHAR(40) DEFAULT '' AFTER Amount;

-- 3. Add created_at timestamp to doctorapp (Members) for sorting
ALTER TABLE doctorapp
    ADD COLUMN IF NOT EXISTS created_at DATETIME DEFAULT CURRENT_TIMESTAMP;

-- 4. Insert sample MembershipProgram data for demonstration
INSERT INTO MembershipProgram (member_id, package_id, start_date, end_date, status) VALUES
('201', '1', '2025-01-01', '2025-12-31', 'ACTIVE'),
('202', '2', '2025-03-15', '2025-09-15', 'ACTIVE'),
('203', '3', '2024-06-01', '2024-12-01', 'EXPIRED'),
('204', '1', '2025-06-01', '2025-12-01', 'PENDING'),
('205', '2', '2025-02-01', '2025-08-01', 'ACTIVE');
