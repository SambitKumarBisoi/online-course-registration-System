<?php
session_start();
include 'db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_GET['course_id'])){
    $course_id = intval($_GET['course_id']);

    $stmt = $conn->prepare("SELECT * FROM enrollments WHERE user_id=? AND course_id=?");
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        echo "<p style='color:red;'>❌ Already enrolled.</p>";
    } else {
        $stmt2 = $conn->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
        $stmt2->bind_param("ii", $user_id, $course_id);
        if($stmt2->execute()){
            echo "<p style='color:green;'>✅ Successfully enrolled!</p>";
        } else {
            echo "<p style='color:red;'>❌ Error: ".$stmt2->error."</p>";
        }
        $stmt2->close();
    }
    $stmt->close();
} else {
    echo "<p style='color:red;'>❌ Invalid course ID.</p>";
}
$conn->close();
?>
<p><a href="student_dashboard.php">Back to Dashboard</a></p>