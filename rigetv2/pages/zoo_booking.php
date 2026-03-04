<?php
session_start();
require_once __DIR__ . '/../php/connection.php';


if (!isset($_SESSION['user_id'])) {
    setcookie('redirect_after_login', $_SERVER['REQUEST_URI'], time() + 300, '/', '', false, true);
    header("Location: ../pages/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Zoo Visit Booking</title>

<link rel="stylesheet" href="../css/login_form.css">
<link rel="stylesheet" href="../css/info_bar.css">
<link rel="stylesheet" href="../css/index.css">
</head>
<body>

<div id="info_bar"></div>
<div id="top_bar"></div>

<div class="maincontent">
   <div id="form_container">
      <h1>Zoo Visit Booking</h1>

      <form method="POST" id="zooBookingForm" action="../php/create_zoo_booking.php">

         <label for="ticket_type">Ticket Type</label>
         <select id="ticket_type" name="ticket_type">
            <option value="">-- Select Ticket Type --</option>
            <option value="standard">Standard</option>
            <option value="premium">Premium</option>
         </select>

         <label for="num_adults">Number of Adults</label>
         <input type="number" id="num_adults" name="num_adults" min="0" max="30" value="1">

         <label for="num_children">Number of Children</label>
         <input type="number" id="num_children" name="num_children" min="0" max="30" value="0">

         <label for="visit_date">Visit Date</label>
         <input id="visit_date" type="date" name="visit_date">

         <button type="submit">Book Now</button>


         <p id="totalCost" style="margin-top: 15px; font-weight: bold;">Total: $0</p>
      </form>
   </div>
</div>

<div id="footer"></div>

<script>

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
        burger.addEventListener("click", () => navLinks.classList.toggle("open"));
    }
});
loadSection("../sections/info_bar.php", "info_bar");
loadSection("../sections/footer.html", "footer");


document.getElementById('zooBookingForm').addEventListener('submit', function(e) {
    let errors = [];
    const ticketType = document.getElementById('ticket_type').value;
    const visitDate = document.getElementById('visit_date').value;
    const adults = parseInt(document.getElementById('num_adults').value, 10) || 0;
    const children = parseInt(document.getElementById('num_children').value, 10) || 0;
    const today = new Date().setHours(0,0,0,0);

    if (!ticketType) errors.push("Ticket type is required.");
    if (!visitDate) errors.push("Visit date is required.");
    if (adults < 0) errors.push("Number of adults cannot be negative.");
    if (children < 0) errors.push("Number of children cannot be negative.");
    if (adults === 0 && children === 0) errors.push("At least one visitor is required.");

    if (visitDate && new Date(visitDate) < today) {
        errors.push("Visit date cannot be in the past.");
    }

    if ((adults + children) > 30) {
        errors.push("You can book a maximum of 30 tickets (adults and children combined).");
    }

    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join("\n"));
    }
});


const ticketPrices = {
    standard: { adult: 30, child: 20 },
    premium: { adult: 50, child: 35 }
};

function updateTotalCost() {
    const ticketType = document.getElementById('ticket_type').value;
    const adults = parseInt(document.getElementById('num_adults').value, 10) || 0;
    const children = parseInt(document.getElementById('num_children').value, 10) || 0;
    const totalDisplay = document.getElementById('totalCost');

    if (!ticketType) {
        totalDisplay.textContent = "Total: $0";
        return;
    }


    if ((adults + children) > 30) {
        totalDisplay.textContent = "Maximum 30 tickets allowed";
        return;
    }

    const priceAdult = ticketPrices[ticketType].adult;
    const priceChild = ticketPrices[ticketType].child;
    const total = (adults * priceAdult) + (children * priceChild);

    totalDisplay.textContent = `Total: $${total}`;
}

document.getElementById('ticket_type').addEventListener('change', updateTotalCost);
document.getElementById('num_adults').addEventListener('input', updateTotalCost);
document.getElementById('num_children').addEventListener('input', updateTotalCost);
document.getElementById('visit_date').addEventListener('change', updateTotalCost);
</script>

</body>
</html>
