-- Cinepedia database. Import lewat phpMyAdmin atau: mysql -u root < database.sql
CREATE DATABASE IF NOT EXISTS cinepedia_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cinepedia_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    genre VARCHAR(100) NOT NULL,
    release_year INT NOT NULL,
    duration VARCHAR(50) DEFAULT NULL,
    synopsis TEXT DEFAULT NULL,
    poster_url VARCHAR(255) DEFAULT NULL,
    trailer_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rating 1-5, satu review per user per film (dipakai untuk upsert di movie/index.php)
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_movie (movie_id, user_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Akun admin awal: admin@cinepedia.com / admin123  (GANTI passwordnya setelah login pertama)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@cinepedia.com', '$2y$10$zj8JbIBtEShzApW4sfzAee5GU.MMahqTs/pvBMkZo/1RpSwGXGb9y', 'admin');

INSERT INTO movies (title, genre, release_year, duration, synopsis, poster_url, trailer_url) VALUES
('Oppenheimer', 'Biography, Drama', 2023, '3h 0m',
 'The story of American scientist J. Robert Oppenheimer and his pivotal role in developing the atomic bomb during World War II.',
 'https://image.tmdb.org/t/p/w500/ptpr0kGAckfQkJeJIt8st5dglvd.jpg', 'https://www.youtube.com/watch?v=uYPbbksJxIg'),
('Interstellar', 'Adventure, Drama, Sci-Fi', 2014, '2h 49m',
 'When Earth becomes increasingly uninhabitable, a team of explorers travels beyond this galaxy to discover whether mankind has a future among the stars.',
 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg', 'https://www.youtube.com/watch?v=zSWdZVtXT7E'),
('Inception', 'Action, Adventure, Sci-Fi', 2010, '2h 28m',
 'A skilled thief who steals secrets through dream-sharing technology is offered a chance to have his criminal record erased.',
 'https://image.tmdb.org/t/p/w500/oYuLEt3zVCKq57qu2F8dT7NIa6f.jpg', 'https://www.youtube.com/watch?v=YoHD9XEInc0'),
('The Dark Knight', 'Action, Crime, Drama', 2008, '2h 32m',
 'Batman raises the stakes in his war on crime with the help of Lieutenant Jim Gordon and District Attorney Harvey Dent.',
 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg', 'https://www.youtube.com/watch?v=EXeTwQWrcwY'),
('Dune: Part Two', 'Action, Adventure, Drama', 2024, '2h 46m',
 'Paul Atreides unites with Chani and the Fremen while seeking revenge against the conspirators who destroyed his family.',
 'https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg', 'https://www.youtube.com/watch?v=Way9Dexny3w'),
('Across the Spider-Verse', 'Animation, Action, Adventure', 2023, '2h 20m',
 'Miles Morales reunites with Gwen Stacy and is launched across the Multiverse, where he encounters a team of Spider-People.',
 'https://image.tmdb.org/t/p/w500/8Vt6mWEReuy4Of61Lnj5Xj704m8.jpg', 'https://www.youtube.com/watch?v=shW9i6k8cB0'),
('Arrival', 'Drama, Mystery, Sci-Fi', 2016, '1h 56m',
 'When mysterious spacecraft arrive around the world, a linguist is recruited to communicate with the visitors.',
 'https://image.tmdb.org/t/p/w500/x2FJsf1ElAgr63Y3PNPtJrcmpoe.jpg', NULL),
('Whiplash', 'Drama', 2014, '1h 47m',
 'A young and ambitious jazz drummer enrolls at a cutthroat music conservatory, where his pursuit of greatness is pushed to the limit.',
 'https://image.tmdb.org/t/p/w500/7fn624j5lj3xTme2SgiLCeuedmO.jpg', NULL);
