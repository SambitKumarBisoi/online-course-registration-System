<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'] ?? 0;
if(!$user_id){ header("Location: login.php"); exit(); }

// Pending enrollments
$stmt = $conn->prepare("
    SELECT e.course_id, c.course_name, c.price 
    FROM enrollments e 
    JOIN courses c ON e.course_id=c.id
    WHERE e.user_id=? AND e.payment_status='pending'
");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$pending_courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Cart courses
$stmt2 = $conn->prepare("
    SELECT c.id, c.course_name, c.price 
    FROM cart ct
    JOIN courses c ON ct.course_id=c.id
    WHERE ct.user_id=?
");
$stmt2->bind_param("i",$user_id);
$stmt2->execute();
$cart_courses = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

// Totals
$total_pending = array_sum(array_column($pending_courses,'price'));
$total_cart = array_sum(array_column($cart_courses,'price'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout — SkillPilot</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;background:#f4f6f8;color:#07203a;margin:0;padding:0;}
.container{max-width:700px;margin:40px auto;padding:20px;background:#fff;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.1);}
h2{text-align:center;margin-bottom:24px;}
.course-list{margin-bottom:20px;}
.course-item{display:flex;justify-content:space-between;margin-bottom:8px;padding:8px 12px;background:#f0f4f8;border-radius:8px;}
.total{font-weight:600;margin-top:12px;text-align:right;}
.btn{display:inline-block;padding:10px 16px;background:#1e88e5;color:#fff;border:none;border-radius:8px;cursor:pointer;margin-top:16px;font-weight:600;text-align:center;}
.btn:hover{background:#1565c0;}
.remove{color:#ff5252;text-decoration:none;margin-left:10px;}
</style>
</head>
<body>
<div class="container">
<h2>Checkout</h2>

<?php if(count($pending_courses) > 0): ?>
<h3>Pending Enrollments</h3>
<div class="course-list">
    <?php foreach($pending_courses as $c): ?>
        <div class="course-item">
            <span><?= htmlspecialchars($c['course_name']) ?></span>
            <span>₹<?= $c['price'] ?></span>
        </div>
    <?php endforeach; ?>
</div>
<div class="total">Total Pending: ₹<?= $total_pending ?></div>
<form method="POST" action="payment_process.php">
    <input type="hidden" name="from" value="pending">
    <button type="submit" name="payment_method" value="card" class="btn">Pay Pending (Card)</button>
    <button type="submit" name="payment_method" value="upi" class="btn">Pay Pending (UPI)</button>
</form>
<?php endif; ?>

<?php if(count($cart_courses) > 0): ?>
<h3>Cart</h3>
<div class="course-list">
    <?php foreach($cart_courses as $c): ?>
        <div class="course-item">
            <span><?= htmlspecialchars($c['course_name']) ?></span>
            <span>₹<?= $c['price'] ?></span>
            <a href="remove_from_cart.php?course_id=<?= $c['id'] ?>" class="remove">Remove</a>
        </div>
    <?php endforeach; ?>
</div>
<div class="total">Total Cart: ₹<?= $total_cart ?></div>
<form method="POST" action="payment_process.php">
    <input type="hidden" name="from" value="cart">
    <button type="submit" name="payment_method" value="card" class="btn">Pay Cart (Card)</button>
    <button type="submit" name="payment_method" value="upi" class="btn">Pay Cart (UPI)</button>
</form>
<?php endif; ?>

<?php if(count($pending_courses)===0 && count($cart_courses)===0): ?>
<p style="text-align:center;">No pending payments or courses in cart.</p>
<?php endif; ?>

</div>
</body>
</html>
