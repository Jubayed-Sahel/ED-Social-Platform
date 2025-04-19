<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'"));
$courses = mysqli_query($conn, "SELECT * FROM user_courses WHERE user_email = '$email'");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_course'])) {
        $course = $_POST['course_code'];
        if (!empty($course)) {
            mysqli_query($conn, "INSERT INTO user_courses (user_email, course_code) VALUES ('$email', '$course')");
            header("Location: profile.php");
        }
    }

    if (isset($_POST['remove_course'])) {
        $id = $_POST['course_id'];
        mysqli_query($conn, "DELETE FROM user_courses WHERE id = $id AND user_email = '$email'");
        header("Location: profile.php");
    }

    if (isset($_POST['update_credits'])) {
        $credits = $_POST['credits'];
        mysqli_query($conn, "UPDATE users SET credits = $credits WHERE email = '$email'");
        header("Location: profile.php");
    }

    if (isset($_POST['update_cgpa'])) {
        $cgpa = $_POST['cgpa'];
        mysqli_query($conn, "UPDATE users SET cgpa = $cgpa WHERE email = '$email'");
        header("Location: profile.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <style>
        /* NAVBAR */
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
        nav ul li a u {
            text-decoration: underline;
        }

        /* PAGE STYLING */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 60%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2, h3 {
            text-align: center;
        }

        input, button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            font-size: 16px;
        }

        input {
            width: 98%;
            border: 1px solid #ccc;
        }

        .button {
            background: linear-gradient(90deg, #1e90ff, #00bfff);
            color: white;
            cursor: pointer;
            border: none;
        }

        .button:hover {
            background: linear-gradient(90deg, #00bfff, #1e90ff);
        }

        .course-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 5px;
            padding: 5px 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .remove-button {
            background: #c0392b;
            color: white;
            border: none;
            cursor: pointer;
            padding: 5px 10px;
            margin-left: 10px;
            border-radius: 5px;
        }

        .remove-button:hover {
            background: #a93226;
        }

        .change-password-button {
            display: block;
            width: 98%;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            background: #E91E63;
            color: white;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .change-password-button:hover {
            background: darkred;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav>
        <div class="logo">Ed-Social</div>
        <ul>
            <li><a href="home.html">Home</a></li>
            <li><a href="profile.php"><u>Profile</u></a></li>
            <li><a href="cgpa.php">Assume CGPA</a></li>
            <li><a href="material.html">Materials</a></li>
            <li><a href="course_mates.php">Course-Mates</a></li>
            <li><a href="login.html">Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h2>~Welcome~</h2>
        <h1><?php echo $user['username']; ?></h1>

        <h2>Contact Information</h2>
        <p><strong>Email:</strong> <?php echo $user['email']; ?></p>

        <h2>Academic Details</h2>

        <h3>Taken Courses</h3>
        <ul style="list-style: none; padding: 0;">
            <?php while ($course = mysqli_fetch_assoc($courses)) { ?>
                <li class="course-item">
                    <?php echo $course['course_code']; ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                        <button type="submit" name="remove_course" class="remove-button">Remove</button>
                    </form>
                </li>
            <?php } ?>
        </ul>

        <form method="POST">
            <input type="text" name="course_code" placeholder="Enter Course Code" required>
            <button type="submit" name="add_course" class="button">Add Course</button>
        </form>

        <p><strong>Completed Credits:</strong> <?php echo $user['credits']; ?></p>
        <form method="POST">
            <input type="number" name="credits" placeholder="Update Completed Credits" required>
            <button type="submit" name="update_credits" class="button">Update Completed Credits</button>
        </form>

        <p><strong>Current CGPA:</strong> <?php echo $user['cgpa'] ?? 'N/A'; ?></p>
        <form method="POST">
            <input type="number" step="0.01" name="cgpa" placeholder="Update CGPA" required>
            <button type="submit" name="update_cgpa" class="button">Update CGPA</button>
        </form>

        <a href="change_password.php"><button class="change-password-button">Change Password</button></a>
    </div>

</body>
</html>
