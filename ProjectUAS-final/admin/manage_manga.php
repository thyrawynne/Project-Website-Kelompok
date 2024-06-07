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
$loggedInUserId = $_SESSION['user_id'];

// Define alert messages
$alertMessage = '';

function getGenresForManga($conn, $manga_id) {
    $stmt = $conn->prepare("SELECT genre_id FROM manga_genres WHERE manga_id = ?");
    $stmt->execute([$manga_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

// If the form is submitted for adding a manga
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['manga_name'])) {
    $upload_dir = '../picture/'; // Directory to save the uploaded images

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

    // Add Manga
    try {
        $publisher_id = $userRole == 'admin' ? ($_POST['publisher_id'] ?? $loggedInUserId) : $loggedInUserId;
        if ($manga_image) {
            $target_file = $upload_dir . basename($manga_image);
            move_uploaded_file($_FILES['manga_image']['tmp_name'], $target_file);
            $stmt = $conn->prepare("INSERT INTO manga (manga_name, author_name, status, publish, description, manga_image, publisher_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$manga_name, $author_name, $status, $publish, $description, 'picture/' . $manga_image, $publisher_id]);
        } else {
            $stmt = $conn->prepare("INSERT INTO manga (manga_name, author_name, status, publish, description, publisher_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$manga_name, $author_name, $status, $publish, $description, $publisher_id]);
        }

        $manga_id = $conn->lastInsertId();
        $stmt = $conn->prepare("INSERT INTO manga_genres (manga_id, genre_id) VALUES (?, ?)");
        foreach ($genre_ids as $genre_id) {
            $stmt->execute([$manga_id, $genre_id]);
        }

        // Set success alert message
        $alertMessage = "Manga Added Successfully! Wait for Admin Approval.";
    } catch (PDOException $e) {
        // Check if the error is due to duplicate entry
        if ($e->errorInfo[1] == 1062) {
            // Set error alert message for duplicate manga name
            $alertMessage = "Error: Manga with the same name already exists!";
        } else {
            // Set generic error alert message
            $alertMessage = "Error: " . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_genre'])) {
    $manga_id = $_POST['manga_id'];
    $genre_ids = $_POST['genre_ids'];

    // Get current genres for the manga
    $current_genres = getGenresForManga($conn, $manga_id);

    // Insert new genre associations
    $stmt = $conn->prepare("INSERT INTO manga_genres (manga_id, genre_id) VALUES (?, ?)");
    foreach ($genre_ids as $genre_id) {
        if (!in_array($genre_id, $current_genres)) {
            $stmt->execute([$manga_id, $genre_id]);
        }
    }

    // Set success alert message for assigning genres
    $alertMessage = "Genres assigned successfully!";
}

// Modify the query based on the user role
if ($userRole == 'admin') {
    $mangaList = $conn->query("SELECT * FROM manga")->fetchAll();
} else {
    $stmt = $conn->prepare("SELECT * FROM manga WHERE publisher_id = ?");
    $stmt->execute([$loggedInUserId]);
    $mangaList = $stmt->fetchAll();
}

$genreList = $conn->query("SELECT * FROM genres")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Manga</title>
    <link rel="stylesheet" type="text/css" href="../styles/admin.css">

    <script src="../js/admin.js"></script>
</head>
<body>
    <div class="container">
        <h1>Manage Manga</h1>
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
                <?php if ($userRole == 'admin'): ?>
                    <li><a href="manage_genres.php">Manage Genres</a></li>
                <?php endif; ?>
                <li><a href="manage_chapters.php">Manage Chapters</a></li>
                <?php if ($userRole == 'admin'): ?>
                    <li><a href="view_contacts.php">View Contact Messages</a></li>
                <?php endif; ?>
                <li><a href="../process/logout.php">Logout</a></li> <!-- Logout link -->
            </ul>
        </nav>
        <h2>Add Manga</h2>
        <form action="manage_manga.php" method="post" enctype="multipart/form-data">
            <input type="text" name="manga_name" placeholder="Manga Name" required class="input-field">
            <input type="text" name="author_name" placeholder="Author Name" required class="input-field">
            <?php if ($userRole == 'admin'): ?>
                <select name="status" class="input-field">
                    <option value="" disabled selected>Status</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                </select>
                <select name="publish" class="input-field">
                    <option value="" disabled selected>Publish Status</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                </select>
                <input type="text" name="publisher_id" placeholder="Publisher ID" required class="input-field">
            <?php else: ?>
                <input type="hidden" name="publisher_id" value="<?= $loggedInUserId ?>" class="input-field">
            <?php endif; ?>
            <textarea name="description" placeholder="Description" class="input-field"></textarea>
            <input type="file" name="manga_image" class="input-field">
            <label for="genre_ids">Select Genres:</label>
            <select name="genre_ids[]" id="genre_ids" multiple required class="input-field">
                <?php foreach ($genreList as $genre): ?>
                    <option value="<?= htmlspecialchars($genre['genre_id']) ?>"><?= htmlspecialchars($genre['genre_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">Add Manga</button>
        </form>
        <?php if ($userRole == 'admin'): ?>
            <hr>
            <h2>Assign Genre</h2>
            <form action="manage_manga.php" method="post">
                <label for="manga_id">Select Manga:</label>
                <select name="manga_id" id="manga_id" required class="input-field">
                    <?php foreach ($mangaList as $manga): ?>
                        <option value="<?= htmlspecialchars($manga['manga_id']) ?>"><?= htmlspecialchars($manga['manga_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="genre_ids">Select Genres:</label>
                <select name="genre_ids[]" id="genre_ids" multiple required class="input-field">
                    <?php foreach ($genreList as $genre): ?>
                        <option value="<?= htmlspecialchars($genre['genre_id']) ?>"><?= htmlspecialchars($genre['genre_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="assign_genre" class="btn">Assign Genres</button>
            </form>
        <?php endif; ?>
        <hr>
        <h2>Edit/Delete Manga</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Manga Cover</th>
                    <th>Genres</th>
                    <th>Publish</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mangaList as $manga): ?>
                    <tr>
                        <td><?= htmlspecialchars($manga['manga_id']) ?></td>
                        <td><?= htmlspecialchars($manga['manga_name']) ?></td>
                        <td><?= htmlspecialchars($manga['author_name']) ?></td>
                        <td><?= htmlspecialchars($manga['status']) ?></td>
                        <td><img src="../<?= htmlspecialchars($manga['manga_image']) ?>" alt="<?= htmlspecialchars($manga['manga_name']) ?>" style="max-width: 100px;"></td>
                        <td>
                            <?php
                            // Get genres for the manga
                            $genresForManga = getGenresForManga($conn, $manga['manga_id']);
                            $genreNames = [];
                            foreach ($genresForManga as $genreId) {
                                foreach ($genreList as $genre) {
                                    if ($genre['genre_id'] == $genreId) {
                                        $genreNames[] = $genre['genre_name'];
                                    }
                                }
                            }
                            echo implode(', ', $genreNames);
                            ?>
                        </td>
                        <td><?= htmlspecialchars($manga['publish']) ?></td>
                        <td>
                            <?php if ($userRole == 'admin' || $manga['publisher_id'] == $loggedInUserId): ?>
                                <a href="../process/edit_manga.php?manga_id=<?= htmlspecialchars($manga['manga_id']) ?>" class="btn">Edit</a>
                                <a href="../process/delete_manga.php?manga_id=<?= htmlspecialchars($manga['manga_id']) ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this manga?')">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
