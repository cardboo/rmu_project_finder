<?php
/**
 * RMU Project Finder - Central Database Configuration
 * 
 * This file serves as a single source of truth for database connectivity.
 * All database operations should use the $conn object defined here.
 * 
 * This replaces the need for multiple datacon.php files across the application.
 */

// Database credentials
$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "project_finder";

// Create connection
$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8");

// Return connection object for use in controllers
?>
