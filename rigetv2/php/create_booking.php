<?php
session_start();
require_once __DIR__ . '/connection.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: ../pages/login.php");
    exit;
}

$user_email = $_SESSION['user_email'];

$room_type = $_POST['room_type'] ?? '';
$checkin   = $_POST['checkin'] ?? '';
$checkout  = $_POST['checkout'] ?? '';

if (!$room_type || !$checkin || !$checkout) {
    die("All fields are required.");
}

if (strtotime($checkout) <= strtotime($checkin)) {
    die("Invalid date selection.");
}

$query = "
    INSERT INTO hotel_bookings (user_email, room_type, checkin, checkout)
    VALUES ($1, $2, $3, $4)
";

$result = pg_query_params($connection, $query, [
    $user_email,
    $room_type,
    $checkin,
    $checkout
]);

if (!$result) {
    die(pg_last_error($connection));
}

header("Location: ../pages/index.php?booking=success");
exit;
