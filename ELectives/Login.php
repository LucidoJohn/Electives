<?php
// Database connection setup
$servername = "localhost";
$username = "root";
$password = "12345";
$dbname = "feedback_app";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the login form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Query the database to check the credentials
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $userType = $row["user_type"];

        // Set session variables based on user type
        session_start();
        $_SESSION["username"] = $username;
        if ($userType == "admin") {
            $_SESSION["user_type"] = "admin";
            // Redirect to admin dashboard
            header("Location: admin_dashboard.php");
        } else {
            $_SESSION["user_type"] = "user";
            // Redirect to user dashboard
            header("Location: user_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid username or password.";
        header("Location: Login.html?error=" . urlencode($error));
        exit();
    }
}


// Close the database connection
$conn->close();
?>
