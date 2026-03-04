<?php
include '../php/create_table.php'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- Main stylesheet -->
     <link rel="stylesheet" href="../css/info_bar.css">
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>


    <!-- this fixes the weird stage in loading where the html is visible before the css is applied-->

    <style>
        body {
          visibility: hidden;
        }
    </style>

    <!-- ================= HERO SECTION ================= -->
    <div id="hero">

        <!-- Dynamic info bar -->
        <div id="info_bar"></div>


        <!-- Dynamic top navigation bar -->
        <div id="top_bar"></div>

        <!-- Hero content -->
        <div class="container_hero">
            <div>NEW ATTRACTIONS AND ENCLOSURES OPEN NOW!</div>

            <div>
                <a class="button" href="../pages/booking.html">Book Tickets</a>
                <a class="button" href="#attractions">Explore Our Zoo</a>
            </div>
        </div>

    </div>
    <!-- ================= END HERO SECTION ================= -->

    <div id="prices"></div>
    <style>
        #prices{
            width: 100%;
            
        }
    </style>
    <!-- Attractions section -->
     <section id="attractions">
    <div id="our_attractions"></div>
     </section>
     

    <!-- Educational section -->
    <div id="educational_section"></div>

    <!-- Footer -->
    <div id="footer"></div>


    <!-- ================= SCRIPT SECTION ================= -->
    <script>
        /**
         * Utility function to load HTML partials into a target element
         */
        function loadSection(path, targetId, callback) {
            fetch(path)
                .then(res => res.text())
                .then(html => {
                    document.getElementById(targetId).innerHTML = html;
                    if (callback) callback();
                })
                .catch(err => console.error(`Error loading ${path}:`, err));
        }

        // Load top bar and attach burger menu functionality
        loadSection("../sections/top_bar.html", "top_bar", () => {
            const burger = document.getElementById("burger");
            const navLinks = document.getElementById("nav_links");

            if (burger && navLinks) {
                burger.addEventListener("click", () => {
                    navLinks.classList.toggle("open");
                });
            }
        });

        // Load remaining reusable sections
        loadSection("../sections/info_bar.php", "info_bar");
        loadSection("../sections/attractions.html", "our_attractions");
        loadSection("../sections/educational.html", "educational_section");
        loadSection("../sections/footer.html", "footer");
        loadSection("../sections/prices_zoo.html","prices");
    </script>
    <!-- ================= END SCRIPT SECTION ================= -->

</body>
</html>
