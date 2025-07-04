<?php
include 'db.php'; // koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_pengguna'];
    $username = $_POST['username'];
    $telepon = $_POST['telepon'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $alamat = $_POST['alamat'];
    $level = $_POST['level']; // Admin atau Kasir

    // upload foto
    $foto_nama = $_FILES['foto']['name'];
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $folder = "../Cover/cover_user/";
    $path = "Cover/cover_user/" . $foto_nama;

    if (move_uploaded_file($foto_tmp, $folder . $foto_nama)) {
        $sql = "INSERT INTO users (nama, username, no_telpon, password, alamat, role, gambar_user)
                VALUES ('$nama', '$username', '$telepon', '$password', '$alamat', '$level', '$path')";

        if (mysqli_query($conn, $sql)) {
            header("Location: ../Admin/pengguna.php");
            exit();
        } else {
            echo "Gagal menyimpan ke database: " . mysqli_error($conn);
        }
    } else {
        echo "Gagal upload foto.";
    }
} else {
    echo "Metode tidak diizinkan.";
}
