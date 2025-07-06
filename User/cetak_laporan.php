<?php
session_start();
if (!isset($_SESSION['role'])) {
  header("Location: ../index.php");
  exit();
}

if ($_SESSION['role'] != 'admin') {
  header("Location: ../KasirOnly/kasir.php");
  exit();
}
include '../Handling/db.php';

// Ambil filter tanggal jika ada
$where = [];
if (!empty($_GET['from']) && !empty($_GET['to'])) {
  $from = mysqli_real_escape_string($conn, $_GET['from']);
  $to = mysqli_real_escape_string($conn, $_GET['to']);
  $where[] = "DATE(created_at) BETWEEN '$from' AND '$to'";
}

$whereClause = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$query = mysqli_query($conn, "SELECT * FROM transaksi $whereClause ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Cetak Laporan Transaksi</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 14px;
      color: #333;
      margin: 20px;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
    }

    table,
    th,
    td {
      border: 1px solid #000;
    }

    th,
    td {
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    .totals {
      width: 100%;
      margin-top: 10px;
      text-align: right;
    }

    .totals p {
      font-weight: bold;
      margin: 4px 0;
    }

    .footer {
      text-align: center;
      margin-top: 30px;
      font-size: 12px;
    }

    @media print {
      button#print-btn {
        display: none;
      }
    }
  </style>
</head>

<body>
  <button id="print-btn" onclick="window.print()">Cetak / Print</button>

  <h2>Laporan Transaksi Penjualan</h2>

  <?php if (!empty($_GET['from']) && !empty($_GET['to'])): ?>
    <p>Periode: <?= htmlspecialchars($_GET['from']) ?> s/d <?= htmlspecialchars($_GET['to']) ?></p>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>ID Order</th>
        <th>Pelanggan</th>
        <th>Kasir</th>
        <th>Tanggal</th>
        <th>Qty</th>
        <th>Total Bayar</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = 1;
      $totalQty = 0;
      $totalBayar = 0;

      while ($row = mysqli_fetch_assoc($query)) :
        $qty = $row['jumlah_pesanan'] ?? 0;
        $totalQty += $qty;
        $totalBayar += $row['total_bayar'];
      ?>

        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['id_order']) ?></td>
          <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
          <td><?= htmlspecialchars($row['kasir'] ?? '-') ?></td>
          <td><?= $row['created_at'] ?></td>
          <td><?= $qty ?></td>
          <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
    <?php
    // Hitung total harga pokok
    $totalHargaPokok = 0;

    // Filter tanggal yang sama untuk detail
    $dateFilter = '';
    if (!empty($_GET['from']) && !empty($_GET['to'])) {
      $from = mysqli_real_escape_string($conn, $_GET['from']);
      $to = mysqli_real_escape_string($conn, $_GET['to']);
      $dateFilter = "AND DATE(t.created_at) BETWEEN '$from' AND '$to'";
    }

    $labaQuery = mysqli_query($conn, "
      SELECT d.nama_menu, d.jumlah, m.harga_pokok
      FROM detail_transaksi d
      JOIN transaksi t ON d.id_order = t.id_order
      LEFT JOIN menu m ON d.nama_menu = m.nama_menu
      WHERE 1=1 $dateFilter
    ");

    while ($labaRow = mysqli_fetch_assoc($labaQuery)) {
      $hargaPokok = (int)$labaRow['harga_pokok'];
      $jumlah = (int)$labaRow['jumlah'];
      $totalHargaPokok += $hargaPokok * $jumlah;
    }

    $labaBersih = $totalBayar - $totalHargaPokok;
    ?>
  </table>

  <!-- Totals Section -->
  <div class="totals">
    <p>Total Jumlah Pesanan: <?= $totalQty ?></p>
    <p>Total Laba Kotor : Rp <?= number_format($totalBayar, 0, ',', '.') ?></p>
    <p>Total Harga Pokok : Rp <?= number_format($totalHargaPokok, 0, ',', '.') ?></p>
    <p>Total Laba Bersih : Rp <?= number_format($labaBersih, 0, ',', '.') ?></p>
  </div>


  <div class="footer">
    Dicetak pada: <?= date('d-m-Y H:i:s') ?>
  </div>
</body>

</html>