<?php
session_start();

$conn = new mysqli("localhost", "root", "", "lost_found");
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

function requireLogin() {
    if (empty($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function isLoggedIn() {
    return !empty($_SESSION['user_id']);
}
