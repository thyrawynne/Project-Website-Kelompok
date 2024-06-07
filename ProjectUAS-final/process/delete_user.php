<?php
include '../config/db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    // Redirect to dashboard if not an admin
    header("Location: ../admin/dashboard.php");
    exit();
}

// Check if user ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: ../admin/dashboard.php");
    exit();
}

$userID = $_GET['id'];

// Delete user from the database
$query = "DELETE FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$userID]);

// Set alert message for user deletion
$alertMessage = "User Deleted Successfully!";

// Redirect to dashboard after deletion
header("Location: ../admin/dashboard.php?alert=" . urlencode($alertMessage));
exit();
?>
