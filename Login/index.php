<?php
require_once 'db.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email is already registered.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            if ($stmt->execute([$username, $email, $hashed_password])) {
                $success = 'Registration successful! You can now log in.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#050505">
  <title>Cinepedia — Sign In</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body data-page="sign-in">
  <main class="page-shell">
    <header class="brand" aria-label="Cinepedia">
      <span class="brand-mark" aria-hidden="true"></span>
      <span class="brand-name">CINEPEDIA</span>
    </header>

    <section class="auth-card" aria-labelledby="form-title">
      <div class="card-heading">
        <h1 id="form-title">SIGN IN</h1>
        <p id="form-description">Enter your details to register and access the archive.</p>
      </div>

      <?php if (!empty($error)): ?>
        <p class="form-status is-error" role="status" aria-live="polite"><?php echo htmlspecialchars($error); ?></p>
      <?php elseif (!empty($success)): ?>
        <p class="form-status is-success" role="status" aria-live="polite"><?php echo htmlspecialchars($success); ?></p>
      <?php endif; ?>

      <form id="auth-form" method="POST" action="">
        <div class="form-field">
          <label for="username">USERNAME</label>
          <input type="text" id="username" name="username" placeholder="Your username" autocomplete="username" required>
        </div>

        <div class="form-field">
          <label for="email">EMAIL</label>
          <input type="email" id="email" name="email" placeholder="name@example.com" autocomplete="email" required>
        </div>

        <div class="form-field password-field">
          <div class="label-row">
            <label for="password">PASSWORD</label>
            <button class="text-button" type="button" id="toggle-password" aria-controls="password" aria-pressed="false">SHOW</button>
          </div>
          <input type="password" id="password" name="password" placeholder="••••••••••••" autocomplete="new-password" minlength="6" required>
        </div>

        <button class="submit-button" type="submit" id="submit-button">SIGN IN</button>
      </form>

      <footer class="card-footer">
        <p>Already have an account? <a class="switch-button" href="login.php">Go to Log In</a></p>
      </footer>
    </section>
    <p class="demo-note">CINEPEDIA ARCHIVE ACCESS</p>
  </main>
  <script>
    const togglePasswordButton = document.querySelector("#toggle-password");
    const passwordInput = document.querySelector("#password");
    if (togglePasswordButton && passwordInput) {
      togglePasswordButton.addEventListener("click", () => {
        const shouldShow = passwordInput.type === "password";
        passwordInput.type = shouldShow ? "text" : "password";
        togglePasswordButton.textContent = shouldShow ? "HIDE" : "SHOW";
        togglePasswordButton.setAttribute("aria-pressed", String(shouldShow));
      });
    }
  </script>
</body>
</html>
```[cite: 1, 3, 4]

---

### 3. `login.php` (Log In)
Konversi dari `log-in.html`[cite: 2] yang memverifikasi kredensial pengguna terhadap tabel `users`.

```php
<?php
require_once 'db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#050505">
  <title>Cinepedia — Log In</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body data-page="log-in">
  <main class="page-shell">
    <header class="brand" aria-label="Cinepedia">
      <span class="brand-mark" aria-hidden="true"></span>
      <span class="brand-name">CINEPEDIA</span>
    </header>

    <section class="auth-card" aria-labelledby="form-title">
      <div class="card-heading">
        <h1 id="form-title">LOG IN</h1>
        <p>Log in with your credentials to access the archive.</p>
      </div>

      <?php if (!empty($error)): ?>
        <p class="form-status is-error" role="status" aria-live="polite"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>

      <form id="auth-form" method="POST" action="">
        <div class="form-field">
          <label for="email">EMAIL</label>
          <input type="email" id="email" name="email" placeholder="name@example.com" autocomplete="email" required>
        </div>

        <div class="form-field password-field">
          <div class="label-row">
            <label for="password">PASSWORD</label>
            <button class="text-button" type="button" id="toggle-password" aria-controls="password" aria-pressed="false">SHOW</button>
          </div>
          <input type="password" id="password" name="password" placeholder="••••••••••••" autocomplete="current-password" minlength="6" required>
        </div>

        <button class="submit-button" type="submit" id="submit-button">LOG IN</button>
      </form>

      <footer class="card-footer">
        <p>Don't have an account? <a class="switch-button" href="index.php">Go to Sign In</a></p>
      </footer>
    </section>
    <p class="demo-note">CINEPEDIA ARCHIVE ACCESS</p>
  </main>
  <script>
    const togglePasswordButton = document.querySelector("#toggle-password");
    const passwordInput = document.querySelector("#password");
    if (togglePasswordButton && passwordInput) {
      togglePasswordButton.addEventListener("click", () => {
        const shouldShow = passwordInput.type === "password";
        passwordInput.type = shouldShow ? "text" : "password";
        togglePasswordButton.textContent = shouldShow ? "HIDE" : "SHOW";
        togglePasswordButton.setAttribute("aria-pressed", String(shouldShow));
      });
    }
  </script>
</body>
</html>
```[cite: 2, 3, 4]

---

### 4. `dashboard.php` (Halaman Utama / Arsip Film)
Halaman tambahan untuk membuktikan integrasi penuh dengan tabel `movies` dan `reviews` setelah pengguna berhasil login.

```php
<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil data movies dari database
$stmt = $pdo->query("SELECT * FROM movies ORDER BY created_at DESC");
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cinepedia — Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .dashboard-container { width: min(900px, 100%); margin: 40px auto; padding: 20px; }
    .movie-card { background: var(--panel); border: 1px solid var(--panel-border); padding: 20px; margin-bottom: 20px; }
    .logout-btn { color: var(--accent); text-decoration: none; font-weight: bold; float: right; }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <header style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
      <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
      <a href="logout.php" class="logout-btn">LOGOUT</a>
    </header>

    <h3>Movie Archive</h3>
    <?php if (empty($movies)): ?>
      <p style="color: var(--muted);">No movies found in the database yet.</p>
    <?php else: ?>
      <?php foreach ($movies as $movie): ?>
        <div class="movie-card">
          <h4 style="margin-top:0; color: var(--text);"><?php echo htmlspecialchars($movie['title']); ?> (<?php echo $movie['release_year']; ?>)</h4>
          <p style="color: var(--muted); font-size: 14px;">Genre: <?php echo htmlspecialchars($movie['genre']); ?> | Duration: <?php echo htmlspecialchars($movie['duration']); ?></p>
          <p><?php echo nl2br(htmlspecialchars($movie['synopsis'])); ?></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>