<?php
// Start the session to allow use of $_SESSION variables
session_start();

// Include the database connection file
require_once __DIR__ . '/connection.php';

// Only allow POST requests; otherwise redirect to login page
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/login.php");
    exit;
}

// Get and sanitize user input
$email = trim($_POST['email'] ?? '');   // Remove whitespace from email
$password = $_POST['password'] ?? '';   // Get password

// If email or password is empty, redirect with error
if ($email === '' || $password === '') {
    header("Location: ../pages/login.php?error=1");
    exit;
}

// SQL query to fetch user data using a prepared statement
$query = "SELECT id, name, email, password FROM users WHERE email = $1";

// Execute the query safely with parameters (prevents SQL injection)
$result = pg_query_params($connection, $query, [$email]);

// If query fails, log error and stop execution
if (!$result) {
    error_log("DB error: " . pg_last_error($connection));
    exit("Database query failed.");
}

// If no user or multiple users found, redirect with error
if (pg_num_rows($result) !== 1) {
    error_log("No user found for email: $email");
    header("Location: ../pages/login.php?error=1");
    exit;
}

// Fetch user data as an associative array
$user = pg_fetch_assoc($result);
error_log("User found: " . print_r($user, true));

// Verify the entered password against the hashed password in the database
if (!password_verify($password, $user['password'])) {
    error_log("Password verification failed for email: $email");
    header("Location: ../pages/login.php?error=1");
    exit;
}

// Regenerate session ID to prevent session fixation attacks
session_regenerate_id(true);

// Store user information in session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];

// Redirect the user to the dashboard/home page after successful login
header("Location: ../pages/index.php");
exit;
