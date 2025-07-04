<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    mysqli_query($conn, "DELETE FROM menu WHERE id=$id");
    header("Location: ../Admin/index.php");
    exit();
}