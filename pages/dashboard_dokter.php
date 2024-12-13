<?php
session_start();
include_once('../config/db.php'); // Pastikan ini adalah jalur yang benar ke file db.php

// Pastikan dokter sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login_dokter.php"); // Ganti dengan halaman login Anda
    exit();
}

// Ambil ID dokter dari session
$dokter_id = $_SESSION['username']; // Ganti ini jika Anda menyimpan ID dokter dengan cara berbeda

// Periksa koneksi ke database
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Ambil daftar pasien
$patients = [];
$stmt = $conn->prepare("SELECT p.id, p.nama, dp.keluhan, dp.no_antrian 
                        FROM daftar_poli dp 
                        JOIN pasien p ON dp.id_pasien = p.id 
                        JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id 
                        WHERE jp.id_dokter = ?");
$stmt->bind_param('s', $dokter_id); // Hanya ada satu placeholder `?`

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}
$stmt->close();

// Ambil jadwal pemeriksaan dokter
$schedules = [];
$sql = "SELECT jp.hari, jp.jam_mulai, jp.jam_selesai, p.nama_poli 
        FROM jadwal_periksa jp 
        JOIN dokter d ON jp.id_dokter = d.id 
        JOIN poli p ON d.id_poli = p.id_poli 
        WHERE d.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $dokter_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }
}
$stmt->close();

// Ambil riwayat pemeriksaan pasien
$patient_history = [];
$sql = "SELECT ph.tgl_periksa, p.nama, ph.catatan, ph.biaya_periksa 
        FROM periksa ph 
        JOIN daftar_poli dp ON ph.id_daftar_poli = dp.id 
        JOIN pasien p ON dp.id_pasien = p.id 
        WHERE dp.id_jadwal IN (SELECT id FROM jadwal_periksa WHERE id_dokter = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $dokter_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patient_history[] = $row;
    }
}
$stmt->close();
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
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
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
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .content {
            margin-left: 270px; /* Memberikan ruang untuk sidebar */
            padding: 20px;
            flex: 1;
        }

        .content h1 {
            color: #2c3e50;
            text-align: center;
        }

        .card {
            background-color: #ecf0f1;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #dcdcdc;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .content {
                margin-left: 220px;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Dashboard Dokter</h2>
        <a href="dashboard_dokter.php">Daftar Pasien</a>
        <a href="jadwal_periksa.php">Jadwal Pemeriksaan</a>
        <a href="riwayat_pasien.php">Riwayat Pasien</a>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="content">
        <h1>Daftar Pasien</h1>
        <div class="card">
            <h2>Pasien Terdaftar</h2>
            <table>
                <tr>
                    <th>No Antrian</th>
                    <th>Nama Pasien</th>
                    <th>Keluhan</th>
                </tr>
                <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?php echo htmlspecialchars($patient['no_antrian']); ?></td>
                    <td><?php echo htmlspecialchars($patient['nama']); ?></td>
                    <td><?php echo htmlspecialchars($patient['keluhan']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <h1>Jadwal Pemeriksaan</h1>
        <div class="card">
            <h2>Jadwal Saya</h2>
            <table>
                <tr>
                    <th>Hari</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Poli</th>
                </tr>
                <?php foreach ($schedules as $schedule): ?>
                <tr>
                    <td><?php echo htmlspecialchars($schedule['hari']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['jam_mulai']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['jam_selesai']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['nama_poli']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <h1>Riwayat Pasien</h1>
        <div class="card">
            <h2>Riwayat Pemeriksaan</h2>
            <table>
                <tr>
                    <th>Tanggal Pemeriksaan</th>
                    <th>Nama Pasien</th>
                    <th>Catatan</th>
                    <th>Biaya Pemeriksaan</th>
                </tr>
                <?php foreach ($patient_history as $history): ?>
                <tr>
                    <td><?php echo htmlspecialchars($history['tgl_periksa']); ?></td>
                    <td><?php echo htmlspecialchars($history['nama']); ?></td>
                    <td><?php echo htmlspecialchars($history['catatan']); ?></td>
                    <td><?php echo htmlspecialchars($history['biaya_periksa']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
