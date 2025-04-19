<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user's current CGPA and credits
$userInfo = mysqli_query($conn, "SELECT cgpa, credits FROM users WHERE email = '$email'");
$userData = mysqli_fetch_assoc($userInfo);
$currentCgpa = $userData['cgpa'];
$currentCredits = $userData['credits'];

// Fetch user's courses
$courses = [];
$result = mysqli_query($conn, "SELECT course_code FROM user_courses WHERE user_email = '$email'");
while ($row = mysqli_fetch_assoc($result)) {
    $courses[] = $row['course_code'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assume CGPA</title>
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
            padding: 15px;
            margin: 50px auto;
            width: 60%;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        select, input {
            width: 80%;
            padding: 10px;
            margin: 10px;
            border-radius: 5px;
            border: 1px solid #4e15d2;
        }
        button {
            padding: 10px 15px;
            background-color: #E91E63;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #2196F3;
        }
        .result {
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
            color: #333;
            display: none;
        }
    </style>
</head>
<body>

<nav>
    <div class="logo">Ed-Social</div>
    <ul>
        <li><a href="home.html">Home</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="cgpa.php"><u>Assume CGPA</u></a></li>
        <li><a href="material.html">Materials</a></li>
        <li><a href="course_mates.php">Course-Mates</a></li>
        <li><a href="login.html">Logout</a></li>
    </ul>
</nav>

<div class="container">
    <h2>Assume Your CGPA</h2>
    <p>Your current CGPA: <strong><?php echo $currentCgpa; ?></strong> | Total Credits: <strong><?php echo $currentCredits; ?></strong></p>
    <div id="courses"></div>
    <button onclick="loadCourses()">Load My Courses</button>
    <button onclick="calculateCGPA()">Calculate</button>
    <div class="result" id="result"></div>
</div>

<script>
    const userCourses = <?php echo json_encode($courses); ?>;
    const currentCgpa = <?php echo $currentCgpa ?? 0; ?>;
    const currentCredits = <?php echo $currentCredits ?? 0; ?>;

    function loadCourses() {
        const coursesContainer = document.getElementById("courses");
        coursesContainer.innerHTML = "";

        if (userCourses.length === 0) {
            coursesContainer.innerHTML = "<p>No courses found. Please add courses in your profile first.</p>";
            return;
        }

        userCourses.forEach(course => {
            const courseDiv = document.createElement("div");
            courseDiv.classList.add("course");
            courseDiv.innerHTML = `
                <h4>${course}</h4>
                <select class="grade">
                    <option value="4.0">A</option>
                    <option value="3.7">A-</option>
                    <option value="3.3">B+</option>
                    <option value="3.0">B</option>
                    <option value="2.7">B-</option>
                    <option value="2.3">C+</option>
                    <option value="2.0">C</option>
                    <option value="1.7">C-</option>
                    <option value="1.3">D+</option>
                    <option value="1.0">D</option>
                    <option value="0.7">D-</option>
                    <option value="0.0">F</option>
                </select>
                <input type="number" class="credit" placeholder="Credit Hours" min="1" max="4" required>
            `;
            coursesContainer.appendChild(courseDiv);
        });
    }

    function calculateCGPA() {
        let assumedCredits = 0;
        let assumedGradePoints = 0;

        document.querySelectorAll('.course').forEach(course => {
            const grade = parseFloat(course.querySelector('.grade').value);
            const credit = parseFloat(course.querySelector('.credit').value);
            if (!isNaN(grade) && !isNaN(credit)) {
                assumedGradePoints += grade * credit;
                assumedCredits += credit;
            }
        });

        const totalCredits = currentCredits + assumedCredits;
        const totalGradePoints = (currentCgpa * currentCredits) + assumedGradePoints;
        const predictedCgpa = totalCredits ? (totalGradePoints / totalCredits).toFixed(2) : 'N/A';

        const result = document.getElementById("result");
        result.innerHTML = `Predicted CGPA: ${predictedCgpa}`;
        result.style.display = 'block';
    }
</script>

</body>
</html>
