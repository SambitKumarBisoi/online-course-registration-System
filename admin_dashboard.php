<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$result = $conn->query("SELECT * FROM courses");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Welcome, <?= htmlspecialchars($username) ?> (Admin)</h2>
    <div>
        <a href="add_course.php" class="btn btn-success">Add Course</a>
        <a href="view_enrollments.php" class="btn btn-info">View Enrollments</a>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>
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
                <p><strong>Seats:</strong> <?= $course['seats'] ?></p>
                <a href="edit_course.php?course_id=<?= $course['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                <a href="delete_course.php?course_id=<?= $course['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>

</div>
</body>
</html>
            