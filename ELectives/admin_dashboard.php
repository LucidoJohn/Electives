<?php
// Check if the user is authenticated as admin
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: Login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION["username"]; ?> (Admin)!</h2>
    <!-- Admin dashboard content -->
   <?php
     // MySQL database configuration
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "12345";
    $db_name = "feedback_app";

    // FTP configuration
    $ftp_host = "127.0.0.1";
    $ftp_user = "Lucido";
    $ftp_pass = "12345";

    // Connect to the MySQL database
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the user is an admin
    function isAdmin($user) {
        // Implement your logic to determine if the user is an admin
        // For simplicity, let's assume the admin username is "admin"
        return ($user === "admin");
    }
    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = $_POST["user"];
        $rating = $_POST["rating"];
        $review = $_POST["review"];
        $attachment = $_FILES["attachment"];

        // Save the review details in the database
        $sql = "INSERT INTO reviews (user, rating, review) VALUES ('$user', '$rating', '$review')";
        if ($conn->query($sql) === TRUE) {
            $review_id = $conn->insert_id;
            echo "Review submitted successfully with ID: " . $review_id . "<br>";

            // Upload attachment using FTP
            $ftp_conn = ftp_connect($ftp_host);
            $ftp_login = ftp_login($ftp_conn, $ftp_user, $ftp_pass);
            if ($ftp_conn && $ftp_login) {
                $attachment_name = $attachment["name"];
                $attachment_tmp = $attachment["tmp_name"];
                $upload_path = "/attachments/" . $review_id . "_" . $attachment_name;
                if (ftp_put($ftp_conn, $upload_path, $attachment_tmp, FTP_BINARY)) {
                    echo "Attachment uploaded successfully.<br>";
                } else {
                    echo "Failed to upload attachment.<br>";
                }
                ftp_close($ftp_conn);
            } else {
                echo "FTP connection failed.<br>";
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Display the reviews
    $sql = "SELECT * FROM reviews";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $review_id = $row["id"];
            $user = $row["user"];
            $rating = $row["rating"];
            $review = $row["review"];

            echo "Review ID: " . $review_id . "<br>";
            echo "User: " . $user . "<br>";
            echo "Rating: " . $rating . "<br>";
            echo "Review: " . $review . "<br>";

            // Display download link for admin
            if (isAdmin($user)) {
                echo '<a href="download.php?id=' . $review_id . '">Download Attachment</a>';
            }

            echo "<hr>";
        }
    } else {
        echo "No reviews found.";
    }

    // Close the database connection
    $conn->close();
    ?>
     <h2>Logout Form</h2>
    <form action="logout.php" method="post">
      
        <button type="submit">Logout</button>
    </form>
    <br>
    <a href="Login.html"></a>
</body>
</html>