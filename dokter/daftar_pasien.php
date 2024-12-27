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

// Ambil daftar pasien yang akan diperiksa
$pasienQuery = $pdo->prepare("SELECT p.*, dp.id AS id_daftar_poli, dp.keluhan FROM pasien p JOIN daftar_poli dp ON p.id = dp.id_pasien WHERE dp.id_jadwal IN (SELECT id FROM jadwal_periksa WHERE id_dokter = ?)");
$pasienQuery->execute([$dokter_id]);
$daftar_pasien = $pasienQuery->fetchAll(PDO::FETCH_ASSOC);

// Proses pemeriksaan
if (isset($_POST['periksa'])) {
    $id_daftar_poli = $_POST['id_daftar_poli'];
    $catatan = $_POST['catatan'];
    $biaya_periksa = $_POST['biaya_periksa'];
    $id_obat = $_POST['id_obat'];

    // Simpan data pemeriksaan
    try {
        // Simpan ke tabel periksa
        $stmt = $pdo->prepare("INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa, id_dokter) VALUES (?, CURDATE(), ?, ?, ?)");
        $stmt->execute([$id_daftar_poli, $catatan, $biaya_periksa, $dokter_id]);
        $id_periksa = $pdo->lastInsertId();

        // Simpan detail obat
        if (!empty($id_obat)) {
            $stmt = $pdo->prepare("INSERT INTO detail_periksa (id_periksa, id_obat) VALUES (?, ?)");
            $stmt->execute([$id_periksa, $id_obat]);
        }

        $msg = "Pemeriksaan berhasil dilakukan!";
    } catch (PDOException $e) {
        $msg = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan Pasien</title>
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
            margin-top: 40px; /* Jarak lebih banyak di atas tabel */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-button {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            transition: background 0.3s;
            max-width: 120px;
            text-align: center; /* Memastikan teks berada di tengah */
            text-decoration: none; /* Menghilangkan garis bawah */
        }
        .action-button:hover {
            background-color: #27ae60;
        }
        .form-container {
            margin-top: 20px;
            padding: 20px;
            border-radius: 5px;
            background-color: #ecf0f1;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .form-container label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container select,
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border-color 0.3s;
        }
        .form-container input[type="text"]:focus,
        .form-container input[type="number"]:focus,
        .form-container select:focus,
        .form-container textarea:focus {
            border-color: #3498db;
            outline: none;
        }
        .submit-button {
            background-color: #2c3e50;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
            border-radius: 5px;
        }
        .submit-button:hover {
            background-color: #34495e;
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
        <h1>Pemeriksaan Pasien</h1>
        <?php if (isset($msg)) { echo "<p>$msg</p>"; } ?>
        <table>
            <thead>
                <tr>
                    <th>Nama Pasien</th>
                    <th>Keluhan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftar_pasien as $pasien) { 
                    // Cek apakah pasien sudah diperiksa
                    $periksaQuery = $pdo->prepare("SELECT COUNT(*) FROM periksa WHERE id_daftar_poli = ?");
                    $periksaQuery->execute([$pasien['id_daftar_poli']]);
                    $sudah_diperiksa = $periksaQuery->fetchColumn() > 0;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pasien['nama']); ?></td>
                        <td><?php echo htmlspecialchars($pasien['keluhan']); ?></td>
                        <td>
                            <?php if ($sudah_diperiksa): ?>
                                <a href="../pages/edit_pemeriksaan.php?id_daftar_poli=<?php echo $pasien['id_daftar_poli']; ?>" class="action-button">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            <?php else: ?>
                                <a href="../pages/pemeriksaan_pasien.php?id_daftar_poli=<?php echo $pasien['id_daftar_poli']; ?>" class="action-button">
                                    <i class="fas fa-stethoscope"></i> Periksa
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>