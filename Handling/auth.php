<?php
session_start();
include 'db.php';

$username = $_POST['username'];
$password = md5($_POST['password']);

$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = $conn->query($query);

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] == 'admin') {
        header("Location: ../Admin/index.php");
    } else {
        header("Location: ");
    }
    exit();
} else {
    echo "<script>alert('Login gagal! Username atau password salah'); window.location='login.php';</script>";
}
?>
