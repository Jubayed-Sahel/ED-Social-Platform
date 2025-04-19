<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $credits = $_POST['credits'];
    $nsu_id = $_POST['nsu_id'];

    $sql = "INSERT INTO users (email, username, password, credits, nsu_id) 
            VALUES ('$email', '$username', '$password', '$credits', '$nsu_id')";

    if ($conn->query($sql) === TRUE) {
        echo "✅ Registration successful!";
    } else {
        echo "❌ Error: " . $conn->error;
    }
}

$conn->close();
?>
