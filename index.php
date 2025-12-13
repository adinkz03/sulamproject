<?php
/**
 * Front Controller
 * Central entry point for all requests
 */

// Load configuration if it exists
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

// Load router and routes (moved under features)
// Define base path (empty for root, or '/subfolder' if in a subfolder)
// Auto-detect base path
$scriptName = $_SERVER['SCRIPT_NAME']; // e.g. /sulamprojectex/index.php
$dir = dirname($scriptName); // e.g. /sulamprojectex
define('APP_BASE_PATH', $dir === '/' || $dir === '\\' ? '' : $dir);

// Load router and routes (moved under features)
$router = require_once __DIR__ . '/features/shared/lib/routes.php';

// Get request method and URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Remove query string
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

// Decode URI
$uri = rawurldecode($uri);

// Remove base path if present
if (APP_BASE_PATH !== '' && strpos($uri, APP_BASE_PATH) === 0) {
    $uri = substr($uri, strlen(APP_BASE_PATH));
}

// Ensure URI starts with /
if (empty($uri) || $uri[0] !== '/') {
    $uri = '/' . $uri;
}

// Dispatch request
$router->dispatch($method, $uri);
