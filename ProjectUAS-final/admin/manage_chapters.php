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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get manga name using manga_id
    $manga_id = $_POST['manga_id'];
    $stmt = $conn->prepare("SELECT manga_name FROM manga WHERE manga_id = :manga_id");
    $stmt->bindParam(':manga_id', $manga_id);
    $stmt->execute();
    $manga = $stmt->fetch(PDO::FETCH_ASSOC);
    $manga_name = $manga['manga_name'];

    // Directory to save the uploaded chapter content and cover
    $upload_dir_content = '../Mangas/' . $manga_name . '/';
    $upload_dir_cover = '../Mangas/' . $manga_name . '/cover/';

    if (!is_dir($upload_dir_content)) {
        mkdir($upload_dir_content, 0755, true);
    }

    if (!is_dir($upload_dir_cover)) {
        mkdir($upload_dir_cover, 0755, true);
    }

    if (isset($_POST['chapter_id']) && isset($_POST['delete'])) {
        $chapter_id = $_POST['chapter_id'];
        $stmt = $conn->prepare("DELETE FROM chapters WHERE chapter_id = :chapter_id");
        $stmt->bindParam(':chapter_id', $chapter_id);
        $stmt->execute();

        // Set delete success alert message
        $alertMessage = "Chapter Deleted Successfully!";
    } elseif (isset($_POST['chapter_id']) && isset($_POST['edit'])) {
        $chapter_id = $_POST['chapter_id'];
        $chapter_number = $_POST['chapter_number'];
        $title = $_POST['title'];

        $content = $_FILES['content']['name'] ?? null;
        $chapter_cover = $_FILES['chapter_cover']['name'] ?? null;

        if ($content) {
            $content_target_file = $upload_dir_content . basename($content);
            move_uploaded_file($_FILES['content']['tmp_name'], $content_target_file);
        }

        if ($chapter_cover) {
            $cover_target_file = $upload_dir_cover . basename($chapter_cover);
            move_uploaded_file($_FILES['chapter_cover']['tmp_name'], $cover_target_file);
        }

        $stmt = $conn->prepare("UPDATE chapters SET chapter_number = :chapter_number, title = :title" . 
                               ($content ? ", content = :content" : "") . 
                               ($chapter_cover ? ", chapter_cover = :chapter_cover" : "") . 
                               " WHERE chapter_id = :chapter_id");
        $stmt->bindParam(':chapter_number', $chapter_number);
        $stmt->bindParam(':title', $title);
        if ($content) $stmt->bindParam(':content', $content);
        if ($chapter_cover) $stmt->bindParam(':chapter_cover', $chapter_cover);
        $stmt->bindParam(':chapter_id', $chapter_id);
        $stmt->execute();

        // Set edit success alert message
        $alertMessage = "Chapter Edited Successfully!";
    } elseif (isset($_POST['manga_id'])) {
        $chapter_number = $_POST['chapter_number'];
        $title = $_POST['title'];

        // Check if the chapter number already exists
        $stmt = $conn->prepare("SELECT * FROM chapters WHERE manga_id = :manga_id AND chapter_number = :chapter_number");
        $stmt->bindParam(':manga_id', $manga_id);
        $stmt->bindParam(':chapter_number', $chapter_number);
        $stmt->execute();
        $existingChapter = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingChapter) {
            // Set duplicate chapter number alert message
            $alertMessage = "Error: Chapter with the same number already exists!";
        } else {
            $content = $_FILES['content']['name'];
            $chapter_cover = $_FILES['chapter_cover']['name'];

            $content_target_file = $upload_dir_content . basename($content);
            $cover_target_file = $upload_dir_cover . basename($chapter_cover);

            if (move_uploaded_file($_FILES['content']['tmp_name'], $content_target_file) && move_uploaded_file($_FILES['chapter_cover']['tmp_name'], $cover_target_file)) {
                $stmt = $conn->prepare("INSERT INTO chapters (manga_id, chapter_number, title, content, chapter_cover) VALUES (:manga_id, :chapter_number, :title, :content, :chapter_cover)");
                $stmt->bindParam(':manga_id', $manga_id);
                $stmt->bindParam(':chapter_number', $chapter_number);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':content', $content);
                $stmt->bindParam(':chapter_cover', $chapter_cover);
                $stmt->execute();

                // Set success alert message
                $alertMessage = "Chapter Added Successfully!";
            } else {
                $stmt = $conn->prepare("INSERT INTO chapters (manga_id, chapter_number, title) VALUES (:manga_id, :chapter_number, :title)");
                $stmt->bindParam(':manga_id', $manga_id);
                $stmt->bindParam(':chapter_number', $chapter_number);
                $stmt->bindParam(':title', $title);
                $stmt->execute();

                // Set success alert message
                $alertMessage = "Chapter Added Successfully!";
            }
        }
    }
}

