<?php
session_start();
require 'includes/db.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = $_POST['name']; $email = $_POST['email']; $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
    $stmt->bind_param('sss',$name,$email,$hash);
    if($stmt->execute()){
        header('Location: index.php?m=Registered. Please login.');
    }else{
        header('Location: register.php?m=Error registering');
    }
}
?>
