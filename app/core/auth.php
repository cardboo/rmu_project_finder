<?php
/**
 * RMU Project Finder - Authentication Utilities
 * 
 * Provides functions for session management, user authentication,
 * and role-based access control checks.
 */

/**
 * Initialize session if not already started
 */
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user has active session, false otherwise
 */
function isLoggedIn() {
    initSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user's role
 * 
 * @return string|null User role (admin, dep_admin, public) or null if not logged in
 */
function getUserRole() {
    initSession();
    return $_SESSION['role'] ?? 'public';
}

/**
 * Get current user's ID
 * 
 * @return int|null User ID or null if not logged in
 */
function getUserId() {
    initSession();
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user's department (if applicable)
 * 
 * @return int|null Department ID or null
 */
function getUserDepartment() {
    initSession();
    return $_SESSION['department_id'] ?? null;
}

/**
 * Set user session data
 * 
 * @param int $userId User ID
 * @param string $role User role (admin, dep_admin)
 * @param int|null $departmentId Department ID (for dep_admin)
 * @param array $additionalData Any additional session data
 */
function setUserSession($userId, $role, $departmentId = null, $additionalData = []) {
    initSession();
    
    $_SESSION['user_id'] = $userId;
    $_SESSION['role'] = $role;
    
    if ($departmentId !== null) {
        $_SESSION['department_id'] = $departmentId;
    }
    
    // Add any additional data
    foreach ($additionalData as $key => $value) {
        $_SESSION[$key] = $value;
    }
}

/**
 * Destroy user session (logout)
 */
function destroyUserSession() {
    initSession();
    session_destroy();
    header("Location: /");
    exit;
}

/**
 * Check if user has specific role
 * 
 * @param string $requiredRole Role to check (admin, dep_admin, public)
 * @return bool True if user has required role
 */
function hasRole($requiredRole) {
    return getUserRole() === $requiredRole;
}

/**
 * Check if user has any of the specified roles
 * 
 * @param array $roles Array of roles to check
 * @return bool True if user has any of the specified roles
 */
function hasAnyRole($roles = []) {
    $userRole = getUserRole();
    return in_array($userRole, $roles);
}

?>
