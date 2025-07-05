<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_item = (int)$_POST['id_item'];
    $id_order = $_POST['id_order'] ?? '';

    if ($id_item) {
        $stmt = $conn->prepare("DELETE FROM detail_transaksi WHERE id = ?");
        $stmt->bind_param("i", $id_item);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: ../User/detail_order.php?id_order=" . urlencode($id_order));
    exit;
}
