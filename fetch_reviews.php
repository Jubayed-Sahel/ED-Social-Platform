<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$faculty = isset($_GET['faculty']) ? trim($_GET['faculty']) : "";

if (!empty($faculty)) {
    $sql = "SELECT faculty_name, course_name, rating, review, user_email FROM reviews WHERE faculty_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $faculty . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT faculty_name, course_name, rating, review, user_email FROM reviews ORDER BY id DESC";
    $result = $conn->query($sql);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='post'>";
        echo "<h3>" . htmlspecialchars($row["faculty_name"]) . " - " . htmlspecialchars($row["course_name"]) . "</h3>";
        echo "<p>" . str_repeat("⭐", (int)$row["rating"]) . "</p>";
        echo "<p>" . htmlspecialchars($row["review"]) . "</p>";
        echo "<p><strong>Posted by:</strong> " . htmlspecialchars($row["user_email"]) . "</p>";
        echo "</div>";
    }
} else {
    echo "<p>No reviews found.</p>";
}

$conn->close();
?>
