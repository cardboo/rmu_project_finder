<?php
/**
 * Audit Log Helper for Admin Panel
 *
 * Logs sensitive operations to the audit_log table.
 * Requires an active $conn (mysqli) and an active session.
 *
 * Usage:
 *   audit_log($conn, 'department_created', "Created department: $dep_name (ID: $dep_id)");
 */

function audit_log(mysqli $conn, string $action, string $details = ''): void {
    $username = $_SESSION['username'] ?? 'unknown';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    $stmt = $conn->prepare(
        "INSERT INTO audit_log (username, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())"
    );

    if ($stmt) {
        $stmt->bind_param("ssss", $username, $action, $details, $ip);
        $stmt->execute();
        $stmt->close();
    }
}
