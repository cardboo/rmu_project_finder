<?php
/**
 * Audit Log Helper for Department Admin Panel
 *
 * Logs sensitive operations to the audit_log table.
 * Requires an active $conn (mysqli) and an active session.
 *
 * Usage:
 *   audit_log($conn, 'project_created', "Created project: $title");
 */

function audit_log(mysqli $conn, string $action, string $details = ''): void {
    $username = $_SESSION['username'] ?? 'unknown';
    $dep_id = $_SESSION['dep_id'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    $stmt = $conn->prepare(
        "INSERT INTO audit_log (username, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())"
    );

    if ($stmt) {
        $full_details = $dep_id ? "[{$dep_id}] {$details}" : $details;
        $stmt->bind_param("ssss", $username, $action, $full_details, $ip);
        $stmt->execute();
        $stmt->close();
    }
}
