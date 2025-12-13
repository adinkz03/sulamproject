<?php
// === ENVIRONMENT DETECTION ===
// Auto-detect if we're on local development or production server
$isLocal = (
    $_SERVER['SERVER_NAME'] === 'localhost' || 
    $_SERVER['SERVER_ADDR'] === '127.0.0.1' ||
    strpos($_SERVER['SERVER_NAME'], 'localhost') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'localhost') !== false
);

// === DEBUGGING MODE ===
// Show errors on local, hide on production
if ($isLocal) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 1);  // Temporarily ON for production debugging
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    // TODO: After fixing, set these to 0 for production security
}

// === DATABASE CONFIGURATION ===
if ($isLocal) {
    // LOCAL DEVELOPMENT SETTINGS (Laragon)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'masjidkamek');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    // PRODUCTION SETTINGS (Hostinger)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u771315171_masjidkamek');
    define('DB_USER', 'u771315171_root');  // <-- VERIFY THIS IN HOSTINGER PANEL
    define('DB_PASS', 'Masjidkamek25');
}

// Set Environment Variables (for code using getenv)
putenv("DB_HOST=" . DB_HOST);
putenv("DB_NAME=" . DB_NAME);
putenv("DB_USER=" . DB_USER);
putenv("DB_PASS=" . DB_PASS);
