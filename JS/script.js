document.addEventListener("DOMContentLoaded", function () {
  const mobileMenuButton = document.getElementById("menu-toggle-mobile");
  const body = document.body;

  if (mobileMenuButton) {
    mobileMenuButton.addEventListener("click", () => {
      body.classList.toggle("sidebar-open");
      mobileMenuButton.classList.toggle("is-active");
    });
  }

  const dropdowns = document.querySelectorAll(".nav-item-dropdown");

  dropdowns.forEach((dropdown) => {
    const toggle = dropdown.querySelector(".dropdown-toggle");

    toggle.addEventListener("click", () => {
      dropdowns.forEach((otherDropdown) => {
        if (
          otherDropdown !== dropdown &&
          otherDropdown.classList.contains("open")
        ) {
          otherDropdown.classList.remove("open");
          otherDropdown.querySelector(".sub-menu").style.maxHeight = null;
        }
      });

      const subMenu = dropdown.querySelector(".sub-menu");
      dropdown.classList.toggle("open");
      if (dropdown.classList.contains("open")) {
        subMenu.style.maxHeight = subMenu.scrollHeight + "px";
      } else {
        subMenu.style.maxHeight = null;
      }
    });
  });

  const initiallyOpen = document.querySelector(".nav-item-dropdown.open");
  if (initiallyOpen) {
    const subMenu = initiallyOpen.querySelector(".sub-menu");
    if (subMenu) {
      subMenu.style.maxHeight = subMenu.scrollHeight + "px";
    }
  }
});

// --- KASIR SPECIFIC JAVASCRIPT ---
const menuItems = document.querySelectorAll(".menu-card");
const orderItemsList = document.querySelector(".order-items-list");
let orderCounter = 1;

function formatRupiah(number) {
  return `Rp ${number.toLocaleString("id-ID")}`;
}

function updateOrderSummary() {
  let currentOrderTotal = 0; // Menggunakan variabel lokal
  let currentItemNo = 1;

  orderItemsList.querySelectorAll(".order-item").forEach((item) => {
    const price = parseFloat(item.dataset.price); // Harga per unit
    const qty = parseInt(item.querySelector(".quantity-value").textContent);
    currentOrderTotal += price * qty; // Hitung total

    item.querySelector(".item-no").textContent = currentItemNo++; // Update nomor urut
    item.querySelector(".item-price").textContent = formatRupiah(
      price * qty
    ).replace("Rp ", ""); // Update total harga per item
  });

  document.getElementById("total_bayar").value = formatRupiah(
    currentOrderTotal
  ).replace("Rp ", "");

  const dibayarInput = document.getElementById("dibayar");
  const dibayarValue = parseFloat(dibayarInput.value.replace(/\D/g, "")) || 0;
  document.getElementById("kembali").value = formatRupiah(
    dibayarValue - currentOrderTotal
  ).replace("Rp ", "");
}

// Fungsi untuk memasang event listener pada item order (digunakan saat load dan saat menambah item baru)
function attachOrderItemListeners(orderItemElement) {
  const itemUnitPrice = parseFloat(orderItemElement.dataset.price);

  orderItemElement
    .querySelector(".increase-qty")
    .addEventListener("click", function () {
      const qtySpan = orderItemElement.querySelector(".quantity-value");
      qtySpan.textContent = parseInt(qtySpan.textContent) + 1;
      updateOrderSummary();
    });

  orderItemElement
    .querySelector(".decrease-qty")
    .addEventListener("click", function () {
      const qtySpan = orderItemElement.querySelector(".quantity-value");
      let currentQty = parseInt(qtySpan.textContent);
      if (currentQty > 1) {
        qtySpan.textContent = currentQty - 1;
      } else {
        orderItemElement.remove(); // Hapus item jika kuantitas menjadi 0
      }
      updateOrderSummary();
    });

  orderItemElement
    .querySelector(".item-delete-btn")
    .addEventListener("click", function () {
      orderItemElement.remove();
      updateOrderSummary();
    });
}

