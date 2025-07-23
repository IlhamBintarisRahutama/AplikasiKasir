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

$id_order = $_GET['id_order'] ?? '';

$items_with_images = [];
if ($id_order) {
    $stmt = $conn->prepare("
        SELECT d.nama_menu, d.harga, d.jumlah, m.gambar_menu
        FROM detail_transaksi d
        JOIN menu m ON d.nama_menu = m.nama_menu
        WHERE d.id_order = ?
    ");
    if (!$stmt) {
        die("Query error: " . $conn->error);
    }

    $stmt->bind_param("s", $id_order);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $items_with_images[] = $row;
    }
    $stmt->close();
}

$detail_list = [];
if ($id_order) {
    $stmt = $conn->prepare("SELECT * FROM detail_transaksi WHERE id_order = ?");
    if ($stmt) {
        $stmt->bind_param("s", $id_order);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $detail_list[] = $row;
        }
        $stmt->close();
    } else {
        die("Query error: " . $conn->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_transaksi'])) {
    $new_status = $_POST['status'];
    $new_dibayar = (int)str_replace('.', '', $_POST['dibayar']);
    $id_order_post = $_POST['id_order'];

    $stmt = $conn->prepare("UPDATE transaksi SET status = ?, dibayar = ? WHERE id_order = ?");
    $stmt->bind_param("sis", $new_status, $new_dibayar, $id_order_post);
    $stmt->execute();
    $stmt->close();

    header("Location: order.php");
    exit;
}

$transaksi_data = null;
if ($id_order) {
    $stmt = $conn->prepare("SELECT * FROM transaksi WHERE id_order = ?");
    if ($stmt) {
        $stmt->bind_param("s", $id_order);
        $stmt->execute();
        $result = $stmt->get_result();
        $transaksi_data = $result->fetch_assoc();
        $stmt->close();
    } else {
        die("Query error: " . $conn->error);
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Order - Mazt Budi</title>
    <link rel="stylesheet" href="../CSS/dashboard3.css">
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
    <div class="main-content">
        <div class="page-header">
            <button class="hamburger-btn" id="menu-toggle-mobile" aria-label="Buka Menu">
                 <span></span>
          </button>
            <h1 class="page-title">Detail Order</h1>
        </div>

        <div class="kasir-main-layout2">
            <div class="menu-column content-container">
                <div class="content-header">
                    <h2>Daftar Menu</h2>
                </div>
                <div class="content-body">
                    <div class="menu-items-grid" id="menu-items-grid">
                        <?php if (empty($items_with_images)): ?>
                            <p>Belum ada item untuk order ini.</p>
                        <?php else: ?>
                            <?php foreach ($items_with_images as $item): ?>
                                <div class="menu-card">
                                    <img src="../<?= htmlspecialchars($item['gambar_menu']) ?>" alt="<?= htmlspecialchars($item['nama_menu']) ?>" width="100">
                                    <h3 class="menu-name"><?= htmlspecialchars($item['nama_menu']) ?></h3>
                                    <p class="menu-price">Harga: IDR <?= number_format($item['harga'], 0, ',', '.') ?></p>
                                    <p class="menu-price">Jumlah: <?= (int)$item['jumlah'] ?></p>
                                    <p class="menu-price">Subtotal: IDR <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="list-order-column">
                <div class="list-order-header">
                    <h2>List Oder</h2>
                </div>
                <div class="list-order-body">
                    <table class="list-order-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Qty</th>
                                <th>Sub Total</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($detail_list)): ?>
                                <tr>
                                    <td colspan="5">Tidak ada item dalam order ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; ?>
                                <?php foreach ($detail_list as $item): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($item['nama_menu']) ?></td>
                                        <td><?= (int)$item['jumlah'] ?></td>
                                        <td>IDR <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></td>
                                        <td>
                                            <form method="post" action="../Handling/hapus_item.php" onsubmit="return confirm('Hapus item ini?');">
                                                <input type="hidden" name="id_item" value="<?= (int)$item['id'] ?>">
                                                <input type="hidden" name="id_order" value="<?= htmlspecialchars($id_order) ?>">
                                                <button type="submit" class="btn-delete-item" title="Hapus">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="order-column content-container">
                <div class="content-header">
                    <h2>Detail Order</h2>
                    
                </div>
                <div class="content-body">
                    <form method="post" class="transaction-save-form">
                        <input type="hidden" name="id_order" value="<?= htmlspecialchars($id_order) ?>">
                        <input type="hidden" id="mode_form" value="update">
                        <div class="form-group">
                            <label for="id_order">ID Order</label>
                            <input type="text" id="id_order" value="<?= htmlspecialchars($id_order ?? '') ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="pelanggan">Nama Pelanggan</label>
                            <input type="text" id="pelanggan" value="<?= htmlspecialchars($transaksi_data['nama_pelanggan'] ?? '') ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="kasir">Nama Kasir</label>
                            <input type="text" id="kasir" value="<?= htmlspecialchars($transaksi_data['kasir'] ?? '') ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status">
                                <option value="Lunas" <?= (isset($transaksi_data['status']) && $transaksi_data['status'] === 'Lunas') ? 'selected' : '' ?>>Lunas</option>
                                <option value="Bayar Nanti" <?= (isset($transaksi_data['status']) && $transaksi_data['status'] === 'Bayar Nanti') ? 'selected' : '' ?>>Bayar Nanti</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="order_type">Order</label>
                            <input type="text" id="order_type" value="<?= htmlspecialchars($transaksi_data['order_type'] ?? '') ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="total_bayar">Total bayar</label>
                            <input type="text" id="total_bayar" value="<?= number_format($transaksi_data['total_bayar'] ?? 0, 0, ',', '.') ?>" readonly> <span class="currency-label">IDR</span>
                        </div>
                        <div class="form-group">
                            <label for="dibayar">Dibayar</label>
                            <input type="text" name="dibayar" id="dibayar" value="<?= number_format($transaksi_data['dibayar'] ?? 0, 0, ',', '.') ?>"> <span class="currency-label">IDR</span>
                        </div>
                        <div class="form-group">
                            <label for="kembali">Kembalian</label>
                            <input type="text" id="kembali" value="0" readonly> <span class="currency-label">IDR</span>
                        </div>
                        <div class="transaction-save-button-wrapper">
                            <button type="submit" name="update_transaksi" class="btn btn-primary btn-save-transaction">
                                <i class='bx bx-save'></i>
                                Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../JS/kasir.js"></script>
    <script src="../JS/script.js"></script>
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

        document.addEventListener('DOMContentLoaded', function() {
            const dibayarInput = document.getElementById('dibayar');
            const kembaliInput = document.getElementById('kembali');
            const totalBayarInput = document.getElementById('total_bayar');

            function formatRupiah(number) {
                return number.toLocaleString('id-ID');
            }

            function hitungKembalian() {
                let totalBayar = parseInt(totalBayarInput.value.replace(/\D/g, '')) || 0;
                let dibayar = parseInt(dibayarInput.value.replace(/\D/g, '')) || 0;
                let kembali = dibayar - totalBayar;

                kembaliInput.value = formatRupiah(kembali);
            }

            dibayarInput.addEventListener('input', function() {
                // Hapus format lama
                this.value = this.value.replace(/[^\d]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                hitungKembalian();
            });

            // hitung awal kalau ada value
            hitungKembalian();
        });
    </script>
</body>

</html>