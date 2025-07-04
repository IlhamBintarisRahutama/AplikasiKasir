<?php
include '../Handling/db.php';

// Tangkap parameter tahun dari GET
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Buat array bulan 1-12
$bulanTotals = array_fill(1, 12, 0);

// Ambil transaksi lunas dari DB untuk tahun itu
$query = mysqli_query($conn, "
  SELECT MONTH(created_at) AS bulan, SUM(total_bayar) AS total
  FROM transaksi
  WHERE status = 'Lunas' AND YEAR(created_at) = $tahun
  GROUP BY MONTH(created_at)
");

while ($row = mysqli_fetch_assoc($query)) {
    $bulan = (int)$row['bulan'];
    $total = (int)$row['total'];
    $bulanTotals[$bulan] = $total;
}

// Buat array yang hanya angka total per bulan (Jan = index 0)
$totalsByMonth = array_values($bulanTotals);

// Kembalikan dalam format JSON
header('Content-Type: application/json');
echo json_encode([
    'labels' => [
        "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
        "Jul", "Ags", "Sep", "Okt", "Nov", "Des"
    ],
    'data' => $totalsByMonth
]);
?>
