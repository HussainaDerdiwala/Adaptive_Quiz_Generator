<?php
// Enhanced header with modern navigation
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">
      <span style="font-size: 1.5rem;">🎯</span> Adaptive Quiz
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if(isset($_SESSION['user_id'])): ?>
        <li class="nav-item"><a class="nav-link" href="dashboard.php">📊 Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="quiz.php">📝 Take Quiz</a></li>
        <li class="nav-item"><a class="nav-link" href="profile.php">👤 Profile</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">🚪 Logout</a></li>
        <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="index.php">🔐 Login</a></li>
        <li class="nav-item"><a class="nav-link" href="register.php">📝 Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>