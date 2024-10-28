<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Reset and Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f3f4f6;
            color: #333;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        /* Header */
        header {
            background: linear-gradient(135deg, #4a90e2, #34495e);
            padding: 15px 20px;
            color: #fff;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            font-size: 24px;
            font-weight: 700;
        }

        /* Container */
        .container {
            display: flex;
            max-width: 1200px;
            width: 100%;
            margin-top: 80px;
            padding: 0 20px;
        }

        /* Navigation */
        nav {
            width: 250px;
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: width 0.3s ease, padding 0.3s ease;
            position: relative;
        }
        nav.collapsed {
            width: 70px;
            padding: 20px 10px; /* Adjust padding for collapsed view */
        }
        nav .toggle-btn {
            position: absolute;
            top: 10px;
            right: -15px;
            width: 30px;
            height: 30px;
            background: #4a90e2;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.3s ease;
            z-index: 100;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        nav .toggle-btn i {
            transition: transform 0.3s ease;
        }
        nav.collapsed .toggle-btn i {
            transform: rotate(180deg); /* Rotate icon when collapsed */
        }
        nav h2 {
            color: #4a90e2;
            font-size: 20px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
            opacity: 1;
            transition: opacity 0.3s ease;
        }
        nav.collapsed h2 {
            opacity: 0; /* Hide the header in collapsed state */
        }
        nav a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #34495e;
            padding: 15px;
            margin: 12px 0;
            border-radius: 10px;
            transition: background 0.3s, padding 0.3s, color 0.3s;
            opacity: 1;
        }
        nav a:hover {
            background: #e8f0ff;
            color: #4a90e2;
            transform: translateX(10px);
        }
        nav a i {
            margin-right: 12px;
            color: #4a90e2;
            font-size: 18px;
        }
        nav.collapsed a i {
            margin: 0 auto; /* Center icons when collapsed */
            font-size: 22px; /* Adjust icon size for visibility */
        }
        nav.collapsed a span {
            display: none; /* Hide text in collapsed view */
        }

        /* Content */
        .content {
            flex-grow: 1;
            padding: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: margin-left 0.3s ease;
            margin-left: 20px;
        }

        /* Welcome Box */
        .welcome-box {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
            animation: fadeIn 1s ease-in-out;
        }
        .welcome-box i {
            font-size: 36px;
            margin-bottom: 12px;
        }

        /* Slideshow */
        .slideshow-container {
            max-width: 100%;
            position: relative;
            margin: 30px auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .mySlides {
            display: none;
            position: relative;
        }
        .slideshow-container img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 12px;
        }

        /* Slide Buttons */
        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            padding: 16px;
            font-size: 24px;
            color: rgba(255, 255, 255, 0.8);
            transition: color 0.3s;
            user-select: none;
            transform: translateY(-50%);
        }
        .next {
            right: 0;
        }
        .prev:hover, .next:hover {
            color: rgba(255, 255, 255, 1);
        }

        /* Slide Text */
        .text {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

<header>
    <h1>Student Dashboard</h1>
</header>

<div class="container">
    <nav id="sidebar">
        <div class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-angle-double-left"></i></div>
        <h2>Actions</h2>
        <a href="borrow_book.php"><i class="fas fa-book-open"></i><span> Borrow a Book</span></a>
        <a href="return_book.php"><i class="fas fa-undo"></i><span> Return a Book</span></a>
        <a href="view_borrowings.php"><i class="fas fa-list"></i><span> View My Borrowed Books</span></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a>
    </nav>

    <div class="content">
        <div class="welcome-box">
            <i class="fas fa-user-circle"></i>
            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
        </div>

        <div class="slideshow-container">
            <div class="mySlides fade">
                <img src="borrow.jpg" alt="Library Image 1">
                <div class="text">Explore a vast collection of books.</div>
            </div>
            <div class="mySlides fade">
                <img src="return.jpg" alt="Library Image 2">
                <div class="text">Easy borrowing and returning process.</div>
            </div>
            <div class="mySlides fade">
                <img src="access.jpg" alt="Library Image 3">
                <div class="text">Access resources anytime, anywhere.</div>
            </div>

            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>
    </div>
</div>

<script>
    // Toggle Sidebar
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
        const toggleIcon = sidebar.querySelector('.toggle-btn i');
        toggleIcon.classList.toggle('fa-angle-double-left');
        toggleIcon.classList.toggle('fa-angle-double-right');
    }

    // Slideshow
    let slideIndex = 0;
    showSlides();

    function showSlides() {
        let i;
        const slides = document.getElementsByClassName("mySlides");
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";  
        }
        slideIndex++;
        if (slideIndex > slides.length) {slideIndex = 1}    
        slides[slideIndex - 1].style.display = "block";  
        setTimeout(showSlides, 5000);
    }

    function plusSlides(n) {
        slideIndex += n;
        if (slideIndex > slides.length) {slideIndex = 1}    
        if (slideIndex < 1) {slideIndex = slides.length}
        showSlidesManually();
    }

    function showSlidesManually() {
        let i;
        const slides = document.getElementsByClassName("mySlides");
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";  
        }
        slides[slideIndex - 1].style.display = "block";  
    }
</script>

</body>
</html>
