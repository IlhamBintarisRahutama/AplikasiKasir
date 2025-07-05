<?php
session_start();
include 'db.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password_input = $_POST['password'];

$query = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($query);

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $stored_hash = $user['password'];

    $is_valid = false;

    // Cek dengan bcrypt
    if (password_verify($password_input, $stored_hash)) {
        $is_valid = true;
    }
    // Cek jika masih pakai MD5
    elseif ($stored_hash === md5($password_input)) {
        $is_valid = true;

        // Upgrade password ke bcrypt
        $new_bcrypt = password_hash($password_input, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $new_bcrypt, $username);
        $stmt->execute();
    }

    if ($is_valid) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: ../Admin/index.php");
        } elseif ($user['role'] == 'kasir') {
            header("Location: ../KasirOnly/kasir.php");
        }
        exit();
    } else {
        echo "<script>alert('Login gagal! Username atau Password salah'); window.location='../index.php';</script>";
    }
} else {
    echo "<script>alert('Login gagal! Username atau Password salah'); window.location='../index.php';</script>";
}
