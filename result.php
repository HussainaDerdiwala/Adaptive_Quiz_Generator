<?php
session_start();
if(!isset($_SESSION['user_id'])) header('Location: index.php');
require 'includes/db.php';
$score = isset($_GET['score'])?floatval($_GET['score']):0;
$uid = $_SESSION['user_id'];
// compute weaknesses by category
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
  <title>Quiz Results - Adaptive Quiz</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container py-4">
  <!-- Score Display Card -->
  <div class="card mb-4 stagger-item">
    <div class="card-body">
      <div class="score-display">
        <h2 class="mb-4">🎉 Quiz Completed!</h2>
        
        <div class="score-circle">
          <div class="score-number"><?php echo $score; ?>%</div>
        </div>
        
        <h4 class="mb-2">
          <?php 
          if($score >= 90) echo "🏆 Outstanding Performance!";
          elseif($score >= 75) echo "🌟 Great Job!";
          elseif($score >= 60) echo "👍 Good Effort!";
          else echo "💪 Keep Practicing!";
          ?>
        </h4>
        
        <p class="text-muted">
          <?php 
          if($score >= 90) echo "You're mastering this material!";
          elseif($score >= 75) echo "You're doing really well!";
          elseif($score >= 60) echo "You're on the right track!";
          else echo "Review the material and try again!";
          ?>
        </p>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Performance Breakdown -->
    <div class="col-lg-8">
      <!-- Category Performance List -->
      <div class="card mb-4 stagger-item" style="animation-delay: 0.2s;">
        <div class="card-body">
          <h5 class="mb-4">📊 Category Performance Breakdown</h5>
          
          <?php if(!empty($cats)): ?>
            <div class="list-group">
              <?php 
              foreach($cats as $index => $c){
                $accuracy = round(100 * ($c['s']/($c['t']?:1)), 1);
                $wrong = round(100 * (1 - ($c['s']/($c['t']?:1))), 1);
                $color = $accuracy >= 75 ? 'success' : ($accuracy >= 50 ? 'warning' : 'danger');
              ?>
                <div class="list-group-item stagger-item" style="animation-delay: <?php echo (0.3 + $index * 0.1); ?>s;">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong><?php echo htmlspecialchars($c['category']); ?></strong>
                    <span class="badge bg-<?php echo $color; ?>"><?php echo $accuracy; ?>% Correct</span>
                  </div>
                  <div class="progress" style="height: 25px;">
                    <div class="progress-bar bg-<?php echo $color; ?>" role="progressbar" 
                         style="width: <?php echo $accuracy; ?>%;" 
                         aria-valuenow="<?php echo $accuracy; ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                      <?php echo $c['s']; ?>/<?php echo $c['t']; ?> questions
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
          <?php else: ?>
            <div class="text-center py-4">
              <div class="fs-1 mb-3">📚</div>
              <p class="text-muted">No detailed category data available yet.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Radar Chart -->
      <?php if(!empty($cats)): ?>
      <div class="card stagger-item" style="animation-delay: 0.4s;">
        <div class="card-body">
          <h5 class="mb-4">📈 Performance Radar</h5>
          <div class="chart-container">
            <canvas id="chart" height="80"></canvas>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Actions Sidebar -->
    <div class="col-lg-4">
      <!-- Quick Actions -->
      <div class="card mb-4 stagger-item" style="animation-delay: 0.5s;">
        <div class="card-body">
          <h6 class="mb-3">🎯 What's Next?</h6>
          
          <a href="quiz.php" class="btn btn-primary w-100 mb-3">
            <div class="fs-4 mb-1">🔄</div>
            <div class="fw-bold">Take Another Quiz</div>
            <small class="d-block opacity-75">Practice makes perfect</small>
          </a>
          
          <a href="dashboard.php" class="btn btn-success w-100 mb-3">
            <div class="fs-4 mb-1">📊</div>
            <div class="fw-bold">View Dashboard</div>
            <small class="d-block opacity-75">Check your progress</small>
          </a>
          
          <a href="profile.php" class="btn btn-outline-primary w-100">
            <div class="fs-4 mb-1">📜</div>
            <div class="fw-bold">Quiz History</div>
            <small class="d-block opacity-75">Review past attempts</small>
          </a>
        </div>
      </div>

      <!-- Recommendations -->
      <div class="card stagger-item" style="animation-delay: 0.6s;">
        <div class="card-body">
          <h6 class="mb-3">💡 Recommendations</h6>
          
          <?php if(!empty($cats)): ?>
            <?php
            // Find weakest category
            $weakest = null;
            $lowestAccuracy = 100;
            foreach($cats as $c) {
              $acc = 100 * ($c['s']/($c['t']?:1));
              if($acc < $lowestAccuracy) {
                $lowestAccuracy = $acc;
                $weakest = $c['category'];
              }
            }
            ?>
            
            <div class="alert alert-info mb-3">
              <strong>Focus Area:</strong><br>
              <small>Work on <strong><?php echo htmlspecialchars($weakest); ?></strong> to improve your overall score.</small>
            </div>
          <?php endif; ?>
          
          <ul class="list-unstyled mb-0 small">
            <li class="mb-2">✓ Review incorrect answers</li>
            <li class="mb-2">✓ Practice weak categories</li>
            <li class="mb-2">✓ Take quizzes regularly</li>
            <li class="mb-0">✓ Track your progress</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<script>
// Radar Chart for Category Weaknesses
const cats = <?php echo json_encode($cats); ?>;
if(cats && cats.length > 0) {
  const labels = cats.map(c => c.category);
  const accuracyData = cats.map(c => Math.round(100 * (c.s/(c.t||1))));
  
  const ctx = document.getElementById('chart').getContext('2d');
  
  // Create gradient
  const gradient = ctx.createLinearGradient(0, 0, 0, 400);
  gradient.addColorStop(0, 'rgba(79, 172, 254, 0.6)');
  gradient.addColorStop(1, 'rgba(0, 242, 254, 0.6)');
  
  new Chart(ctx, {
    type: 'radar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Accuracy %',
        data: accuracyData,
        backgroundColor: gradient,
        borderColor: '#4facfe',
        borderWidth: 3,
        pointBackgroundColor: '#4facfe',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7
      }]
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
          callbacks: {
            label: function(context) {
              return context.dataset.label + ': ' + context.parsed.r + '%';
            }
          }
        }
      },
      scales: {
        r: {
          beginAtZero: true,
          max: 100,
          ticks: {
            stepSize: 20,
            callback: function(value) {
              return value + '%';
            }
          },
          grid: {
            color: 'rgba(0,0,0,0.1)'
          },
          angleLines: {
            color: 'rgba(0,0,0,0.1)'
          }
        }
      }
    }
  });
}

// Animate progress bars
document.addEventListener('DOMContentLoaded', function() {
  const progressBars = document.querySelectorAll('.progress-bar');
  progressBars.forEach((bar, index) => {
    const width = bar.style.width;
    bar.style.width = '0%';
    setTimeout(() => {
      bar.style.width = width;
    }, 500 + (index * 200));
  });
});
</script>
</body>
</html>