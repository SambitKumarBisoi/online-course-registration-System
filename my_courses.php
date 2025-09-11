<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch courses the student is enrolled in
$stmt = $conn->prepare("
    SELECT c.course_name, c.description, c.duration, c.price, c.seats, e.enrollment_date
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>My Courses</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><?= htmlspecialchars($username) ?>'s Courses</h2>
    <a href="student_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

<div class="row">
<?php while($course = $result->fetch_assoc()): ?>
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($course['course_name']) ?></h5>
                <p class="card-text"><?= htmlspecialchars($course['description']) ?></p>
                <p><strong>Duration:</strong> <?= htmlspecialchars($course['duration']) ?></p>
                <p><strong>Price:</strong> ₹<?= htmlspecialchars($course['price']) ?></p>
                <p><strong>Enrolled On:</strong> <?= $course['enrollment_date'] ?></p>
            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>

</div>
</body>
</html>
