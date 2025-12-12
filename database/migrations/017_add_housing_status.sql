-- Migration: 014_add_housing_status.sql
-- Adds `housing_status` column to `users` table after `address` column
ALTER TABLE users ADD COLUMN housing_status VARCHAR(64) DEFAULT NULL AFTER address;

