<?php
include_once('../config/db.php');

if (isset($_GET['dokter_id'])) {
    $dokterId = $_GET['dokter_id'];
    $stmt = $pdo->prepare("SELECT id, hari, jam_mulai, jam_selesai FROM jadwal_periksa WHERE id_dokter = ?");
    $stmt->execute([$dokterId]);
    $jadwalList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($jadwalList);
}
?>
