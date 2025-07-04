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
                    <li><a href="#" class="active">Menu</a></li>
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
    
<!-- Daftar Menu Start -->
    <main class="main-content">
       <header class="page-header">
         <button class="hamburger-btn" id="menu-toggle-mobile" aria-label="Buka Menu">
                 <span></span>
          </button>
                 <h1 class="page-title">Menu</h1>

         <button class="btn btn-add" onclick="window.location.href='tambah.php'">
                   <i class='bx bx-plus'></i>
                     <span>Tambah Menu</span>
                     </button>
                </header>

        <section class="content-container">
            <div class="content-header">
                <h2>Daftar Menu</h2>
                <div class="search-toolbar">
                    <input type="text" placeholder="Cari menu...." class="search-input">
                    <button class="btn btn-search">Search</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Gambar</th>
                            <th>Id</th>
                            <th>Kategori</th>
                            <th>Nama menu</th>
                            <th>Harga pokok</th>
                            <th>Harga Jual</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 1;
                            $query = mysqli_query($conn, "SELECT menu.*, kategori.nama_kategori 
                                                        FROM menu 
                                                        JOIN kategori ON menu.kategori_id = kategori.id 
                                                        ORDER BY menu.id DESC");

                            while ($row = mysqli_fetch_assoc($query)) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td><img src='../" . $row['gambar_menu'] . "' alt='" . $row['nama_menu'] . "' width='60'></td>";
                                echo "<td>" . $row['kode_menu'] . "</td>";
                                echo "<td>" . $row['nama_kategori'] . "</td>"; // tampilkan nama kategori
                                echo "<td>" . $row['nama_menu'] . "</td>";
                                echo "<td>Rp " . number_format($row['harga_pokok'], 0, ',', '.') . "</td>";
                                echo "<td>Rp " . number_format($row['harga_jual'], 0, ',', '.') . "</td>";
                                echo "<td class='action-cell'>
                                        <form method='GET' action='edit_menu.php' style='display:inline;'>
                                            <input type='hidden' name='id' value='{$row['id']}'>
                                            <button type='submit' class='btn-action edit'><i class='bx bxs-edit'></i></button>
                                        </form>
                                        <form method='POST' action='../Handling/delete_menu.php' style='display:inline;' onsubmit='return confirm(\"Yakin ingin menghapus?\")'>
                                            <input type='hidden' name='id' value='{$row['id']}'>
                                            <button type='submit' class='btn-action delete'><i class='bx bxs-trash'></i></button>
                                        </form>
                                    </td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    
    <!-- Daftar Menu End -->

    <script src="../JS/script.js"></script>

</body>
</html>