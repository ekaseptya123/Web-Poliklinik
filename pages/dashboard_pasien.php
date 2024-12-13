<?php
session_start();
include_once('../config/db.php');  // Pastikan jalur ini benar

$login_successful = false; 
// Cek apakah pengguna sudah login
// Setelah login berhasil
if ($login_successful) {
    $_SESSION['id_pasien'] = $id_pasien; // Atur session
    header("Location: dashboard_pasien.php");
    exit();
}

// Di dashboard_pasien.php
/*if (isset($_SESSION['id_pasien'])) {
    $id_pasien = $_SESSION['id_pasien'];
} else {
    header("Location: login_pasien.php");
    exit();
}*/

// Ambil data poli
$poliQuery = $pdo->query("SELECT * FROM poli");
$poliList = $poliQuery->fetchAll(PDO::FETCH_ASSOC);

// Ambil data dokter berdasarkan poli
$dokter = [];
if (isset($_POST['poli'])) {
    $id_poli = $_POST['poli'];
    $dokterQuery = $pdo->prepare("SELECT * FROM dokter WHERE id_poli = ?");
    $dokterQuery->execute([$id_poli]);
    $dokter = $dokterQuery->fetchAll(PDO::FETCH_ASSOC);
}

// Ambil jadwal periksa berdasarkan dokter
// Ambil jadwal periksa berdasarkan dokter
$jadwalList = [];
if (isset($_POST['dokter'])) {
    $id_dokter = $_POST['dokter'];
    $jadwalQuery = $pdo->prepare("SELECT * FROM jadwal_periksa WHERE id_dokter = ?");
    $jadwalQuery->execute([$id_dokter]);
    $jadwalList = $jadwalQuery->fetchAll(PDO::FETCH_ASSOC);
}

// Proses pendaftaran
if (isset($_POST['daftar_poli'])) {
    $keluhan = isset($_POST['keluhan']) ? trim($_POST['keluhan']) : '';
    $id_jadwal = $_POST['jadwal'];

    // Validasi input
    if (empty($keluhan)) {
        $msg = "Keluhan tidak boleh kosong.";
    } else {
        // Simpan pendaftaran poli dan nomor antrian
        try {
            $stmt = $pdo->prepare("INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian) VALUES (?, ?, ?, ?)");
            $nomor_antrian = rand(1000, 9999); // Nomor antrian acak
            $stmt->execute([$id_pasien, $id_jadwal, $keluhan, $nomor_antrian]);

            // Menampilkan informasi pendaftaran dan nomor antrian
            $msg = "Pendaftaran Poli berhasil! Nomor Antrian Anda: " . $nomor_antrian;
        } catch (PDOException $e) {
            $msg = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pasien</title>
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
            background-color: #ecf0f1;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .form-container label {
            display: block;
            margin: 10px 0 5px;
        }
        .form-container select, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom:             10px;
        }
        .form-container button {
            background-color: #2c3e50;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .form-container button:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Dashboard Pasien</h2>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="content">
        <h1>Pendaftaran Poli</h1>
        <div class="form-container">
            <?php if (isset($msg)) { echo "<p>$msg</p>"; } ?>
            <form method="POST" action="">
                <label for="poli">Pilih Poli:</label>
                <select name="poli" id="poli" required>
                    <option value="">-- Pilih Poli --</option>
                    <?php foreach ($poliList as $poli) { ?>
                        <option value="<?php echo $poli['id_poli']; ?>"><?php echo $poli['nama_poli']; ?></option>
                    <?php } ?>
                </select>

                <label for="dokter">Pilih Dokter:</label>
                <select name="dokter" id="dokter" required>
                    <option value="">-- Pilih Dokter --</option>
                    <?php foreach ($dokter as $dokter) { ?>
                        <option value="<?php echo $dokter['id_dokter']; ?>"><?php echo $polidokter['nama_dokter']; ?></option>
                    <?php } ?>
                </select>

                <label for="jadwal">Pilih Jadwal:</label>
                <select name="jadwal" id="jadwal" required>
                    <option value="">-- Pilih Jadwal --</option>
                    <?php foreach ($jadwalList as $jadwal) { ?>
                        <option value="<?php echo $jadwal['id_jadwal']; ?>"><?php echo $jadwal['tanggal'] . ' ' . $jadwal['jam']; ?></option>
                    <?php } ?>
                </select>

                <label for="keluhan">Keluhan:</label>
                <textarea name="keluhan" id="keluhan" rows="4" required></textarea>

                <button type="submit" name="daftar_poli">Daftar</button>
            </form>
        </div>
    </div>
</body>
</html>