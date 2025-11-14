<?php
/**
 * Input Validation Utilities
 * Common validation functions
 */

function validateRequired($value, $fieldName = 'Field') {
    if (empty(trim($value))) {
        return ['valid' => false, 'message' => "$fieldName is required"];
    }
    return ['valid' => true];
}

function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'message' => 'Invalid email format'];
    }
    return ['valid' => true];
}

function validateLength($value, $min, $max, $fieldName = 'Field') {
    $length = strlen($value);
    if ($length < $min || $length > $max) {
        return ['valid' => false, 'message' => "$fieldName must be between $min and $max characters"];
    }
    return ['valid' => true];
}

function validateUsername($username) {
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        return ['valid' => false, 'message' => 'Username must be 3-20 characters (letters, numbers, underscore only)'];
    }
    return ['valid' => true];
}

function validatePassword($password) {
    if (strlen($password) < 8) {
        return ['valid' => false, 'message' => 'Password must be at least 8 characters'];
    }
    return ['valid' => true];
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateAndSanitize($value, $validators = []) {
    foreach ($validators as $validator) {
        $result = $validator($value);
        if (!$result['valid']) {
            return $result;
        }
    }
    return ['valid' => true, 'value' => sanitizeInput($value)];
}
