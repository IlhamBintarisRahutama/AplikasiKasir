<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $kode = $_POST['kode_menu'];
    $nama = $_POST['nama_menu'];
    $kategori = $_POST['kategori_id'];
    $harga_pokok = $_POST['harga_menu'];
    $harga_jual = $_POST['harga_jual'];
    
    $gambar = $_FILES['gambar_menu']['name'];
    $path = "";
    if ($gambar !== '') {
        $tmp = $_FILES['gambar_menu']['tmp_name'];
        $path = "cover/menu/" . $gambar;
        move_uploaded_file($tmp, "../" . $path);
        $update = ", gambar_menu = '$path'";
    } else {
        $update = "";
    }

    $sql = "UPDATE menu SET kode_menu='$kode', nama_menu='$nama', kategori_id='$kategori', harga_pokok='$harga_pokok', harga_jual='$harga_jual' $update WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../Admin/index.php");
    } else {
        echo "Gagal update";
    }
}
