<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}
if ($_SESSION['role'] == 'user') { 
    header("Location: user.php"); 
    exit();}

include '../Handling/db.php';
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM menu WHERE id = $id"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Menu - Mazt Budi</title>
    
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
            <li>
                <a href="dashboard.php">
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
                    <li><a href="index.php">Menu</a></li>
                    <li><a href="kategori.php">Kategori</a></li>
                    <li><a href="pengguna.php">Pengguna</a></li>
                </ul>
            </li>
            <li>
                <a href="../User/kasir.php">
                    <i class='bx bx-money-withdraw'></i>
                    <span class="link-name">Kasir</span>
                </a>
            </li>
            <li>
                <a href="../User/order.php">
                    <i class='bx bx-cart-alt'></i>
                    <span class="link-name">Order</span>
                </a>
            </li>
            <li>
                <a href="../User/laporan.php">
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

    <!-- Tambah Menu -->
  <div class="main-content">
    
        <div class="page-header">
            <button class="hamburger-btn" id="menu-toggle-mobile" aria-label="Buka Menu">
                 <span></span>
          </button>
            <h1 class="page-title">Update Menu</h1>
        </div>

        <div class="content-container">
            <div class="content-header">
                <h2>Form Update Menu</h2>
            </div>
            <div class="content-body">
                <form action="../Handling/update_menu.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="hidden" name="id" value="<?= $data['id'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="kode_menu">Kode Menu</label>
                        <input type="text" id="kode_menu" name="kode_menu" value="<?= $data['kode_menu'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_menu">Nama Menu</label>
                        <input type="text" id="nama_menu" name="nama_menu" value="<?= $data['nama_menu'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="kategori_menu">Kategori</label>
                            <select id="kategori_menu" name="kategori_id" required>
                            <option value="">Pilih Kategori</option>
                                    <?php
                                    include '../Handling/db.php';
                                    $result = mysqli_query($conn, "SELECT * FROM kategori");
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $selected = ($row['id'] == $data['kategori_id']) ? 'selected' : '';
                                        echo "<option value='{$row['id']}'$selected>{$row['nama_kategori']}</option>";
                                    }
                                    ?>
                            </select>
                    </div>
                    <div class="form-group">
                        <label for="harga_menu">Harga Pokok</label>
                        <input type="text" id="harga_menu" name="harga_menu" value="<?= $data['harga_pokok'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="harga_jual">Harga Jual</label>
                        <input type="text" id="harga_menu" name="harga_jual" value="<?= $data['harga_jual'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gambar_menu">Gambar Menu</label>
                        <input type="file" id="gambar_menu" name="gambar_menu" accept="image/*">
                    </div>
                    <div class="form-buttons">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    <!-- End Tambah Menu -->
    
    <script src="../JS/script.js"></script>

</body>
</html>