<?php
include '../Handling/db.php';

// Ambil data dari POST
$id_order = $_POST['id_order'];
$nama_pelanggan = $_POST['nama_pelanggan'];
$status = $_POST['status'];
$kasir = $_POST['kasir'];
$order_type = $_POST['order_type'];
$total_bayar = $_POST['total_bayar'];
$dibayar = $_POST['dibayar'];
$kembali = $_POST['kembali'];
$jumlah_pesanan = $_POST['jumlah_pesanan'];

// Simpan ke tabel transaksi
$stmt = $conn->prepare("INSERT INTO transaksi (id_order, nama_pelanggan, kasir, status, order_type, total_bayar, dibayar, kembali, jumlah_pesanan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt) {
    $stmt->bind_param(
        "sssssssss",
        $id_order,
        $nama_pelanggan,
        $kasir,
        $status,
        $order_type,
        $total_bayar,
        $dibayar,
        $kembali,
        $jumlah_pesanan
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal eksekusi query']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal prepare query']);
}

$conn->close();
?>
