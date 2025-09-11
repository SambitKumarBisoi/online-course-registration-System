<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Fetch all enrollments
$result = $conn->query("
    SELECT e.id, u.username, u.email, c.course_name, e.enrollment_date
    FROM enrollments e
    JOIN users u ON e.user_id = u.id
    JOIN courses c ON e.course_id = c.id
    ORDER BY e.enrollment_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>All Enrollments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">

<h2>All Enrollments</h2>
<a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

<table class="table table-striped table-bordered">
<thead class="table-dark">
<tr>
<th>Student Name</th>
<th>Email</th>
<th>Course Name</th>
<th>Enrollment Date</th>
</tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['username']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
<td><?= htmlspecialchars($row['course_name']) ?></td>
<td><?= $row['enrollment_date'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</div>
</body>
</html>
