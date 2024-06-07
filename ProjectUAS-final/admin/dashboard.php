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

// Define alert messages
$alertMessage = '';

// Function to fetch all users from the database
function getUsers($conn) {
    $query = "SELECT * FROM users";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to fetch the current user's details from the database
function getUserById($conn, $id) {
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// If the form is submitted for adding a new user (only accessible by admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && $userRole === 'admin') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Encrypt password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user into the database
    $query = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    try {
        $success = $stmt->execute([$username, $hashedPassword, $email, $role]);

        // Set alert message based on execution success
        if ($success) {
            $alertMessage = "User Added Successfully!";
        } else {
            $alertMessage = "Failed to Add User!";
        }
    } catch (PDOException $e) {
        // Check if the error is due to duplicate entry
        if ($e->errorInfo[1] == 1062) {
            $alertMessage = "Username or Email already exists!";
        } else {
            $alertMessage = "Error: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo ucfirst($userRole); ?> Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../styles/admin.css">

    <script src="../js/admin.js"></script>
</head>
<body>
    <div class="container">
        <h1><?php echo ucfirst($userRole); ?> Dashboard</h1>
        <?php if (!empty($alertMessage)): ?>
            <script>
                // Display pop-up alert using JavaScript
                showAlert("<?php echo $alertMessage; ?>");
            </script>
        <?php endif; ?>
        <nav>
            <ul>
                <?php if ($userRole === 'admin'): ?>
                    <li><a href="manage_users.php">Manage Users</a></li>
                    <li><a href="manage_genres.php">Manage Genres</a></li>
                <?php endif; ?>
                <li><a href="manage_manga.php">Manage Manga</a></li>
                <li><a href="manage_chapters.php">Manage Chapters</a></li>
                <?php if ($userRole === 'admin'): ?>
                    <li><a href="view_contacts.php">View Contact Messages</a></li>
                <?php endif; ?>
                <li><a href="../process/logout.php">Logout</a></li> <!-- Logout link -->
            </ul>
        </nav>
        <h2>Overview</h2>
        <p>Use the navigation links above to manage <?php if ($userRole === 'admin'): ?>genres, view contact messages,<?php endif; ?>  manga, and chapters.</p>

        <?php if ($userRole === 'admin'): ?>
            <h2>Add New User</h2>
            <form method="post" action="">
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required class="input-field">
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required class="input-field">
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required class="input-field"><br>
                <label for="role">Role:</label><br>
                <select id="role" name="role" required class="input-field">
                    <option value="admin">Admin</option>
                    <option value="publisher">Publisher</option>
                    <option value="reader">Reader</option>
                </select><br>
                <input type="submit" name="submit" value="Add User" class="btn">
            </form>
        <?php endif; ?>
        
        <h2>Users</h2>
        <?php
        // Fetch and display users based on role
        if ($userRole === 'admin') {
            $users = getUsers($conn);
        } else if ($userRole === 'publisher') {
            $users = [getUserById($conn, $loggedInUserId)];
        }

        if (count($users) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Action</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>".$user['user_id']."</td>";
                echo "<td>".$user['username']."</td>";
                echo "<td>".$user['email']."</td>";
                echo "<td>".$user['role']."</td>";
                echo "<td><a href='../process/edit_user.php?id=".$user['user_id']."' class='btn'>Edit</a>";
                if ($userRole === 'admin') {
                    echo " | <a href='../process/delete_user.php?id=".$user['user_id']."' class='btn delete'>Delete</a>";
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No users found.";
        }
        ?>
    </div>
</body>
</html>
