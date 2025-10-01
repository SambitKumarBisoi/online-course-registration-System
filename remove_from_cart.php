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
    header("Location: student_dashboard.php?msg=" . urlencode("Invalid request"));
    exit();
}

$stmt = $conn->prepare("DELETE FROM cart WHERE user_id=? AND course_id=?");
$stmt->bind_param("ii",$user_id,$course_id);
$stmt->execute();
$stmt->close();

header("Location: student_dashboard.php?msg=" . urlencode("Removed from cart"));
exit();
