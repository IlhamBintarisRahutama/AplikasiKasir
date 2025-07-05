<?php
session_start();
include 'db.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password_input = $_POST['password'];

$query = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($query);

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password_input, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: ../Admin/index.php");
        }
        if ($user['role'] == 'kasir') {
            header("Location: ../KasirOnly/kasir.php");
        }
        exit();
    } else {
        echo "<script>alert('Login gagal! Username atau Password salah'); window.location='../index.php';</script>";
    }
} else {
    echo "<script>alert('Login gagal! Username atau Password salah'); window.location='../index.php';</script>";
}
