<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: ../Admin/pengguna.php");
    exit();
}