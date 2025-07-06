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
if (isset($_GET['delete'])) {
    $deleteId = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM transaksi WHERE id_order = '$deleteId'");
    header("Location: laporan.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Mazt Budi</title> 
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
     <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <link rel="stylesheet" href="../CSS/dashboard2.css">
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-header">
            <span class="logo">Mazt Budi</span>
        </div>
        
        <div class="admin-profile">
            <img src="https://i.pravatar.cc/40?u=admin" alt="Admin" class="admin-avatar">
            <span class="admin-name">Admin</span>
        </div>

        <ul class="nav-links">
            <li>
                <a href="../Admin/dashboard.php">
                    <i class='bx bxs-dashboard'></i>
                    <span class="link-name">Dashboard</span>
                </a>
            </li>
            <li class="nav-item-dropdown open">
                <div class="dropdown-toggle">
                    <i class='bx bx-list-ul'></i>
                    <span class="link-name">Manajemen</span>
                    <i class='bx bx-chevron-down arrow'></i>
                </div>
                <ul class="sub-menu" style="max-height: 200px;">
                    <li><a href="../Admin/index.php">Menu</a></li>
                    <li><a href="../Admin/kategori.php">Kategori</a></li>
                    <li><a href="../Admin/pengguna.php">Pengguna</a></li>
                </ul>
            </li>
            <li>
                <a href="kasir.php">
                    <i class='bx bx-money-withdraw'></i>
                    <span class="link-name">Kasir</span>
                </a>
            </li>
            <li>
                <a href="order.php">
                    <i class='bx bx-cart-alt'></i>
                    <span class="link-name">Order</span>
                </a>
            </li>
            <li>
                <a href="laporan.php" class="active"> <i class='bx bxs-report'></i>
                    <span class="link-name">Laporan</span>
                </a>
            </li>
            <li>
                <a href="../Handling/logout.php">
                    <i class='bx bxs-report'></i>
                    <span class="link-name">Logout</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="content-wrapper">
        <header class="top-navbar">
            <button class="hamburger-btn mobile-toggle-btn" id="menu-toggle-mobile" aria-label="Buka Menu">
                <span></span>
            </button>
            <div class="navbar-left">
                <h1 class="page-title">Laporan</h1> </div>
        </header>

        <main class="main-content">
            <?php
            include '../Handling/db.php';

            // Default tanggal: awal dan akhir bulan ini
            $today = date('Y-m-d');
            $firstDay = date('Y-m-01');
            $lastDay = date('Y-m-t');

            // Ambil dari GET jika ada, kalau tidak gunakan default
            $from = isset($_GET['from']) && $_GET['from'] !== '' ? $_GET['from'] : $firstDay;
            $to   = isset($_GET['to']) && $_GET['to'] !== '' ? $_GET['to']   : $lastDay;

            $where = [];
            $where[] = "DATE(created_at) BETWEEN '" . mysqli_real_escape_string($conn, $from) . "' AND '" . mysqli_real_escape_string($conn, $to) . "'";

            // Filter keyword jika ada
            if (!empty($_GET['keyword'])) {
                $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
                $where[] = "id_order LIKE '%$keyword%'";
            }

            $whereClause = 'WHERE ' . implode(' AND ', $where);

            // Ambil data transaksi
            $query = mysqli_query($conn, "SELECT * FROM transaksi $whereClause ORDER BY created_at DESC");

            // Variabel total
            $totalQty = 0;
            $totalBayar = 0;
            $no = 1;
            ?>
            <div class="order-controls">
                <form method="GET" class="order-controls">
                    <div class="date-filter">
                        <form method="GET">
                            <input type="date" name="from" value="<?= htmlspecialchars($from) ?>">
                            <span>-</span>
                            <input type="date" name="to" value="<?= htmlspecialchars($to) ?>">
                            <button type="submit" class="btn btn-search">Search</button>
                        </form>
                    </div>
                    <!-- <button type="button" class="btn btn-add-transaction" onclick="window.print()"> 
                        <i class='bx bx-printer'></i> Cetak
                    </button> -->
                </form>
                <a href="cetak_laporan.php" target="_blank" class="btn btn-add-transaction">
                    <i class='bx bx-printer'></i> Cetak
                </a>
            </div>
            <div class="order-list-container content-container">
                <div class="card-header-orange">
                    <h3 class="section-title">Laporan Penjualan</h3> </div>
                <div class="order-list-header content-body">
                    <form method="GET" class="order-list-header content-body">
                        <div class="search-box">
                            <input type="text" name="keyword" placeholder="Cari ID Order...." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                            <button class="btn btn-search">Search</button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">

                    <table>
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>ID Order</th>
                                <th>Pelanggan</th>
                                <th>Kasir</th>
                                <th>Tanggal</th>
                                <th>Qty</th>
                                <th>Total Bayar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($query)) : ?>
                                <tr>
                                    <td><?= $no++ ?>.</td>
                                    <td><?= htmlspecialchars($row['id_order']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                    <td><?= htmlspecialchars($row['kasir'] ?? '-') ?></td>
                                    <td><?= $row['created_at'] ?></td>
                                    <td><?= $row['jumlah_pesanan'] ?? 0 ?></td>
                                    <td><?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                                    <td>
                                        <a href="?delete=<?= urlencode($row['id_order']) ?>" onclick="return confirm('Yakin ingin menghapus transaksi ini?');" class="action-btn">
                                            <i class='bx bx-trash'></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    $totalQty += $row['jumlah_pesanan'] ?? 0;
                                    $totalBayar += $row['total_bayar'];
                                ?>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" style="text-align: right; font-weight: 600;">Total</td>
                                <td style="font-weight: 600;"><?= $totalQty ?></td>
                                <td style="font-weight: 600;"><?= number_format($totalBayar, 0, ',', '.') ?></td>
                                <td></td>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <script src="script.js"></script>

</body>
</html>