// Pasang event listener untuk item-item yang sudah ada di HTML saat DOMContentLoaded
orderItemsList.querySelectorAll(".order-item").forEach((item) => {
  attachOrderItemListeners(item);
});

// Event listener untuk klik menu item
menuItems.forEach((menuCard) => {
  menuCard.addEventListener("click", function () {
    const itemName = this.dataset.name;
    const itemUnitPrice = parseFloat(this.dataset.price);

    let existingItem = null;
    orderItemsList.querySelectorAll(".order-item").forEach((orderItem) => {
      if (orderItem.dataset.name === itemName) {
        existingItem = orderItem;
      }
    });

    if (existingItem) {
      const qtySpan = existingItem.querySelector(".quantity-value");
      qtySpan.textContent = parseInt(qtySpan.textContent) + 1;
    } else {
      const newOrderItem = document.createElement("div");
      newOrderItem.classList.add("order-item");
      newOrderItem.dataset.name = itemName;
      newOrderItem.dataset.price = itemUnitPrice;

      newOrderItem.innerHTML = `
                            <span class="item-no"></span> <span class="item-name">${itemName}</span>
                            <div class="item-quantity-control">
                                <button class="quantity-btn decrease-qty">-</button>
                                <span class="quantity-value">1</span>
                                <button class="quantity-btn increase-qty">+</button>
                            </div>
                            <span class="item-price"></span> <button class="item-delete-btn"><i class='bx bx-trash'></i></button>
                        `;
      orderItemsList.appendChild(newOrderItem);

      attachOrderItemListeners(newOrderItem); // Pasang listener untuk item baru
    }
    updateOrderSummary();
  });
});

document.getElementById("dibayar").addEventListener("input", function () {
  this.value = this.value
    .replace(/[^\d]/g, "")
    .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  updateOrderSummary();
});

updateOrderSummary(); // Panggil saat awal untuk menghitung total dan nomor urut

document
  .querySelectorAll(".category-filter-buttons .filter-btn")
  .forEach((button) => {
    button.addEventListener("click", function () {
      document
        .querySelectorAll(".category-filter-buttons .filter-btn")
        .forEach((btn) => btn.classList.remove("active"));
      this.classList.add("active");
      console.log("Filter by:", this.textContent);
    });
  });

// Chart.js Configuration
// Chart.js Configuration
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = "#4a5568"; // Default text color for charts

// Sales Report Chart (Bar Chart)
const salesReportCtx = document
  .getElementById("salesReportChart")
  .getContext("2d");
new Chart(salesReportCtx, {
  type: "bar",
  data: {
    labels: [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "Mei",
      "Jun",
      "Jul",
      "Ags",
      "Sep",
      "Okt",
      "Nov",
      "Des",
    ],
    datasets: [
      {
        label: "Penjualan (k)",
        data: [
          4500, 2800, 3900, 2100, 3950, 2850, 4300, 1900, 4400, 5200, 3500,
          5600,
        ],
        backgroundColor: "#f97316" /* orange-500 */,
        borderRadius: 6,
      },
    ],
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        grid: {
          color: "#e2e8f0" /* gray-200 */,
          borderDash: [5, 5],
        },
        ticks: {
          callback: function (value) {
            return value / 1000 + "k"; // Format to 2000k, 3000k etc.
          },
        },
      },
      x: {
        grid: {
          display: false,
        },
      },
    },
    plugins: {
      legend: {
        display: false,
      },
      tooltip: {
        backgroundColor: "#2d3748" /* dark gray */,
        titleColor: "#fff",
        bodyColor: "#fff",
        cornerRadius: 6,
        displayColors: false,
        padding: 12,
        callbacks: {
          label: function (context) {
            return `Penjualan: Rp.${context.formattedValue}.000`;
          },
        },
      },
    },
  },
});
