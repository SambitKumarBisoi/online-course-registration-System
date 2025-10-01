<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$method = $_GET['method'] ?? 'unknown';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Payment Success</title>
<style>
body {font-family: Arial, sans-serif; background:#f0f8ff; text-align:center; padding:50px;}
.box {background:#fff; border-radius:12px; padding:30px; max-width:400px; margin:0 auto; box-shadow:0 0 10px rgba(0,0,0,0.1);}
h2 {color:green;}
a {display:inline-block; margin-top:20px; text-decoration:none; background:#007bff; color:#fff; padding:10px 20px; border-radius:8px;}
</style>
</head>
<body>
<div class="box">
    <h2>✅ Payment Successful!</h2>
    <p>Your payment via <b><?php echo htmlspecialchars($method); ?></b> has been completed.</p>
    <a href="student_dashboard.php">Go to Dashboard</a>
</div>
</body>
</html>
