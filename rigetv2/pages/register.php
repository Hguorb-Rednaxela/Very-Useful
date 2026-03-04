<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
<link rel="stylesheet" href="../css/login_form.css">
<link rel="stylesheet" href="../css/info_bar.css">
<link rel="stylesheet" href="../css/index.css">
</head>
<body>

<!-- Dynamic info bar -->
<div id="info_bar"></div>

<!-- Dynamic top navigation bar -->
<div id="top_bar"></div>

<div class="maincontent">
   <div id="form_container">
      <h1>Register</h1>
      <form method="POST" id="form" action="../php/create_user.php">

         <label for="first_name">First Name</label>
         <input id="first_name" name="name" type="text" placeholder="First Name">

         <label for="surname">Surname</label>
         <input id="surname" type="text" placeholder="Surname" name="surname">

         <label for="email">Email</label>
         <input id="email" type="text" placeholder="Email" name="email">

         <label for="password">Password</label>
         <input id="password" type="password" placeholder="Password" name="password">

         <label for="repeat_password">Repeat Password</label>
         <input id="repeat_password" type="password" placeholder="Repeat Password">

         <button id="submit" type="submit">Register</button>
         <p>Already have an account? <a href="login.php">Login</a></p>
      </form>
   </div>
</div>

<!-- Footer -->
<div id="footer"></div>

<script>
    // Load HTML/PHP sections dynamically
    function loadSection(path, targetId, callback) {
        fetch(path)
            .then(res => res.text())
            .then(html => {
                document.getElementById(targetId).innerHTML = html;
                if (callback) callback();
            })
            .catch(err => console.error(`Error loading ${path}:`, err));
    }

    // Load top bar with burger menu functionality
    loadSection("../sections/top_bar.html", "top_bar", () => {
        const burger = document.getElementById("burger");
        const navLinks = document.getElementById("nav_links");

        if (burger && navLinks) {
            burger.addEventListener("click", () => {
                navLinks.classList.toggle("open");
            });
        }
    });

    // Load other reusable sections
    loadSection("../sections/info_bar.php", "info_bar");
    loadSection("../sections/footer.html", "footer");

    // Form validation
    document.getElementById('form').addEventListener('submit', function(e) {
        let errors = [];
        const firstName = document.getElementById('first_name').value.trim();
        const surname = document.getElementById('surname').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const repeatPassword = document.getElementById('repeat_password').value;

        if (!firstName) errors.push("First Name is required.");
        if (!surname) errors.push("Surname is required.");
        if (!email) {
            errors.push("Email is required.");
        } else {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) errors.push("Invalid email format.");
        }
        if (!password) errors.push("Password is required.");
        else if (password.length < 6) errors.push("Password must be at least 6 characters.");
        if (!repeatPassword) errors.push("Repeat Password is required.");
        else if (password !== repeatPassword) errors.push("Passwords do not match.");

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join("\n"));
        }
    });
</script>
</body>
</html>
