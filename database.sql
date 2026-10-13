-- Adaptive Quiz prototype database
CREATE DATABASE IF NOT EXISTS adaptive_quiz_db;
USE adaptive_quiz_db;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  password VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS questions;
CREATE TABLE questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(50),
  question TEXT,
  options TEXT,
  answer VARCHAR(10),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS results;
CREATE TABLE results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  question_id INT,
  category VARCHAR(50),
  correct TINYINT(1),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS quizzes;
CREATE TABLE quizzes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  score FLOAT,
  summary TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample questions (one per category for brevity)
INSERT INTO questions (category, question, options, answer) VALUES
('Maths', 'What is 7 + 5?', '["10","11","12","13"]', '2'),
('HTML', 'Which tag is used for a paragraph?', '["<div>","<p>","<span>"]', '1'),
('CSS', 'Which property changes text color?', '["background-color","font-size","color"]', '2'),
('JavaScript', 'Which is used to declare a variable in modern JS?', '["var","let","const"]', '2'),
('React', 'React is primarily a ___ library.', '["Database","UI","Server"]', '1'),
('MySQL', 'Which SQL keyword is used to get unique values?', '["DISTINCT","UNIQUE","GROUP"]', '0'),
('PHP', 'Which symbol starts a PHP variable?', '["$","%","#"]', '0'),
('Science', 'Water boils at what°C at sea level?', '["90","100","110"]', '1'),
('History', 'Who was the first President of the United States?', '["Abraham Lincoln","George Washington","Thomas Jefferson"]', '1');