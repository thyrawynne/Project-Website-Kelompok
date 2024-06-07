<?php
include '../config/db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

// Retrieve the user's role from the session
$userRole = $_SESSION['role'];

$contacts = $conn->query("SELECT * FROM contact_messages")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Contact Messages</title>
    <link rel="stylesheet" type="text/css" href="../styles/admin.css">
</head>
<body>
    <div class="container">
        <h1>View Contact Messages</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <?php if ($userRole == 'admin'): ?>
                    <li><a href="manage_genres.php">Manage Genres</a></li>
                <?php endif; ?>
                <li><a href="manage_manga.php">Manage Manga</a></li>
                <li><a href="manage_chapters.php">Manage Chapters</a></li>
                <li><a href="../process/logout.php">Logout</a></li> <!-- Logout link -->
            </ul>
        </nav>
        
        <h2>Contact Messages</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact): ?>
                <tr>
                    <td><?= $contact['message_id'] ?></td>
                    <td><?= $contact['name'] ?></td>
                    <td><?= $contact['email_address'] ?></td>
                    <td><?= $contact['subject'] ?></td>
                    <td><?= $contact['message'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
