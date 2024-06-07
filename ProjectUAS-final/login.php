<?php
// Start the session
session_start();

// Include your database connection file
include 'config/db_connect.php';

// Function to hash the password if it's not already hashed
function hashPasswordIfNeeded($password) {
    if (password_needs_rehash($password, PASSWORD_DEFAULT)) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    return $password;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if it's a login or registration form
    if (isset($_POST['login'])) {
        // Handle login
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare a statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists
        if ($result) {
            // Check if the password matches the hashed password
            if (password_verify($password, $result['password'])) {
                // Hash the password if not already hashed
                if (password_needs_rehash($result['password'], PASSWORD_DEFAULT)) {
                    $newHashedPassword = hashPasswordIfNeeded($password);
                    $updateStmt = $conn->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
                    $updateStmt->bindParam(':password', $newHashedPassword);
                    $updateStmt->bindParam(':user_id', $result['user_id']);
                    $updateStmt->execute();
                }

                // User found and password is correct, set session variables
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $result['user_id'];
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $result['role']; // Set the role in the session

                // Redirect user based on role
                if ($result['role'] == 'admin' || $result['role'] == 'publisher') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } elseif ($password == $result['password']) {
                // Plain-text password match, rehash and update it
                $newHashedPassword = hashPasswordIfNeeded($password);
                $updateStmt = $conn->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
                $updateStmt->bindParam(':password', $newHashedPassword);
                $updateStmt->bindParam(':user_id', $result['user_id']);
                $updateStmt->execute();

                // Set session variables as above
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $result['user_id'];
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $result['role'];

                // Redirect user based on role
                if ($result['role'] == 'admin' || $result['role'] == 'publisher') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                // User not found or incorrect password
                $error = "Invalid email or password.";
            }
        } else {
            // User not found
            $error = "Invalid email or password.";
        }
    } elseif (isset($_POST['register'])) {
        // Handle registration
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (:name, :email, :password, 'reader')");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        if ($stmt->execute()) {
            // Registration successful, redirect user to login page
            header("Location: login.php");
            exit();
        } else {
            // Registration failed
            $error = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>Login - Resonance</title>
    <meta name="title" content="Manga - Read More For Autism">
    <meta name="description" content="Read More For Autism Rizz Sibidi Tralala, You're A Simp. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
    Suspendisse vitae sapien velit. Integer eu pharetra neque. Donec consectetur malesuada lectus, vel convallis justo cursus at. Nam venenatis sit amet est et semper.">
    <!-- FAVICON -->
    <link rel="shortcut icon" href="picture/favicon.png" type="image/png">
    <!-- CSS -->
    <link rel="stylesheet" href="styles/login.css">
    <!-- FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">
    <!-- Preload -->
    <link rel="Preload" href="picture/makima_container.png" as="image">
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <h1>Create Account</h1>
                <span>Use your email for registration</span>
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="register" class="button">Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <h1>Sign In</h1>
                <span>Use your email and password</span>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login" class="button">Sign In</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Enter your personal details to use all of site features</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hello!</h1>
                    <p>Register with your personal details to use all of site features</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
    <script src="js/login.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
