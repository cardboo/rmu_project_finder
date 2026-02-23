<?php
/**
 * RMU Project Finder - Middleware Functions
 * 
 * Provides middleware for role-based access control and authentication checks.
 * These functions should be called at the beginning of protected pages.
 */

require_once __DIR__ . '/auth.php';

/**
 * Require user to have a specific role
 * Redirects to login if not authenticated or authorized
 * 
 * @param string $requiredRole Role required to access page (admin, dep_admin, public)
 * @param string $loginRedirect URL to redirect if not authenticated
 */
function requireRole($requiredRole, $loginRedirect = '/admin/login/') {
    // Initialize session
    session_start();
    
    // Check if user is logged in
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: " . $loginRedirect);
        exit;
    }
    
    // Check if user has required role
    $userRole = getUserRole();
    if ($userRole !== $requiredRole) {
        http_response_code(403);
        die("Access Denied: You do not have permission to access this page.");
    }
}

/**
 * Require user to have any of the specified roles
 * 
 * @param array $allowedRoles Array of roles that are allowed
 * @param string $loginRedirect URL to redirect if not authenticated
 */
function requireAnyRole($allowedRoles = [], $loginRedirect = '/admin/login/') {
    session_start();
    
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: " . $loginRedirect);
        exit;
    }
    
    if (!hasAnyRole($allowedRoles)) {
        http_response_code(403);
        die("Access Denied: You do not have permission to access this page.");
    }
}

/**
 * Require admin role (super admin)
 * Convenience function for admin-only pages
 */
function requireAdmin() {
    requireRole('admin', '/admin/login/');
}

/**
 * Require department admin role
 * Convenience function for department admin-only pages
 */
function requireDepartmentAdmin() {
    requireRole('dep_admin', '/dep_admin/login/');
}

/**
 * Require user to be authenticated (any role)
 * For pages that should be accessible to logged-in users of any role
 */
function requireAuthenticated() {
    session_start();
    
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        // Determine which login page to redirect to based on referrer
        $loginRedirect = '/admin/login/';
        header("Location: " . $loginRedirect);
        exit;
    }
}

/**
 * Require specific department (for department admins)
 * Ensures user can only access their own department's data
 * 
 * @param int $departmentId Department ID to check against
 */
function requireDepartmentAccess($departmentId) {
    session_start();
    
    if (!isLoggedIn()) {
        http_response_code(401);
        die("Authentication required.");
    }
    
    $userRole = getUserRole();
    
    if ($userRole === 'admin') {
        // Super admin can access all departments
        return true;
    }
    
    if ($userRole === 'dep_admin') {
        $userDepartment = getUserDepartment();
        if ($userDepartment != $departmentId) {
            http_response_code(403);
            die("Access Denied: You can only access your own department.");
        }
        return true;
    }
    
    http_response_code(403);
    die("Access Denied.");
}

?>
