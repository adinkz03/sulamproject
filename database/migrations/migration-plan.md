# Database Migration Plan

## Overview
This document outlines the database tables to be provisioned for the SulamProject feature-based architecture.

## Migration Strategy
- **Current**: Auto-provisioning via `db-bootstrap.php` (creates `users` table only)
- **Target**: Expand auto-provisioning to include all core tables
- **Approach**: Create migration files in `database/migrations/` and update bootstrap logic

## Required Tables

### 1. Authentication & Authorization

#### `roles`
```sql
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) UNIQUE NOT NULL,
    `description` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### `user_roles`
```sql
CREATE TABLE IF NOT EXISTS `user_roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `role_id` INT NOT NULL,
    `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_user_role` (`user_id`, `role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### `auth_attempts`
```sql
CREATE TABLE IF NOT EXISTS `auth_attempts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL,
    `ip_address` VARCHAR(45),
    `success` BOOLEAN DEFAULT FALSE,
    `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_username` (`username`),
    INDEX `idx_attempted_at` (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. Residents Module

#### `households`
```sql
CREATE TABLE IF NOT EXISTS `households` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `head_of_household_id` INT,
    `address` TEXT,
    `phone` VARCHAR(20),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`head_of_household_id`) REFERENCES `residents`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### `residents`
```sql
CREATE TABLE IF NOT EXISTS `residents` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(255) NOT NULL,
    `ic_number` VARCHAR(20) UNIQUE,
    `date_of_birth` DATE,
    `gender` ENUM('male', 'female') NOT NULL,
    `phone` VARCHAR(20),
    `email` VARCHAR(100),
    `occupation` VARCHAR(100),
    `marital_status` ENUM('single', 'married', 'divorced', 'widowed'),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_full_name` (`full_name`),
    INDEX `idx_ic_number` (`ic_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### `resident_household`
```sql
CREATE TABLE IF NOT EXISTS `resident_household` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `resident_id` INT NOT NULL,
    `household_id` INT NOT NULL,
    `relationship` VARCHAR(50),
    `joined_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`resident_id`) REFERENCES `residents`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`household_id`) REFERENCES `households`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_resident_household` (`resident_id`, `household_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. Financial Assistance Module

#### `assistance_applications`
```sql
CREATE TABLE IF NOT EXISTS `assistance_applications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `resident_id` INT NOT NULL,
    `application_date` DATE NOT NULL,
    `assistance_type` ENUM('zakat', 'emergency', 'education', 'medical', 'other') NOT NULL,
    `amount_requested` DECIMAL(10, 2),
    `reason` TEXT,
    `status` ENUM('pending', 'approved', 'rejected', 'disbursed') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`resident_id`) REFERENCES `residents`(`id`) ON DELETE CASCADE,
    INDEX `idx_status` (`status`),
    INDEX `idx_application_date` (`application_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### `assistance_assessments`
```sql
CREATE TABLE IF NOT EXISTS `assistance_assessments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `application_id` INT NOT NULL,
    `assessed_by` INT NOT NULL,
    `household_income` DECIMAL(10, 2),
    `dependents_count` INT,
    `assessment_notes` TEXT,
    `recommended_amount` DECIMAL(10, 2),
    `assessed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`application_id`) REFERENCES `assistance_applications`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`assessed_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### `assistance_approvals`
```sql
CREATE TABLE IF NOT EXISTS `assistance_approvals` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `application_id` INT NOT NULL,
    `approved_by` INT NOT NULL,
    `approved_amount` DECIMAL(10, 2) NOT NULL,
    `approval_notes` TEXT,
    `approved_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`application_id`) REFERENCES `assistance_applications`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### `assistance_disbursements`
```sql
CREATE TABLE IF NOT EXISTS `assistance_disbursements` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `application_id` INT NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `disbursement_method` ENUM('cash', 'bank_transfer', 'cheque') NOT NULL,
    `reference_number` VARCHAR(100),
    `disbursed_by` INT NOT NULL,
    `disbursed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`application_id`) REFERENCES `assistance_applications`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`disbursed_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4. Donations Module

#### `donors`
```sql
CREATE TABLE IF NOT EXISTS `donors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `email` VARCHAR(100),
    `address` TEXT,
    `is_anonymous` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_full_name` (`full_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### `donations`
```sql
CREATE TABLE IF NOT EXISTS `donations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `donor_id` INT,
    `amount` DECIMAL(10, 2) NOT NULL,
    `donation_type` ENUM('general', 'zakat', 'sadaqah', 'building_fund', 'other') NOT NULL,
    `payment_method` ENUM('cash', 'bank_transfer', 'cheque', 'online') NOT NULL,
    `reference_number` VARCHAR(100),
    `donation_date` DATE NOT NULL,
    `notes` TEXT,
    `receipt_issued` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`donor_id`) REFERENCES `donors`(`id`) ON DELETE SET NULL,
    INDEX `idx_donation_date` (`donation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### `donation_receipts`
```sql
CREATE TABLE IF NOT EXISTS `donation_receipts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `donation_id` INT NOT NULL,
    `receipt_number` VARCHAR(50) UNIQUE NOT NULL,
    `issued_date` DATE NOT NULL,
    `issued_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`donation_id`) REFERENCES `donations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`issued_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5. Death & Funeral Module

#### `death_notifications`
```sql
CREATE TABLE IF NOT EXISTS `death_notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `deceased_name` VARCHAR(255) NOT NULL,
    `ic_number` VARCHAR(20),
    `date_of_death` DATE NOT NULL,
    `place_of_death` VARCHAR(255),
    `cause_of_death` VARCHAR(255),
    `next_of_kin_name` VARCHAR(255),
    `next_of_kin_phone` VARCHAR(20),
    `reported_by` INT,
    `verified` BOOLEAN DEFAULT FALSE,
    `verified_by` INT,
    `verified_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`reported_by`) REFERENCES `users`(`id`),
    FOREIGN KEY (`verified_by`) REFERENCES `users`(`id`),
    INDEX `idx_date_of_death` (`date_of_death`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### `funeral_logistics`
```sql
CREATE TABLE IF NOT EXISTS `funeral_logistics` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `death_notification_id` INT NOT NULL,
    `burial_date` DATE,
    `burial_location` VARCHAR(255),
    `grave_number` VARCHAR(50),
    `arranged_by` INT NOT NULL,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`death_notification_id`) REFERENCES `death_notifications`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`arranged_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 6. Events Module

#### `events`
```sql
CREATE TABLE IF NOT EXISTS `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `event_date` DATE NOT NULL,
    `start_time` TIME,
    `end_time` TIME,
    `location` VARCHAR(255),
    `capacity` INT,
    `status` ENUM('draft', 'published', 'cancelled') DEFAULT 'draft',
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`),
    INDEX `idx_event_date` (`event_date`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 7. Audit & System Tables

#### `audit_logs`
```sql
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT,
    `details` TEXT,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_entity` (`entity_type`, `entity_id`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Implementation Steps

1. **Update `db-bootstrap.php`**: Expand to create all tables above
2. **Create migration files**: Individual SQL files per module in `database/migrations/`
3. **Add migration runner**: PHP script to apply migrations in order
4. **Seed initial data**: Insert default roles (admin, user) in `roles` table
5. **Update existing `db.php`**: Maintain backward compatibility, call new bootstrap

## Notes

- All tables use InnoDB engine for transaction support
- Soft deletes not implemented; using CASCADE where appropriate
- Timestamps use MySQL TIMESTAMP for automatic handling
- Foreign keys enforce referential integrity
- Indexes added for common query patterns
- Charset utf8mb4 for full Unicode support including emojis
