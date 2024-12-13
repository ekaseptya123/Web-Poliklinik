<?php
session_start();
include_once('../config/db.php');  // Pastikan jalur ini benar

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Menghitung jumlah dokter, pasien, dan poli
$stmt = $pdo->query("SELECT COUNT(*) FROM dokter");
$total_doctors = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM pasien");
$total_patients = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM poli");
$total_polies = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #f4f4f4; /* Warna latar belakang halaman */
        }

        .sidebar {
            width: 250px;
            background-color: #34495e; /* Warna sidebar yang lebih gelap */
            color: white;
            height: 100vh;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* Bayangan untuk sidebar */
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            transition: background 0.3s, padding-left 0.3s; /* Transisi untuk efek hover */
        }

        .sidebar a:hover {
            background-color: #2980b9; /* Warna saat hover */
            padding-left: 15px; /* Efek geser saat hover */
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        .content h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px; /* Jarak bawah untuk judul */
        }

        .widget-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .widget {
            background-color: #ecf0f1; /* Warna latar belakang widget */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Bayangan untuk widget */
            text-align: center;
            flex: 1;
            min-width: 200px; /* Minimum width for each widget */
            transition: transform 0.3s; /* Transisi untuk efek hover */
        }

        .widget:hover {
            transform: translateY(-5px); /* Efek angkat saat hover */
        }

        .widget h2 {
            margin: 0;
            color: #2c3e50;
        }

        .widget p {
            font-size: 24px;
            margin: 10px 0 0;
            font-weight: bold; /* Menebalkan teks jumlah */
            color: #2980b9; /* Warna teks jumlah */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Menu Admin</h2>
        <a href="dashboard_admin.php">Dashboard</a>
        <a href="dokter.php">Dokter</a>
        <a href="pasien.php">Pasien</a>
        <a href="obat.php">Obat</a>
        <a href="poli.php">Poli</a>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="content">
        <h1>Dashboard Admin</h1>
        <div class="widget-container">
            <div class="widget">
                <h2>Jumlah Dokter</h2>
                <p><?php echo $total_doctors; ?></p>
            </div>
            <div class="widget">
                <h2>Jumlah Pasien</h2>
                <p><?php echo $total_patients; ?></p>
            </div>
            <div class="widget">
                <h2>Jumlah Poli</h2>
                <p><?php echo $total_polies; ?></p>
            </div>
        </div>
    </div>
</body>
</html>