<?php
session_start();
include 'db.php';

// Check student login
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Search functionality
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM courses";
if($search){
    $query .= " WHERE course_name LIKE ?";
    $stmt = $conn->prepare($query);
    $searchTerm = "%$search%";
    $stmt->bind_param("s", $searchTerm);
} else {
    $stmt = $conn->prepare($query);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Welcome, <?= htmlspecialchars($username) ?></h2>
    <div>
        <a href="my_courses.php" class="btn btn-info">My Courses</a>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>
</div>

<form method="GET" class="mb-3">
    <input type="text" name="search" class="form-control" placeholder="Search courses..." value="<?= htmlspecialchars($search) ?>">
</form>

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
                <a href="enroll.php?course_id=<?= $course['id'] ?>" class="btn btn-success">Enroll</a>
            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>

</div>
</body>
</html>
