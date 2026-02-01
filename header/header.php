<header class="nav-container">
  <div class="logo">
    <a href="home2.php">ptrmaster</a>
  </div>

  <nav class="nav">
    <a href="mycourses.php">My Courses</a>
    <a href="#">Practice</a>
    <a href="#">About</a>
  </nav>

  <div class="header-right">
    <?php if (isset($_SESSION['user_id'])): ?>
      <a class="profile" href="profile.php">
        <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
      </a>
    <?php else: ?>
      <a class="login" href="login.php">Login</a>
    <?php endif; ?>

    <!-- Theme toggle -->
    <label class="theme-switch">
      <input type="checkbox" id="theme-toggle" />
      <span class="slider"></span>
    </label>
  </div>
</header>
