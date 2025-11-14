-- Migration: Add role column to users table
-- Date: 2025-11-14

-- Add role column to users table
ALTER TABLE `users` 
ADD COLUMN `role` varchar(20) NOT NULL DEFAULT 'user' AFTER `password_hash`;

-- Update existing users to have user role (if any exist)
UPDATE `users` SET `role` = 'user' WHERE `role` IS NULL OR `role` = '';
