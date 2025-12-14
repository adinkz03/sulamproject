<?php
/**
 * Common Utility Functions
 * General-purpose helper functions
 */

if (!defined('APP_BASE_PATH')) {
    // Auto-detect base path if not defined (e.g. when accessing files directly)
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $dir = dirname($scriptName);
    
    // Walk up directories until we find the root (where index.php or config.php usually is)
    // This is a heuristic. A better way is to rely on a known relative path.
    // Since we know this file is in features/shared/lib/utilities/, we can calculate the root.
    // But $scriptName is the executed script (e.g. /sulamproject/features/events/admin/pages/events.php).
    
    // Simple fallback: assume the project root is the first directory after localhost if strictly structured,
    // or just use the relative path logic if we know the depth.
    
    // Better approach for this project structure:
    // If we are in /sulamproject/features/..., the base is /sulamproject
    
    // Let's try to find 'sulamproject' or just use the relative path from document root.
    // Actually, the index.php logic was:
    // $dir = dirname($scriptName);
    // define('APP_BASE_PATH', $dir === '/' || $dir === '\\' ? '' : $dir);
    
    // But for deep files, dirname($scriptName) is /sulamproject/features/events/admin/pages.
    // We want /sulamproject.
    
    // Let's use the $ROOT variable if available, or calculate it.
    // The $ROOT in the calling script is usually dirname(__DIR__, 4).
    // We can't easily get the web path from the file path without knowing the Document Root.
    
    // Standard way:
    $relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__, 4)));
    define('APP_BASE_PATH', $relativePath === '/' ? '' : $relativePath);
}

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function url($path = '') {
    $base = defined('APP_BASE_PATH') ? APP_BASE_PATH : '';
    $path = ltrim($path, '/');
    return $base . '/' . $path;
}

function redirect($url) {
    // If it's a full URL, just redirect
    if (strpos($url, 'http') === 0) {
        header("Location: $url");
        exit();
    }
    
    // Otherwise, use the url() helper
    $url = url($url);
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

/**
 * Convert a numeric amount to words in Malay (Ringgit Malaysia)
 * 
 * @param float $number The amount to convert
 * @return string The amount in words (e.g., "Satu Ratus Lima Puluh Ringgit Dan Lima Puluh Sen Sahaja")
 */
function numberToWords($number) {
    $number = abs(floatval($number));
    
    // Split into ringgit and sen
    $ringgit = floor($number);
    $sen = round(($number - $ringgit) * 100);
    
    // Malay number words
    $ones = [
        0 => 'Sifar', 1 => 'Satu', 2 => 'Dua', 3 => 'Tiga', 4 => 'Empat',
        5 => 'Lima', 6 => 'Enam', 7 => 'Tujuh', 8 => 'Lapan', 9 => 'Sembilan',
        10 => 'Sepuluh', 11 => 'Sebelas', 12 => 'Dua Belas', 13 => 'Tiga Belas',
        14 => 'Empat Belas', 15 => 'Lima Belas', 16 => 'Enam Belas',
        17 => 'Tujuh Belas', 18 => 'Lapan Belas', 19 => 'Sembilan Belas'
    ];
    
    $tens = [
        2 => 'Dua Puluh', 3 => 'Tiga Puluh', 4 => 'Empat Puluh', 5 => 'Lima Puluh',
        6 => 'Enam Puluh', 7 => 'Tujuh Puluh', 8 => 'Lapan Puluh', 9 => 'Sembilan Puluh'
    ];
    
    /**
     * Convert a number less than 1000 to words
     */
    $convertHundreds = function($num) use ($ones, $tens) {
        if ($num == 0) return '';
        
        $words = '';
        
        if ($num >= 100) {
            $hundreds = floor($num / 100);
            if ($hundreds == 1) {
                $words .= 'Seratus ';
            } else {
                $words .= $ones[$hundreds] . ' Ratus ';
            }
            $num %= 100;
        }
        
        if ($num >= 20) {
            $words .= $tens[floor($num / 10)] . ' ';
            $num %= 10;
            if ($num > 0) {
                $words .= $ones[$num] . ' ';
            }
        } elseif ($num > 0) {
            $words .= $ones[$num] . ' ';
        }
        
        return trim($words);
    };
    
    /**
     * Convert full number to words
     */
    $convertNumber = function($num) use ($convertHundreds, $ones) {
        if ($num == 0) return 'Sifar';
        
        $words = '';
        
        // Billions (Bilion)
        if ($num >= 1000000000) {
            $billions = floor($num / 1000000000);
            if ($billions == 1) {
                $words .= 'Satu Bilion ';
            } else {
                $words .= $convertHundreds($billions) . ' Bilion ';
            }
            $num %= 1000000000;
        }
        
        // Millions (Juta)
        if ($num >= 1000000) {
            $millions = floor($num / 1000000);
            if ($millions == 1) {
                $words .= 'Satu Juta ';
            } else {
                $words .= $convertHundreds($millions) . ' Juta ';
            }
            $num %= 1000000;
        }
        
        // Thousands (Ribu)
        if ($num >= 1000) {
            $thousands = floor($num / 1000);
            if ($thousands == 1) {
                $words .= 'Seribu ';
            } else {
                $words .= $convertHundreds($thousands) . ' Ribu ';
            }
            $num %= 1000;
        }
        
        // Hundreds
        if ($num > 0) {
            $words .= $convertHundreds($num);
        }
        
        return trim($words);
    };
    
    // Build the result
    $result = '';
    
    if ($ringgit > 0) {
        $result = $convertNumber($ringgit) . ' Ringgit';
    }
    
    if ($sen > 0) {
        if ($ringgit > 0) {
            $result .= ' Dan ';
        }
        $result .= $convertNumber($sen) . ' Sen';
    }
    
    if (empty($result)) {
        $result = 'Sifar Ringgit';
    }
    
    return $result . ' Sahaja';
}
