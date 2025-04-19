<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$currentUserEmail = $_SESSION['email'];

// Step 1: Get current user's course codes
$userCourses = [];
$sql = "SELECT course_code FROM user_courses WHERE user_email = '$currentUserEmail'";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $userCourses[] = $row['course_code'];
}

// Step 2: Find other users who share those courses
$courseMates = [];

if (!empty($userCourses)) {
    $courseList = "'" . implode("','", $userCourses) . "'";
    $sql = "SELECT DISTINCT user_email, course_code 
            FROM user_courses 
            WHERE course_code IN ($courseList) 
            AND user_email != '$currentUserEmail'";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $courseMates[$row['course_code']][] = $row['user_email'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Materials</title>
    <link rel="stylesheet" href="style.css">
    <style>

         /* Navigation bar */
         nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to right, #2196F3, #E91E63);
            padding: 15px 30px;
            color: white;
        }

        nav .logo {
            font-size: 30px;
            font-weight: bold;
        }

        nav ul {
            list-style: none;
            display: flex;
        }

        nav ul li {
            margin: 0 10px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
        }

        nav ul li a:hover {
            color: #fff200;
        }

        .container {
            width: 70%;
            margin: 30px auto;
        }

        .post-form, .search-bar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .post-form h2 {
            margin-bottom: 15px;
        }

        .post-form input, .post-form textarea, .search-bar input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .post-form textarea {
            resize: vertical;
            height: 100px;
        }

        button {
            padding: 10px 20px;
            background-color: #E91E63;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #2196F3;
        }

        .post-feed {
            margin-top: 20px;
        }

        .post {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .post:hover {
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }


        

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
        }
        .container {
            width: 60%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .department {
            width: 80%;
            padding: 10px;
            margin: 10px auto;
            border-radius: 5px;
            border: 1px solid #4e15d2;
            font-weight: bold;
            background-color: #E91E63;
            color: white;
        }
        .course-list {
            margin-top: 20px;
            text-align: left;
        }
        .course {
            background: #f9f9f9;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
            color: black;
        }
        .course:hover {
            background-color: #ddd;
        }
        .buttons {
            display: flex;
            gap: 10px;
        }
        .button {
            padding: 5px 10px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #E91E63;
            color: white;
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">Ed-Social</div>
        <ul>
            <li><a href="home.html">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="cgpa.php">Assume CGPA</a></li>
            <li><a href="material.html">Materials</a></li>
            <li><a href="course_mates.php"><u>Course-Mates</u></a></li>
            <li><a href="login.html">Logout</a></li>
        </ul>
    </nav>

<div class="container">
    <h2>Course Mates</h2>

    <?php if (!empty($courseMates)) {
        foreach ($courseMates as $courseCode => $emails) {
            echo "<div class='course-group'>";
            echo "<h3>$courseCode</h3>";
            foreach ($emails as $email) {
                echo "<div class='mate'>📧 $email</div>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>No course mates found. You're the only one in your listed courses!</p>";
    } ?>
</div>

</body>
</html>
