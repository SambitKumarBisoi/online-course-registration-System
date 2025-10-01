<?php
session_start();
include 'db.php';
ini_set('display_errors',1); error_reporting(E_ALL);

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: login.php");
    exit();
}
$user_id = (int)$_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

if($course_id <= 0){
    header("Location: student_dashboard.php?msg=" . urlencode("Invalid course id"));
    exit();
}

// Already enrolled?
$chk = $conn->prepare("SELECT id FROM enrollments WHERE user_id=? AND course_id=?");
$chk->bind_param("ii",$user_id,$course_id);
$chk->execute();
$chk->store_result();
if($chk->num_rows > 0){
    $chk->close();
    header("Location: student_dashboard.php?msg=" . urlencode("Already enrolled"));
    exit();
}
$chk->close();

// Already in cart?
$chk2 = $conn->prepare("SELECT id FROM cart WHERE user_id=? AND course_id=?");
$chk2->bind_param("ii",$user_id,$course_id);
$chk2->execute();
$chk2->store_result();
if($chk2->num_rows > 0){
    $chk2->close();
    header("Location: student_dashboard.php?msg=" . urlencode("Already in cart"));
    exit();
}
$chk2->close();

// Insert
$ins = $conn->prepare("INSERT INTO cart (user_id, course_id) VALUES (?,?)");
$ins->bind_param("ii",$user_id,$course_id);
$ins->execute();
$ins->close();

header("Location: student_dashboard.php?msg=" . urlencode("Added to cart"));
exit();
