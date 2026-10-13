<?php
session_start();
if(!isset($_SESSION['user_id'])) header('Location: index.php');
require 'includes/db.php';
$uid = $_SESSION['user_id'];
// compute stats
$totq = $conn->query("SELECT COUNT(*) as c FROM results WHERE user_id=$uid")->fetch_assoc()['c'];
$accuracy = 0;
$res = $conn->query("SELECT SUM(correct) as s, COUNT(*) as t FROM results WHERE user_id=$uid")->fetch_assoc();
if($res['t']>0) $accuracy = round(100 * $res['s'] / $res['t'],1);
// category weaknesses
$catRes = $conn->query("SELECT category, SUM(correct) as s, COUNT(*) as t FROM results WHERE user_id=$uid GROUP BY category");
$cats=[];
while($r=$catRes->fetch_assoc()){
  $cats[]=$r;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset='utf-8'>
  <meta name='viewport' content='width=device-width,initial-scale=1'>
  <title>Dashboard - Adaptive Quiz</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container py-4">
  <!-- Welcome Banner -->
  <div class="welcome-banner">
    <h2>👋 Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
    <p>Ready to continue your learning journey? Let's track your progress.</p>
  </div>

  <div class="row g-4">
    <!-- Main Content -->
    <div class="col-lg-8">
      <!-- Stats Cards -->
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <div class="stat-card stagger-item">
            <div class="stat-value"><?php echo $totq; ?></div>
            <div class="stat-label">📝 Questions Answered</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="stat-card stagger-item" style="animation-delay: 0.1s;">
            <div class="stat-value"><?php echo $accuracy; ?>%</div>
            <div class="stat-label">🎯 Overall Accuracy</div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card mb-4 stagger-item" style="animation-delay: 0.2s;">
        <div class="card-body">
          <h5 class="mb-3">🚀 Quick Actions</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <a href="quiz.php" class="btn btn-primary w-100 py-3">
                <div class="fs-4 mb-1">📝</div>
                <div class="fw-bold">Start New Quiz</div>
                <small class="d-block opacity-75">Test your knowledge</small>
              </a>
            </div>
            <div class="col-md-6">
              <a href="profile.php" class="btn btn-success w-100 py-3">
                <div class="fs-4 mb-1">📊</div>
                <div class="fw-bold">View History</div>
                <small class="d-block opacity-75">Check past results</small>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Category Performance Chart -->
      <div class="card stagger-item" style="animation-delay: 0.3s;">
        <div class="card-body">
          <h5 class="mb-4">📈 Category Performance Analysis</h5>
          <div class="chart-container">
            <canvas id="catChart" height="100"></canvas>
          </div>
          <?php if(empty($cats)): ?>
            <div class="text-center py-4">
              <div class="fs-1 mb-3">📚</div>
              <p class="text-muted">No performance data yet. Take your first quiz to see your stats!</p>
              <a href="quiz.php" class="btn btn-primary mt-2">Take First Quiz</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
      <!-- Profile Card -->
      <div class="card mb-4 stagger-item" style="animation-delay: 0.4s;">
        <div class="card-body text-center">
          <div class="mb-3" style="font-size: 4rem;">👤</div>
          <h5 class="mb-1"><?php echo htmlspecialchars($_SESSION['name']); ?></h5>
          <p class="text-muted small mb-3">Active Learner</p>
          <a href="profile.php" class="btn btn-outline-primary btn-sm w-100 mb-2">
            👁️ View Full Profile
          </a>
          <a href="logout.php" class="btn btn-outline-danger btn-sm w-100">
            🚪 Logout
          </a>
        </div>
      </div>

      <!-- Tips Card -->
      <div class="card stagger-item" style="animation-delay: 0.5s;">
        <div class="card-body">
          <h6 class="mb-3">💡 Learning Tips</h6>
          <ul class="list-unstyled mb-0">
            <li class="mb-2">
              <small>✓ Take quizzes regularly to improve retention</small>
            </li>
            <li class="mb-2">
              <small>✓ Focus on your weak categories</small>
            </li>
            <li class="mb-2">
              <small>✓ Review your past quiz results</small>
            </li>
            <li class="mb-0">
              <small>✓ Challenge yourself with harder questions</small>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<script>
// Category Performance Chart
const cats = <?php echo json_encode($cats); ?>;
if(cats && cats.length > 0) {
  const labels = cats.map(c => c.category);
  const weaknessData = cats.map(c => Math.round(100 * (1 - (c.s/(c.t||1)))));
  const accuracyData = cats.map(c => Math.round(100 * (c.s/(c.t||1))));
  
  const ctx = document.getElementById('catChart').getContext('2d');
  
  // Create gradient
  const gradient1 = ctx.createLinearGradient(0, 0, 0, 400);
  gradient1.addColorStop(0, 'rgba(102, 126, 234, 0.8)');
  gradient1.addColorStop(1, 'rgba(118, 75, 162, 0.8)');
  
  const gradient2 = ctx.createLinearGradient(0, 0, 0, 400);
  gradient2.addColorStop(0, 'rgba(79, 172, 254, 0.8)');
  gradient2.addColorStop(1, 'rgba(0, 242, 254, 0.8)');
  
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Accuracy (%)',
          data: accuracyData,
          backgroundColor: gradient2,
          borderColor: '#4facfe',
          borderWidth: 2,
          borderRadius: 8
        },
        {
          label: 'Weakness (%)',
          data: weaknessData,
          backgroundColor: gradient1,
          borderColor: '#667eea',
          borderWidth: 2,
          borderRadius: 8
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          position: 'top',
          labels: {
            usePointStyle: true,
            padding: 15,
            font: {
              size: 12,
              weight: 'bold'
            }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0,0,0,0.8)',
          padding: 12,
          borderRadius: 8,
          titleFont: {
            size: 14,
            weight: 'bold'
          },
          bodyFont: {
            size: 13
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          max: 100,
          grid: {
            color: 'rgba(0,0,0,0.05)'
          },
          ticks: {
            callback: function(value) {
              return value + '%';
            }
          }
        },
        x: {
          grid: {
            display: false
          }
        }
      }
    }
  });
}
</script>
</body>
</html>