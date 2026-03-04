<?php
session_start();
require_once __DIR__ . '/../php/connection.php';

/* ---------------------------------------
   LOGIN PROTECTION
--------------------------------------- */
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
<title>Educational Visit Booking</title>

<link rel="stylesheet" href="../css/login_form.css">
<link rel="stylesheet" href="../css/info_bar.css">
<link rel="stylesheet" href="../css/index.css">
</head>
<body>

<div id="info_bar"></div>
<div id="top_bar"></div>

<div class="maincontent">
   <div id="form_container">
      <h1>Educational Visit Booking</h1>

      <form method="POST" id="eduBookingForm" action="../php/create_educational_booking.php">

         <label for="course_type">Course Type</label>
         <select id="course_type" name="course_type">
            <option value="">-- Select Course Type --</option>
            <option value="science">science ($50)</option>
            <option value="animals">animals ($60)</option>
            <option value="welfare">welfate ($55)</option>
         </select>

         <label for="visit_date">Visit Date</label>
         <input id="visit_date" type="date" name="visit_date">

         <button type="submit">Book Now</button>

         <!-- Total cost display -->
         <p id="totalCost" style="margin-top: 15px; font-weight: bold;">Total: $0</p>
      </form>
   </div>
</div>

<div id="footer"></div>

<script>
/* ---------------------------------------
   LOAD SHARED SECTIONS
--------------------------------------- */
function loadSection(path, targetId, callback) {
    fetch(path)
        .then(res => res.text())
        .then(html => { document.getElementById(targetId).innerHTML = html; if (callback) callback(); })
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

/* ---------------------------------------
   CLIENT-SIDE VALIDATION
--------------------------------------- */
document.getElementById('eduBookingForm').addEventListener('submit', function(e) {
    let errors = [];
    const courseType = document.getElementById('course_type').value;
    const visitDate = document.getElementById('visit_date').value;
    const today = new Date().setHours(0,0,0,0);

    if (!courseType) errors.push("Course type is required.");
    if (!visitDate) errors.push("Visit date is required.");

    if (visitDate && new Date(visitDate) < today) {
        errors.push("Visit date cannot be in the past.");
    }

    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join("\n"));
    }
});

/* ---------------------------------------
   TOTAL COST CALCULATION
--------------------------------------- */
const coursePrices = {
    'science': 50,
    'animals': 60,
    'welfare': 55
};

function updateTotalCost() {
    const courseType = document.getElementById('course_type').value;
    const totalDisplay = document.getElementById('totalCost');
    if (!courseType) {
        totalDisplay.textContent = "Total: $0";
        return;
    }
    totalDisplay.textContent = `Total: $${coursePrices[courseType]}`;
}

document.getElementById('course_type').addEventListener('change', updateTotalCost);
document.getElementById('visit_date').addEventListener('change', updateTotalCost);
</script>

</body>
</html>
