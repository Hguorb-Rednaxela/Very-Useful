<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
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
      <h1>LOGIN</h1>

      <form action="../php/user_login.php" id="form" method="POST">

         <label for="email">Email</label>
         <input id="email" name="email" type="text" placeholder="Email" required>

         <label for="password">Password</label>
         <input id="password" name="password" type="password" placeholder="Password" required>

         <button id="submit" type="submit">LOGIN</button>

         <p>Don't have an account? <a href="register.php">Register</a></p>
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
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

        if (email === '') {
            errors.push("Email is required.");
        } else {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) errors.push("Invalid email format.");
        }

        if (password === '') errors.push("Password is required.");

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join("\n"));
        }
    });
</script>
</body>
</html>
