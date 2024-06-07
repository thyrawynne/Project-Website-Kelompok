<?php
include '../config/db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['manga_id'])) {
    header("Location: ../admin/manage_manga.php");
    exit();
}

$manga_id = $_GET['manga_id'];

try {
    // Begin a transaction
    $conn->beginTransaction();

    // Delete associated rows in the manga_genres table
    $stmt = $conn->prepare("DELETE FROM manga_genres WHERE manga_id = ?");
    $stmt->execute([$manga_id]);

    // Delete associated rows in the chapters table
    $stmt = $conn->prepare("DELETE FROM chapters WHERE manga_id = ?");
    $stmt->execute([$manga_id]);

    // Delete the manga
    $stmt = $conn->prepare("DELETE FROM manga WHERE manga_id = ?");
    $stmt->execute([$manga_id]);

    // Commit the transaction
    $conn->commit();

    header("Location: ../admin/manage_manga.php");
    exit();
} catch (PDOException $e) {
    // Rollback the transaction if something went wrong
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
