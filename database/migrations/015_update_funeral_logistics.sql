--based on migration-plan.md

CREATE TABLE IF NOT EXISTS `funeral_logistics` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `death_notification_id` INT NOT NULL, -- Ensure death_notifications.id is also INT (Signed or Unsigned matching this)
    `burial_date` DATE,
    `burial_location` VARCHAR(255),
    `grave_number` VARCHAR(50),
    `arranged_by` INT UNSIGNED NOT NULL,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`death_notification_id`) REFERENCES `death_notifications`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`arranged_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;