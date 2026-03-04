<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Risk Assessment</title>
<link rel="stylesheet" href="css/login_form.css">
</head>
<body>
<div class="maincontent">
   <div id="form_container">
      <h1>Book Risk Assessment</h1>
      <form method="POST" id="bookingForm" action="create_booking.php">
         <label for="time">Booking Date And Time</label>
         <input id="time" type="datetime-local" name="time" required>

         <label for="address">Booking Location</label>
         <input id="address" type="text" placeholder="Address" name="address" required>

         <button type="submit">Book</button>
      </form>
   </div>
</div>

<script>
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    let errors = [];

    const timeInput = document.getElementById('time').value;
    const address = document.getElementById('address').value.trim();

    // Check if date/time is provided
    if (!timeInput) {
        errors.push("Booking date and time is required.");
    } else {
        const selectedDate = new Date(timeInput);
        const now = new Date();
        if (selectedDate <= now) {
            errors.push("Booking date and time must be in the future.");
        }
    }

    // Check if address is provided
    if (!address) {
        errors.push("Booking location is required.");
    }

    // Prevent submission if there are errors
    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join("\n"));
    }
});
</script>
</body>
</html>
