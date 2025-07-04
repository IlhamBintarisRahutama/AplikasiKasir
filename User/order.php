<?php
include '../Handling/db.php';
$query = mysqli_query($conn, "SELECT * FROM transaksi ORDER BY created_at DESC");
$orders = [];
while ($row = mysqli_fetch_assoc($query)) {
    $orders[] = $row;
}

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
    <title>Order - Mazt Budi</title>
    
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
                    <li><a href="../Admin/index.php" class="active">Menu</a></li>
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
                <a href="laporan.php">
                    <i class='bx bxs-report'></i>
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
    
<!-- Daftar Order Start -->
   <div class="content-wrapper">
    <header class="top-navbar">
        <button class="hamburger-btn mobile-toggle-btn" id="menu-toggle-mobile" aria-label="Buka Menu">
            <span></span>
        </button>
        <div class="navbar-left">
            <h1 class="page-title">Order</h1>
        </div>
        </header>

    <main class="main-content">
        <div class="order-controls">
            <div class="date-filter">
                <input type="date" value="2025-05-31">
                <button class="btn btn-search">Search</button>
            </div>
            <button class="btn btn-add-transaction" onclick="window.location.href='kasir.php'">
                <i class='bx bx-plus'></i>
                Tambah Transaksi
            </button>
        </div>

        <div class="order-list-container content-container"> <div class="card-header-orange"> <h3 class="section-title">Daftar Order</h3>
            </div>
            <div class="order-list-header content-body"> 
                <form method="GET" class="order-list-header content-body" style="display: flex; gap: 1rem;">
                <div class="search-box">
                    <input type="text" name="keyword" placeholder="Cari ID Order..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                    <button class="btn btn-search">Search</button>
                </div>
                <div class="status-filter">
                    <span>Status</span>
                    <select name="status" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <option value="Bayar Nanti" <?= (isset($_GET['status']) && $_GET['status'] == 'Bayar Nanti') ? 'selected' : '' ?>>Bayar Nanti</option>
                        <option value="Lunas" <?= (isset($_GET['status']) && $_GET['status'] == 'Lunas') ? 'selected' : '' ?>>Lunas</option>
                    </select>
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
                            <th>Total bayar</th>
                            <th>Dibayar</th>
                            <th>Kembali</th>
                            <th>Tanggal</th>
                            <th>Jenis Order</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include '../Handling/db.php';
                        $where = [];
                        $params = [];

                        if (!empty($_GET['status']) && in_array($_GET['status'], ['Lunas', 'Bayar Nanti'])) {
                            $status = mysqli_real_escape_string($conn, $_GET['status']);
                            $where[] = "status = '$status'";
                        }

                        if (!empty($_GET['keyword'])) {
                            $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
                            $where[] = "id_order LIKE '%$keyword%'";
                        }

                        $whereClause = '';
                        if (!empty($where)) {
                            $whereClause = 'WHERE ' . implode(' AND ', $where);
                        }

                        $query = mysqli_query($conn, "SELECT * FROM transaksi $whereClause ORDER BY created_at DESC");
                        $no = 1;
                        while ($data = mysqli_fetch_assoc($query)) {
                            echo "<tr>";
                            echo "<td>" . $no++ . ".</td>";
                            echo "<td>" . htmlspecialchars($data['id_order']) . "</td>";
                            echo "<td>" . htmlspecialchars($data['nama_pelanggan']) . "</td>";
                            echo "<td>" . number_format($data['total_bayar'], 0, ',', '.') . "</td>";
                            echo "<td>" . number_format($data['dibayar'], 0, ',', '.') . "</td>";
                            echo "<td>" . number_format($data['kembali'], 0, ',', '.') . "</td>";
                            echo "<td>" . $data['created_at'] . "</td>";
                            echo "<td><span class='badge " . strtolower(str_replace(' ', '-', $data['order_type'])) . "'>" . $data['order_type'] . "</span></td>";
                            echo "<td><span class='badge " . strtolower(str_replace(' ', '-', $data['status'])) . "'>" . $data['status'] . "</span></td>";
                            echo "<td>
                                    <button class='action-btn'><i class='bx bx-edit'></i></button>
                                    <a href=\"?delete=" . urlencode($data['id_order']) . "\" onclick=\"return confirm('Yakin ingin menghapus order ini?');\" class=\"action-btn\">
                                        <i class='bx bx-trash'></i>
                                    </a>
                                </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

    <!-- Daftar Order End -->

    <script src="script.js"></script>

</body>
</html>