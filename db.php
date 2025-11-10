<?php
// Database connection bootstrap for Laragon + phpMyAdmin
// Target DB: masjid; Target table: user
// Exposes $mysqli (mysqli connection)

$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'masjid';
$DB_CHARSET = 'utf8mb4';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
    $mysqli->set_charset($DB_CHARSET);

    // Create database if it doesn't exist
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `{$DB_NAME}` CHARACTER SET {$DB_CHARSET} COLLATE {$DB_CHARSET}_unicode_ci");
    $mysqli->select_db($DB_NAME);

    // Ensure the `user` table exists
    $createUserTableSql = <<<SQL
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET={$DB_CHARSET} COLLATE {$DB_CHARSET}_unicode_ci;
SQL;
    $mysqli->query($createUserTableSql);
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo '<h2>Database connection error</h2>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}
