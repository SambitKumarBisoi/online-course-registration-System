<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db.php';

// ✅ Only admin can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// ✅ Handle add enrollment
if(isset($_POST['add_enrollment'])){
    $user_id = $_POST['user_id'];
    $course_id = $_POST['course_id'];
    $stmt = $conn->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_enrollments.php");
    exit();
}

// ✅ Handle edit enrollment
if(isset($_POST['edit_enrollment'])){
    $enroll_id = $_POST['enroll_id'];
    $user_id = $_POST['user_id'];
    $course_id = $_POST['course_id'];
    $stmt = $conn->prepare("UPDATE enrollments SET user_id=?, course_id=? WHERE id=?");
    $stmt->bind_param("iii", $user_id, $course_id, $enroll_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_enrollments.php");
    exit();
}

// ✅ Handle delete enrollment
if(isset($_GET['delete'])){
    $enroll_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM enrollments WHERE id=?");
    $stmt->bind_param("i", $enroll_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_enrollments.php");
    exit();
}

// ✅ Fetch enrollments
$enrollments = $conn->query("SELECT e.id, u.id AS user_id, u.username, u.email, c.id AS course_id, c.course_name, e.enrollment_date
    FROM enrollments e
    JOIN users u ON e.user_id=u.id
    JOIN courses c ON e.course_id=c.id
    ORDER BY e.enrollment_date DESC");

// ✅ Fetch students
$students = $conn->query("SELECT id, username FROM users WHERE role='student'");

// ✅ Fetch courses
$courses = $conn->query("SELECT id, course_name FROM courses");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>SkillPilot — Manage Enrollments</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --trans:0.3s; --radius:12px; --shadow-soft:0 8px 20px rgba(2,6,23,0.4);
  --bg-dark:linear-gradient(180deg,#0a192f,#112240); --bg-light:linear-gradient(180deg,#e9f8ff,#ffffff);
  --card-dark:rgba(255,255,255,0.05); --card-light:#fff;
  --text-dark:#e6f7ff; --text-light:#07203a;
  --muted-dark:#9ad6ff; --muted-light:#475569;
  --gradient-btn:linear-gradient(90deg,#00c6ff,#7b2ff7);
}
body{margin:0;font-family:'Poppins',sans-serif;background:var(--bg-dark);color:var(--text-dark);transition:background var(--trans),color var(--trans);}
body.light{background:var(--bg-light);color:var(--text-light);}
.navbar{display:flex;justify-content:space-between;align-items:center;padding:16px 24px;background:rgba(255,255,255,0.05);border-bottom:1px solid rgba(255,255,255,0.1);}
body.light .navbar{background:#fff;border-bottom:1px solid #ddd;}
.navbar h1{font-size:20px;font-weight:700;margin:0;}
.user{margin-right:12px;color:var(--muted-dark);}
body.light .user{color:var(--muted-light);}
.container{max-width:1100px;margin:30px auto;padding:0 16px;}
.card{background:var(--card-dark);padding:20px;border-radius:var(--radius);box-shadow:var(--shadow-soft);margin-bottom:20px;}
body.light .card{background:var(--card-light);}
.btn{padding:6px 12px;border-radius:8px;font-size:14px;background:var(--gradient-btn);color:#fff;font-weight:600;border:none;cursor:pointer;text-decoration:none;margin-right:5px;}
.btn-danger{background:#dc3545;}
.btn-info{background:#0dcaf0;}
.btn-success{background:#198754;}
table{width:100%;border-collapse:collapse;margin-top:15px;}
th,td{padding:12px 14px;text-align:left;}
th{background:rgba(255,255,255,0.1);}
body.light th{background:#f1f5f9;}
tr:nth-child(even){background:rgba(255,255,255,0.05);}
body.light tr:nth-child(even){background:#f9fafb;}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;}
.modal-content{background:var(--card-dark);padding:20px;border-radius:var(--radius);width:400px;max-width:90%;}
body.light .modal-content{background:#fff;}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;}
.modal-header h5{margin:0;}
.close{cursor:pointer;font-weight:bold;}

/* Toggle icons inside ball */
.toggle-ball span{position:absolute;font-size:12px;}
.icon-sun{left:4px;top:50%;transform:translateY(-50%);}
.icon-moon{right:4px;top:50%;transform:translateY(-50%);}
</style>
</head>
<body>

<div class="navbar">
  <h1>SkillPilot — Manage Enrollments</h1>
  <div style="display:flex;align-items:center;gap:10px;">
    <span class="user">👤 <?= htmlspecialchars($username) ?> (Admin)</span>
    <a href="admin_dashboard.php" class="btn btn-info">⬅ Dashboard</a>
    <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
    <!-- ✅ Theme Toggle -->
    <div class="theme-toggle" id="themeToggle" style="cursor:pointer;">
      <div class="toggle-shell" style="width:60px;height:28px;border-radius:999px;padding:4px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);position:relative;">
        <div class="toggle-ball" id="toggleBall" style="width:24px;height:24px;border-radius:50%;background:#ffd54a;position:absolute;top:2px;left:2px;display:flex;align-items:center;justify-content:center;font-size:14px;transition: transform 0.3s ease, background 0.3s ease;">
          <span class="icon-sun">☀️</span>
          <span class="icon-moon">🌙</span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h2>📊 Student Enrollments</h2>

    <!-- Add Enrollment Form -->
    <form method="POST" style="margin-bottom:15px;">
      <select name="user_id" required>
        <option value="">-- Select Student --</option>
        <?php while($s=$students->fetch_assoc()): ?>
          <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['username']) ?></option>
        <?php endwhile; ?>
      </select>
      <select name="course_id" required>
        <option value="">-- Select Course --</option>
        <?php $courses->data_seek(0); while($c=$courses->fetch_assoc()): ?>
          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['course_name']) ?></option>
        <?php endwhile; ?>
      </select>
      <button type="submit" name="add_enrollment" class="btn btn-success">Add Enrollment</button>
    </form>

    <!-- Enrollment Table -->
    <table>
      <thead>
        <tr>
          <th>#</th><th>Student</th><th>Email</th><th>Course</th><th>Enrolled At</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $i=1; $enrollments->data_seek(0); while($row=$enrollments->fetch_assoc()): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['course_name']) ?></td>
            <td><?= htmlspecialchars($row['enrollment_date']) ?></td>
            <td>
              <button class="btn btn-info" onclick="openModal(<?= $row['id'] ?>)">✏️ Edit</button>
              <a href="manage_enrollments.php?delete=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">🗑 Delete</a>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal" id="modal<?= $row['id'] ?>">
            <div class="modal-content">
              <div class="modal-header">
                <h5>Edit Enrollment</h5>
                <span class="close" onclick="closeModal(<?= $row['id'] ?>)">&times;</span>
              </div>
              <form method="POST">
                <input type="hidden" name="enroll_id" value="<?= $row['id'] ?>">
                <label>Student:</label>
                <select name="user_id" required>
                  <?php $students->data_seek(0); while($s=$students->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>" <?= $s['id']==$row['user_id']?'selected':'' ?>><?= htmlspecialchars($s['username']) ?></option>
                  <?php endwhile; ?>
                </select>
                <label>Course:</label>
                <select name="course_id" required>
                  <?php $courses->data_seek(0); while($c=$courses->fetch_assoc()): ?>
                    <option value="<?= $c['id'] ?>" <?= $c['id']==$row['course_id']?'selected':'' ?>><?= htmlspecialchars($c['course_name']) ?></option>
                  <?php endwhile; ?>
                </select>
                <button type="submit" name="edit_enrollment" class="btn btn-success">Save</button>
              </form>
            </div>
          </div>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
// Theme toggle
const body=document.body;
const toggle=document.getElementById('themeToggle');
const ball=document.getElementById('toggleBall');
const sun=ball.querySelector('.icon-sun');
const moon=ball.querySelector('.icon-moon');

function setTheme(isLight){
  if(isLight){
    body.classList.add('light');
    ball.style.transform='translateX(32px)';
    sun.style.display='inline';
    moon.style.display='none';
    localStorage.setItem('skillpilot_theme','light');
  } else {
    body.classList.remove('light');
    ball.style.transform='translateX(0)';
    sun.style.display='none';
    moon.style.display='inline';
    localStorage.setItem('skillpilot_theme','dark');
  }
}

// Load theme from localStorage
setTheme(localStorage.getItem('skillpilot_theme')==='light');

// Toggle on click
toggle.addEventListener('click',()=>{
  setTheme(!body.classList.contains('light'));
});

// Modal functions
function openModal(id){ document.getElementById('modal'+id).style.display='flex'; }
function closeModal(id){ document.getElementById('modal'+id).style.display='none'; }
</script>

</body>
</html>
