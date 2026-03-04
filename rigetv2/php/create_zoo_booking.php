<?php
session_start();
require_once __DIR__ . '/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

$user_email = $_SESSION['user_email'] ?? $_COOKIE['user_email'] ?? null;
if (!$user_email) die("User email not found.");

/* -------------------------
   GET POST DATA
------------------------- */
$ticket_type = trim($_POST['ticket_type'] ?? '');
$num_adults = max(0, (int) ($_POST['num_adults'] ?? 0));
$num_children = max(0, (int) ($_POST['num_children'] ?? 0));
$visit_date = trim($_POST['visit_date'] ?? '');

if (!$ticket_type || !$visit_date || ($num_adults + $num_children) === 0) {
    die("All fields are required and at least one visitor must be selected.");
}

if (strtotime($visit_date) < strtotime(date('Y-m-d'))) {
    die("Visit date cannot be in the past.");
}

/* -------------------------
   INSERT INTO DATABASE
------------------------- */
$query = "
INSERT INTO zoo_bookings (user_email, ticket_type, num_adults, num_children, visit_date)
VALUES ($1, $2, $3, $4, $5)
";

$result = pg_query_params($connection, $query, [
    $user_email,
    $ticket_type,
    $num_adults,
    $num_children,
    $visit_date
]);

if (!$result) {
    die("Booking failed: " . pg_last_error($connection));
}

header("Location: ../pages/index.php");
exit();
?>
