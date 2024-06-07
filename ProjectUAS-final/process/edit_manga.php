<?php
include '../config/db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: ../login.php");
    exit();
}

// Retrieve the user's role from the session
$userRole = $_SESSION['role'];

if (!isset($_GET['manga_id'])) {
    header("Location: ../admin/manage_manga.php");
    exit();
}

$manga_id = $_GET['manga_id'];

// Define the getGenresForManga function
function getGenresForManga($conn, $manga_id) {
    $stmt = $conn->prepare("SELECT genre_id FROM manga_genres WHERE manga_id = ?");
    $stmt->execute([$manga_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

// Fetch manga details
$stmt = $conn->prepare("SELECT * FROM manga WHERE manga_id = ?");
$stmt->execute([$manga_id]);
$manga = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$manga) {
    header("Location: manage_manga.php");
    exit();
}

$genreList = $conn->query("SELECT * FROM genres")->fetchAll();

// If the form is submitted for updating the manga
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $upload_dir = '../picture/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $manga_name = $_POST['manga_name'];
    $author_name = $_POST['author_name'];
    $status = $_POST['status'] ?? 'ongoing';
    $publish = $_POST['publish'] ?? 'pending';
    $description = $_POST['description'];
    $genre_ids = $_POST['genre_ids'];
    $manga_image = isset($_FILES['manga_image']['name']) ? $_FILES['manga_image']['name'] : '';

    try {
        if ($manga_image) {
            $target_file = $upload_dir . basename($manga_image);
            move_uploaded_file($_FILES['manga_image']['tmp_name'], $target_file);
            $stmt = $conn->prepare("UPDATE manga SET manga_name = ?, author_name = ?, status = ?, publish = ?, description = ?, manga_image = ? WHERE manga_id = ?");
            $stmt->execute([$manga_name, $author_name, $status, $publish, $description, 'picture/' . $manga_image, $manga_id]);
        } else {
            $stmt = $conn->prepare("UPDATE manga SET manga_name = ?, author_name = ?, status = ?, publish = ?, description = ? WHERE manga_id = ?");
            $stmt->execute([$manga_name, $author_name, $status, $publish, $description, $manga_id]);
        }

        // Update genres
        $stmt = $conn->prepare("DELETE FROM manga_genres WHERE manga_id = ?");
        $stmt->execute([$manga_id]);

        $stmt = $conn->prepare("INSERT INTO manga_genres (manga_id, genre_id) VALUES (?, ?)");
        foreach ($genre_ids as $genre_id) {
            $stmt->execute([$manga_id, $genre_id]);
        }

        header("Location: ../admin/manage_manga.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Manga</title>
    <link rel="stylesheet" type="text/css" href="../styles/admin.css">
</head>
<body>
    <div class="container">
        <h1>Edit Manga</h1>
        <form action="edit_manga.php?manga_id=<?= $manga_id ?>" method="post" enctype="multipart/form-data">
            <input type="text" name="manga_name" value="<?= htmlspecialchars($manga['manga_name']) ?>" required class="input-field">
            <input type="text" name="author_name" value="<?= htmlspecialchars($manga['author_name']) ?>" required class="input-field">
            <?php if ($userRole == 'admin'): ?>
                <select name="status" class="input-field">
                    <option value="ongoing" <?= $manga['status'] == 'ongoing' ? 'selected' : '' ?>>Ongoing</option>
                    <option value="completed" <?= $manga['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                </select>
                <select name="publish" class="input-field">
                    <option value="approved" <?= $manga['publish'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="pending" <?= $manga['publish'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="rejected" <?= $manga['publish'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            <?php endif; ?>
            <textarea name="description" class="input-field"><?= htmlspecialchars($manga['description']) ?></textarea>
            <input type="file" name="manga_image" class="input-field">
            <label for="genre_ids">Select Genres:</label>
            <select name="genre_ids[]" id="genre_ids" multiple required class="input-field">
                <?php foreach ($genreList as $genre): ?>
                    <option value="<?= htmlspecialchars($genre['genre_id']) ?>" <?= in_array($genre['genre_id'], getGenresForManga($conn, $manga_id)) ? 'selected' : '' ?>><?= htmlspecialchars($genre['genre_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">Update Manga</button>
            <a href="../admin/manage_manga.php" class="btn">Back to Manage Manga</a>
        </form>
    </div>
</body>
</html>
