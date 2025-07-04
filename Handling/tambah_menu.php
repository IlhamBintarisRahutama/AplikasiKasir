<?php
session_start();
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_menu = $_POST['kode_menu'];
    $nama_menu = $_POST['nama_menu'];
    $kategori_id = $_POST['kategori_id'];
    $harga_pokok = $_POST['harga_menu'];
    $harga_jual  = $_POST['harga_jual'];

    $gambar_path = null;
    if (!empty($_FILES['gambar_menu']['name'])) {
        $target_dir = "../Cover/cover_menu/";
        $file_name = basename($_FILES["gambar_menu"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["gambar_menu"]["tmp_name"]);
        if ($check === false) {
            die("File bukan gambar.");
        }

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            die("Format gambar tidak diizinkan.");
        }

        if (move_uploaded_file($_FILES["gambar_menu"]["tmp_name"], $target_file)) {
            $gambar_path = str_replace("../", "", $target_file);
        } else {
            die("Gagal upload gambar.");
        }
    }

    $stmt = $conn->prepare("INSERT INTO menu (kode_menu, nama_menu, kategori_id, harga_pokok, harga_jual, gambar_menu) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdds", $kode_menu, $nama_menu, $kategori_id, $harga_pokok, $harga_jual, $gambar_path);

    if ($stmt->execute()) {
        header("Location: ../Admin/index.php");
        echo "<script>alert('Menu berhasil ditambahkan'); </script>";
    } else {
        echo "<script>alert('Gagal menambahkan menu'); window.history.back();</script>";
    }
}
?>
