<?php
/**
 * Common Utility Functions
 * General-purpose helper functions
 */

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    // Add base path if not already present and not an absolute URL
    if (strpos($url, 'http') !== 0 && strpos($url, '/sulamproject') !== 0) {
        $url = '/sulamproject' . $url;
    }
    header("Location: $url");
    exit();
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

function getCurrentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'];
}

function formatDate($date, $format = 'Y-m-d H:i:s') {
    if (empty($date)) return '';
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

function logError($message, $context = []) {
    $logFile = __DIR__ . '/../../../../storage/logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? json_encode($context) : '';
    $logMessage = "[$timestamp] $message $contextStr" . PHP_EOL;
    error_log($logMessage, 3, $logFile);
}

function debugLog($message, $data = null) {
    if (getenv('APP_DEBUG') === 'true') {
        $logFile = __DIR__ . '/../../../../storage/logs/debug.log';
        $timestamp = date('Y-m-d H:i:s');
        $dataStr = $data !== null ? json_encode($data) : '';
        $logMessage = "[$timestamp] $message $dataStr" . PHP_EOL;
        error_log($logMessage, 3, $logFile);
    }
}
