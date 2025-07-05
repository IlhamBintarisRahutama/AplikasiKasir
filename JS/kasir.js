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

document.querySelector(".btn-save-transaction").addEventListener("click", () => {
  const id_order = document.getElementById("id_order").value;
  const nama_pelanggan = document.getElementById("pelanggan").value;
  const kasir = document.getElementById("kasir").value;
  const status = document.getElementById("status").value;
  const order_type = document.getElementById("order_type").value;
  const total_bayar = parseInt(document.getElementById("total_bayar").value.replace(/\D/g, '')) || 0;
  const dibayar = parseInt(document.getElementById("dibayar").value.replace(/\D/g, '')) || 0;
  const kembali = parseInt(document.getElementById("kembali").value.replace(/\D/g, '')) || 0;

  // Kumpulkan semua item
  const items = [];
  document.querySelectorAll("#order-items-list .order-item").forEach(item => {
    const nama_menu = item.querySelector(".item-name").textContent.trim();
    const harga = parseInt(item.dataset.price);
    const jumlah = parseInt(item.querySelector(".quantity-value").textContent);

    items.push({
      nama_menu,
      harga,
      jumlah
    });
  });

  const mode = document.getElementById("mode_form")?.value || "create";

  if (items.length === 0 && mode === "create") {
    alert("Belum ada item yang dipesan!");
    return;
  }

  if (!nama_pelanggan) {
    alert("Nama pelanggan harus diisi!");
    return;
  }

  if (status === "Bayar Nanti" && dibayar < 0) {
    alert("Dibayar tidak boleh negatif!");
    return;
  }

  if (dibayar < total_bayar && !/bayar nanti/i.test(status)) {
    alert("Pembayaran kurang!");
    return;
  }


  // Kirim ke server
  fetch('../User/simpan_transaksi.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      id_order,
      nama_pelanggan,
      kasir,
      status,
      order_type,
      total_bayar,
      dibayar,
      kembali,
      jumlah_pesanan: items.reduce((sum, item) => sum + item.jumlah, 0),
      items
    })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Transaksi berhasil disimpan!');
        window.location.href = "order.php";
      } else {
        alert('Gagal menyimpan transaksi: ' + data.message);
      }
    })
    .catch(err => {
      console.error(err);
      alert('Terjadi kesalahan pada server.');
    });
});
