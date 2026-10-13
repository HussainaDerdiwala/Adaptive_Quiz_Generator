<?php
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'adaptive_quiz_db';
$conn = new mysqli($servername,$username,$password,$dbname);
if($conn->connect_error) die('DB Connect error: '.$conn->connect_error);
?>
