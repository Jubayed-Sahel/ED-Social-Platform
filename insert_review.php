<?php
session_start();

if (!isset($_SESSION['email'])) {
    echo "You must be logged in to post a review.";
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo "Database connection failed: " . $conn->connect_error;
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['faculty']) && !empty($_POST['course']) && !empty($_POST['rating']) && !empty($_POST['review'])) {

        $faculty = trim($_POST['faculty']);
        $course = trim($_POST['course']);
        $rating = intval($_POST['rating']);
        $review = trim($_POST['review']);
        $user_email = $_SESSION['email'];

        if ($rating < 1 || $rating > 5) {
            echo "Invalid rating! Must be between 1 and 5.";
        } else {
            $stmt = $conn->prepare("INSERT INTO reviews (faculty_name, course_name, rating, review, user_email) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiss", $faculty, $course, $rating, $review, $user_email);

            if ($stmt->execute()) {
                echo "Review posted successfully!";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    } else {
        echo "Please fill out all fields!";
    }
}

$conn->close();
?>
