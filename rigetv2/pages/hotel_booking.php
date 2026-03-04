<?php
session_start();
require_once __DIR__ . '/../php/connection.php'; // Make sure your connection.php path is correct

/* ---------------------------------------
   LOGIN PROTECTION
--------------------------------------- */
if (!isset($_SESSION['user_id'])) {
    // Optional: remember intended page for redirect after login
    setcookie(
        'redirect_after_login',
        $_SERVER['REQUEST_URI'],
        time() + 300,  // 5 minutes
        '/',
        '',
        false,
        true
    );

    header("Location: ../pages/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hotel Booking</title>

<link rel="stylesheet" href="../css/login_form.css">
<link rel="stylesheet" href="../css/info_bar.css">
<link rel="stylesheet" href="../css/index.css">
</head>
<body>

<!-- Info bar -->
<div id="info_bar"></div>

<!-- Top navigation bar -->
<div id="top_bar"></div>

<div class="maincontent">
   <div id="form_container">
      <h1>Hotel Booking</h1>

      <form method="POST" id="bookingForm" action="../php/create_booking.php">

         <label for="room_type">Room Type</label>
         <select id="room_type" name="room_type">
            <option value="">-- Select Room Type --</option>
            <option value="standard">Standard ($100/night)</option>
            <option value="deluxe">Deluxe ($150/night)</option>
            <option value="executive">Executive ($200/night)</option>
         </select>

         <label for="checkin">Check-in Date</label>
         <input id="checkin" type="date" name="checkin">

         <label for="checkout">Check-out Date</label>
         <input id="checkout" type="date" name="checkout">

         <button type="submit">Book Now</button>

         <!-- Total cost display -->
         <p id="totalCost" style="margin-top: 15px; font-weight: bold;">Total: $0</p>
      </form>
   </div>
</div>

<!-- Footer -->
<div id="footer"></div>

<script>
/* ---------------------------------------
   LOAD SHARED SECTIONS
--------------------------------------- */
function loadSection(path, targetId, callback) {
    fetch(path)
        .then(res => res.text())
        .then(html => {
            document.getElementById(targetId).innerHTML = html;
            if (callback) callback();
        })
        .catch(err => console.error(err));
}

loadSection("../sections/top_bar.html", "top_bar", () => {
    const burger = document.getElementById("burger");
    const navLinks = document.getElementById("nav_links");

    if (burger && navLinks) {
        burger.addEventListener("click", () => {
            navLinks.classList.toggle("open");
        });
    }
});

loadSection("../sections/info_bar.php", "info_bar");
loadSection("../sections/footer.html", "footer");

/* ---------------------------------------
   CLIENT-SIDE VALIDATION
--------------------------------------- */
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    let errors = [];

    const roomType = document.getElementById('room_type').value;
    const checkin = document.getElementById('checkin').value;
    const checkout = document.getElementById('checkout').value;
    const today = new Date().setHours(0,0,0,0); // today at midnight

    // Room type validation
    if (!roomType) errors.push("Room type is required.");

    // Date validation
    if (!checkin) errors.push("Check-in date is required.");
    if (!checkout) errors.push("Check-out date is required.");

    if (checkin && checkout) {
        const checkinDate = new Date(checkin);
        const checkoutDate = new Date(checkout);

        if (checkoutDate <= checkinDate) {
            errors.push("Check-out date must be after check-in date.");
        }

        if (checkinDate < today) {
            errors.push("Check-in date cannot be in the past.");
        }
    }

    // Show errors if any
    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join("\n"));
    }
});

/* ---------------------------------------
   LIVE TOTAL COST CALCULATION
--------------------------------------- */
const roomPrices = {
    'standard': 100,
    'deluxe': 150,
    'executive': 200
};

function updateTotalCost() {
    const roomType = document.getElementById('room_type').value;
    const checkin = document.getElementById('checkin').value;
    const checkout = document.getElementById('checkout').value;
    const totalDisplay = document.getElementById('totalCost');

    if (!roomType || !checkin || !checkout) {
        totalDisplay.textContent = "Total: $0";
        return;
    }

    const checkinDate = new Date(checkin);
    const checkoutDate = new Date(checkout);

    if (checkoutDate <= checkinDate) {
        totalDisplay.textContent = "Total: $0";
        return;
    }

    const diffTime = checkoutDate - checkinDate;
    const nights = diffTime / (1000 * 60 * 60 * 24); // milliseconds → days
    const pricePerNight = roomPrices[roomType];

    const total = nights * pricePerNight;
    totalDisplay.textContent = `Total: $${total}`;
}

// Update total when user changes room type or dates
document.getElementById('room_type').addEventListener('change', updateTotalCost);
document.getElementById('checkin').addEventListener('change', updateTotalCost);
document.getElementById('checkout').addEventListener('change', updateTotalCost);
</script>

</body>
</html>
