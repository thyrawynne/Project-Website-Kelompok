<?php
include 'config/db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Retrieve the user's role from the session
$userRole = $_SESSION['role'];

// Function to fetch genres from the database
function fetchGenres($conn) {
    $genres = array();
    // Example query:
    $sql = "SELECT * FROM genres";
    $stmt = $conn->query($sql);
    $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $genres;
}

// Function to fetch manga data with associated genres from the database
function fetchMangaData($conn) {
    $mangaData = array();
    // Example query with JOIN to get manga data along with associated genres
    $sql = "SELECT manga.*, GROUP_CONCAT(genres.genre_name SEPARATOR ', ') AS genre_names
            FROM manga
            LEFT JOIN manga_genres ON manga.manga_id = manga_genres.manga_id
            LEFT JOIN genres ON manga_genres.genre_id = genres.genre_id
            WHERE manga.publish = 'approved' -- Add this condition to filter only approved manga
            GROUP BY manga.manga_id";
    $stmt = $conn->query($sql);
    $mangaData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $mangaData;
}


// Fetch genres and manga data
$genres = fetchGenres($conn);
$mangaData = fetchMangaData($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manga List - Resonance</title>
    <meta name="title" content="Manga - Read More For Autism">
    <meta name="description" content="Read More For Autism Rizz Sibidi Tralala, You're A Simp. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
    Suspendisse vitae sapien velit. Integer eu pharetra neque. Donec consectetur malesuada lectus, vel convallis justo cursus at. Nam venenatis sit amet est et semper.">

     <!-- FAVICON -->

     <link rel="shortcut icon" href="picture/favicon.png" type="image/png">

     <!-- CSS -->
 
     <link rel="stylesheet" href="styles/manga.css">
 
     <!-- FONTS -->
 
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
 
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

            <section class="section" id="manga-list">
                <div class="container">
                    <h2 class="h2 section-title has-underline">
                        Manga List
                        <span class="span has-before"></span>
                    </h2>

                    <div id="manga-container" class="manga-container">
                        <ul class="manga-genre">
                            <?php
                            // Loop through each genre to generate genre cards
                            foreach ($genres as $genre) {
                                echo '<li class="manga-genre-list" id="' . $genre['genre_name'] . '" aria-label="' . $genre['genre_name'] . '">';
                                echo '<div class="card-icon">';
                                echo '<img src="picture/' . $genre['genre_icon'] . '" width="40" height="40" loading="lazy" alt="' . $genre['genre_name'] . '">';
                                echo '<h2 class="h2 section-text">' . $genre['genre_name'] . '</h2>';
                                echo '</div>';

                                echo '<div class="grid-list">';
                                // Generate manga cards for this genre
                                foreach ($mangaData as $manga) {
                                    // Check if this manga belongs to the current genre
                                    $genreNames = explode(', ', $manga['genre_names']);
                                    if (in_array($genre['genre_name'], $genreNames)) {
                                        // Generate manga card
                                        echo '<a href="desc-manga.php?id=' . $manga['manga_id'] . '" class="manga-link">';
                                        echo '<div class="manga-card has-before has-after">';
                                        echo '<img src="' . $manga['manga_image'] . '" class="manga-cover">';
                                        echo '<h3 class="h3 card-title">' . $manga['manga_name'] . '</h3>';
                                        echo '<p class="card-text">' . $manga['author_name'] . '</p>';
                                        echo '<div class="manga-tooltip">';
                                        echo '<img src="' . $manga['manga_image'] . '" alt="Manga ToolTip" class="manga-tooltip-picture">';
                                        echo '<div class="tooltip-info">';
                                        echo '<p class="manga-tooltip-name">' . $manga['manga_name'] . '</p>';
                                        echo '<p class="manga-tooltip-author">' . $manga['author_name'] . '</p>';
                                        echo '<p class="manga-tooltip-genres">' . $manga['genre_names'] . '</p>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</a>';
                                    }
                                }
                                echo '</div>';
                                echo '</li>';
                            }
                            ?>
                        </ul>
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

    <script src="js/index.js"></script>
    <script src="js/manga.js"></script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
