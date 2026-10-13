<?php
session_start();
if(!isset($_SESSION['user_id'])) header('Location: index.php');
require 'includes/db.php';
$uid = $_SESSION['user_id'];
// Determine weak categories for adaptive selection
$catRes = $conn->query("SELECT category, SUM(correct) as s, COUNT(*) as t FROM results WHERE user_id=$uid GROUP BY category");
$weakness = [];
while($r=$catRes->fetch_assoc()){
  $pctWrong = 1 - ($r['s'] / ($r['t']?:1));
  $weakness[$r['category']] = $pctWrong;
}
// top weak categories
arsort($weakness);
$preferred = array_slice(array_keys($weakness),0,3);
// build query to fetch 10 questions prioritizing weak categories
$where = "";
if(count($preferred)>0){
  $list = implode("','", $preferred);
  $where = " AND (category IN ('$list')) ";
}
// fetch 10 prioritized questions, fallback to any if not enough
$sql = "SELECT * FROM questions WHERE 1 $where ORDER BY RAND() LIMIT 10";
$qres = $conn->query($sql);
if($qres->num_rows < 10){
  $needed = 10 - $qres->num_rows;
  $sql2 = "SELECT * FROM questions WHERE 1 AND id NOT IN (";
  $ids=[];
  while($r=$qres->fetch_assoc()) $ids[]=$r['id'];
  $qrows = [];
  if(count($ids)) $sql2 .= implode(',', $ids) . ") ORDER BY RAND() LIMIT $needed";
  else $sql2 = "SELECT * FROM questions ORDER BY RAND() LIMIT $needed";
  $qres2 = $conn->query($sql2);
  // reset cursor by requerying full prioritized set then append second set
  $qres = $conn->query("SELECT * FROM questions WHERE 1 $where ORDER BY RAND() LIMIT 10");
  $combined = [];
  while($r=$qres->fetch_assoc()) $combined[]=$r;
  while($r=$qres2->fetch_assoc()) $combined[]=$r;
} else {
  $combined = []; while($r=$qres->fetch_assoc()) $combined[]=$r;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset='utf-8'>
  <meta name='viewport' content='width=device-width,initial-scale=1'>
  <title>Quiz - Adaptive Quiz</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container py-4">
  <!-- Quiz Header -->
  <div class="card mb-4 stagger-item">
    <div class="card-body text-center">
      <div class="fs-1 mb-3">📝</div>
      <h3 class="card-title mb-2">Adaptive Quiz Challenge</h3>
      <p class="text-muted mb-0">Answer all 10 questions to test your knowledge</p>
    </div>
  </div>

  <!-- Quiz Form -->
  <div class="card stagger-item" style="animation-delay: 0.2s;">
    <div class="card-body">
      <form method="post" action="submit_quiz.php" id="quizForm">
        <?php foreach($combined as $i => $q): ?>
          <div class="question-card stagger-item" style="animation-delay: <?php echo ($i * 0.05); ?>s;">
            <div class="d-flex align-items-start mb-3">
              <span class="question-number">Q<?php echo $i+1; ?></span>
              <span class="category-badge"><?php echo htmlspecialchars($q['category']); ?></span>
            </div>
            
            <p class="fw-bold mb-3" style="font-size: 1.1rem; color: #333;">
              <?php echo htmlspecialchars($q['question']); ?>
            </p>
            
            <div class="options-container">
              <?php 
              $opts = json_decode($q['options'], true); 
              foreach($opts as $k=>$opt): 
              ?>
                <div class="form-check">
                  <input 
                    class="form-check-input" 
                    type="radio" 
                    name="q_<?php echo $q['id']; ?>" 
                    id="q_<?php echo $q['id'].'_'.$k; ?>" 
                    value="<?php echo $k; ?>"
                    required
                  >
                  <label class="form-check-label" for="q_<?php echo $q['id'].'_'.$k; ?>">
                    <strong><?php echo chr(65 + $k); ?>.</strong> <?php echo htmlspecialchars($opt); ?>
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
        
        <div class="text-center mt-4">
          <button type="submit" class="btn btn-success btn-lg px-5">
            ✅ Submit Quiz
          </button>
          <p class="text-muted small mt-3">
            💡 Make sure you've answered all questions before submitting
          </p>
        </div>
      </form>
    </div>
  </div>

  <!-- Help Card -->
  <div class="card mt-4 stagger-item" style="animation-delay: 0.5s;">
    <div class="card-body">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h6 class="mb-2">💡 Quiz Tips</h6>
          <small class="text-muted">
            • Read each question carefully • Select the best answer • You can change your answers before submitting
          </small>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
          <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
            ← Back to Dashboard
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<script>
// Smooth scroll to first unanswered question
document.querySelectorAll('.form-check-input').forEach(input => {
  input.addEventListener('change', function() {
    // Find next unanswered question
    const allQuestions = document.querySelectorAll('.question-card');
    for(let i = 0; i < allQuestions.length; i++) {
      const questionInputs = allQuestions[i].querySelectorAll('input[type="radio"]');
      const isAnswered = Array.from(questionInputs).some(inp => inp.checked);
      if(!isAnswered) {
        // Smooth scroll to next question
        setTimeout(() => {
          allQuestions[i].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 300);
        break;
      }
    }
  });
});

// Form submission validation
document.getElementById('quizForm').addEventListener('submit', function(e) {
  const allQuestions = document.querySelectorAll('.question-card');
  let unanswered = 0;
  
  allQuestions.forEach(question => {
    const inputs = question.querySelectorAll('input[type="radio"]');
    const isAnswered = Array.from(inputs).some(inp => inp.checked);
    if(!isAnswered) {
      unanswered++;
      question.style.border = '2px solid #fa709a';
      setTimeout(() => {
        question.style.border = 'none';
      }, 2000);
    }
  });
  
  if(unanswered > 0) {
    e.preventDefault();
    alert(`⚠️ Please answer all questions! ${unanswered} question(s) remaining.`);
    return false;
  }
  
  // Show loading state
  const submitBtn = this.querySelector('button[type="submit"]');
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
});
</script>
</body>
</html>