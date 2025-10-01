<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'] ?? 0;
$payment_method = $_POST['payment_method'] ?? 'card';
$from = $_POST['from'] ?? '';

if(!$user_id){ header("Location: login.php"); exit(); }

if($from==='cart'){
    $stmt = $conn->prepare("SELECT course_id, c.price FROM cart ct JOIN courses c ON ct.course_id=c.id WHERE ct.user_id=?");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()){
        $course_id = $row['course_id'];
        $amount = $row['price'];
        $status='paid';
        $conn->query("INSERT INTO payments (user_id, amount, payment_method, status) VALUES ($user_id,$amount,'$payment_method','$status')");
        $conn->query("INSERT INTO enrollments (user_id, course_id, payment_status) VALUES ($user_id,$course_id,'$status') ON DUPLICATE KEY UPDATE payment_status='paid'");
    }
    $conn->query("DELETE FROM cart WHERE user_id=$user_id");

} elseif($from==='pending'){
    $stmt = $conn->prepare("SELECT e.course_id, c.price FROM enrollments e JOIN courses c ON e.course_id=c.id WHERE e.user_id=? AND e.payment_status='pending'");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()){
        $course_id = $row['course_id'];
        $amount = $row['price'];
        $status='paid';
        $conn->query("INSERT INTO payments (user_id, amount, payment_method, status) VALUES ($user_id,$amount,'$payment_method','$status')");
        $conn->query("UPDATE enrollments SET payment_status='paid' WHERE user_id=$user_id AND course_id=$course_id");
    }
}

// Redirect back to dashboard with success
header("Location: student_dashboard.php?payment=success");
exit();
