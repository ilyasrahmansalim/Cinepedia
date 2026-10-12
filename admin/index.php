<?php
require_once 'koneksi.php';

$message = "";

// --- FUNGSI CREATE (Tambah Film Baru) ---
if (isset($_POST['add_movie'])) {
    $title        = mysqli_real_escape_string($conn, $_POST['title']);
    $genre        = mysqli_real_escape_string($conn, $_POST['genre']);
    $release_year = (int)$_POST['release_year'];
    $duration     = mysqli_real_escape_string($conn, $_POST['duration']);
    $poster_url   = mysqli_real_escape_string($conn, $_POST['poster_url']);
    $trailer_url  = mysqli_real_escape_string($conn, $_POST['trailer_url']);
    $synopsis     = mysqli_real_escape_string($conn, $_POST['synopsis']);

    $queryInsert = "INSERT INTO movies (title, genre, release_year, duration, poster_url, trailer_url, synopsis) 
                    VALUES ('$title', '$genre', '$release_year', '$duration', '$poster_url', '$trailer_url', '$synopsis')";

    if (mysqli_query($conn, $queryInsert)) {
        header("Location: index.php?status=success_add");
        exit();
    } else {
        $message = "Gagal menambahkan film: " . mysqli_error($conn);
    }
}

// --- FUNGSI DELETE (Hapus Film) ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $queryDelete = "DELETE FROM movies WHERE id = $id";

    if (mysqli_query($conn, $queryDelete)) {
        header("Location: index.php?status=success_delete");
        exit();
    } else {
        $message = "Gagal menghapus film: " . mysqli_error($conn);
    }
}

// --- FUNGSI READ (Mengambil Daftar Film & Rating Rata-Rata) ---
$queryMovies = "SELECT m.*, COALESCE(AVG(r.rating), 0) AS avg_rating 
                FROM movies m 
                LEFT JOIN reviews r ON m.id = r.movie_id 
                GROUP BY m.id 
                ORDER BY m.id DESC";
$resultMovies = mysqli_query($conn, $queryMovies);
$totalMovies  = mysqli_num_rows($resultMovies);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinepedia - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Top Navbar -->
    <header class="navbar">
      <div class="nav-left">
        <div class="logo">
          <span class="logo-icon">C</span>
          <span class="logo-text">CINEPEDIA</span>
        </div>
      </div>

      <div class="nav-right">
        <div class="user-dropdown">
          <button type="button" class="user-profile-btn" id="userDropdownBtn">
            <span class="avatar">AD</span>
            <span class="user-name">Admin User</span>
            <span class="dropdown-arrow">&#9660;</span>
          </button>

          <div class="dropdown-menu" id="userDropdownMenu">
            <div class="dropdown-header">
              <div class="dropdown-avatar">AD</div>
              <div class="dropdown-user-info">
                <span class="dropdown-name">Admin User</span>
                <span class="dropdown-email">admin@cinepedia.com</span>
              </div>
            </div>
            <div class="dropdown-divider"></div>
            <a href="logout.php" class="dropdown-item logout-item">
              <span class="item-icon">&#128682;</span> Logout
            </a>
          </div>
        </div>
      </div>
    </header>

    <main class="main-container">
        <?php if (!empty($message)): ?>
            <div style="background: #4a1d1d; color: #ff6b6b; padding: 12px; border-radius: 4px; margin-bottom: 12px;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Form Tambah Film -->
        <section class="card">
            <div class="card-header">
                <div class="card-title">
                    <span class="icon">&#127916;</span> ADD NEW MOVIE
                </div>
            </div>
            <form action="index.php" method="POST" id="addMovieForm" class="form-row">
                <div class="form-group">
                    <label>MOVIE TITLE</label>
                    <input type="text" name="title" placeholder="e.g. Blade Runner 2049" required>
                </div>
                <div class="form-group flex-genre">
                    <label>GENRE</label>
                    <select name="genre" required>
                        <option value="Sci-Fi">Sci-Fi</option>
                        <option value="Biography">Biography</option>
                        <option value="Crime">Crime</option>
                        <option value="Action">Action</option>
                        <option value="Drama">Drama</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 0.6;">
                    <label>RELEASE YEAR</label>
                    <input type="number" name="release_year" placeholder="2024" min="1900" max="2099" required>
                </div>
                <div class="form-group" style="flex: 0.8;">
                    <label>DURATION</label>
                    <input type="text" name="duration" placeholder="120 min" required>
                </div>
                <div class="form-group flex-url">
                    <label>COVER IMAGE URL (POSTER)</label>
                    <input type="url" name="poster_url" placeholder="https://.../poster.jpg" required>
                </div>
                <div class="form-group flex-url">
                    <label>YOUTUBE TRAILER URL</label>
                    <input type="url" name="trailer_url" placeholder="https://youtube.com/watch?v=..." required>
                </div>
                <input type="hidden" name="synopsis" value="">
                <div class="form-group flex-btn">
                    <button type="submit" name="add_movie" class="btn btn-primary">
                        <span class="save-icon">&#128190;</span> ADD
                    </button>
                </div>
            </form>
        </section>

        <!-- Tabel Database Film -->
        <section class="card">
            <div class="card-header border-bottom">
                <div class="title-with-badge">
                    <h2>MOVIE DATABASE</h2>
                    <span class="badge-count" id="movieCount"><?= $totalMovies ?> TITLES</span>
                </div>
                <div class="search-box">
                    <span class="search-icon">&#128065;</span>
                    <input type="text" id="searchInput" placeholder="Filter title or genre...">
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>POSTER</th>
                            <th>TITLE</th>
                            <th>GENRE</th>
                            <th>RATING</th>
                            <th>YOUTUBE TRAILER LINK</th>
                            <th class="text-right">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody id="movieTableBody">
                        <?php if ($totalMovies > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($resultMovies)): ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars($row['poster_url']) ?>" alt="<?= htmlspecialchars($row['title']) ?>" class="poster-img" onerror="this.src='https://via.placeholder.com/48x68/282828/ffffff?text=No+Img'">
                                    </td>
                                    <td>
                                        <div class="movie-info">
                                            <div class="movie-title"><?= htmlspecialchars($row['title']) ?></div>
                                            <div class="movie-meta"><?= htmlspecialchars($row['release_year']) ?> &bull; <?= htmlspecialchars($row['duration']) ?></div>
                                        </div>
                                    </td>
                                    <td><span class="genre-tag"><?= strtoupper(htmlspecialchars($row['genre'])) ?></span></td>
                                    <td><span class="rating-tag">&#9733; <?= number_format((float)$row['avg_rating'], 1) ?></span></td>
                                    <td>
                                        <?php if (!empty($row['trailer_url'])): ?>
                                            <a href="<?= htmlspecialchars($row['trailer_url']) ?>" target="_blank" class="btn-trailer">
                                                <span class="play-icon">&#9654;</span> Watch Trailer
                                            </a>
                                        <?php else: ?>
                                            <span style="color: #666;">No Link</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <div class="action-btns">
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">EDIT</a>
                                            <a href="index.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus film ini?')">DELETE</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #666; padding: 24px;">
                                    Belum ada data film di basis data.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                <span>Cinepedia Catalog Manager</span>
            </div>
        </section>
    </main>

    <footer class="global-footer">
        <span><strong>CINEPEDIA</strong> &bull; Content Management Interface</span>
        <span>&copy; 2025 Cinepedia Media. All rights reserved.</span>
    </footer>

    <script src="script.js"></script>
</body>
</html>