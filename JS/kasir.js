document.addEventListener("DOMContentLoaded", function () {
  const orderItemsList = document.querySelector("#order-items-list");
  const menuCards = document.querySelectorAll(".menu-card");
  let orderCounter = 1;

  function formatRupiah(number) {
    return `Rp ${number.toLocaleString("id-ID")}`;
  }

  function updateOrderSummary() {
    let orderQuantity = 0;
    let currentOrderTotal = 0;
    let currentItemNo = 1;

    orderItemsList.querySelectorAll(".order-item").forEach((item) => {
      const price = parseFloat(item.dataset.price);
      const qty = parseInt(item.querySelector(".quantity-value").textContent);
      currentOrderTotal += price * qty;

      item.querySelector(".item-no").textContent = currentItemNo++;
      item.querySelector(".item-price").textContent = formatRupiah(
        price * qty
      ).replace("Rp ", "");
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

  function attachOrderItemListeners(orderItemElement) {
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
          orderItemElement.remove();
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

  menuCards.forEach((menuCard) => {
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
          <span class="item-no"></span>
          <span class="item-name">${itemName}</span>
          <div class="item-quantity-control">
            <button class="quantity-btn decrease-qty">-</button>
            <span class="quantity-value">1</span>
            <button class="quantity-btn increase-qty">+</button>
          </div>
          <span class="item-price"></span>
          <button class="item-delete-btn"><i class='bx bx-trash'></i></button>
        `;

        orderItemsList.appendChild(newOrderItem);
        attachOrderItemListeners(newOrderItem);
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

  updateOrderSummary();
});

document.querySelector('.btn-save-transaction').addEventListener('click', function () {
    const id_order = document.getElementById("id_order").value;
    const nama_pelanggan = document.getElementById("pelanggan").value;
    const status = document.getElementById("status").value;
    const order_type = document.getElementById("order_type").value;
    const total_bayar = document.getElementById("total_bayar").value.replace(/\./g, '');
    const dibayar = document.getElementById("dibayar").value.replace(/\./g, '');
    const kembali = document.getElementById("kembali").value.replace(/\./g, '');
    const kasirName = document.getElementById("kasir").value;

    // validasi sederhana
    if (!nama_pelanggan || parseInt(dibayar) < parseInt(total_bayar)) {
        alert("Pastikan data valid. Pelanggan tidak boleh kosong dan pembayaran mencukupi.");
        return;
    }

    // Hitung jumlah_pesanan dari semua item
    const orderItems = document.querySelectorAll("#order-items-list .order-item");
    let jumlah_pesanan = 0;
    orderItems.forEach(item => {
      const qty = parseInt(item.querySelector(".quantity-value").textContent) || 0;
      jumlah_pesanan += qty;
    });

    // Siapkan data
    const formData = new FormData();
    formData.append("id_order", id_order);
    formData.append("nama_pelanggan", nama_pelanggan);
    formData.append("status", status);
    formData.append("kasir", kasirName);
    formData.append("order_type", order_type);
    formData.append("total_bayar", total_bayar);
    formData.append("dibayar", dibayar);
    formData.append("kembali", kembali);
    formData.append("jumlah_pesanan", jumlah_pesanan);

    // Kirim data ke PHP menggunakan fetch
    fetch('simpan_transaksi.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Transaksi berhasil disimpan');
            window.location.href = 'order.php';
        } else {
            alert('Gagal menyimpan transaksi: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan saat mengirim data ke server');
        console.error(error);
    });
});
