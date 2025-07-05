<?php
include '../Handling/db.php';

// Terima JSON dari fetch
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
  echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
  exit;
}

$id_order = $data['id_order'];
$nama_pelanggan = $data['nama_pelanggan'];
$kasir = $data['kasir'];
$status = $data['status'];
$order_type = $data['order_type'];
$total_bayar = $data['total_bayar'];
$dibayar = $data['dibayar'];
$kembali = $data['kembali'];
$jumlah_pesanan = $data['jumlah_pesanan'];
$items = $data['items'] ?? [];

// 0. Cek apakah transaksi dengan id_order sudah ada
$cek_stmt = $conn->prepare("SELECT COUNT(*) FROM transaksi WHERE id_order = ?");
$cek_stmt->bind_param("s", $id_order);
$cek_stmt->execute();
$cek_stmt->bind_result($count);
$cek_stmt->fetch();
$cek_stmt->close();

if ($count > 0) {
  // 1A. Sudah ada ➜ lakukan UPDATE transaksi
  $stmt = $conn->prepare("UPDATE transaksi SET 
    nama_pelanggan = ?, kasir = ?, status = ?, order_type = ?, total_bayar = ?, dibayar = ?, kembali = ?, jumlah_pesanan = ?
    WHERE id_order = ?");
  $stmt->bind_param(
    "ssssiiiis",
    $nama_pelanggan,
    $kasir,
    $status,
    $order_type,
    $total_bayar,
    $dibayar,
    $kembali,
    $jumlah_pesanan,
    $id_order
  );
} else {
  // 1B. Belum ada ➜ lakukan INSERT transaksi
  $stmt = $conn->prepare("INSERT INTO transaksi 
    (id_order, nama_pelanggan, kasir, status, order_type, total_bayar, dibayar, kembali, jumlah_pesanan) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param(
    "sssssiiii",
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
}

// 2. Eksekusi query transaksi
if ($stmt && $stmt->execute()) {
  $stmt->close();

  // 3. Simpan detail_transaksi
  if (!empty($items)) {
    // Hapus dulu detail lama untuk id_order ini (agar bersih)
    $delete_stmt = $conn->prepare("DELETE FROM detail_transaksi WHERE id_order = ?");
    if ($delete_stmt) {
      $delete_stmt->bind_param("s", $id_order);
      $delete_stmt->execute();
      $delete_stmt->close();
    }

    $detail_stmt = $conn->prepare("INSERT INTO detail_transaksi (id_order, nama_menu, harga, jumlah) VALUES (?, ?, ?, ?)");
    if ($detail_stmt) {
      foreach ($items as $item) {
        $nama_menu = $item['nama_menu'];
        $harga = $item['harga'];
        $jumlah = $item['jumlah'];
        $detail_stmt->bind_param("ssii", $id_order, $nama_menu, $harga, $jumlah);
        $detail_stmt->execute();
      }
      $detail_stmt->close();
    }
  }

  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => 'Gagal eksekusi query transaksi: ' . ($stmt ? $stmt->error : 'Unknown error')]);
  if ($stmt) $stmt->close();
}

$conn->close();
