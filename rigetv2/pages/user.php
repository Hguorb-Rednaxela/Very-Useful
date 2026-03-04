<?php
session_start();
require_once __DIR__ . '/../php/connection.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'] ?? $_COOKIE['user_email'] ?? null;
if (!$user_email) {
    die("User email not found.");
}

/* -------------------------------
   HANDLE CANCEL REQUEST
--------------------------------*/
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'], $_POST['booking_type'])) {
    $booking_id = (int) $_POST['cancel_booking_id'];
    $booking_type = $_POST['booking_type'];

    if (!in_array($booking_type, ['zoo', 'hotel', 'educational'])) {
        $message = "Invalid booking type for cancellation.";
    } else {
        $table = $booking_type . '_bookings';
        $deleteQuery = "DELETE FROM $table WHERE id = $1 AND user_email = $2";
        $resultDelete = pg_query_params($connection, $deleteQuery, [$booking_id, $user_email]);

        if ($resultDelete) {
            $message = ucfirst($booking_type) . " booking #$booking_id cancelled successfully.";
        } else {
            $message = "Failed to cancel booking: " . pg_last_error($connection);
        }
    }
}

/* -------------------------------
   FETCH ALL BOOKINGS
--------------------------------*/
$all_bookings = [];

/* Zoo bookings */
$zooQuery = "SELECT id, 'Zoo' AS type, ticket_type AS details, num_adults, num_children, visit_date AS date, created_at
             FROM zoo_bookings
             WHERE user_email = $1";
$resultZoo = pg_query_params($connection, $zooQuery, [$user_email]);
if ($resultZoo) {
    while ($row = pg_fetch_assoc($resultZoo)) {
        $all_bookings[] = $row + ['cancelable' => true, 'booking_type' => 'zoo'];
    }
}

/* Hotel bookings */
$hotelQuery = "SELECT id, 'Hotel' AS type, room_type AS details, '-' AS num_adults, '-' AS num_children, checkin AS date, created_at
               FROM hotel_bookings
               WHERE user_email = $1";
$resultHotel = pg_query_params($connection, $hotelQuery, [$user_email]);
if ($resultHotel) {
    while ($row = pg_fetch_assoc($resultHotel)) {
        $all_bookings[] = $row + ['cancelable' => true, 'booking_type' => 'hotel'];
    }
}

/* Educational bookings */
$eduQuery = "SELECT id, 'Educational' AS type, course_type AS details, '-' AS num_adults, '-' AS num_children, visit_date AS date, created_at
             FROM educational_bookings
             WHERE user_email = $1";
$resultEdu = pg_query_params($connection, $eduQuery, [$user_email]);
if ($resultEdu) {
    while ($row = pg_fetch_assoc($resultEdu)) {
        $all_bookings[] = $row + ['cancelable' => true, 'booking_type' => 'educational'];
    }
}

/* Sort by date ascending */
usort($all_bookings, function($a, $b) {
    return strtotime($a['date']) - strtotime($b['date']);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Your Bookings</title>
    <link rel="stylesheet" href="../css/info_bar.css" />
    <link rel="stylesheet" href="../css/top_bar.css">
    <link rel="stylesheet" href="../css/users.css" />
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background-color: #f2f2f2; }
        .cancel-btn {
            color: red;
            font-weight: bold;
            cursor: pointer;
            border: none;
            background: none;
        }
        .message {
            margin-top: 15px;
            font-weight: bold;
            color: green;
        }
        .no-bookings {
            margin-top: 20px;
            font-style: italic;
            color: #555;
        }
        form { margin: 0; }
        .button-box {
            margin: 20px 0;
            display: flex;
            gap: 15px;
        }
        .button-box a {
            text-decoration: none;
            color: white;
            background-color: #faca39;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
        }
        .button-box a:hover {
            background-color: #e0b231;
        }
    </style>
</head>
<body>

    <!-- Prevent flash of unstyled content -->
    <style>body { visibility: hidden; }</style>

    <!-- Dynamic info bar -->
    <div id="info_bar"></div>

    <!-- Dynamic top navigation bar -->
    <div id="top_bar"></div>

    <div class="maincontent" style="padding: 2rem; max-width: 900px; margin: auto;">
        <h1>Your Bookings</h1>

        <!-- Account buttons -->
        <div class="button-box">
            <a href="../pages/index.php">Home</a>
            <a href="../php/logout.php">Logout</a>
        </div>

        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <?php if (empty($all_bookings)): ?>
            <p class="no-bookings">You have no bookings yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Details</th>
                        <th>Adults / Students</th>
                        <th>Children</th>
                        <th>Date</th>
                        <th>Booked At</th>
                        <th>Cancel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_bookings as $b): ?>
                        <tr>
                            <td><?= htmlspecialchars($b['id']) ?></td>
                            <td><?= htmlspecialchars($b['type']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($b['details'])) ?></td>
                            <td><?= htmlspecialchars($b['num_adults']) ?></td>
                            <td><?= htmlspecialchars($b['num_children']) ?></td>
                            <td><?= htmlspecialchars($b['date']) ?></td>
                            <td><?= htmlspecialchars($b['created_at']) ?></td>
                            <td>
                                <?php if ($b['cancelable']): ?>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                    <input type="hidden" name="cancel_booking_id" value="<?= htmlspecialchars($b['id']) ?>">
                                    <input type="hidden" name="booking_type" value="<?= htmlspecialchars($b['booking_type']) ?>">
                                    <button type="submit" class="cancel-btn">X</button>
                                </form>
                                <?php else: ?>
                                    <span style="color: #888;">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div id="footer"></div>

    <script>
        // Load shared sections
        function loadSection(path, targetId, callback) {
            fetch(path)
                .then(res => res.text())
                .then(html => {
                    document.getElementById(targetId).innerHTML = html;
                    if (callback) callback();
                })
                .catch(err => console.error(`Error loading ${path}:`, err));
        }

        loadSection("../sections/top_bar.html", "top_bar", () => {
            const burger = document.getElementById("burger");
            const navLinks = document.getElementById("nav_links");
            if (burger && navLinks) {
                burger.addEventListener("click", () => navLinks.classList.toggle("open"));
            }
        });
        loadSection("../sections/info_bar.php", "info_bar");
        loadSection("../sections/footer.html", "footer");

        window.addEventListener('load', () => {
            document.body.style.visibility = 'visible';
        });
    </script>

</body>
</html>
