// ============================================
// ADAPTIVE QUIZ - INTERACTIVE JAVASCRIPT
// ============================================

document.addEventListener('DOMContentLoaded', function() {
  
  // Initialize all interactive features
  initParticles();
  initFormValidation();
  initTooltips();
  initAnimations();
  initQuizFeatures();
  initChartEnhancements();
  
});

// ============================================
// FLOATING PARTICLES BACKGROUND
// ============================================
function initParticles() {
  const particleCount = 20;
  const body = document.body;
  
  for (let i = 0; i < particleCount; i++) {
    const particle = document.createElement('div');
    particle.className = 'particle';
    
    const size = Math.random() * 60 + 20;
    particle.style.width = size + 'px';
    particle.style.height = size + 'px';
    particle.style.left = Math.random() * 100 + '%';
    particle.style.top = Math.random() * 100 + '%';
    particle.style.animationDelay = Math.random() * 20 + 's';
    particle.style.animationDuration = (Math.random() * 10 + 15) + 's';
    
    body.appendChild(particle);
  }
}

// ============================================
// FORM VALIDATION & INTERACTIVITY
// ============================================
function initFormValidation() {
  const forms = document.querySelectorAll('form');
  
  forms.forEach(form => {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
      // Real-time validation
      input.addEventListener('blur', function() {
        validateInput(this);
      });
      
      input.addEventListener('input', function() {
        if (this.classList.contains('is-invalid')) {
          validateInput(this);
        }
      });
      
      // Add focus animation
      input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
        this.parentElement.style.transition = 'transform 0.3s ease';
      });
      
      input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
      });
    });
    
    // Form submission with loading state
    form.addEventListener('submit', function(e) {
      let isValid = true;
      
      inputs.forEach(input => {
        if (!validateInput(input)) {
          isValid = false;
        }
      });
      
      if (isValid) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        }
      } else {
        e.preventDefault();
        showNotification('Please fill all required fields correctly', 'error');
      }
    });
  });
}

function validateInput(input) {
  const value = input.value.trim();
  let isValid = true;
  let errorMessage = '';
  
  if (input.hasAttribute('required') && !value) {
    isValid = false;
    errorMessage = 'This field is required';
  } else if (input.type === 'email' && value) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
      isValid = false;
      errorMessage = 'Please enter a valid email';
    }
  } else if (input.type === 'password' && value && value.length < 6) {
    isValid = false;
    errorMessage = 'Password must be at least 6 characters';
  }
  
  if (!isValid) {
    input.classList.add('is-invalid');
    input.classList.remove('is-valid');
    
    let feedback = input.nextElementSibling;
    if (!feedback || !feedback.classList.contains('invalid-feedback')) {
      feedback = document.createElement('div');
      feedback.className = 'invalid-feedback';
      input.parentNode.appendChild(feedback);
    }
    feedback.textContent = errorMessage;
  } else if (value) {
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
  }
  
  return isValid;
}

