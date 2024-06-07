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

// Define alert messages
$alertMessage = '';

$upload_dir = '../picture/'; // Directory to save the uploaded images

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['genre_id']) && isset($_POST['delete'])) {
        $genre_id = $_POST['genre_id'];
        $stmt = $conn->prepare("DELETE FROM genres WHERE genre_id = :genre_id");
        $stmt->bindParam(':genre_id', $genre_id);
        $stmt->execute();
        $alertMessage = "Genre Deleted Successfully!";
    } elseif (isset($_POST['genre_id']) && isset($_POST['edit'])) {
        $genre_id = $_POST['genre_id'];
        $genre_name = $_POST['genre_name'];
        $genre_desc = $_POST['genre_desc'];
        $genre_icon = $_FILES['genre_icon']['name'];

        if ($genre_icon) {
            move_uploaded_file($_FILES['genre_icon']['tmp_name'], $upload_dir . $genre_icon);
        }

        $stmt = $conn->prepare("UPDATE genres SET genre_name = :genre_name, genre_desc = :genre_desc, genre_icon = :genre_icon WHERE genre_id = :genre_id");
        $stmt->bindParam(':genre_name', $genre_name);
        $stmt->bindParam(':genre_desc', $genre_desc);
        $stmt->bindParam(':genre_icon', $genre_icon);
        $stmt->bindParam(':genre_id', $genre_id);
        $stmt->execute();
        $alertMessage = "Genre Edited Successfully!";
    } elseif (isset($_POST['genre_name'])) {
        $genre_name = $_POST['genre_name'];
        $genre_desc = $_POST['genre_desc'];
        $genre_icon = $_FILES['genre_icon']['name'];

        try {
            $stmt = $conn->prepare("INSERT INTO genres (genre_name, genre_desc, genre_icon) VALUES (:genre_name, :genre_desc, :genre_icon)");
            $stmt->bindParam(':genre_name', $genre_name);
            $stmt->bindParam(':genre_desc', $genre_desc);
            $stmt->bindParam(':genre_icon', $genre_icon);
            $stmt->execute();

            move_uploaded_file($_FILES['genre_icon']['tmp_name'], $upload_dir . $genre_icon);

            // Set success alert message
            $alertMessage = "Genre Added Successfully!";
        } catch (PDOException $e) {
            // Check if the error is due to duplicate entry
            if ($e->errorInfo[1] == 1062) {
                // Set error alert message for duplicate genre name
                $alertMessage = "Error: Genre with the same name already exists!";
            } else {
                // Set generic error alert message
                $alertMessage = "Error: " . $e->getMessage();
            }
        }
    }
}

$genres = $conn->query("SELECT * FROM genres")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Genres</title>
    <link rel="stylesheet" type="text/css" href="../styles/admin.css">

    <script src="../js/admin.js"></script>
</head>
<body>
    <div class="container">
        <h1>Manage Genres</h1>
        <!-- Display alert if there's any -->
        <?php if (!empty($alertMessage)): ?>
            <script>
                // Display pop-up alert using JavaScript
                showAlert("<?php echo $alertMessage; ?>");
            </script>
        <?php endif; ?>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_manga.php">Manage Manga</a></li>
                <li><a href="manage_chapters.php">Manage Chapters</a></li>
                <?php if ($userRole == 'admin'): ?>
                    <li><a href="view_contacts.php">View Contact Messages</a></li>
                <?php endif; ?>
                <li><a href="../process/logout.php">Logout</a></li> <!-- Logout link -->
            </ul>
        </nav>
        <h2>Add Genre</h2>
        <form action="manage_genres.php" method="post" enctype="multipart/form-data">
            <input type="text" name="genre_name" placeholder="Genre Name" required class="input-field">
            <textarea name="genre_desc" placeholder="Genre Description" class="input-field"></textarea>
            <label for="genre_icon">Genre Icon:</label>
            <input type="file" name="genre_icon" class="input-field">
            <button type="submit" class="btn">Add Genre</button>
        </form>

        <h2>Edit/Delete Genres</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Icon</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($genres as $genre): ?>
                <tr>
                    <td><?= $genre['genre_id'] ?></td>
                    <td><?= $genre['genre_name'] ?></td>
                    <td><?= $genre['genre_desc'] ?></td>
                    <td><img src="<?= $upload_dir . $genre['genre_icon'] ?>" alt="Genre Icon" width="50"></td>
                    <td>
                        <form action="manage_genres.php" method="post" enctype="multipart/form-data" style="display:inline;">
                            <input type="hidden" name="genre_id" value="<?= $genre['genre_id'] ?>">
                            <input type="text" name="genre_name" value="<?= $genre['genre_name'] ?>" required class="input-field">
                            <textarea name="genre_desc" class="input-field"><?= $genre['genre_desc'] ?></textarea>
                            <input type="file" name="genre_icon" class="input-field">
                            <button type="submit" name="edit" class="btn">Edit</button>
                        </form>
                        <form action="manage_genres.php" method="post" style="display:inline;">
                            <input type="hidden" name="genre_id" value="<?= $genre['genre_id'] ?>">
                            <button type="submit" name="delete" class="btn delete">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
