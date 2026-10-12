<?php
require_once 'koneksi.php';

$message = "";

// Memastikan parameter ID tersedia
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// --- FUNGSI UPDATE (Simpan Perubahan) ---
if (isset($_POST['update_movie'])) {
    $title        = mysqli_real_escape_string($conn, $_POST['title']);
    $genre        = mysqli_real_escape_string($conn, $_POST['genre']);
    $release_year = (int)$_POST['release_year'];
    $duration     = mysqli_real_escape_string($conn, $_POST['duration']);
    $poster_url   = mysqli_real_escape_string($conn, $_POST['poster_url']);
    $trailer_url  = mysqli_real_escape_string($conn, $_POST['trailer_url']);
    $synopsis     = mysqli_real_escape_string($conn, $_POST['synopsis']);

    $queryUpdate = "UPDATE movies 
                    SET title = '$title', 
                        genre = '$genre', 
                        release_year = '$release_year', 
                        duration = '$duration', 
                        poster_url = '$poster_url', 
                        trailer_url = '$trailer_url', 
                        synopsis = '$synopsis' 
                    WHERE id = $id";

    if (mysqli_query($conn, $queryUpdate)) {
        header("Location: index.php?status=success_update");
        exit();
    } else {
        $message = "Gagal memperbarui film: " . mysqli_error($conn);
    }
}

// Mengambil Data Film Berdasarkan ID
$queryGet = "SELECT * FROM movies WHERE id = $id";
$resultGet = mysqli_query($conn, $queryGet);

if (mysqli_num_rows($resultGet) === 0) {
    header("Location: index.php");
    exit();
}

$movie = mysqli_fetch_assoc($resultGet);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie - Cinepedia Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
        <div class="page-header">
            <a href="index.php" class="back-link">
                &#129120; Back to Movie Database
            </a>
        </div>

        <?php if (!empty($message)): ?>
            <div style="background: #4a1d1d; color: #ff6b6b; padding: 12px; border-radius: 4px; margin-bottom: 12px;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <section class="card">
            <div class="card-header border-bottom">
                <div class="card-title">
                    <span class="icon">&#9998;</span> EDIT MOVIE DETAILS
                </div>
                <div class="card-subtitle">ID: #<?= $movie['id'] ?></div>
            </div>

            <form action="edit.php?id=<?= $movie['id'] ?>" method="POST" style="padding-top: 16px;">
                <div class="poster-preview-wrapper">
                    <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="Poster Preview" class="poster-preview" id="posterPreview" onerror="this.src='https://via.placeholder.com/110x160/121212/ffffff?text=No+Poster'">

                    <div class="form-grid" style="flex: 1;">
                        <div class="form-group full-width">
                            <label>MOVIE TITLE</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($movie['title']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label>GENRE</label>
                            <select name="genre" required>
                                <option value="Sci-Fi" <?= $movie['genre'] === 'Sci-Fi' ? 'selected' : '' ?>>Sci-Fi</option>
                                <option value="Biography" <?= $movie['genre'] === 'Biography' ? 'selected' : '' ?>>Biography</option>
                                <option value="Crime" <?= $movie['genre'] === 'Crime' ? 'selected' : '' ?>>Crime</option>
                                <option value="Action" <?= $movie['genre'] === 'Action' ? 'selected' : '' ?>>Action</option>
                                <option value="Drama" <?= $movie['genre'] === 'Drama' ? 'selected' : '' ?>>Drama</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>RELEASE YEAR</label>
                            <input type="number" name="release_year" value="<?= htmlspecialchars($movie['release_year']) ?>" required>
                        </div>

                        <div class="form-group full-width">
                            <label>DURATION</label>
                            <input type="text" name="duration" value="<?= htmlspecialchars($movie['duration']) ?>" required>
                        </div>

                        <div class="form-group full-width">
                            <label>COVER IMAGE URL (POSTER)</label>
                            <input type="url" name="poster_url" id="editPosterInput" value="<?= htmlspecialchars($movie['poster_url']) ?>" required>
                        </div>

                        <div class="form-group full-width">
                            <label>YOUTUBE TRAILER URL</label>
                            <input type="url" name="trailer_url" value="<?= htmlspecialchars($movie['trailer_url']) ?>" required>
                        </div>

                        <div class="form-group full-width">
                            <label>SYNOPSIS</label>
                            <textarea name="synopsis" rows="3" style="background-color: #121212; border: 1px solid #333333; color: #ffffff; padding: 10px; border-radius: 4px; font-size: 13px; font-family: inherit; resize: vertical;"><?= htmlspecialchars($movie['synopsis']) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn btn-secondary">CANCEL</a>
                    <button type="submit" name="update_movie" class="btn btn-primary">
                        <span class="save-icon">&#128190;</span> UPDATE MOVIE
                    </button>
                </div>
            </form>
        </section>
    </main>

    <footer class="global-footer">
        <span><strong>CINEPEDIA</strong> &bull; Content Management Interface</span>
        <span>&copy; 2025 Cinepedia Media. All rights reserved.</span>
    </footer>

    <script src="script.js"></script>
    <script>
        const posterInput = document.getElementById('editPosterInput');
        const posterPreview = document.getElementById('posterPreview');

        if (posterInput && posterPreview) {
            posterInput.addEventListener('input', (e) => {
                posterPreview.src = e.target.value || 'https://via.placeholder.com/110x160/121212/ffffff?text=No+Poster';
            });
        }
    </script>
</body>
</html>