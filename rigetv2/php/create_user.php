<?php
session_start(); 

include 'connection.php';
include 'create_table.php';

function sanitise($data) {
    return htmlspecialchars(stripslashes(trim($data ?? "")));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $names     = sanitise($_POST['name'] ?? '');
    $surnames  = sanitise($_POST['surname'] ?? '');
    $emails    = sanitise($_POST['email'] ?? '');
    $passwords = sanitise($_POST['password'] ?? '');

    $errors = [];

    if (empty($names))    $errors[] = "Name is required.";
    if (empty($surnames)) $errors[] = "Surname is required.";
    if (empty($emails))   $errors[] = "Email is required.";
    if (empty($passwords))$errors[] = "Password is required.";

    if (empty($errors)) {

        // Hash the password before storing it
        $hashedPassword = password_hash($passwords, PASSWORD_DEFAULT);

        // Use pg_query_params to prevent SQL injection
        $query = "INSERT INTO users (name, surname, email, password) VALUES ($1, $2, $3, $4) RETURNING id";
        $result = pg_query_params($connection, $query, [$names, $surnames, $emails, $hashedPassword]);

        if (!$result) {
            echo "Error: " . pg_last_error($connection);
        } else {
            // Fetch the newly created user's ID
            $row = pg_fetch_assoc($result);
            $userId = $row['id'];

            // Set session variables
            $_SESSION['user_id']    = $userId;
            $_SESSION['user_name']  = $names;
            $_SESSION['user_surname'] = $surnames;
            $_SESSION['user_email'] = $emails;
            $_SESSION['logged_in']  = true;

            header("Location: ../pages/index.php");
            exit();
        }
    }

    pg_close($connection);
}
?>
