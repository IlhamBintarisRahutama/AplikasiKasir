<?php
header('Content-Type: application/json');
include '../Handling/db.php';

$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');

// Inisialisasi array bulan (Jan–Des)
$bulan_labels = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$penjualan_bulanan = array_fill(0, 12, 0);

// Ambil total_bayar per bulan
$query = "
  SELECT 
    MONTH(created_at) AS bulan, 
    SUM(total_bayar) AS total
  FROM transaksi
  WHERE YEAR(created_at) = ?
  GROUP BY MONTH(created_at)
  ORDER BY MONTH(created_at)
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $tahun);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $bulan_index = (int)$row['bulan'] - 1;
  $penjualan_bulanan[$bulan_index] = (int)$row['total'];
}

echo json_encode([
  'labels' => $bulan_labels,
  'data' => $penjualan_bulanan
]);