// Fetch the manga list based on user role
if ($userRole == 'admin') {
    $mangaList = $conn->query("SELECT * FROM manga WHERE publish = 'approved'")->fetchAll();
} else {
    $stmt = $conn->prepare("SELECT * FROM manga WHERE publisher_id = :publisher_id AND publish = 'approved'");
    $stmt->bindParam(':publisher_id', $loggedInUserId);
    $stmt->execute();
    $mangaList = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch the chapters based on user role
if ($userRole == 'admin') {
    $chapters = $conn->query("SELECT * FROM chapters")->fetchAll();
} else {
    $stmt = $conn->prepare("SELECT chapters.* FROM chapters JOIN manga ON chapters.manga_id = manga.manga_id WHERE manga.publisher_id = :publisher_id AND manga.publish = 'approved'");
    $stmt->bindParam(':publisher_id', $loggedInUserId);
    $stmt->execute();
    $chapters = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Chapters</title>
    <link rel="stylesheet" type="text/css" href="../styles/admin.css">

    <script src="../js/admin.js"></script>
</head>
<body>
    <div class="container">
        <h1>Manage Chapters</h1>
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
                <li><a href="manage_manga.php">Manage Manga</a></li>
                <?php if ($userRole == 'admin'): ?>
                    <li><a href="view_contacts.php">View Contact Messages</a></li>
                <?php endif; ?>
                <li><a href="../process/logout.php">Logout</a></li> <!-- Logout link -->
            </ul>
        </nav>
        <h2>Add Chapter</h2>
        <form action="manage_chapters.php" method="post" enctype="multipart/form-data">
            <label for="manga_id">Manga:</label>
            <select name="manga_id" id="manga_id" required class="input-field">
                <?php foreach ($mangaList as $manga): ?>
                    <option value="<?= htmlspecialchars($manga['manga_id']) ?>"><?= htmlspecialchars($manga['manga_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <label for="chapter_number">Chapter Number:</label>
            <input type="text" name="chapter_number" placeholder="Chapter Number" required class="input-field">
            <label for="title">Chapter Title:</label>
            <input type="text" name="title" placeholder="Chapter Title" required class="input-field">
            <label for="content">Upload PDF:</label>
            <input type="file" name="content" placeholder="Chapter Content (PDF)" required class="input-field">
            <label for="chapter_cover">Upload Chapter Image:</label>
            <input type="file" name="chapter_cover" placeholder="Chapter Cover Image" required class="input-field">
            <button type="submit" class="btn">Add Chapter</button>
        </form>
        
        <h2>Edit/Delete Chapters</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Manga ID</th>
                    <th>Chapter Number</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Chapter Cover</th>
                    <?php if ($userRole == 'admin'): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($chapters as $chapter): ?>
                <tr>
                    <td><?= htmlspecialchars($chapter['chapter_id']) ?></td>
                    <td><?= htmlspecialchars($chapter['manga_id']) ?></td>
                    <td><?= htmlspecialchars($chapter['chapter_number']) ?></td>
                    <td><?= htmlspecialchars($chapter['title']) ?></td>
                    <td>
                        <?php
                        $manga_id = $chapter['manga_id'];
                        $stmt = $conn->prepare("SELECT manga_name FROM manga WHERE manga_id = :manga_id");
                        $stmt->bindParam(':manga_id', $manga_id);
                        $stmt->execute();
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        $manga_name = $result['manga_name'];
                        $content_path = "../Mangas/$manga_name/" . htmlspecialchars($chapter['content']);
                        ?>
                        <a href="<?= $content_path ?>" target="_blank"><?= htmlspecialchars($chapter['content']) ?></a>
                    </td>
                    <td>
                        <?php
                        $cover_path = "../Mangas/$manga_name/cover/" . htmlspecialchars($chapter['chapter_cover']);
                        ?>
                        <img src="<?= $cover_path ?>" alt="<?= htmlspecialchars($chapter['title']) ?>" width="50">
                    </td>
                    <?php if ($userRole == 'admin'): ?>
                        <td>
                        <form action="manage_chapters.php" method="post" enctype="multipart/form-data" style="display:inline;">
                            <input type="hidden" name="chapter_id" value="<?= htmlspecialchars($chapter['chapter_id']) ?>">
                            <input type="text" name="manga_id" value="<?= htmlspecialchars($chapter['manga_id']) ?>" required class="input-field">
                            <input type="text" name="chapter_number" value="<?= htmlspecialchars($chapter['chapter_number']) ?>" required class="input-field">
                            <input type="text" name="title" value="<?= htmlspecialchars($chapter['title']) ?>" required class="input-field">
                            <input type="file" name="content" class="input-field">
                            <input type="file" name="chapter_cover" class="input-field">
                            <button type="submit" name="edit" class="btn">Edit</button>
                        </form>
                        <form action="manage_chapters.php" method="post" style="display:inline;">
                            <input type="hidden" name="chapter_id" value="<?= htmlspecialchars($chapter['chapter_id']) ?>">
                            <button type="submit" name="delete" class="btn delete">Delete</button>
                        </form>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
