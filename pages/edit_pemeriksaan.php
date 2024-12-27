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

// Ambil ID daftar poli dari URL
$id_daftar_poli = $_GET['id_daftar_poli'] ?? null;

if ($id_daftar_poli) {
    // Ambil data pemeriksaan berdasarkan ID
    $pemeriksaanQuery = $pdo->prepare("SELECT p.*, dp.keluhan, pa.nama FROM periksa p JOIN daftar_poli dp ON p.id_daftar_poli = dp.id JOIN pasien pa ON dp.id_pasien = pa.id WHERE dp.id = ?");
    $pemeriksaanQuery->execute([$id_daftar_poli]);
    $pemeriksaan = $pemeriksaanQuery->fetch(PDO::FETCH_ASSOC);
}

// Proses pembaruan pemeriksaan
if (isset($_POST['update'])) {
    $catatan = $_POST['catatan'];
    $biaya_periksa = $_POST['biaya_periksa'];

    // Simpan data pemeriksaan yang diperbarui
    try {
        $stmt = $pdo->prepare("UPDATE periksa SET catatan = ?, biaya_periksa = ? WHERE id_daftar_poli = ?");
        $stmt->execute([$catatan, $biaya_periksa, $id_daftar_poli]);

        $msg = "Pemeriksaan berhasil diperbarui!";
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
    <title>Edit Pemeriksaan Pasien</title>
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
        .form-container input[type="date"],
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
        .form-container input[type="date"]:focus,
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
        <h1>Edit Pemeriksaan Pasien</h1>
        <?php if (isset($msg)) { echo "<p>$msg</p>"; } ?>
        <?php if ($pemeriksaan): ?>
            <div class="form-container">
                <form method="POST" action="">
                    <input type="hidden" name="id_daftar_poli" value="<?php echo $id_daftar_poli; ?>">

                    <label for="nama_pasien">Nama Pasien:</label>
                    <input type="text" name="nama_pasien" id="nama_pasien" value="<?php echo htmlspecialchars($pemeriksaan['nama']); ?>" readonly>

                    <label for="tgl_periksa">Tanggal Pemeriksaan:</label>
                    <input type="date" name="tgl_periksa" id="tgl_periksa" value="<?php echo htmlspecialchars($pemeriksaan['tgl_periksa']); ?>" readonly>

                    <label for="catatan">Catatan Kesehatan:</label>
                    <textarea name="catatan" id="catatan" rows="4" required><?php echo htmlspecialchars($pemeriksaan['catatan']); ?></textarea>

                    <label for="biaya_periksa">Biaya Pemeriksaan:</label>
                    <input type="number" name="biaya_periksa" id="biaya_periksa" value="<?php echo htmlspecialchars($pemeriksaan['biaya_periksa']); ?>" required>

                    <button type="submit" name="update" class="submit-button">Perbarui Pemeriksaan</button>
                </form>
            </div>
        <?php else: ?>
            <p>Data pemeriksaan tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</body>
</html>