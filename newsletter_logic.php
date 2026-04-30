<?php
require_once 'common/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subscribe_email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['subscribe_email']);

    // Check karein ke email valid hai ya nahi
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format'); window.history.back();</script>";
        exit();
    }

    // Database mein check karein ke pehle se exist toh nahi karti
    $check = "SELECT * FROM subscribers WHERE email = '$email'";
    $res = $conn->query($check);

    if ($res->num_rows > 0) {
        echo "<script>alert('You are already subscribed!'); window.history.back();</script>";
    } else {
        $sql = "INSERT INTO subscribers (email) VALUES ('$email')";
        if ($conn->query($sql)) {
            echo "<script>alert('Thank you for subscribing!'); window.history.back();</script>";
        }
    }
}
?>