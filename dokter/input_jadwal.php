<?php
session_start();
require '../config/db.php'; // Pastikan jalur ini benar

// Cek apakah dokter sudah login
if (!isset($_SESSION['id'])) {
    header("Location: login_dokter.php");
    exit;
}

// Ambil ID dokter dari session
$dokter_id = $_SESSION['id'];

// Proses input jadwal periksa
if (isset($_POST['input_jadwal'])) {
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    // Validasi input jam
    if ($jam_mulai >= $jam_selesai) {
        $msg_jadwal = "Jam mulai harus lebih awal dari jam selesai.";
    } else {
        // Cek apakah sudah ada jadwal aktif untuk hari yang sama
        $stmt = $pdo->prepare("SELECT * FROM jadwal_periksa WHERE id_dokter = ? AND hari = ? AND is_active = 1");
        $stmt->execute([$dokter_id, $hari]);
        $existing_jadwal = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_jadwal) {
            // Jika ada jadwal aktif, setel jadwal yang ada menjadi tidak aktif
            $stmt = $pdo->prepare("UPDATE jadwal_periksa SET is_active = 0 WHERE id_dokter = ? AND hari = ?");
            $stmt->execute([$dokter_id, $hari]);
        }

        // Simpan jadwal baru dengan status aktif
        $stmt = $pdo->prepare("INSERT INTO jadwal_periksa (id_dokter, hari, jam_mulai, jam_selesai, is_active) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$dokter_id, $hari, $jam_mulai, $jam_selesai]);

        $msg_jadwal = "Jadwal berhasil ditambahkan!";
        // Redirect untuk mencegah pengiriman ulang form
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Ambil semua jadwal dokter
$stmt = $pdo->prepare("SELECT * FROM jadwal_periksa WHERE id_dokter = :id_dokter");
$stmt->execute([':id_dokter' => $dokter_id]);
$jadwal_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Jadwal Periksa</title>
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
            box-shadow: 0 2px 5px rgba(0, 0,             0, 0.1);
        }

        .form-container label {
            display: block;
            margin: 10px 0 5px;
        }

        .form-container input, .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
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

        .jadwal-table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        .jadwal-table th, .jadwal-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .jadwal-table th {
            background-color: #3498db;
            color: white;
        }

        .jadwal-table tr:hover {
            background-color: #f1f1f1;
        }

        .jadwal-table td {
            transition: background 0.3s;
        }
        .status-active {
            background-color: #28a745; /* Hijau untuk aktif */
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-align: center;
        }

        .status-inactive {
            background-color: #dc3545; /* Merah untuk tidak aktif */
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-align: center;
        }
        .edit-button {
            background-color: #007bff; /* Biru */
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s, transform 0.2s;
            display: inline-block; /* Agar padding berfungsi */
        }

        .edit-button:hover {
            background-color: #0056b3; /* Biru lebih gelap saat hover */
            transform: scale(1.05); /* Efek zoom saat hover */
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
        <h1>Input Jadwal Periksa</h1>
        <?php if (isset($msg_jadwal)) { echo "<p>$msg_jadwal</p>"; } ?>
        <div class="form-container">
            <form method="POST" action="">
                <label for="hari">Hari:</label>
                <select name="hari" id="hari" required>
                    <option value="">-- Pilih Hari --</option>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                    <option value="Minggu">Minggu</option>
                </select>

                <label for="jam_mulai">Jam Mulai:</label>
                <input type="time" name="jam_mulai" id="jam_mulai" required>

                <label for="jam_selesai">Jam Selesai:</label>
                <input type="time" name="jam_selesai" id="jam_selesai" required>

                <button type="submit" name="input_jadwal">Input Jadwal</button>
            </form>
        </div>

        <h2>Jadwal Periksa yang Didaftarkan</h2>
        <table class="jadwal-table">
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($jadwal_list): ?>
                    <?php foreach ($jadwal_list as $jadwal): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($jadwal['hari']); ?></td>
                            <td><?php echo htmlspecialchars($jadwal['jam_mulai']); ?></td>
                            <td><?php echo htmlspecialchars($jadwal['jam_selesai']); ?></td>
                            <td>
                                <?php if ($jadwal['is_active']): ?>
                                    <span class="status-active">Aktif</span>
                                <?php else: ?>
                                    <span class="status-inactive">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="../pages/edit_jadwal.php?id=<?php echo $jadwal['id']; ?>" class="edit-button">
                                    <i class="fas fa-pencil-alt"></i> Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">Tidak ada jadwal yang terdaftar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>