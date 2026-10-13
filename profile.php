<?php
session_start();
if(!isset($_SESSION['user_id'])) header('Location: index.php');
require 'includes/db.php';
$uid = $_SESSION['user_id'];
// fetch quizzes
$q = $conn->query("SELECT * FROM quizzes WHERE user_id=$uid ORDER BY created_at DESC LIMIT 10");
$quizzes = [];
while($r=$q->fetch_assoc()) $quizzes[]=$r;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset='utf-8'>
  <meta name='viewport' content='width=device-width,initial-scale=1'>
  <title>Profile - Adaptive Quiz</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container py-4">
  <!-- Profile Header -->
  <div class="card mb-4 stagger-item">
    <div class="card-body">
      <div class="row align-items-center">
        <div class="col-md-2 text-center">
          <div style="font-size: 5rem;">👤</div>
        </div>
        <div class="col-md-10">
          <h3 class="card-title mb-2"><?php echo htmlspecialchars($_SESSION['name']); ?></h3>
          <p class="text-muted mb-3">Active Learner • Member since registration</p>
          <div class="row g-3">
            <div class="col-md-4">
              <div class="stat-card">
                <div class="stat-value"><?php echo count($quizzes); ?></div>
                <div class="stat-label">📝 Quizzes Taken</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stat-card">
                <div class="stat-value">
                  <?php 
                  $avgScore = 0;
                  if(count($quizzes) > 0) {
                    $totalScore = 0;
                    foreach($quizzes as $q) $totalScore += $q['score'];
                    $avgScore = round($totalScore / count($quizzes), 1);
                  }
                  echo $avgScore;
                  ?>%
                </div>
                <div class="stat-label">📊 Average Score</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stat-card">
                <div class="stat-value">
                  <?php 
                  $bestScore = 0;
                  foreach($quizzes as $q) {
                    if($q['score'] > $bestScore) $bestScore = $q['score'];
                  }
                  echo $bestScore;
                  ?>%
                </div>
                <div class="stat-label">🏆 Best Score</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quiz History -->
  <div class="card stagger-item" style="animation-delay: 0.2s;">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">📜 Quiz History</h5>
        <a href="quiz.php" class="btn btn-primary btn-sm">
          ➕ Take New Quiz
        </a>
      </div>

      <?php if(!empty($quizzes)): ?>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th width="60">#</th>
                <th width="100">Score</th>
                <th>Performance Summary</th>
                <th width="180">Date & Time</th>
                <th width="80">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($quizzes as $i=>$qq): ?>
                <tr class="stagger-item" style="animation-delay: <?php echo (0.3 + $i * 0.05); ?>s;">
                  <td>
                    <span class="badge bg-secondary"><?php echo $i+1; ?></span>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div style="font-size: 1.5rem; margin-right: 8px;">
                        <?php 
                        if($qq['score'] >= 90) echo "🏆";
                        elseif($qq['score'] >= 75) echo "🌟";
                        elseif($qq['score'] >= 60) echo "👍";
                        else echo "📚";
                        ?>
                      </div>
                      <strong style="font-size: 1.2rem; color: <?php 
                        if($qq['score'] >= 75) echo '#43e97b';
                        elseif($qq['score'] >= 50) echo '#f5af19';
                        else echo '#fa709a';
                      ?>;">
                        <?php echo $qq['score']; ?>%
                      </strong>
                    </div>
                  </td>
                  <td>
                    <small class="text-muted">
                      <?php echo htmlspecialchars($qq['summary']); ?>
                    </small>
                  </td>
                  <td>
                    <small>
                      <?php 
                      $date = new DateTime($qq['created_at']);
                      echo $date->format('M d, Y');
                      ?>
                      <br>
                      <span class="text-muted">
                        <?php echo $date->format('h:i A'); ?>
                      </span>
                    </small>
                  </td>
                  <td>
                    <?php if($qq['score'] >= 75): ?>
                      <span class="badge bg-success">Passed</span>
                    <?php elseif($qq['score'] >= 50): ?>
                      <span class="badge bg-warning">Average</span>
                    <?php else: ?>
                      <span class="badge bg-danger">Review</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination Info -->
        <div class="text-center mt-3">
          <small class="text-muted">
            Showing <?php echo count($quizzes); ?> most recent quiz<?php echo count($quizzes) != 1 ? 'zes' : ''; ?>
          </small>
        </div>
      <?php else: ?>
        <div class="text-center py-5">
          <div style="font-size: 4rem; margin-bottom: 1rem;">📚</div>
          <h5 class="mb-3">No Quiz History Yet</h5>
          <p class="text-muted mb-4">Start your learning journey by taking your first quiz!</p>
          <a href="quiz.php" class="btn btn-primary btn-lg">
            🚀 Take Your First Quiz
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Actions -->
  <div class="row g-3 mt-3">
    <div class="col-md-6">
      <div class="card stagger-item" style="animation-delay: 0.4s;">
        <div class="card-body text-center">
          <div class="fs-1 mb-3">📊</div>
          <h6 class="mb-2">View Dashboard</h6>
          <p class="text-muted small mb-3">Check your overall performance and analytics</p>
          <a href="dashboard.php" class="btn btn-outline-primary w-100">
            Go to Dashboard
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card stagger-item" style="animation-delay: 0.5s;">
        <div class="card-body text-center">
          <div class="fs-1 mb-3">🔄</div>
          <h6 class="mb-2">Practice More</h6>
          <p class="text-muted small mb-3">Keep improving with adaptive quizzes</p>
          <a href="quiz.php" class="btn btn-outline-success w-100">
            Start New Quiz
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<script>
// Add hover effect to table rows
document.querySelectorAll('.table tbody tr').forEach(row => {
  row.addEventListener('mouseenter', function() {
    this.style.transform = 'scale(1.01)';
    this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
  });
  
  row.addEventListener('mouseleave', function() {
    this.style.transform = 'scale(1)';
    this.style.boxShadow = 'none';
  });
});

// Add click effect
document.querySelectorAll('.table tbody tr').forEach(row => {
  row.style.cursor = 'pointer';
  row.addEventListener('click', function() {
    this.style.background = 'rgba(102, 126, 234, 0.1)';
    setTimeout(() => {
      this.style.background = '';
    }, 300);
  });
});
</script>
</body>
</html>