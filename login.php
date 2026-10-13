<?php
session_start();
require 'includes/db.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $email = $_POST['email']; $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id,name,password FROM users WHERE email=?"); $stmt->bind_param('s',$email); $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows){
        $row = $res->fetch_assoc();
        if(password_verify($password,$row['password'])){
            $_SESSION['user_id']=$row['id']; $_SESSION['name']=$row['name'];
            header('Location: dashboard.php');
            exit;
        }
    }
    header('Location: index.php?m=Invalid credentials');
}
?>
