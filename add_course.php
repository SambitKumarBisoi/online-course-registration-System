<?php
session_start();
include 'db.php';

// Only admin access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $name = $_POST['course_name'];
    $desc = $_POST['description'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];
    $seats = $_POST['seats'];

    $stmt = $conn->prepare("INSERT INTO courses (course_name, description, duration, price, seats) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssdi", $name, $desc, $duration, $price, $seats);

    if($stmt->execute()){
        $success="✅ Course added successfully!";
    }else{
        $error="❌ Error: ".$stmt->error;
    }

    $stmt->close();
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
<h2>Add New Course</h2>

<?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
<?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Course Name</label>
        <input type="text" name="course_name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" required></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Duration</label>
        <input type="text" name="duration" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" name="price" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Seats</label>
        <input type="number" name="seats" class="form-control" required>
    </div>
    <button class="btn btn-success">Add Course</button>
    <a href="admin_dashboard.php" class="btn btn-secondary">Back</a>
</form>
</div>
</body>
</html>
