<?php
session_start();
include '../Handling/db.php';

// Generate ID Order jika belum ada
$date = date("dmY");
$id_order = 'SP' . substr(str_shuffle("1234567890"), 0, 6) . $date;

// Ambil kategori dari database
$kategori_query = mysqli_query($conn, "SELECT * FROM kategori");
$kategori_list = [];
while ($row = mysqli_fetch_assoc($kategori_query)) {
    $kategori_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier - Mazt Budi</title>
    <link rel="stylesheet" href="../CSS/dashboard1.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
                <a href="#">
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
    
<!-- Daftar Kasir Start -->
      <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Kasir</h1>
            <button class="hamburger-btn" id="hamburger-btn-main">
                <span></span>
            </button>
        </div>

        <div class="kasir-main-layout">
            <div class="menu-column content-container">
                <div class="content-header">
                    <h2>Daftar Menu</h2>
                </div>
                <div class="content-body">
                    <div class="category-filter-buttons">
                        <button class="filter-btn active" data-filter="all">Semua kategori</button>
                        <?php foreach ($kategori_list as $kategori): ?>
                            <button class="filter-btn" data-filter="<?= $kategori['nama_kategori'] ?>"><?= $kategori['nama_kategori'] ?></button>
                        <?php endforeach; ?>
                    </div>

                    <div class="menu-search-bar">
                        <input type="text" placeholder="Cari menu..." class="search-input" id="search-input">
                        <button class="btn btn-search" id="search-btn">Search</button>
                    </div>

                    <div class="menu-items-grid" id="menu-items-grid">
                        <?php
                        $query = mysqli_query($conn, "SELECT menu.*, kategori.nama_kategori FROM menu JOIN kategori ON menu.kategori_id = kategori.id ORDER BY menu.id DESC");
                        while ($menu = mysqli_fetch_assoc($query)) {
                            $kode = $menu['kode_menu'];
                            echo "<div class='menu-card' data-name='{$menu['nama_menu']}' data-price='{$menu['harga_jual']}' data-kategori='{$menu['nama_kategori']}' data-id='{$kode}{$date}'>";
                            echo "<img src='../{$menu['gambar_menu']}' alt='{$menu['nama_menu']}' width='100'>";
                            echo "<h3 class='menu-name'>{$menu['nama_menu']}</h3>";
                            echo "<p class='menu-price'>IDR " . number_format($menu['harga_jual'], 0, ',', '.') . "</p>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="order-column content-container">
                <div class="content-header">
                    <h2>Detail Order</h2>
                </div>
                <div class="content-body">
                    <div class="form-group">
                        <label for="id_order">ID Order</label>
                        <input type="text" id="id_order" value="<?= htmlspecialchars($id_order ?? '') ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="pelanggan">Nama Pelanggan</label>
                        <input type="text" id="pelanggan" value="">
                    </div>

                    <div class="form-group">
                        <label for="kasir">Nama Kasir</label>
                        <select id="kasir" name="kasir">
                            <?php
                            include '../Handling/db.php';
                            $kasir_query = mysqli_query($conn, "SELECT nama FROM users WHERE role = 'kasir'");
                            while ($kasir = mysqli_fetch_assoc($kasir_query)) {
                                echo "<option value=\"" . htmlspecialchars($kasir['nama']) . "\">" . htmlspecialchars($kasir['nama']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="order-items-table-wrapper">
                        <div class="order-items-table-header">
                            <span>No</span>
                            <span>Nama</span>
                            <span>Jmlh</span>
                            <span>Harga</span>
                        </div>
                        <div class="order-items-list" id="order-items-list">
                            <!-- Diisi lewat JS -->
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status">
                            <option value="Lunas">Lunas</option>
                            <option value="Bayar Nanti">Bayar Nanti</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="order_type">Order</label>
                        <select id="order_type">
                            <option value="Take away">Take away</option>
                            <option value="Dine in">Dine in</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="total_bayar">Total bayar</label>
                        <input type="text" id="total_bayar" value="0" readonly> <span class="currency-label">IDR</span>
                    </div>
                    <div class="form-group">
                        <label for="dibayar">Dibayar</label>
                        <input type="text" id="dibayar" value=""> <span class="currency-label">IDR</span>
                    </div>
                    <div class="form-group">
                        <label for="kembali">Kembali</label>
                        <input type="text" id="kembali" value="0" readonly> <span class="currency-label">IDR</span>
                    </div>

                    <div class="transaction-save-button-wrapper">
                        <button class="btn btn-primary btn-save-transaction">
                            <i class='bx bx-save'></i>
                            Simpan Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="../JS/kasir.js"></script>
<script>
// Filtering kategori dari tombol
const filterButtons = document.querySelectorAll(".filter-btn");
const menuCards = document.querySelectorAll(".menu-card");

filterButtons.forEach(btn => {
    btn.addEventListener("click", () => {
        const kategori = btn.dataset.filter;

        filterButtons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        menuCards.forEach(card => {
            const cardKategori = card.dataset.kategori;
            if (kategori === "all" || cardKategori === kategori) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    });
});
</script>
</body>
</html>
