<?php
include '../config/db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

// Retrieve the user's role and ID from the session
$userRole = $_SESSION['role'];
$loggedInUserId = $_SESSION['user_id'];

// Check if user ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: ../admin/dashboard.php");
    exit();
}

$userID = $_GET['id'];

// If the user is a publisher and tries to edit another user's profile, redirect to dashboard
if ($userRole === 'publisher' && $userID != $loggedInUserId) {
    header("Location: ../admin/dashboard.php");
    exit();
}

// Fetch user details from the database
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If user is not found, redirect to dashboard
if (!$user) {
    header("Location: ../admin/dashboard.php");
    exit();
}

// If form is submitted for updating user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Ensure that publishers cannot change roles
    if ($userRole !== 'admin') {
        $role = $user['role'];
    }

    // Update user details in the database
    $query = "UPDATE users SET username = ?, email = ?, role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$username, $email, $role, $userID]);

    // If password change is requested
    if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        // Verify current password
        if (password_verify($currentPassword, $user['password'])) {
            // Check if new password and confirm password match
            if ($newPassword === $confirmPassword) {
                // Hash new password
                $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update password in the database
                $query = "UPDATE users SET password = ? WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$hashedNewPassword, $userID]);

                echo "Password updated successfully.";
            } else {
                echo "New password and confirm password do not match.";
            }
        } else {
            echo "Current password is incorrect.";
        }
    }

    // Redirect to dashboard after update
    header("Location: ../admin/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" type="text/css" href="../styles/admin.css">
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required class="input-field">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="input-field">
            <?php if ($userRole === 'admin'): ?>
                <label for="role">Role:</label>
                <select id="role" name="role" required class="input-field">
                    <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="publisher" <?php echo ($user['role'] == 'publisher') ? 'selected' : ''; ?>>Publisher</option>
                    <option value="reader" <?php echo ($user['role'] == 'reader') ? 'selected' : ''; ?>>Reader</option>
                </select>
            <?php endif; ?>
            <br>
            <h2>Change Password</h2>
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" class="input-field">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" class="input-field">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" class="input-field">
            <br>
            <input type="submit" name="submit" value="Update User" class ="btn">
        </form>
        <br>
        <button onclick="window.location.href='../admin/dashboard.php';" class="btn delete">Back</button>
    </div>
</body>
</html>
