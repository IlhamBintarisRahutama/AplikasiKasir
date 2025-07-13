<?php
include '../Handling/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama_pengguna'];
    $username = $_POST['username'];
    $telepon = $_POST['telepon'];
    $password = $_POST['password'];
    $alamat = $_POST['alamat'];
    $level = $_POST['level'];

    // Proses upload foto
    $fotoPath = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../Cover/cover_user/";
        $filename = basename($_FILES['foto']['name']);
        $targetFile = $targetDir . time() . "_" . $filename;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
            $fotoPath = substr($targetFile, 3); // simpan path relatif (tanpa ../)
        }
    }
    // Hash password baru
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    // Buat query update
    $query = "UPDATE users SET nama = ?, username = ?, no_telpon = ?, password = ?, alamat = ?, role = ?";
    $params = [$nama, $username, $telepon, $hashedPassword, $alamat, $level];
    $types = "ssssss";

    if ($fotoPath) {
        $query .= ", gambar_user = ?";
        $params[] = $fotoPath;
        $types .= "s";
    }
    $query .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, $types, ...$params);


    if ($fotoPath) {
        mysqli_stmt_bind_param($stmt, "sssssssi", $nama, $username, $telepon, $hashedPassword, $alamat, $level, $fotoPath, $id);
    } else {
        mysqli_stmt_bind_param($stmt, "ssssssi", $nama, $username, $telepon, $hashedPassword, $alamat, $level, $id);
    }

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Data pengguna berhasil diupdate!'); window.location.href = '../Admin/pengguna.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: ../Admin/pengguna.php");
    exit();
}
