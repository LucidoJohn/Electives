 <!DOCTYPE html>
<html>
<head>
    <title>Rating Feedback Application</title>
</head>
<body>
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
            

            // Upload a file
$local_file = 'local_file.txt';
$remote_file = 'remote_file.txt';

if (ftp_put($ftp_conn, $remote_file, $local_file, FTP_BINARY)) {
    echo 'File uploaded successfully.';
} else {
    echo 'Failed to upload file.';
}

// Download a file
$local_file = 'downloaded_file.txt';
$remote_file = 'remote_file.txt';

if (ftp_get($ftp_conn, $local_file, $remote_file, FTP_BINARY)) {
    echo 'File downloaded successfully.';
} else {
    echo 'Failed to download file.';
}

// Upload an attachment
$attachment_path = 'attachment_file.pdf';
$attachment_remote_path = 'attachment_file.pdf';

if (ftp_put($ftp_conn, $attachment_remote_path, $attachment_path, FTP_BINARY)) {
    echo 'Attachment uploaded successfully.';
} else {
    echo 'Failed to upload attachment.';
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
    <hr>
    <h2>Submit a Review</h2>
    <form method="POST" enctype="multipart/form-data">
        User: <input type="text" name="user" required><br>
        Rating: <input type="number" name="rating" min="1" max="5" required><br>
        Review: <textarea name="review" required></textarea><br>
        Attachment: <input type="file" name="attachment"><br>
        <input type="submit" value="Submit Review">
    </form>
</body>
</html>