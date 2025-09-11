<?php
include 'db.php';
session_start();

// Fetch all courses
$sql = "SELECT * FROM courses";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Online Course Registration</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .hero {
      background: linear-gradient(to right, #0072ff, #00c6ff);
      color: white;
      padding: 80px 20px;
      text-align: center;
    }
    .course-card {
      transition: transform 0.2s ease-in-out;
    }
    .course-card:hover {
      transform: translateY(-5px);
    }
  </style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Learnify</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- ✅ Hero Section -->
<div class="hero">
  <h1 class="display-4 fw-bold">Upgrade Your Skills Online</h1>
  <p class="lead">Explore expert-led courses in tech, business, and more.</p>
  <a href="register.php" class="btn btn-light btn-lg mt-3">Get Started</a>
</div>

<!-- ✅ Course List -->
<div class="container my-5">
  <h2 class="mb-4 text-center">Available Courses</h2>
  <div class="row">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
          <div class="card course-card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($row['course_name']) ?></h5>
              <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
              <p><strong>Duration:</strong> <?= htmlspecialchars($row['duration']) ?></p>
              <p><strong>Price:</strong> ₹<?= number_format($row['price'], 2) ?></p>
              <p><strong>Seats:</strong> <?= htmlspecialchars($row['seats']) ?></p>
              <a href="login.php" class="btn btn-primary">Enroll Now</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-center">🚀 New courses coming soon. Stay tuned!</p>
    <?php endif; ?>
  </div>
</div>

<!-- ✅ Footer -->
<footer class="bg-dark text-white text-center py-3">
  <p>&copy;
