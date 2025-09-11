<?php
session_start();
include 'db.php';

// Only admin access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$course_id = $_GET['id'] ?? 0;

// Fetch current course data
$stmt = $conn->prepare("SELECT * FROM courses WHERE id=?");
$stmt->bind_param("i",$course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$stmt->close();

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $name = $_POST['course_name'];
    $desc = $_POST['description'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];
    $seats = $_POST['seats'];

    $stmt2=$conn->prepare("UPDATE courses SET course_name=?, description=?, duration=?, price=?, seats=? WHERE id=?");
    $stmt2->bind_param("sssdis",$name,$desc,$duration,$price,$seats,$course_id);

    if($stmt2->execute()){
        $success="✅ Course updated successfully!";
    }else{
        $error="❌ Error: ".$stmt2->error;
    }

    $stmt2->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
<h2>Edit Course</h2>

<?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
<?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Course Name</label>
        <input type="text" name="course_name" class="form-control" value="<?= htmlspecialchars($course['course_name']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" required><?= htmlspecialchars($course['description']) ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Duration</label>
        <input type="text" name="duration" class="form-control" value="<?= htmlspecialchars($course['duration']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($course['price']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Seats</label>
        <input type="number" name="seats" class="form-control" value="<?= htmlspecialchars($course['seats']) ?>" required>
    </div>
    <button class="btn btn-primary">Update Course</button>
    <a href="admin_dashboard.php" class="btn btn-secondary">Back</a>
</form>
</div>
</body>
</html>
