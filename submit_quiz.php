<?php
session_start();
if(!isset($_SESSION['user_id'])) header('Location: index.php');
require 'includes/db.php';
$uid = $_SESSION['user_id'];
// collect answers
$answers = [];
foreach($_POST as $k=>$v){
  if(strpos($k,'q_')===0){
    $id = intval(substr($k,2));
    $answers[$id] = $v;
  }
}
$total=0; $correct=0;
$category_summary = [];
foreach($answers as $qid=>$ans){
  $stmt = $conn->prepare("SELECT id,answer,category FROM questions WHERE id=?"); $stmt->bind_param('i',$qid); $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();
  $isCorrect = ($res['answer']==$ans)?1:0;
  $total++; if($isCorrect) $correct++;
  $cat = $res['category'];
  if(!isset($category_summary[$cat])) $category_summary[$cat] = ['t'=>0,'s'=>0];
  $category_summary[$cat]['t']++; $category_summary[$cat]['s'] += $isCorrect;
  // insert into results
  $ins = $conn->prepare("INSERT INTO results (user_id, question_id, category, correct, created_at) VALUES (?,?,?,?,NOW())");
  $ins->bind_param('iiis',$uid,$qid,$cat,$isCorrect);
  // note: bind_param types: i i s i -> adjust by formatting; we'll use execute with types conversion
  $ins->execute();
}
$score = round(100 * $correct / ($total?:1),1);
// save a summary row
$cats_json = json_encode($category_summary);
$stmt = $conn->prepare("INSERT INTO quizzes (user_id,score,summary,created_at) VALUES (?,?,?,NOW())");
$stmt->bind_param('iis',$uid,$score,$cats_json);
$stmt->execute();
header('Location: result.php?score='.$score);
?>
