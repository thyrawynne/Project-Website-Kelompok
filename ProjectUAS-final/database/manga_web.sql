-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2024 at 03:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `manga_web`
--

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `chapter_id` int(11) NOT NULL,
  `manga_id` int(11) DEFAULT NULL,
  `chapter_number` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `chapter_cover` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`chapter_id`, `manga_id`, `chapter_number`, `title`, `content`, `chapter_cover`) VALUES
(1, 1, 1, 'Ch.1', 'ChainsawMan_Chapter1.pdf', 'chainsawman_tooltip.webp'),
(2, 1, 2, 'Ch.2', 'ChainsawMan_Chapter2.pdf', 'chainsawman_tooltip.webp'),
(3, 1, 3, 'Ch.3', 'ChainsawMan_Chapter3.pdf', 'chainsawman_tooltip.webp'),
(4, 2, 1, 'Ch.1', 'Gintama_Chapter1.pdf', 'Gintama_Tooltip.webp'),
(5, 3, 1, 'Ch.1', 'MadeInAbyss_Chapter1.pdf', 'Made-in-Abyss-Cover-V3.webp'),
(6, 3, 2, 'Ch.2', 'MadeInAbyss_Chapter2.pdf', 'Made-in-Abyss-Cover-V3.webp'),
(7, 4, 1, 'Ch.1', 'TheDangerInMyHeart_Chapter1.pdf', 'The_Dangers_in_My_Heart_cover.jpg'),
(8, 5, 1, 'Ch.1', 'BlueLock_Chapter1.pdf', 'bluelock_cover.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`message_id`, `name`, `email_address`, `subject`, `message`, `created_at`) VALUES
(1, 'Mosaku', 'mosaku@mosaku.com', 'Chainsaw Man', 'This Manga Is Relatable Skibidi dop dop dop yes yes', '2024-06-07 13:18:35');

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `genre_id` int(11) NOT NULL,
  `genre_name` varchar(255) NOT NULL,
  `genre_desc` text DEFAULT NULL,
  `genre_icon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`genre_id`, `genre_name`, `genre_desc`, `genre_icon`) VALUES
(1, 'Action', 'Action manga are characterized by fast-paced plots, intense fight scenes, and a focus on physical feats and battles. They often involve protagonists who face numerous challenges and adversaries, with themes of bravery, justice, and personal growth.', 'action_genre.png'),
(2, 'Comedy', 'Comedy manga aim to entertain and amuse the reader through humor. This genre often features witty dialogue, absurd situations, and a lighthearted tone. Characters may find themselves in ridiculous predicaments, leading to humorous outcomes.', 'comedy_genre.png'),
(3, 'Fantasy', 'Fantasy manga transport readers to imaginary worlds filled with magic, mythical creatures, and extraordinary adventures. These stories often involve quests, battles between good and evil, and complex magical systems.', 'fantasy_genre.png'),
(4, 'Horor', 'Horror manga aim to evoke fear, suspense, and unease in the reader. They often feature supernatural elements, psychological thrills, and grotesque imagery. Themes may include the unknown, monstrous creatures, and human psychology.', 'horor_genre.png'),
(5, 'Sport', 'Sports manga center around the lives of athletes and the world of sports competition. They emphasize themes of teamwork, perseverance, and personal growth. The narrative typically follows the protagonist’s journey to achieve greatness in their chosen sport, with detailed depictions of matches and training.', 'sport_genre.png'),
(6, 'Romance', 'Romance manga focus on the development of romantic relationships between characters. They explore themes of love, heartache, and emotional connection, often set against a backdrop of everyday life or more fantastical settings. They can range from sweet and heartwarming to dramatic and intense.', 'romance_genre.png');

-- --------------------------------------------------------

--
-- Table structure for table `manga`
--

CREATE TABLE `manga` (
  `manga_id` int(11) NOT NULL,
  `manga_name` varchar(255) NOT NULL,
  `author_name` varchar(255) NOT NULL,
  `status` enum('ongoing','completed') DEFAULT 'ongoing',
  `publish` enum('approved','pending','rejected') DEFAULT 'pending',
  `description` text DEFAULT NULL,
  `manga_image` varchar(255) DEFAULT NULL,
  `publisher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manga`
--

INSERT INTO `manga` (`manga_id`, `manga_name`, `author_name`, `status`, `publish`, `description`, `manga_image`, `publisher_id`) VALUES
(1, 'Chainsaw Man', 'Tatsuki Fujimoto', 'ongoing', 'approved', 'Denji has a simple dream—to live a happy and peaceful life, spending time with a girl he likes. This is a far cry from reality, however, as Denji is forced by the yakuza into killing devils in order to pay off his crushing debts. Using his pet devil Pochita as a weapon, he is ready to do anything for a bit of cash. Unfortunately, he has outlived his usefulness and is murdered by a devil in contract with the yakuza. However, in an unexpected turn of events, Pochita merges with Denji\'s dead body and grants him the powers of a chainsaw devil. Now able to transform parts of his body into chainsaws, a revived Denji uses his new abilities to quickly and brutally dispatch his enemies. Catching the eye of the official devil hunters who arrive at the scene, he is offered work at the Public Safety Bureau as one of them. Now with the means to face even the toughest of enemies, Denji will stop at nothing to achieve his simple teenage dreams.', 'picture/ChainsawMan_Volume12.webp', 2),
(2, 'Gintama', 'Hideaki Sorachi', 'completed', 'approved', 'During the Edo period, Japan is suddenly invaded by alien creatures known as the \"Amanto.\" Despite the samurai\'s attempts to combat the extraterrestrial menace, the Shogun soon realizes that their efforts are futile and decides to surrender. This marks the beginning of an uneasy agreement between the Shogunate and Amanto, one that results in a countrywide sword ban and the disappearance of the samurai spirit. However, there exists one eccentric individual who wields a wooden sword and refuses to let his samurai status die. Now that his kind are no longer needed, Gintoki Sakata performs various odd jobs around town in order to make ends meet. Joined by his self-proclaimed disciple Shinpachi Shimura, the fearsome alien Kagura, and a giant dog named Sadaharu, they run the business known as Yorozuya, often getting caught up in all sorts of crazy and hilarious shenanigans.', 'picture/Gintama_Tooltip.webp', 2),
(3, 'Made In Abyss', 'Akihito Tsukushi ', 'ongoing', 'approved', 'The Abyss, a hole of unprecedented depth—one young girl and a robot brave its dangers to find the truth.The town of Orth is a special one, as it is built around the edges of the massive Abyss, a wonder which has never been fully explored. Those who venture too far down never return, but those brave enough to traverse its territories are known as \"Cave Raiders\" and are heralded as legends. Within this town lives a young girl called Riko, the child of one of the most famous Cave Raiders of all time who disappeared on an expedition many years ago. One day, Riko\'s life changes when she meets a strange robot called Regu, who seems to appear from within the Abyss. Believing this to be a sign from her mother stuck at the bottom of the Abyss, Riko descends into its depths with Regu, ready to confront all the dangers within it.', 'picture/Made-in-Abyss-Cover-V3.webp', 2),
(4, 'The Danger In My Heart', 'Norio Sakurai ', 'ongoing', 'approved', 'A \"fearsome psycho-thriller\" centering on \"the dark side of adolescence.\" The manga centers on Kyoutarou Ichikawa, a person at the very bottom caste of his school, and who hides murderous impulse that lurks at the very bottom of his soul.', 'picture/The_Dangers_in_My_Heart_cover.jpg', 4),
(5, 'Blue Lock', 'KANESHIRO Muneyuki', 'ongoing', 'approved', 'After reflecting on the current state of Japanese soccer, the Japanese Football Association decides to hire the enigmatic and eccentric coach Jinpachi Ego to achieve their dream of winning the World Cup. Believing that Japan has lacked an egoistic striker hungry for goals, Jinpachi initiates the Blue Lock—a prison-like facility where three hundred talented strikers from high schools all over Japan are isolated and pitted against each other. The sole survivor of Blue Lock will earn the right to become the national team\'s striker, and those who are defeated shall be banned from joining the team forever. Selected to join this risky project is Yoichi Isagi, a striker who failed to bring his high school soccer team to the national tournament. After choosing to pass to a teammate who missed instead of scoring on his own, he could not help but wonder if the results would have been different had he been more selfish. Using this golden opportunity given by the Blue Lock Project, Yoichi aims to clear his doubts and chase his ultimate desire—to become the greatest striker in the world and lead Japan to World Cup glory.', 'picture/bluelock_cover.jpg', 4);

-- --------------------------------------------------------

--
-- Table structure for table `manga_genres`
--

CREATE TABLE `manga_genres` (
  `manga_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manga_genres`
--

INSERT INTO `manga_genres` (`manga_id`, `genre_id`) VALUES
(1, 1),
(1, 3),
(1, 4),
(2, 2),
(3, 3),
(3, 4),
(4, 6),
(5, 1),
(5, 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','publisher','reader') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`) VALUES
(1, 'admin', '$2y$10$3nWUl80iYmDf5lGER40QS.eTAeLkitNhW4S1LpOtj0DjzJpjRu1Ie', 'admin@admin.com', 'admin'),
(2, 'publisher', '$2y$10$r80BS9ppqiB1TJ5.JDQpAO4yafjh2dWaAG6vESSRGJ19u8RA22iaK', 'publisher@publisher.com', 'publisher'),
(3, 'reader', '$2y$10$o92MFZJLYfddKOLz5zftLeC5Ql2jIcc3lfbdY11L0yRqUN.lJtKKW', 'reader@reader.com', 'reader'),
(4, 'publish', '$2y$10$IBebG3fNfz4s4XpBJruU8OYyujUXSGm19.CBVtJVd2CPKsGZ7sAi2', 'publish@publish.com', 'publisher');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`chapter_id`),
  ADD KEY `manga_id` (`manga_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`genre_id`),
  ADD UNIQUE KEY `genre_name` (`genre_name`);

--
-- Indexes for table `manga`
--
ALTER TABLE `manga`
  ADD PRIMARY KEY (`manga_id`),
  ADD UNIQUE KEY `manga_name` (`manga_name`),
  ADD KEY `publisher_id` (`publisher_id`);

--
-- Indexes for table `manga_genres`
--
ALTER TABLE `manga_genres`
  ADD PRIMARY KEY (`manga_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chapters`
--
ALTER TABLE `chapters`
  MODIFY `chapter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `genre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `manga`
--
ALTER TABLE `manga`
  MODIFY `manga_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chapters`
--
ALTER TABLE `chapters`
  ADD CONSTRAINT `chapters_ibfk_1` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`manga_id`) ON DELETE CASCADE;

--
-- Constraints for table `manga`
--
ALTER TABLE `manga`
  ADD CONSTRAINT `manga_ibfk_1` FOREIGN KEY (`publisher_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `manga_genres`
--
ALTER TABLE `manga_genres`
  ADD CONSTRAINT `manga_genres_ibfk_1` FOREIGN KEY (`manga_id`) REFERENCES `manga` (`manga_id`),
  ADD CONSTRAINT `manga_genres_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`genre_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
