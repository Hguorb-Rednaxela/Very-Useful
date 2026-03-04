<?php
require_once __DIR__ . '/connection.php';

/* ---------------------------------------
   CREATE USERS TABLE
--------------------------------------- */
$queryUsers = "
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$resultUsers = pg_query($connection, $queryUsers);
if (!$resultUsers) {
    echo "Error creating users table: " . pg_last_error($connection);
}

/* ---------------------------------------
   CREATE HOTEL BOOKINGS TABLE
--------------------------------------- */
$queryHotelBookings = "
CREATE TABLE IF NOT EXISTS hotel_bookings (
    id SERIAL PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    room_type VARCHAR(50) NOT NULL,
    checkin DATE NOT NULL,
    checkout DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$resultHotel = pg_query($connection, $queryHotelBookings);
if (!$resultHotel) {
    echo "Error creating hotel_bookings table: " . pg_last_error($connection);
}

/* ---------------------------------------
   CREATE ZOO BOOKINGS TABLE
--------------------------------------- */
$queryZooBookings = "
CREATE TABLE IF NOT EXISTS zoo_bookings (
    id SERIAL PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    ticket_type VARCHAR(255) NOT NULL,
    num_adults INT NOT NULL DEFAULT 0,
    num_children INT NOT NULL DEFAULT 0,
    visit_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$resultZoo = pg_query($connection, $queryZooBookings);
if (!$resultZoo) {
    echo "Error creating zoo_bookings table: " . pg_last_error($connection);
}

/* ---------------------------------------
   CREATE EDUCATIONAL BOOKINGS TABLE
--------------------------------------- */
$queryEduBookings = "
CREATE TABLE IF NOT EXISTS educational_bookings (
    id SERIAL PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    course_type VARCHAR(50) NOT NULL,
    visit_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$resultEdu = pg_query($connection, $queryEduBookings);
if (!$resultEdu) {
    echo "Error creating educational_bookings table: " . pg_last_error($connection);
}
?>
