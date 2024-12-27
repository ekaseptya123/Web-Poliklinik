<?php
session_start();
require '../config/db.php'; // Pastikan jalur ini benar

// Cek apakah dokter sudah login
if (!isset($_SESSION['id'])) {
    header("Location: login_dokter.php");
    exit();
}

// Ambil ID pemeriksaan dari URL
$id_periksa = $_GET['id'] ?? null;

if ($id_periksa) {
    // Ambil detail pemeriksaan berdasarkan ID
    $detailQuery = $pdo->prepare("
        SELECT 
            p.nama AS nama_pasien, 
            d.nama AS nama_dokter, 
            rp.tgl_periksa, 
            dp.keluhan, 
            rp.catatan, 
            o.nama_obat AS obat, 
            rp.biaya_periksa 
        FROM periksa rp
        JOIN daftar_poli dp ON rp.id_daftar_poli = dp.id 
        JOIN pasien p ON dp.id_pasien = p.id 
        JOIN jadwal_periksa j ON dp.id_jadwal = j.id
        JOIN dokter d ON j.id_dokter = d.id
        JOIN detail_periksa dp_obat ON rp.id = dp_obat.id_periksa
        JOIN obat o ON dp_obat.id_obat = o.id
        WHERE rp.id = ?
    ");
    
    // Eksekusi query
    $detailQuery->execute([$id_periksa]);
    $detail_pemeriksaan = $detailQuery->fetch(PDO::FETCH_ASSOC);

    // Debugging: Tampilkan ID yang dicari
    if (!$detail_pemeriksaan) {
        echo "Detail pemeriksaan tidak ditemukan untuk ID: " . htmlspecialchars($id_periksa);
        exit();
    }
} else {
    header("Location: riwayat_pemeriksaan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemeriksaan Pasien</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .content {
            padding: 20px;
        }
        .content h1 {
            color: #2c3e50;
            text-align: center;
        }
        .detail-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .detail-container label {
            font-weight: bold;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .back-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Detail Pemeriksaan Pasien</h1>
        <div class="detail-container">
            <?php if ($detail_pemeriksaan): ?>
                <p><label>Nama Pasien:</label> <?php echo htmlspecialchars($detail_pemeriksaan['nama_pasien']); ?></p>
                <p><label>Nama Dokter:</label> <?php echo htmlspecialchars($detail_pemeriksaan['nama_dokter']); ?></p>
                <p><label>Tanggal Pemeriksaan:</label> <?php echo htmlspecialchars($detail_pemeriksaan['tgl_periksa']); ?></p>
                <p><label>Keluhan:</label> <?php echo htmlspecialchars($detail_pemeriksaan['keluhan']); ?></p>
                <p><label>Catatan:</label> <?php echo htmlspecialchars($detail_pemeriksaan['catatan']); ?></p>
                <p><label>Obat:</label> <?php echo htmlspecialchars($detail_pemeriksaan['obat']); ?></p>
                <p><label>Biaya Pemeriksaan:</label> <?php echo htmlspecialchars($detail_pemeriksaan['biaya_periksa']); ?></p>
            <?php else: ?>
                <p>Detail pemeriksaan tidak ditemukan.</p>
            <?php endif; ?>
            <a href="../dokter/riwayat_pemeriksaan.php" class="back-button">Kembali</a>
        </div>
    </div>
</body>
</html>