<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role     = $_POST['role'];
    $adminKey = trim($_POST['admin_key']);

    // Secret key for admin registration
    $secretKey = "Admin123"; 

    // Validate role
    if ($role === 'admin') {
        if ($adminKey !== $secretKey) {
            $role = 'student'; // fallback
            $errorMsg = "⚠️ Invalid admin key! Registered as Student instead.";
        }
    } else {
        $role = 'student';
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);

    if ($stmt->execute()) {
        $success = "✅ Registration successful as <b>$role</b>! <a href='login.php'>Login here</a>";
        if(isset($errorMsg)) $warning = $errorMsg;
    } else {
        $error = "❌ Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>SkillPilot — Register</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
  /* === THEME SYSTEM === */
  :root {
    --trans:0.3s;
    --radius:12px;
    --shadow-soft:0 8px 20px rgba(2,6,23,0.4);

    --bg-dark:linear-gradient(180deg,#0a192f,#112240);
    --bg-light:linear-gradient(180deg,#e9f8ff,#ffffff);

    --card-dark:rgba(255,255,255,0.05);
    --card-light:#fff;

    --text-dark:#e6f7ff;
    --text-light:#07203a;

    --muted-dark:#9ad6ff;
    --muted-light:#475569;

    --gradient-btn:linear-gradient(90deg,#00c6ff,#7b2ff7);
    --outline-btn:#00e5ff;
  }

  body {
    margin:0;
    font-family:'Poppins',sans-serif;
    background:var(--bg-dark);
    color:var(--text-dark);
    transition:background var(--trans),color var(--trans);
  }
  body.light {
    background:var(--bg-light);
    color:var(--text-light);
  }

  *{box-sizing:border-box;}
  a { text-decoration: none; }

  /* === THEME TOGGLE === */
  .theme-toggle{position:absolute;top:20px;right:20px;cursor:pointer;}
  .toggle-shell{width:60px;height:28px;border-radius:999px;padding:4px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);display:flex;align-items:center;position:relative;}
  .toggle-ball{width:24px;height:24px;border-radius:50%;background:#ffd54a;position:absolute;top:2px;left:2px;transition:transform var(--trans),background var(--trans);display:flex;align-items:center;justify-content:center;font-size:14px;}
  body.light .toggle-ball{background:#fff;transform:translateX(32px);}
  .icon-sun,.icon-moon{position:absolute;top:50%;transform:translateY(-50%);font-size:14px;}
  .icon-sun{left:6px;display:none;} body.light .icon-sun{display:inline;}
  .icon-moon{right:6px;display:inline;} body.light .icon-moon{display:none;}

  /* === CARD === */
  .card{
    background:var(--card-dark);
    padding:30px;
    border-radius:var(--radius);
    box-shadow:var(--shadow-soft);
    max-width:420px;
    margin:100px auto;
    text-align:center;
  }
  body.light .card{background:var(--card-light);}

  .card h2{margin-bottom:8px;}
  .card p{margin-bottom:20px;color:var(--muted-dark);}
  body.light .card p{color:var(--muted-light);}

  .form-group{text-align:left;margin-bottom:14px;}
  .form-group label{font-size:14px;font-weight:600;}
  .form-group input, .form-group select{
    width:100%;padding:10px;border-radius:8px;
    border:1px solid rgba(255,255,255,0.2);
    background:rgba(255,255,255,0.05);
    color:inherit;
  }
  body.light .form-group input, body.light .form-group select{
    border:1px solid #ddd;background:#fff;color:#000;
  }

  /* === BUTTONS === */
  .btn{
    display:inline-block;width:100%;padding:10px;
    border-radius:8px;background:var(--gradient-btn);
    color:#fff;font-weight:700;border:none;cursor:pointer;
    transition:transform var(--trans);
  }
  .btn:hover{transform:translateY(-3px);}
  .btn-secondary{
    display:inline-block;width:100%;padding:10px;margin-top:10px;
    border-radius:8px;background:transparent;
    border:2px solid var(--outline-btn);color:var(--outline-btn);
    font-weight:700;cursor:pointer;transition:all var(--trans);
  }
  .btn-secondary:hover{background:var(--outline-btn);color:#fff;}

  .alert{padding:10px;border-radius:8px;margin-bottom:12px;}
  .alert-success{background:#4caf50;color:#fff;}
  .alert-error{background:#ff4d4f;color:#fff;}
  .alert-warning{background:#ffa726;color:#fff;}
  </style>
</head>
<body>
  <!-- THEME TOGGLE -->
  <div class="theme-toggle" id="themeToggle">
    <div class="toggle-shell">
      <div class="toggle-ball">
        <span class="icon-sun">☀️</span><span class="icon-moon">🌙</span>
      </div>
    </div>
  </div>

  <!-- CARD -->
  <div class="card">
    <h2>Create Account</h2>
    <p>Join SkillPilot and start your learning journey 🚀</p>

    <?php if(isset($success)): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php elseif(isset($error)): ?>
      <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    <?php if(isset($warning)): ?>
      <div class="alert alert-warning"><?= $warning ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <div class="form-group">
        <label>Role</label>
        <select name="role" onchange="toggleAdminKey()" required>
          <option value="student" selected>Student</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <div class="form-group" id="adminKeyField" style="display:none;">
        <label>Admin Key</label>
        <input type="password" name="admin_key">
      </div>
      <button type="submit" class="btn">Register</button>
    </form>
    <a href="login.php" class="btn-secondary">Already have an account? Login</a>
  </div>

<script>
/* === THEME TOGGLE SCRIPT === */
(function(){
  const body=document.body;
  const toggle=document.getElementById('themeToggle');
  if(localStorage.getItem('skillpilot_theme')==='light'){ body.classList.add('light'); }
  toggle.addEventListener('click',()=>{ 
    body.classList.toggle('light');
    localStorage.setItem('skillpilot_theme',body.classList.contains('light')?'light':'dark');
  });
})();

/* === SHOW/HIDE ADMIN KEY FIELD === */
function toggleAdminKey(){
  const role=document.querySelector("select[name='role']").value;
  const keyField=document.getElementById("adminKeyField");
  keyField.style.display=(role==="admin")?"block":"none";
}
</script>
</body>
</html>
