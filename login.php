<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $username, $hashedPassword, $role);
    $stmt->fetch();

    if ($id && password_verify($password, $hashedPassword)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        if($role == 'admin'){
            header("Location: admin_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid email or password!";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
<h2>Login</h2>
<?php if(isset($error)) echo "<p style='color:red;'>❌ $error</p>"; ?>
<form method="POST">
    <div class="mb-3">
        <label>Email:</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password:</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>
<p class="mt-2">Don't have an account? <a href="register.php">Register here</a>.</p>
</div>
</body>
</html>
