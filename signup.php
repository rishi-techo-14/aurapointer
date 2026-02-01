<?php
session_start();
$conn = new mysqli("localhost", "root", "", "coding");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle signup form submission
$signup_error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0) {
        $signup_error = "Email already registered. Please login.";
    } else {
        // Insert new user
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt2 = $conn->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
        $stmt2->bind_param("sss", $name, $email, $password_hash);
        if($stmt2->execute()) {
            $_SESSION['user_id'] = $stmt2->insert_id;
            $_SESSION['user_name'] = $name;
            header("Location: home2.php");
            exit();
        } else {
            $signup_error = "Error: " . $stmt2->error;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Sign Up | ptrmaster</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

<style>
/* =====================
    HEADER CSS (Home style)
   ===================== */
header {
    position: sticky;
    top: 0;
    width: 100%;
    background: #fff;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    z-index: 1000;
}
.nav-container {
    max-width: 1200px;
    margin: auto;
    padding: 18px 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.logo a {
    font-family: "Space Grotesk", sans-serif;
    font-size: 22px;
    font-weight: 700;
    text-decoration: none;
    color: #000;
}
nav.nav-links { display: flex; gap: 34px; }
nav.nav-links a {
    font-size: 14px;
    color: #444;
    text-decoration: none;
    font-weight: 500;
}
.header-right { display: flex; align-items: center; gap: 20px; }

/* Theme Toggle Slider */
.theme-switch { position: relative; display: inline-block; width: 44px; height: 22px; }
.theme-switch input { opacity: 0; width: 0; height: 0; }
.slider {
    position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
    background-color: #ccc; transition: 0.4s; border-radius: 34px;
}
.slider:before {
    position: absolute; content: ""; height: 16px; width: 16px;
    left: 3px; bottom: 3px; background-color: white; transition: 0.4s; border-radius: 50%;
}
input:checked + .slider { background-color: #333; }
input:checked + .slider:before { transform: translateX(22px); }

/* Dark Theme Header Overrides */
.dark-theme header { background-color: #0b0b0b; border-bottom: 1px solid #222; }
.dark-theme .logo a, .dark-theme nav.nav-links a { color: #fff; }

/* =====================
    ORIGINAL SIGNUP STYLES
   ===================== */
* { margin:0; padding:0; box-sizing:border-box; font-family:"Inter",sans-serif; }
body { 
    min-height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:flex-start;
    background: linear-gradient(rgba(255,255,255,0.85), rgba(255,255,255,0.85)), url('pat3.png') center/cover no-repeat fixed;
    color:#0f172a; 
    transition: background 0.3s ease;
}

/* Dark mode body override */
.dark-theme { 
    background: linear-gradient(rgba(11,11,11,0.95), rgba(11,11,11,0.95)), url('pat3.png') center/cover no-repeat fixed;
    color: #eee;
}

.login-wrapper { display:flex; justify-content:center; width:100%; margin-top:50px; }
.login-card { background:#fff; width:360px; padding:32px; border-radius:14px; border:1px solid #e5e7eb; box-shadow:0 10px 30px rgba(0,0,0,0.08); transition: background 0.3s ease; }
.dark-theme .login-card { background: #111; border-color: #222; color: #fff; }

.login-card h2 { text-align:center; margin-bottom:24px; font-weight:600; }
.input-group { margin-bottom:16px; }
.input-group label { font-size:14px; margin-bottom:6px; display:block; color:#475569; }
.dark-theme .input-group label { color: #aaa; }
.input-group input { width:100%; padding:12px; border-radius:8px; border:1px solid #d1d5db; outline:none; }
.dark-theme .input-group input { background: #1a1a1a; color: #fff; border-color: #333; }
.input-group input:focus { border-color:#000; }

.login-btn { width:100%; padding:12px; margin-top:10px; background:#000; color:#fff; border:none; border-radius:8px; font-size:15px; cursor:pointer; }
.dark-theme .login-btn { background: #fff; color: #000; }
.login-btn:hover { background:#111; }

.divider { text-align:center; color:#6b7280; margin:20px 0; font-size:14px; }
.social-login { display:flex; flex-direction:column; gap:10px; }
.social-btn { display:flex; align-items:center; justify-content:center; gap:10px; padding:10px; border-radius:8px; background:#fff; border:1px solid #d1d5db; cursor:pointer; text-decoration: none; color: #000; transition: 0.2s; }
.dark-theme .social-btn { background: #111; color: #fff; border-color: #333; }
.social-btn:hover { transform:translateY(-1px); box-shadow:0 6px 14px rgba(0,0,0,0.1); }

.signup-text { text-align:center; margin-top:20px; font-size:14px; color:#475569; }
.dark-theme .signup-text { color: #aaa; }
.signup-text a { color:#000; font-weight:500; text-decoration:none; }
.dark-theme .signup-text a { color: #fff; }
.signup-text a:hover { text-decoration:underline; }
.error-msg { color:red; text-align:center; margin-bottom:12px; font-size:14px; }
</style>
</head>

<body>

<header>
  <div class="nav-container">
    <div class="logo"><a href="home2.php">ptrmaster</a></div>
    <nav class="nav-links">
      <a href="home2.php">Home</a>
      <a href="mycourses.php">Courses</a>
      <a href="about.php">About</a>
    </nav>
    <div class="header-right">
      <label class="theme-switch">
        <input type="checkbox" id="theme-toggle" />
        <span class="slider"></span>
      </label>
    </div>
  </div>
</header>

<div class="login-wrapper">
  <div class="login-card">
    <h2>Create Account</h2>

    <?php if($signup_error): ?>
      <div class="error-msg"><?= htmlspecialchars($signup_error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="input-group">
        <label>Name</label>
        <input type="text" name="name" placeholder="Your name" required />
      </div>

      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" placeholder="Email address" required />
      </div>

      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Create a strong password" required />
      </div>

      <button class="login-btn" type="submit">Sign Up</button>
    </form>

    <div class="divider">or sign up with</div>

    <div class="social-login">
      <a class="social-btn" href="google_oauth.php"><i class="fa-brands fa-google"></i> Google</a>
      <a class="social-btn" href="github_oauth.php"><i class="fa-brands fa-github"></i> GitHub</a>
    </div>

    <div class="signup-text">
      Already have an account? <a href="login.php">Login</a>
    </div>
  </div>
</div>

<script>
  const toggle = document.getElementById('theme-toggle');
  
  // Persistence logic
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-theme');
    if(toggle) toggle.checked = true;
  }

  if(toggle) {
    toggle.addEventListener('change', () => {
      if (toggle.checked) {
        document.body.classList.add('dark-theme');
        localStorage.setItem('theme', 'dark');
      } else {
        document.body.classList.remove('dark-theme');
        localStorage.setItem('theme', 'light');
      }
    });
  }
</script>

</body>
</html>