<?php
/**
 * CSRF Token Helper for Admin Panel
 *
 * Usage:
 *   Include this file after session_start().
 *   In forms:  <?= csrf_field() ?>
 *   In handlers: validate_csrf() at the top of POST handlers.
 */

function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES) . '">';
}

function validate_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die("Invalid or missing CSRF token. Please go back and try again.");
    }
}
