<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ Registration successful! <a href='login.php'>Login here</a></p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
<h2>Student Registration</h2>
<form method="POST">
    <div class="mb-3">
        <label>Username:</label>
        <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email:</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password:</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Register</button>
</form>
<p class="mt-2">Already have an account? <a href="login.php">Login here</a>.</p>
</div>
</body>
</html>
