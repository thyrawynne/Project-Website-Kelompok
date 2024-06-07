<?php
include 'config/db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Check if manga ID and chapter ID are provided in the URL
if (!isset($_GET['manga_id']) || !isset($_GET['chapter_id'])) {
    echo "No manga or chapter ID provided.";
    exit();
}

$manga_id = $_GET['manga_id'];
$chapter_id = $_GET['chapter_id'];

// Fetch manga details from the database
$stmt = $conn->prepare("SELECT m.*, GROUP_CONCAT(g.genre_name SEPARATOR ', ') as genre_names
                        FROM manga m
                        JOIN manga_genres mg ON m.manga_id = mg.manga_id
                        JOIN genres g ON mg.genre_id = g.genre_id
                        WHERE m.manga_id = :manga_id
                        GROUP BY m.manga_id");
$stmt->bindParam(':manga_id', $manga_id, PDO::PARAM_INT);
$stmt->execute();
$manga = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$manga) {
    echo "Manga not found.";
    exit();
}

// Fetch chapter details from the database
$stmt = $conn->prepare("SELECT * FROM chapters WHERE manga_id = :manga_id AND chapter_id = :chapter_id");
$stmt->bindParam(':manga_id', $manga_id, PDO::PARAM_INT);
$stmt->bindParam(':chapter_id', $chapter_id, PDO::PARAM_INT);
$stmt->execute();
$chapter = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$chapter) {
    echo "Chapter not found.";
    exit();
}

// Fetch all chapters for the dropdown menu
$stmt = $conn->prepare("SELECT chapter_id, chapter_number, title FROM chapters WHERE manga_id = :manga_id ORDER BY chapter_number ASC");
$stmt->bindParam(':manga_id', $manga_id, PDO::PARAM_INT);
$stmt->execute();
$chapters = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Find current chapter index
$currentChapterIndex = array_search($chapter_id, array_column($chapters, 'chapter_id'));

// Retrieve the user's role from the session
$userRole = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read - Resonance</title>
    <meta name="title" content="Manga - Read More For Autism">
    <meta name="description" content="Read More For Autism Rizz Sibidi Tralala, You're A Simp. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
    Suspendisse vitae sapien velit. Integer eu pharetra neque. Donec consectetur malesuada lectus, vel convallis justo cursus at. Nam venenatis sit amet est et semper.">

    <!-- FAVICON -->
    <link rel="shortcut icon" href="picture/favicon.png" type="image/png">

    <!-- CSS -->
    <link rel="stylesheet" href="styles/read.css">

    <!-- FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">

    <!-- Preload -->
    <link rel="Preload" href="picture/makima_container.png" as="image">
