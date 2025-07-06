<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../index.php');
  exit();
}

include 'db.php';

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $result = mysqli_query($conn, "SELECT * FROM kategori WHERE id = $id");
  if (mysqli_num_rows($result) > 0) {
    mysqli_query($conn, "DELETE FROM kategori WHERE id = $id");
  }
}

header('Location: ../Admin/kategori.php');
exit();
?>
