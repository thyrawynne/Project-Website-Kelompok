<?php
include '../config/db_connect.php'; // Assuming you have a database connection file
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email_address'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email_address, subject, message) VALUES (:name, :email, :subject, :message)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':subject', $subject);
    $stmt->bindParam(':message', $message);

    // Initialize response variables
    $status = "error";
    $popup = "An error occurred while processing your request.";

    if ($stmt->execute()) {
        // Set response variables for success
        $status = "success";
        $popup = "Your message has been sent successfully!";
    }

    // Close the statement
    $stmt = null;

    // Close the database connection
    $conn = null;

    // Construct JavaScript code to display pop-up message
    echo "<script>";
    echo "alert('$popup');";
    echo "window.location.href = '../index.php';";
    echo "</script>";
    exit();
}

// If the form is accessed directly without submitting, redirect to home page
header("Location: ../index.php");
exit();
?>