</head>
<body>
    <!-- HEADER -->
    <header class="header" data-header>
        <div class="container">
            <div class="resonance">
                <img src="picture/favicon.png" alt="logo-image" class="logo-image">
                <a href="index.php" class="logo">Resonance</a>
            </div>
            <nav class="navbar" data-navbar>
                <ul class="navbar-list">
                    <li class="navbar-item"><a href="index.php#home" class="navbar-link" data-nav-link>Home</a></li>
                    <li class="navbar-item"><a href="manga.php" class="navbar-link" data-nav-link>Mangas's List</a></li>
                    <li class="navbar-item"><a href="index.php#author" class="navbar-link" data-nav-link>Author</a></li>
                    <li class="navbar-item"><a href="index.php#contact" class="navbar-link" data-nav-link>Contact Us</a></li>
                    <li class="navbar-item"><a href="process/logout.php" class="navbar-link" data-nav-link>Log Out</a></li>
                </ul>
            </nav>
            <button class="nav-toggle-btn" aria-label="toggle menu" data-nav-toggler>
                <ion-icon name="menu-outline" aria-hidden="true" class="open"></ion-icon>
                <ion-icon name="close-outline" aria-hidden="true" class="close"></ion-icon>
            </button>
        </div>
    </header>

    <main>
        <article>

            <button id="scrollToTopBtn">
                <ion-icon name="chevron-up-outline" aria-hidden="true"></ion-icon>
            </button>

            <section class="section read">
                <div class="container">
                    <h2 class="section-title"><?= htmlspecialchars($manga['manga_name']) ?></h2>
                    <h4 class="section-subtitle"><?= htmlspecialchars($manga['author_name']) ?></h4>
                    <div class="read-navbar">
                        <div class="prev-bar">
                            <?php if ($currentChapterIndex > 0): ?>
                                <a href="read-manga.php?manga_id=<?= $manga_id ?>&chapter_id=<?= $chapters[$currentChapterIndex - 1]['chapter_id'] ?>"><ion-icon name="chevron-back-outline"></ion-icon></a>
                            <?php else: ?>
                                <ion-icon name="chevron-back-outline" style="color: gray;"></ion-icon>
                            <?php endif; ?>
                        </div>
                        <div class="chapter" data-chapter-toggler>
                            <p class="section-text">Ch.<?= htmlspecialchars($chapter['chapter_number']) ?> - <?= htmlspecialchars($chapter['title']) ?></p>
                            <div class="icon">
                                <ion-icon name="chevron-down-outline"></ion-icon>
                                <ion-icon name="chevron-up-outline"></ion-icon>
                            </div>
                            <div class="dropdown-menu">
                                <?php foreach ($chapters as $chap): ?>
                                    <a href="read-manga.php?manga_id=<?= $manga_id ?>&chapter_id=<?= $chap['chapter_id'] ?>">Ch.<?= htmlspecialchars($chap['chapter_number']) ?> - <?= htmlspecialchars($chap['title']) ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="next-bar">
                            <?php if ($currentChapterIndex < count($chapters) - 1): ?>
                                <a href="read-manga.php?manga_id=<?= $manga_id ?>&chapter_id=<?= $chapters[$currentChapterIndex + 1]['chapter_id'] ?>"><ion-icon name="chevron-forward-outline"></ion-icon></a>
                            <?php else: ?>
                                <ion-icon name="chevron-forward-outline" style="color: gray;"></ion-icon>
                            <?php endif; ?>
                        </div>
                    </div>                    

                    <div class="read-page">
                        <iframe src="Mangas/<?php echo htmlspecialchars($manga['manga_name']); ?>/<?php echo htmlspecialchars($chapter['content']); ?>" frameborder="1" class="manga-frame" loading="lazy" alt="Chapter <?= htmlspecialchars($chapter['chapter_number']) ?>"></iframe>
                    </div>

                    <div class="read-navbar">
                        <div class="prev-bar">
                            <?php if ($currentChapterIndex > 0): ?>
                                <a href="read-manga.php?manga_id=<?= $manga_id ?>&chapter_id=<?= $chapters[$currentChapterIndex - 1]['chapter_id'] ?>"><ion-icon name="chevron-back-outline"></ion-icon></a>
                            <?php else: ?>
                                <ion-icon name="chevron-back-outline" style="color: gray;"></ion-icon>
                            <?php endif; ?>
                        </div>
                        <div class="chapter return">
                            <a href="desc-manga.php?id=<?= $manga_id ?>"><p class="section-text">Return To Description</p></a>
                        </div>
                        <div class="next-bar">
                            <?php if ($currentChapterIndex < count($chapters) - 1): ?>
                                <a href="read-manga.php?manga_id=<?= $manga_id ?>&chapter_id=<?= $chapters[$currentChapterIndex + 1]['chapter_id'] ?>"><ion-icon name="chevron-forward-outline"></ion-icon></a>
                            <?php else: ?>
                                <ion-icon name="chevron-forward-outline" style="color: gray;"></ion-icon>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </article>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-top">
                <a href="index.php#home" class="logo">Resonance</a>
                <ul class="footer-list">
                    <li><a href="index.phpl#home" class="footer-link">Home</a></li>
                    <li><a href="manga.php" class="footer-link">Manga's List</a></li>
                    <li><a href="index.php#author" class="footer-link">Author</a></li>
                    <li><a href="index.php#contact" class="footer-link">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 All right reserved. Made with sprinkle of autism by Fachsyan Rajendra Ismail.</p>
            </div>
        </div>
    </footer>

    <script src="js/read.js"></script>
    <script src="js/index.js"></script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
