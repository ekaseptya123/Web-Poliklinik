<?php
session_start();
require '../config/db.php'; // Pastikan jalur ini benar

// Cek apakah dokter sudah login
if (!isset($_SESSION['id'])) {
    header("Location: login_dokter.php"); // Ganti dengan halaman login Anda
    exit();
}

// Ambil ID dokter dari session
$dokter_id = $_SESSION['id'];

// Ambil data dokter yang sedang login
$stmt = $pdo->prepare("SELECT * FROM dokter WHERE id = :id");
$stmt->execute([':id' => $dokter_id]);
$dokter = $stmt->fetch(PDO::FETCH_ASSOC);

// Cek apakah dokter ditemukan
if (!$dokter) {
    die("Dokter tidak ditemukan. Silakan periksa ID dokter.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dokter</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            padding: 20px;
        }
        .sidebar h2 {
            text-align: center;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        .content h1 {
            color: #2c3e50;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Dashboard Dokter</h2>
        <a href="../pages/dashboard_dokter.php">Beranda</a>
        <a href="../dokter/update_data.php">Profil</a>
        <a href="../dokter/input_jadwal.php">Jadwal Periksa</a>
        <a href="../dokter/daftar_pasien.php">Daftar Pasien</a>
        <a href="../dokter/riwayat_pemeriksaan.php">Riwayat Pemeriksaan</a>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="content">
        <h1>Selamat Datang, Dr. <?php echo htmlspecialchars($dokter['nama']); ?></h1>
        <p>Ini adalah dashboard dokter. Silakan pilih menu di sidebar untuk mengelola data dan jadwal Anda.</p>
    </div>
</body>
</html>