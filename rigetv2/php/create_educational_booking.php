<?php
session_start();
require_once __DIR__ . '/connection.php';

// LOGIN PROTECTION
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

$user_email = $_SESSION['user_email'] ?? null;

if (!$user_email) {
    die("User email not found in session.");
}

// GET FORM DATA
$course_type = $_POST['course_type'] ?? '';
$visit_date  = $_POST['visit_date'] ?? '';

// VALIDATION
if (!$course_type || !$visit_date) {
    die("All fields are required.");
}

if (strtotime($visit_date) < strtotime(date('Y-m-d'))) {
    die("Visit date cannot be in the past.");
}

// INSERT INTO DATABASE
$query = "INSERT INTO educational_bookings (user_email, course_type, visit_date) VALUES ($1, $2, $3)";
$result = pg_query_params($connection, $query, [$user_email, $course_type, $visit_date]);

if (!$result) {
    die("Booking failed: " . pg_last_error($connection));
}
header("Location: ../pages/index.php");
exit();
?>
