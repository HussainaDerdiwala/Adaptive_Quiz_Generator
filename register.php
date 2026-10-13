<?php
session_start();
if(isset($_SESSION['user_id'])) header('Location: dashboard.php');
?> 
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register - Adaptive Quiz</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
  <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-sm" style="animation-delay: 0.2s;">
        <div class="card-body">
          <div class="text-center mb-4">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🎓</div>
            <h3 class="card-title">Join Us Today!</h3>
            <p class="text-muted">Create your account and start learning</p>
          </div>
          
          <form method="post" action="register_action.php" id="registerForm">
            <div class="mb-3">
              <label class="form-label">👤 Full Name</label>
              <input required name="name" type="text" class="form-control" placeholder="Enter your full name" autocomplete="name">
            </div>
            <div class="mb-3">
              <label class="form-label">📧 Email Address</label>
              <input required name="email" type="email" class="form-control" placeholder="Enter your email" autocomplete="email">
            </div>
            <div class="mb-3">
              <label class="form-label">🔒 Password</label>
              <input required name="password" type="password" class="form-control" placeholder="Choose a strong password" autocomplete="new-password" minlength="6">
              <small class="text-muted">Minimum 6 characters</small>
            </div>
            <div class="d-grid mb-3">
              <button type="submit" class="btn btn-success">
                ✨ Create Account
              </button>
            </div>
          </form>
          
          <div class="text-center">
            <hr class="my-4">
            <p class="mb-0">
              Already have an account? 
              <a href="index.php" class="fw-bold" style="text-decoration: none; background: var(--success-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                Login here →
              </a>
            </p>
          </div>
        </div>
      </div>
      
      <div class="text-center mt-4">
        <p class="text-white" style="text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
          🚀 Start your personalized learning experience
        </p>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>