<?php
require_once 'common/config.php';
session_start();

// --- SIGNUP LOGIC ---
if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Password secure karne ke liye

    $check_email = "SELECT * FROM users WHERE email='$email'";
    $run_check = $conn->query($check_email);

    if ($run_check->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location='signup.php';</script>";
    } else {
        $sql = "INSERT INTO users (full_name, email, password) VALUES ('$name', '$email', '$password')";
        if ($conn->query($sql)) {
            echo "<script>alert('Account created! Please login.'); window.location='login.php';</script>";
        }
    }
}

// --- LOGIN LOGIC ---
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            header("Location: index.php");
        } else {
            echo "<script>alert('Wrong password!'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('No user found with this email!'); window.location='login.php';</script>";
    }
}
?>