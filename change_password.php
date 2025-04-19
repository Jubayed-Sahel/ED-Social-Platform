<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oldPassword = $_POST["old_password"];
    $newPassword = $_POST["new_password"];

    // Fetch current password from DB
    $query = "SELECT password FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && $oldPassword === $user['password']) {
        $update = "UPDATE users SET password = '$newPassword' WHERE email = '$email'";
        if (mysqli_query($conn, $update)) {
            $message = "Password updated successfully!";
        } else {
            $message = "Failed to update password!";
        }
    } else {
        $message = "Old password is incorrect!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        input, button, .button-link {
            display: block;
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px auto;
            border-radius: 5px;
            border: 1px solid #ccc;
            text-align: center;
        }
        .button {
            background: #c4446f;
            color: white;
            border: none;
            cursor: pointer;
        }
        .button:hover {
            background: #a23257;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .button-container button, .button-container .button-link {
            width: 48%;
        }
        .button-link {
            background: #c4446f;
            color: white;
            text-decoration: none;
            line-height: 38px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 5px;
        }
        .button-link:hover {
            background: #a23257;
        }
        .message {
            margin-top: 15px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>
        <form method="post">
            <input type="password" name="old_password" placeholder="Enter Old Password" required>
            <input type="password" name="new_password" placeholder="Enter New Password" required>
            <div class="button-container">
                <button type="submit" class="button">Update Password</button>
                <a href="profile.php" class="button-link">Go Back</a>
            </div>
        </form>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
