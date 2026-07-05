<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - PengingatObat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'light')</script>
    <style>
        :root {
            --bg-body: #f0f2f5; --bg-card: #ffffff;
            --text-primary: #1a1a2e; --text-secondary: #495057; --text-muted: #6c757d;
            --bg-input: #ffffff; --text-input: #1a1a2e;
            --border-color: #e8e8ef;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.06); --shadow-md: 0 8px 30px rgba(0,0,0,0.08);
            --accent: #3688C9; --accent-hover: #3659C9;
        }
        [data-theme="dark"] {
            --bg-body: #0d0d1a; --bg-card: #1a1a30;
            --text-primary: #f8f9fa; --text-secondary: #e0e0e8; --text-muted: #a8a8b8;
            --bg-input: #242442; --text-input: #ffffff;
            --border-color: #343459;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.4); --shadow-md: 0 8px 30px rgba(0,0,0,0.6);
        }
        * { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
        .card { background-color: var(--bg-card); border-color: var(--border-color); color: var(--text-primary); }
        .form-control, .form-select { background-color: var(--bg-input); color: var(--text-input); border-color: var(--border-color); }
        .form-control:focus, .form-select:focus { background-color: var(--bg-input); color: var(--text-input); border-color: var(--accent); box-shadow: 0 0 0 3px rgba(54,136,201,0.15); }
        .text-muted { --bs-text-opacity: 1; color: var(--text-muted) !important; }
        body {
            background-color: var(--accent);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-card {
            background-color: var(--bg-card);
            border-radius: 20px;
            box-shadow: var(--shadow-md);
            padding: 40px 36px;
            width: 100%;
            max-width: 420px;
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 28px;
        }
        .auth-logo i { font-size: 2.5rem; color: var(--accent); }
        .auth-logo h3 { font-weight: 700; color: var(--text-primary); margin-top: 8px; }
        .auth-logo p { color: var(--text-secondary); font-size: 0.9rem; }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            background-color: var(--bg-body);
            color: var(--text-primary);
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(54,136,201,0.15);
        }
        .form-label { font-weight: 500; color: var(--text-secondary); font-size: 0.9rem; }
        .btn-auth {
            background-color: var(--accent);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            transition: all 0.2s ease;
        }
        .btn-auth:hover { background-color: var(--accent-hover); transform: translateY(-1px); box-shadow: 0 4px 15px rgba(54,136,201,0.3); }
        .auth-footer { text-align: center; margin-top: 20px; font-size: 0.9rem; color: var(--text-secondary); }
        .auth-footer a { color: var(--accent); text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }
        .alert-custom {
            border-radius: 12px;
            font-size: 0.9rem;
            padding: 12px 16px;
        }
        .theme-toggle-wrap {
            position: fixed;
            top: 20px;
            right: 20px;
        }
        .theme-toggle {
            background: rgba(255,255,255,0.2);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 1.1rem;
            backdrop-filter: blur(10px);
            transition: all 0.2s ease;
        }
        .theme-toggle:hover { background: rgba(255,255,255,0.35); transform: scale(1.05); }
    </style>
</head>
<body>
    <button class="theme-toggle theme-toggle-wrap" onclick="toggleTheme()" title="Mode Gelap">
        <i class="fa-solid fa-moon" id="themeIcon"></i>
    </button>

    <div class="auth-card">
        <div class="auth-logo">
            <i class="fa-solid fa-capsules"></i>
            <h3>Selamat Datang</h3>
            <p>Silakan masuk ke akun Anda</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-custom">
                <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-custom">
                <i class="fa-solid fa-check-circle me-2"></i><?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <form action="../proses/prosesLogin.php" method="POST">
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-user me-1"></i>Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>
            <div class="mb-4">
                <label class="form-label"><i class="fa-solid fa-lock me-1"></i>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn-auth"><i class="fa-solid fa-right-to-bracket me-2"></i>Masuk</button>
        </form>

        <div class="auth-footer">
            Belum punya akun? <a href="../registrasi.php">Daftar di sini</a>
        </div>
    </div>

    <script>
        (function() {
            const saved = localStorage.getItem('theme') || 'light';
            const icon = document.getElementById('themeIcon');
            if (icon) icon.className = saved === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        })();

        function toggleTheme() {
            const html = document.documentElement;
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            const icon = document.getElementById('themeIcon');
            if (icon) icon.className = next === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        }
    </script>
</body>
</html>
