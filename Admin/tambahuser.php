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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengguna - Mazt Budi</title>
    
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
            <h1 class="page-title">Tambah User</h1>
            
        </div>

        <div class="content-container">
            <div class="content-header">
                <h2>Tambah User</h2>
            </div>
            <div class="content-body">
                <form action="../Handling/tambah_user.php" method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nama_pengguna">Nama Pengguna</label>
                            <input type="text" id="nama_pengguna" name="nama_pengguna" value="" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="" required>
                        </div>
                        <div class="form-group">
                            <label for="telepon">Telepon</label>
                            <input type="tel" id="telepon" name="telepon" value="" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" value="" required>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input type="text" id="alamat" name="alamat" value="" required>
                        </div>
                        <div class="form-group">
                            <label for="level">Level</label>
                            <select id="level" name="level" required>
                                <option value="admin" selected>Admin</option>
                                <option value="kasir">Kasir</option>
                               
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="foto">Foto</label>
                            <div class="file-upload-preview">
                                <input type="file" id="foto" name="foto" accept="image/*">
                              
                            </div>
                        </div>
                    </div>
                    <div class="form-buttons">
                        <button type="buton" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Tambah Menu -->
    
    <script src="../JS/script.js"></script>

</body>
</html>