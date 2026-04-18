<?php
require 'auth.php';

if (isLoggedIn()) { header("Location: index.php"); exit; }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!$username || !$email || !$password || !$confirm) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $u = $conn->real_escape_string($username);
        $e = $conn->real_escape_string($email);
        $check = $conn->query("SELECT id FROM users WHERE email='$e' OR username='$u'");
        if ($check->num_rows > 0) {
            $error = 'An account with that username or email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $conn->query("INSERT INTO users (username, email, password) VALUES ('$u', '$e', '$hash')");
            $success = 'Account created! You can now log in.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register – Lost &amp; Found</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{--ink:#0f0e0c;--paper:#faf8f4;--cream:#f0ece3;--amber:#e8a020;--amber-deep:#c4820f;--amber-light:#fef3dc;--muted:#7a7468;--border:#ddd9d0;--shadow-md:0 4px 16px rgba(0,0,0,0.08);}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'DM Sans',sans-serif;background:var(--ink);min-height:100vh;display:flex;flex-direction:column;}
.page-wrap{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px;position:relative;}
.page-wrap::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 50% 100%,rgba(232,160,32,0.12) 0%,transparent 70%);}
.card{background:#fff;border-radius:16px;padding:40px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative;}
.logo{display:flex;align-items:center;gap:10px;margin-bottom:28px;text-decoration:none;}
.logo-icon{width:38px;height:38px;background:var(--amber);border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:18px;}
.logo-name{font-family:'Playfair Display',serif;font-size:19px;font-weight:700;color:var(--ink);}
h1{font-family:'Playfair Display',serif;font-size:26px;font-weight:700;margin-bottom:6px;}
.subtitle{font-size:13px;color:var(--muted);margin-bottom:24px;}
.form-group{margin-bottom:16px;}
label{display:block;font-size:13px;font-weight:600;margin-bottom:5px;color:var(--ink);}
input{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-family:inherit;font-size:14px;outline:none;transition:border-color .2s,box-shadow .2s;background:var(--paper);}
input:focus{border-color:var(--amber);box-shadow:0 0 0 3px rgba(232,160,32,.12);background:#fff;}
.btn-submit{width:100%;padding:13px;background:var(--ink);color:#fff;font-family:inherit;font-size:15px;font-weight:600;border:none;border-radius:8px;cursor:pointer;transition:all .2s;margin-top:6px;}
.btn-submit:hover{background:#2a2824;transform:translateY(-1px);box-shadow:var(--shadow-md);}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;font-weight:500;margin-bottom:18px;display:flex;align-items:flex-start;gap:8px;}
.alert-error{background:#fdecea;border:1px solid #f5b8b3;color:#a02020;}
.alert-success{background:#e8f5eb;border:1px solid #b8ddbf;color:#2e6b38;}
.bottom-link{text-align:center;margin-top:20px;font-size:13px;color:var(--muted);}
.bottom-link a{color:var(--amber-deep);text-decoration:none;font-weight:600;}
.bottom-link a:hover{text-decoration:underline;}
header{background:var(--ink);border-bottom:1px solid rgba(255,255,255,0.06);padding:14px 32px;display:flex;align-items:center;justify-content:space-between;}
header a{color:rgba(255,255,255,.6);text-decoration:none;font-size:13px;font-weight:500;}
header a:hover{color:#fff;}
.header-brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
.header-brand span{font-family:'Playfair Display',serif;font-size:18px;color:#fff;}
</style>
</head>
<body>
<header>
  <a href="index.php" class="header-brand">
    <div style="width:30px;height:30px;background:var(--amber);border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:15px;">&#128269;</div>
    <span>Lost &amp; Found</span>
  </a>
  <a href="login.php">&#8592; Back to Login</a>
</header>

<div class="page-wrap">
  <div class="card">
    <a href="index.php" class="logo">
      <div class="logo-icon">&#128269;</div>
      <div class="logo-name">Lost &amp; Found</div>
    </a>
    <h1>Create an account</h1>
    <p class="subtitle">Join the community and start reporting lost items.</p>

    <?php if ($error): ?>
      <div class="alert alert-error">&#9888; <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success">&#10003; <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="yourname" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="At least 6 characters" required>
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm" placeholder="Repeat your password" required>
      </div>
      <button type="submit" class="btn-submit">Create Account</button>
    </form>

    <div class="bottom-link">Already have an account? <a href="login.php">Log in</a></div>
  </div>
</div>
</body>
</html>
