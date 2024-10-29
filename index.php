<?php
// Start the session
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect directly to the dashboard if logged in
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Library Management System</title>
    <style>
        /* Reset and full-screen setup */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Full-screen slideshow container */
        .slideshow-section {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .slideshow img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
        }

        .slideshow img.active {
            opacity: 1;
        }

        /* Centered welcome section */
        .welcome-section {
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px); /* Blur effect */
            background: rgba(255, 255, 255, 0.6); /* Semi-transparent */
            padding: 2.5em;
            text-align: center;
            border-radius: 15px;
            max-width: 450px;
            box-shadow: 0px 12px 24px rgba(0, 0, 0, 0.4);
            animation: fadeInUp 1s ease;
        }

        /* Title styling */
        h1 {
            color: #333;
            font-size: 2.2em;
            margin-bottom: 10px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            letter-spacing: 1px;
        }

        /* Description styling */
        p {
            color: #444;
            margin-top: 0;
            font-size: 1.1em;
            line-height: 1.6;
            opacity: 0.9;
        }

        /* Start button with gradient and shadow */
        .start-button {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 36px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: #ffffff;
            text-decoration: none;
            border-radius: 25px;
            font-size: 17px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .start-button:hover {
            background: linear-gradient(135deg, #0056b3, #003f7f);
            transform: scale(1.05);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.4);
        }

        /* Fade-in animation for welcome section */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            h1 {
                font-size: 1.8em;
            }

            .welcome-section {
                padding: 2em;
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <!-- Full-screen Slideshow Section -->
    <div class="slideshow-section">
        <div class="slideshow">
            <img src="book1.jpg" alt="Background Image 1" class="active">
            <img src="book2.jpg" alt="Background Image 2">
            <img src="book3.jpg" alt="Background Image 3">
        </div>
    </div>

    <!-- Centered Welcome Section -->
    <div class="welcome-section">
        <h1>Welcome to the Library Management System</h1>
        <p>Your one-stop solution for managing books, members, and library resources.</p>
        
        <!-- Start Now Button -->
        <a href="login.php" class="start-button">Start Now</a>
    </div>

    <!-- JavaScript for slideshow effect -->
    <script>
        let currentImageIndex = 0;
        const images = document.querySelectorAll(".slideshow img");

        function changeImage() {
            images[currentImageIndex].classList.remove("active");
            currentImageIndex = (currentImageIndex + 1) % images.length;
            images[currentImageIndex].classList.add("active");
        }

        setInterval(changeImage, 5000); // Change image every 5 seconds
    </script>
</body>
</html>
