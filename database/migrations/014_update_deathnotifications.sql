--based on migration-plan.md

CREATE TABLE IF NOT EXISTS `death_notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `deceased_name` VARCHAR(255) NOT NULL,
    `ic_number` VARCHAR(20),
    `date_of_death` DATE NOT NULL,
    `place_of_death` VARCHAR(255),
    `cause_of_death` VARCHAR(255),
    `next_of_kin_name` VARCHAR(255),
    `next_of_kin_phone` VARCHAR(20),

    -- UPDATED LINES BELOW: Added UNSIGNED (and potentially BIGINT)
    `reported_by` INT UNSIGNED, 
    `verified` BOOLEAN DEFAULT FALSE,
    `verified_by` INT UNSIGNED, 
    
    `verified_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`reported_by`) REFERENCES `users`(`id`),
    FOREIGN KEY (`verified_by`) REFERENCES `users`(`id`),
    INDEX `idx_date_of_death` (`date_of_death`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;