<?php
include 'config/db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Check if manga ID is provided in the URL
if (!isset($_GET['id'])) {
    echo "No manga ID provided.";
    exit();
}

$manga_id = $_GET['id'];

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

// Fetch chapters for this manga
$stmt = $conn->prepare("SELECT * FROM chapters WHERE manga_id = :manga_id ORDER BY chapter_number ASC");
$stmt->bindParam(':manga_id', $manga_id, PDO::PARAM_INT);
$stmt->execute();
$chapters = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Description - Resonance</title>
    <meta name="title" content="Manga - Read More For Autism">
    <meta name="description" content="Read More For Autism Rizz Sibidi Tralala, You're A Simp. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
    Suspendisse vitae sapien velit. Integer eu pharetra neque. Donec consectetur malesuada lectus, vel convallis justo cursus at. Nam venenatis sit amet est et semper.">

    <!-- FAVICON -->
    <link rel="shortcut icon" href="picture/favicon.png" type="image/png">

    <!-- CSS -->
    <link rel="stylesheet" href="styles/desc.css">

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

            <section class="section main">
                <div class="container">
                    <div class="manga-pic">
                        <img src="<?= htmlspecialchars($manga['manga_image']) ?>" alt="Manga Cover" class="w-100">
                    </div>
                    <div class="manga-stats">
                        <div class="stats">
                            <h1 class="h1 manga-title"><?= htmlspecialchars($manga['manga_name']) ?></h1>
                            <p class="section-subtitle"><?= htmlspecialchars($manga['author_name']) ?></p>
                            <p class="section-text">Status: <?= strtoupper(htmlspecialchars($manga['status'])) ?></p>
                            <p class="section-text">Genres: <?= htmlspecialchars($manga['genre_names']) ?></p>
                        </div>
                        <div class="description">
                            <p class="section-text description-text">
                                <?= htmlspecialchars($manga['description']) ?>
                            </p>
                            <button class="read-more-btn">Read More...</button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section chapter">
                <div class="container">
                    <a href="read-manga.php?manga_id=<?= $manga_id ?>&chapter_id=<?= $chapters[0]['chapter_id'] ?>">
                        <div class="start">
                            <p class="section-text">Start Reading: Ch1</p>
                        </div>
                    </a>
                    <div class="chapter-title">
                        <p class="section-title">Chapter's</p>
                    </div>
                    <ul class="chapter-list">
                        <?php
                        // Fetch chapters for this manga
                        $stmt = $conn->prepare("SELECT * FROM chapters WHERE manga_id = :manga_id ORDER BY chapter_number ASC");
                        $stmt->bindParam(':manga_id', $manga_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $chapters = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($chapters as $chapter) {
                            echo '<li>';
                            echo '<a href="read-manga.php?manga_id=' . $manga_id . '&chapter_id=' . $chapter['chapter_id'] . '">';
                            echo '<p class="section-text">Ch.' . htmlspecialchars($chapter['chapter_number']) . ' - ' . htmlspecialchars($chapter['title']) . '</p>';
                            echo '</a>';
                            echo '<ion-icon name="book-outline"></ion-icon>';
                            echo '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </section>
        </article>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-top">
                <a href="index.php#home" class="logo">Resonance</a>
                <ul class="footer-list">
                    <li><a href="index.php#home" class="footer-link">Home</a></li>
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

    <script src="js/manga.js"></script>
    <script src="js/index.js"></script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
