<?php
session_start();
include 'db.php';

// Check student login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

if (isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);

    // 1. Check if already enrolled
    $stmt = $conn->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ? LIMIT 1");
    if (!$stmt) {
        header("Location: student_dashboard.php?msg=" . rawurlencode("❌ Server error (prepare failed)"));
        exit();
    }
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        header("Location: student_dashboard.php?msg=" . rawurlencode("❌ You are already enrolled in this course!"));
        exit();
    }
    $stmt->close();

    // 2. Check available seats (and that course exists)
    $checkSeats = $conn->prepare("SELECT seats FROM courses WHERE id = ? LIMIT 1");
    if (!$checkSeats) {
        header("Location: student_dashboard.php?msg=" . rawurlencode("❌ Server error (prepare failed)"));
        exit();
    }
    $checkSeats->bind_param("i", $course_id);
    $checkSeats->execute();
    $checkSeats->bind_result($seats);
    $fetched = $checkSeats->fetch();
    $checkSeats->close();

    if (!$fetched) {
        // course not found
        header("Location: student_dashboard.php?msg=" . rawurlencode("❌ Invalid course ID!"));
        exit();
    }

    if ($seats <= 0) {
        header("Location: student_dashboard.php?msg=" . rawurlencode("❌ No seats available!"));
        exit();
    }

    // 3. Enroll student + reduce seats (transaction-safe)
    // Make sure your tables use InnoDB for transactions to work
    $conn->begin_transaction();

    try {
        // Insert enrollment with explicit payment_status = 'pending'
        $stmt2 = $conn->prepare("INSERT INTO enrollments (user_id, course_id, payment_status) VALUES (?, ?, 'pending')");
        if (!$stmt2) {
            throw new Exception("Prepare failed (insert): " . $conn->error);
        }
        $stmt2->bind_param("ii", $user_id, $course_id);
        $stmt2->execute();
        // check if insert succeeded
        if ($stmt2->affected_rows === 0) {
            $stmt2->close();
            throw new Exception("Insert failed");
        }
        $stmt2->close();

        // Reduce seat count atomically (only when seats > 0)
        $stmt3 = $conn->prepare("UPDATE courses SET seats = seats - 1 WHERE id = ? AND seats > 0");
        if (!$stmt3) {
            throw new Exception("Prepare failed (update seats): " . $conn->error);
        }
        $stmt3->bind_param("i", $course_id);
        $stmt3->execute();

        // If no rows affected, seat update failed (concurrent reservation)
        if ($stmt3->affected_rows === 0) {
            $stmt3->close();
            // rollback will undo the enrollment insert
            throw new Exception("Seat update failed (no seats left)");
        }
        $stmt3->close();

        // All good
        $conn->commit();
        header("Location: student_dashboard.php?msg=" . rawurlencode("✅ Successfully enrolled!"));
        exit();

    } catch (Exception $e) {
        // rollback and report
        $conn->rollback();
        error_log("Enrollment error: " . $e->getMessage());
        header("Location: student_dashboard.php?msg=" . rawurlencode("❌ Enrollment failed!"));
        exit();
    }

} else {
    header("Location: student_dashboard.php?msg=" . rawurlencode("❌ Invalid course ID!"));
    exit();
}

$conn->close();
