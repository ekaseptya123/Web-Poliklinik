<?php
session_start();
require '../config/db.php'; // Pastikan jalur ini benar

// Cek apakah dokter sudah login
if (!isset($_SESSION['id'])) {
    header("Location: login_dokter.php");
    exit();
}

// Ambil ID dokter dari session
$dokter_id = $_SESSION['id'];

// Ambil riwayat pemeriksaan pasien yang mendaftar dan sudah diperiksa
$riwayatQuery = $pdo->prepare("
    SELECT p.nama, p.alamat, p.no_hp, p.no_ktp, p.no_rm, rp.tgl_periksa, rp.catatan, rp.id AS id_periksa
    FROM pasien p 
    JOIN daftar_poli dp ON p.id = dp.id_pasien 
    JOIN periksa rp ON rp.id_daftar_poli = dp.id 
    WHERE dp.id_jadwal IN (SELECT id FROM jadwal_periksa WHERE id_dokter = ?)
");
$riwayatQuery->execute([$dokter_id]);
$riwayat_pemeriksaan = $riwayatQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemeriksaan Pasien</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
        .detail-button {
            background-color: #3498db;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .detail-button:hover {
            background-color: #2980b9;
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
        <h1>Riwayat Pasien</h1>
        <table>
            <thead>
                <tr>
                    <th>No RM</th> <!-- Tambahkan kolom No RM -->
                    <th>Nama Pasien</th>
                    <th>Alamat</th>
                    <th>No HP</th>
                    <th>No KTP</th>
                    <th>Tanggal Pemeriksaan</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($riwayat_pemeriksaan): ?>
                    <?php foreach ($riwayat_pemeriksaan as $riwayat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($riwayat['no_rm']); ?></td> <!-- Tampilkan No RM -->
                            <td><?php echo htmlspecialchars($riwayat['nama']); ?></td>
                            <td><?php echo htmlspecialchars($riwayat['alamat']); ?></td>
                            <td><?php echo htmlspecialchars($riwayat['no_hp']); ?></td>
                            <td><?php echo htmlspecialchars($riwayat['no_ktp']); ?></td>
                            <td><?php echo htmlspecialchars($riwayat['tgl_periksa']); ?></td>
                            <td><?php echo htmlspecialchars($riwayat['catatan']); ?></td>
                            <td>
                                <a href="../pages/detail_pemeriksaan.php?id=<?php echo $riwayat['id_periksa']; ?>" class="detail-button">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">Tidak ada riwayat pemeriksaan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>