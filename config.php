<?php
// /turnament-pro/common/config.php

// Start session on all pages
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Database Credentials ---
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'turnament_pro_db';

// --- Establish Connection ---
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for full emoji support etc.
$conn->set_charset("utf8mb4");

// --- Global Functions ---
function format_currency($amount) {
    return '₹' . number_format($amount, 2);
}

function get_setting($conn, $key) {
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }
    return null;
}
?>