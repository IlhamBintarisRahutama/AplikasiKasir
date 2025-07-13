<?php
// SESSION & DB CONNECTION
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

// HANDLE TAMBAH KATEGORI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama_kategori'])) {
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);

    if (!empty($nama_kategori)) {
        $check = mysqli_query($conn, "SELECT * FROM kategori WHERE nama_kategori = '$nama_kategori'");
        if (mysqli_num_rows($check) === 0) {
            mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')");
        }
    }
    header("Location: kategori.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kategori - Mazt Budi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../CSS/dashboard.css">
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
            <li><a href="dashboard.php"><i class='bx bxs-dashboard'></i><span class="link-name">Dashboard</span></a></li>
            <li class="nav-item-dropdown open">
                <div class="dropdown-toggle">
                    <i class='bx bx-list-ul'></i>
                    <span class="link-name">Manajemen</span>
                    <i class='bx bx-chevron-down arrow'></i>
                </div>
                <ul class="sub-menu" style="max-height: 200px;">
                    <li><a href="index.php">Menu</a></li>
                    <li><a href="kategori.php" class="active">Kategori</a></li>
                    <li><a href="pengguna.php">Pengguna</a></li>
                </ul>
            </li>
            <li><a href="../User/kasir.php"><i class='bx bx-money-withdraw'></i><span class="link-name">Kasir</span></a></li>
            <li><a href="../User/order.php"><i class='bx bx-cart-alt'></i><span class="link-name">Order</span></a></li>
            <li><a href="../User/laporan.php"><i class='bx bxs-report'></i><span class="link-name">Laporan</span></a></li>
            <li><a href="../Handling/logout.php"><i class='bx bxs-report'></i><span class="link-name">Logout</span></a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="page-header">
            <button class="hamburger-btn" id="menu-toggle-mobile" aria-label="Buka Menu"><span></span></button>
            <h1 class="page-title">Kategori</h1>
        </div>

        <div class="category-grid-container">
            <div class="content-container add-category-card">
                <div class="content-header">
                    <h2>Tambah Kategori</h2>
                </div>
                <div class="content-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="nama_kategori">Nama Kategori</label>
                            <input type="text" id="nama_kategori" name="nama_kategori" placeholder="Nama kategori" required>
                        </div>
                        <div class="form-buttons justify-content-start">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="content-container list-category-card">
                <div class="content-header">
                    <h2>Daftar Kategori</h2>
                    <div class="search-toolbar">
                        <input type="text" class="search-input" placeholder="Cari kategori...">
                        <button class="btn btn-search">Search</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id DESC");
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($kategori)) :
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                                    <td class="action-cell">
                                        <a href="../Handling/delete_kategori.php?id=<?= urlencode($row['id']) ?>"
                                            class="btn-action delete"
                                            title="Hapus"
                                            onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                            <i class='bx bx-trash'></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../JS/script.js"></script>
</body>

</html>