// ============================================
// NOTIFICATION SYSTEM
// ============================================
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `alert alert-${type} notification-toast`;
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
    animation: slideInRight 0.5s ease-out;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
  `;
  notification.textContent = message;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.style.animation = 'slideOutRight 0.5s ease-out';
    setTimeout(() => notification.remove(), 500);
  }, 3000);
}

// ============================================
// TOOLTIPS
// ============================================
function initTooltips() {
  const tooltipElements = document.querySelectorAll('[data-tooltip]');
  
  tooltipElements.forEach(element => {
    element.classList.add('tooltip-custom');
    
    const tooltipText = document.createElement('span');
    tooltipText.className = 'tooltip-text';
    tooltipText.textContent = element.getAttribute('data-tooltip');
    
    element.appendChild(tooltipText);
  });
}

// ============================================
// SCROLL ANIMATIONS
// ============================================
function initAnimations() {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);
  
  const animatedElements = document.querySelectorAll('.card, .stat-card, .question-card');
  animatedElements.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
    observer.observe(el);
  });
}

// ============================================
// QUIZ SPECIFIC FEATURES
// ============================================
function initQuizFeatures() {
  // Quiz progress tracking
  const quizForm = document.querySelector('form[action="submit_quiz.php"]');
  if (quizForm) {
    initQuizProgress(quizForm);
    initQuestionTracking(quizForm);
    initQuizTimer();
  }
  
  // Radio button enhancement
  const radioInputs = document.querySelectorAll('.form-check-input[type="radio"]');
  radioInputs.forEach(radio => {
    radio.addEventListener('change', function() {
      // Remove selection from siblings
      const name = this.name;
      document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
        r.closest('.form-check').style.background = 'rgba(255,255,255,0.5)';
        r.closest('.form-check').style.borderLeft = 'none';
      });
      
      // Highlight selected
      this.closest('.form-check').style.background = 'rgba(102, 126, 234, 0.15)';
      this.closest('.form-check').style.borderLeft = '4px solid #667eea';
      this.closest('.form-check').style.transition = 'all 0.3s ease';
    });
  });
}

function initQuizProgress(form) {
  const questions = form.querySelectorAll('[name^="q_"]');
  const questionGroups = {};
  
  questions.forEach(q => {
    const name = q.name;
    if (!questionGroups[name]) {
      questionGroups[name] = [];
    }
    questionGroups[name].push(q);
  });
  
  const totalQuestions = Object.keys(questionGroups).length;
  
  // Create progress bar
  const progressContainer = document.createElement('div');
  progressContainer.className = 'mb-4';
  progressContainer.innerHTML = `
    <div class="d-flex justify-content-between mb-2">
      <span class="fw-bold">Progress</span>
      <span id="progress-text">0/${totalQuestions} answered</span>
    </div>
    <div class="progress">
      <div id="quiz-progress-bar" class="progress-bar" role="progressbar" style="width: 0%">0%</div>
    </div>
  `;
  
  form.insertBefore(progressContainer, form.firstChild);
  
  // Update progress on answer selection
  Object.values(questionGroups).forEach(group => {
    group.forEach(radio => {
      radio.addEventListener('change', updateProgress);
    });
  });
  
  function updateProgress() {
    let answered = 0;
    Object.keys(questionGroups).forEach(name => {
      if (document.querySelector(`input[name="${name}"]:checked`)) {
        answered++;
      }
    });
    
    const percentage = Math.round((answered / totalQuestions) * 100);
    const progressBar = document.getElementById('quiz-progress-bar');
    const progressText = document.getElementById('progress-text');
    
    progressBar.style.width = percentage + '%';
    progressBar.textContent = percentage + '%';
    progressText.textContent = `${answered}/${totalQuestions} answered`;
    
    // Enable submit button when all answered
    const submitBtn = form.querySelector('button[type="submit"]');
    if (answered === totalQuestions) {
      submitBtn.classList.add('pulse-animation');
      showNotification('All questions answered! Ready to submit.', 'success');
    }
  }
}

function initQuestionTracking(form) {
  const questions = form.querySelectorAll('[name^="q_"]');
  const questionDivs = [];
  
  questions.forEach(q => {
    const questionDiv = q.closest('.mb-3, .question-card');
    if (questionDiv && !questionDivs.includes(questionDiv)) {
      questionDivs.push(questionDiv);
    }
  });
  
  // Add stagger animation
  questionDivs.forEach((div, index) => {
    div.classList.add('stagger-item');
    div.style.animationDelay = (index * 0.1) + 's';
  });
}

function initQuizTimer() {
  const quizContainer = document.querySelector('form[action="submit_quiz.php"]');
  if (!quizContainer) return;
  
  const timerDiv = document.createElement('div');
  timerDiv.className = 'alert alert-info mb-3';
  timerDiv.innerHTML = `
    <div class="d-flex justify-content-between align-items-center">
      <span><strong>⏱️ Time Elapsed:</strong></span>
      <span id="timer" class="fs-5 fw-bold">00:00</span>
    </div>
  `;
  
  quizContainer.insertBefore(timerDiv, quizContainer.firstChild);
  
  let seconds = 0;
  const timerElement = document.getElementById('timer');
  
  setInterval(() => {
    seconds++;
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    timerElement.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
  }, 1000);
}

// ============================================
// CHART ENHANCEMENTS
// ============================================
function initChartEnhancements() {
  // Add gradient colors to charts
  if (typeof Chart !== 'undefined') {
    Chart.defaults.color = '#666';
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    
    // Override chart creation to add gradients
    const originalChartConstructor = Chart;
    window.Chart = function(ctx, config) {
      if (config.type === 'bar' || config.type === 'line') {
        const canvas = ctx.canvas;
        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
        gradient.addColorStop(0, 'rgba(102, 126, 234, 0.8)');
        gradient.addColorStop(1, 'rgba(118, 75, 162, 0.8)');
        
        if (config.data.datasets[0]) {
          config.data.datasets[0].backgroundColor = gradient;
          config.data.datasets[0].borderColor = '#667eea';
          config.data.datasets[0].borderWidth = 2;
        }
      }
      
      return new originalChartConstructor(ctx, config);
    };
  }
}

// ============================================
// SCORE ANIMATION
// ============================================
function animateScore(targetScore) {
  const scoreElement = document.querySelector('.score-number');
  if (!scoreElement) return;
  
  let currentScore = 0;
  const increment = targetScore / 50;
  const duration = 2000;
  const stepTime = duration / 50;
  
  const timer = setInterval(() => {
    currentScore += increment;
    if (currentScore >= targetScore) {
      currentScore = targetScore;
      clearInterval(timer);
      
      // Celebration effect
      createConfetti();
    }
    scoreElement.textContent = Math.round(currentScore) + '%';
  }, stepTime);
}

// ============================================
// CONFETTI EFFECT
// ============================================
function createConfetti() {
  const colors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe'];
  const confettiCount = 50;
  
  for (let i = 0; i < confettiCount; i++) {
    const confetti = document.createElement('div');
    confetti.style.cssText = `
      position: fixed;
      width: 10px;
      height: 10px;
      background: ${colors[Math.floor(Math.random() * colors.length)]};
      top: -10px;
      left: ${Math.random() * 100}%;
      opacity: 1;
      transform: rotate(${Math.random() * 360}deg);
      z-index: 9999;
      pointer-events: none;
    `;
    
    document.body.appendChild(confetti);
    
    const fallDuration = Math.random() * 3 + 2;
    const fallDistance = Math.random() * window.innerHeight + window.innerHeight;
    const sway = (Math.random() - 0.5) * 200;
    
    confetti.animate([
      { 
        transform: `translateY(0) translateX(0) rotate(0deg)`,
        opacity: 1
      },
      { 
        transform: `translateY(${fallDistance}px) translateX(${sway}px) rotate(${Math.random() * 720}deg)`,
        opacity: 0
      }
    ], {
      duration: fallDuration * 1000,
      easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
    });
    
    setTimeout(() => confetti.remove(), fallDuration * 1000);
  }
}

// ============================================
// TABLE INTERACTIONS
// ============================================
function initTableInteractions() {
  const tables = document.querySelectorAll('.table');
  
  tables.forEach(table => {
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach((row, index) => {
      row.style.animationDelay = (index * 0.05) + 's';
      row.classList.add('stagger-item');
      
      row.addEventListener('click', function() {
        this.style.transform = 'scale(1.02)';
        setTimeout(() => {
          this.style.transform = 'scale(1)';
        }, 200);
      });
    });
  });
}

// Initialize table interactions
document.addEventListener('DOMContentLoaded', initTableInteractions);

// ============================================
// SMOOTH SCROLL
// ============================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  });
});

// ============================================
// NAVBAR SCROLL EFFECT
// ============================================
let lastScroll = 0;
window.addEventListener('scroll', () => {
  const navbar = document.querySelector('.navbar');
  if (!navbar) return;
  
  const currentScroll = window.pageYOffset;
  
  if (currentScroll > 50) {
    navbar.style.boxShadow = '0 5px 30px rgba(0,0,0,0.15)';
  } else {
    navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
  }
  
  lastScroll = currentScroll;
});

// ============================================
// AUTO-TRIGGER SCORE ANIMATION ON RESULT PAGE
// ============================================
window.addEventListener('load', () => {
  const scoreElement = document.querySelector('.score-number');
  if (scoreElement) {
    const targetScore = parseInt(scoreElement.textContent);
    if (!isNaN(targetScore)) {
      scoreElement.textContent = '0%';
      setTimeout(() => animateScore(targetScore), 500);
    }
  }
});

// ============================================
// KEYBOARD SHORTCUTS
// ============================================
document.addEventListener('keydown', (e) => {
  // Ctrl/Cmd + Enter to submit forms
  if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
    const form = document.querySelector('form');
    if (form) {
      form.dispatchEvent(new Event('submit', { cancelable: true }));
    }
  }
});

// ============================================
// UTILITY FUNCTIONS
// ============================================
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
  @keyframes slideOutRight {
    from {
      opacity: 1;
      transform: translateX(0);
    }
    to {
      opacity: 0;
      transform: translateX(100px);
    }
  }
  
  .pulse-animation {
    animation: pulse 1s ease-in-out infinite;
  }
  
  .is-valid {
    border-color: #43e97b !important;
  }
  
  .is-invalid {
    border-color: #fa709a !important;
  }
  
  .invalid-feedback {
    color: #fa709a;
    font-size: 0.875rem;
    margin-top: 0.25rem;
  }
`;
document.head.appendChild(style);
