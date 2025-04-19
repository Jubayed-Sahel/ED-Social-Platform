<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];  // Store email in session for showing in the home page review bar
        header("Location: home.html");
        exit();
    } else {
        $_SESSION['login_error'] = "Invalid email or password!";
        header("Location: wrong_pass.html");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
