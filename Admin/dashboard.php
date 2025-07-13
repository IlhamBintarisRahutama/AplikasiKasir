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
  <title>Dashboard - Mazt Budi</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="../CSS/dashboard2.css">
  <script src="https://kit.fontawesome.com/your-font-awesome-kit-id.js" crossorigin="anonymous"></script>
</head>

<body>

  <nav class="sidebar">
    <div class="sidebar-header">
      <span class="logo">Mazt Budi</span>
      <button class="hamburger-btn" id="hamburger-btn-sidebar-desktop" style="display: none;">
        <span></span>
      </button>
    </div>

    <div class="admin-profile">
      <img src="https://i.pravatar.cc/40?u=admin" alt="Admin" class="admin-avatar">
      <span class="admin-name">Admin</span>
    </div>

    <ul class="nav-links">
      <li>
        <a href="dashboard.php" class="active"> <i class='bx bxs-dashboard'></i>
          <span class="link-name">Dashboard</span>
        </a>
      </li>
      <li class="nav-item-dropdown">
        <div class="dropdown-toggle">
          <i class='bx bx-list-ul'></i>
          <span class="link-name">Manajemen</span>
          <i class='bx bx-chevron-down arrow'></i>
        </div>
        <ul class="sub-menu">
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

  <div class="content-wrapper">
    <header class="top-navbar">
      <button class="hamburger-btn mobile-toggle-btn" id="menu-toggle-mobile" aria-label="Buka Menu">
        <span></span>
      </button>
      
        <h1 class="page-title">Dashboard</h1>
      
    </header>

    <main class="main-content">
      <?php
      include '../Handling/db.php';

      // Hitung jumlah menu
      $menu_result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM menu");
      $menu_count = mysqli_fetch_assoc($menu_result)['count'] ?? 0;

      // Hitung jumlah kategori
      $kategori_result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM kategori");
      $kategori_count = mysqli_fetch_assoc($kategori_result)['count'] ?? 0;

      // Hitung order proses (status != 'Lunas')
      $order_proses_result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM transaksi WHERE status != 'Lunas'");
      $order_proses_count = mysqli_fetch_assoc($order_proses_result)['count'] ?? 0;

      // Hitung order selesai (status = 'Lunas')
      $order_selesai_result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM transaksi WHERE status = 'Lunas'");
      $order_selesai_count = mysqli_fetch_assoc($order_selesai_result)['count'] ?? 0;
      ?>
      <div class="stat-cards-container">
        <div class="stat-card">
          <div class="card-header-orange stat-title">Menu</div>
          <p class="stat-value text-orange-custom"><?= htmlspecialchars($menu_count) ?></p>
        </div>
        <div class="stat-card">
          <div class="card-header-orange stat-title">Kategori</div>
          <p class="stat-value text-orange-custom"><?= htmlspecialchars($kategori_count) ?></p>
        </div>
        <div class="stat-card">
          <div class="card-header-orange stat-title">Order Proses</div>
          <p class="stat-value text-orange-custom"><?= htmlspecialchars($order_proses_count) ?></p>
        </div>
        <div class="stat-card">
          <div class="card-header-orange stat-title">Order Selesai</div>
          <p class="stat-value text-orange-custom"><?= htmlspecialchars($order_selesai_count) ?></p>
        </div>
      </div>

      <div class="content-container">
        <div class="card-header-orange">Laporan Penjualan</div>
        <div class="content-body">
          <div class="flex justify-end items-center mb-4">
            <div class="mb-3">
              <div class="flex justify-end items-center mb-4">
                <label for="tahunSelect">Tahun:</label>
                <select id="tahunSelect"></select>
              </div>
            </div>
          </div>
          <div class="chart-container">
            <canvas id="salesReportChart"></canvas>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="../JS/script.js "></script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const ctx = document.getElementById('salesReportChart').getContext('2d');
      let salesChart;

      // Ambil elemen dropdown tahun
      const tahunSelect = document.getElementById('tahunSelect');

      function loadChartData(tahun) {
        fetch(`.php?tahun=${encodeURIComponent(tahun)}`)
          .then(res => res.json())
          .then(data => {
            if (salesChart) {
              salesChart.destroy();
            }
            salesChart = new Chart(ctx, {
              type: 'bar',
              data: {
                labels: data.labels,
                datasets: [{
                  label: `Penjualan Rp`,
                  data: data.data,
                  backgroundColor: '#f97316',
                  borderRadius: 6
                }]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                  y: {
                    beginAtZero: true,
                    ticks: {
                      callback: v => `Rp.${v.toLocaleString('id-ID')}`
                    }
                  }
                },
                plugins: {
                  legend: {
                    display: false
                  },
                  tooltip: {
                    callbacks: {
                      label: ctx => `Penjualan: Rp.${ctx.parsed.y.toLocaleString('id-ID')}`
                    }
                  }
                }
              }
            });
          })
          .catch(error => {
            console.error('Gagal load data chart:', error);
          });
      }

      ///////////////////////////////

      function loadChartData(tahun) {
        fetch(`sales.php?tahun=${encodeURIComponent(tahun)}`)
          .then(res => res.json())
          .then(data => {
            if (salesChart) {
              salesChart.destroy();
            }
            salesChart = new Chart(ctx, {
              type: 'bar',
              data: {
                labels: data.labels,
                datasets: [{
                  label: `Penjualan Rp`,
                  data: data.data,
                  backgroundColor: '#f97316',
                  borderRadius: 6
                }]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                  y: {
                    beginAtZero: true,
                    ticks: {
                      callback: v => `Rp.${v.toLocaleString('id-ID')}`
                    }
                  }
                },
                plugins: {
                  legend: {
                    display: false
                  },
                  tooltip: {
                    callbacks: {
                      label: ctx => `Penjualan: Rp.${ctx.parsed.y.toLocaleString('id-ID')}`
                    }
                  }
                }
              }
            });
          })
          .catch(error => {
            console.error('Gagal load data chart:', error);
          });
      }

      // Load awal pakai tahun terpilih
      loadChartData(tahunSelect.value);

      // Ganti tahun = reload chart
      tahunSelect.addEventListener('change', () => {
        loadChartData(tahunSelect.value);
      });
    });

    const tahunSelect = document.getElementById("tahunSelect");
    const currentYear = new Date().getFullYear();

    const startYear = currentYear - 5;
    const endYear = currentYear + 2;

    for (let year = endYear; year >= startYear; year--) {
      const option = document.createElement("option");
      option.value = year;
      option.textContent = year;
      tahunSelect.appendChild(option);
    }

    // Default selected = currentYear
    tahunSelect.value = currentYear;
  </script>

</body>

</html>