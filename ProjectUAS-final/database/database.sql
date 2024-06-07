CREATE DATABASE manga_web;

USE manga_web;

-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    role ENUM('admin', 'publisher', 'reader') NOT NULL
);

-- Genres Table
CREATE TABLE genres (
    genre_id INT AUTO_INCREMENT PRIMARY KEY,
    genre_name VARCHAR(255) NOT NULL UNIQUE,
    genre_desc TEXT,
    genre_icon VARCHAR(255)
);


-- Manga Table
CREATE TABLE manga (
    manga_id INT AUTO_INCREMENT PRIMARY KEY,
    manga_name VARCHAR(255) NOT NULL UNIQUE,
    author_name VARCHAR(255) NOT NULL,
    status ENUM('ongoing', 'completed') DEFAULT 'ongoing',
    publish ENUM('approved', 'pending', 'rejected') DEFAULT 'pending', -- Default value set to 'pending'
    description TEXT,
    manga_image VARCHAR(255), -- Column for the manga cover image
    publisher_id INT,
    FOREIGN KEY (publisher_id) REFERENCES users(user_id)
);

-- Manga Genres Junction Table
CREATE TABLE manga_genres (
    manga_id INT,
    genre_id INT,
    PRIMARY KEY (manga_id, genre_id),
    FOREIGN KEY (manga_id) REFERENCES manga(manga_id),
    FOREIGN KEY (genre_id) REFERENCES genres(genre_id)
);

-- Chapters Table
CREATE TABLE chapters (
    chapter_id INT AUTO_INCREMENT PRIMARY KEY,
    manga_id INT,
    chapter_number INT NOT NULL,
    title VARCHAR(255),
    content VARCHAR(255),
    chapter_cover VARCHAR(255),
    FOREIGN KEY (manga_id) REFERENCES manga(manga_id) ON DELETE CASCADE
);

-- Contact Messages Table
CREATE TABLE contact_messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email_address VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
