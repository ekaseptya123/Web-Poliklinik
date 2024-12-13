<?php
include_once('../config/db.php');

if (isset($_GET['poli_id'])) {
    $poliId = $_GET['poli_id'];
    $stmt = $pdo->prepare("SELECT id, nama FROM dokter WHERE id_poli = ?");
    $stmt->execute([$poliId]);
    $dokterList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($dokterList);
}
?>
