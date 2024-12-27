<?php
session_start();
include_once('../config/db.php');  // Pastikan jalur ini benar

// Cek apakah pengguna sudah login
if (!isset($_SESSION['id'])) {
    header("Location: login_pasien.php");
    exit;
}

// Ambil ID pasien dari session
$id_pasien = $_SESSION['id'];

// Ambil data pasien untuk mendapatkan no RM
$stmt = $pdo->prepare("SELECT no_rm FROM pasien WHERE id = :id");
$stmt->execute([':id' => $id_pasien]);
$pasien = $stmt->fetch(PDO::FETCH_ASSOC);
$no_rm = $pasien['no_rm'];

// Proses pendaftaran
if (isset($_POST['daftar_poli'])) {
    $keluhan = isset($_POST['keluhan']) ? trim($_POST['keluhan']) : '';
    $id_jadwal = $_POST['jadwal'];

    // Validasi input
    if (empty($keluhan)) {
        $msg = "Keluhan tidak boleh kosong.";
    } else {
        // Ambil nomor antrian terakhir untuk poli yang sama
        $stmt = $pdo->prepare("SELECT MAX(no_antrian) AS last_antrian FROM daftar_poli WHERE id_jadwal = :id_jadwal");
        $stmt->execute([':id_jadwal' => $id_jadwal]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $last_antrian = $result['last_antrian'] ? $result['last_antrian'] : 0; // Jika tidak ada, mulai dari 0
        $nomor_antrian = $last_antrian + 1; // Nomor antrian baru

        // Simpan pendaftaran poli dan nomor antrian
        try {
            $stmt = $pdo->prepare("INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian, status) VALUES (?, ?, ?, ?, ?)");
            $status = 'Belum Diperiksa'; // Status awal
            $stmt->execute([$id_pasien, $id_jadwal, $keluhan, $nomor_antrian, $status]);

            // Menampilkan informasi pendaftaran dan nomor antrian
            $msg = "Pendaftaran Poli berhasil! Nomor Antrian Anda: " . $nomor_antrian;

            // Redirect ke halaman yang sama untuk menghindari pengiriman ulang form
            header("Location: " . $_SERVER['PHP_SELF']);
            exit; // Pastikan untuk keluar setelah redirect
        } catch (PDOException $e) {
            $msg = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

// Contoh kode untuk memperbarui status setelah pemeriksaan
if (isset($_POST['update_status'])) {
    $id_daftar_poli = $_POST['id_daftar_poli']; // Ambil ID pendaftaran dari form
    $status = 'Sudah Diperiksa'; // Status baru

    // Update status di database
    $stmt = $pdo->prepare("UPDATE daftar_poli SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => $id_daftar_poli]);

    // Redirect atau tampilkan pesan sukses
    header("Location: ../dokter/riwayat_pendaftaran.php"); // Ganti dengan halaman yang sesuai
    exit();
}

// Ambil riwayat pendaftaran poli
$riwayatQuery = $pdo->prepare("SELECT dp.*, p.nama_poli, d.nama AS nama_dokter, jp.hari, jp.jam_mulai, jp.jam_selesai 
                                FROM daftar_poli dp 
                                JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id 
                                JOIN poli p ON jp.id_dokter = p.id_poli 
                                JOIN dokter d ON jp.id_dokter = d.id 
                                WHERE dp.id_pasien = :id_pasien");
$riwayatQuery->execute([':id_pasien' => $id_pasien]);
$riwayatList = $riwayatQuery->fetchAll(PDO::FETCH_ASSOC);
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
        .riwayat-table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .riwayat-table th, .riwayat-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .riwayat-table th {
            background-color: #2c3e50;
            color: white;
        }
        .aksi-button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
        }
        .aksi-button:hover {
            background-color: #2980b9;
        }
        /* CSS untuk status */
        .status-belum {
            background-color: #f39c12; /* Warna oranye */
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-align: center;
        }

        .status-sudah {
            background-color: #2ecc71; /* Warna hijau */
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-align: center;
        }

        .status-icon {
            margin-right: 5px;
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
                <select name="poli" id="poli" required onchange="updateJadwal(this.value);">
                    <option value="">-- Pilih Poli --</option>
                    <option value="1">Poli Gigi</option>
                    <option value="2">Poli Anak</option>
                    <option value="3">Poli Mata</option>
                    <option value="4">Poli Umum</option>
                    <option value="5">Poli Kandungan</option>
                    <option value="6">Poli Ortopedi</option>
                    <option value="7">Poli Jantung</option>
                    <!-- Tambahkan poli lainnya sesuai kebutuhan -->
                </select>

                <label for="jadwal">Jadwal:</label>
                <select name="jadwal" id="jadwal" required>
                    <option value="">-- Pilih Jadwal --</option>
                    <!-- Jadwal akan diisi berdasarkan poli yang dipilih -->
                </select>

                <label for="keluhan">Keluhan:</label>
                <textarea name="keluhan" id="keluhan" rows="4" required></textarea>

                <button type="submit" name="daftar_poli">Daftar</button>
            </form>
        </div>

        <h2>Riwayat Pendaftaran Poli</h2>
        <table class="riwayat-table">
            <thead>
                <tr>
                    <th>No RM</th>
                    <th>Poli</th>
                    <th>Dokter</th>
                    <th>Hari</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>No Antrian</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($riwayatList): ?>
                    <?php foreach ($riwayatList as $riwayat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($no_rm); ?></td>
                            <td><?php echo htmlspecialchars($riwayat['nama_poli']); ?></td>
                            <td><?php echo htmlspecialchars($riwayat['nama_dokter']); ?></td>
                            <td><?php echo htmlspecialchars($riwayat['hari']); ?></td>
                            <td><?php echo htmlspecialchars($riwayat['jam_mulai']); ?></td>
                            <td><?php echo htmlspecialchars($riwayat['jam_selesai']); ?></td>
                            <td><?php echo htmlspecialchars($riwayat['no_antrian']); ?></td>
                            <td>
                                <?php if ($riwayat['status'] == 'Belum Diperiksa'): ?>
                                    <span class="status-belum">
                                        <i class="fas fa-clock status-icon"></i>
                                        <?php echo htmlspecialchars($riwayat['status']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="status-sudah">
                                        <i class="fas fa-check status-icon"></i>
                                        <?php echo htmlspecialchars($riwayat['status']); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">Tidak ada riwayat pendaftaran poli.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function updateJadwal(poliId) {
            const jadwalSelect = document.getElementById('jadwal');
            jadwalSelect.innerHTML = ''; // Kosongkan dropdown jadwal

            // Contoh data jadwal berdasarkan poli
            const jadwalData = {
                1: [
                    { id: 1, dokter: 'Adi', hari: 'Senin', jam_mulai: '08:00', jam_selesai: '14:00' },
                ],
                2: [
                    { id: 2, dokter: 'Eli', hari: 'Selasa', jam_mulai:                     '08:30', jam_selesai: '15:00' },
                ],
                3: [
                    { id: 3, dokter: 'Ifan', hari: 'Rabu', jam_mulai: '08:15', jam_selesai: '14:30' },
                ],
                4: [
                    { id: 4, dokter: 'Kris', hari: 'Senin', jam_mulai: '08:00', jam_selesai: '14:00' },
                ],
                5: [
                    { id: 5, dokter: 'Heni', hari: 'Kamis', jam_mulai: '08:00', jam_selesai: '14:30' },
                ],
                6: [
                    { id: 6, dokter: 'Dewa', hari: 'Jumat', jam_mulai: '07:10', jam_selesai: '13:30' },
                ],
                7: [
                    { id: 7, dokter: 'Dani', hari: 'Kamis', jam_mulai: '08:00', jam_selesai: '14:00' },
                ]
            };

            // Cek apakah poli yang dipilih memiliki jadwal
            if (jadwalData[poliId]) {
                jadwalData[poliId].forEach(jadwal => {
                    const option = document.createElement('option');
                    option.value = jadwal.id;
                    option.textContent = `${jadwal.dokter} - ${jadwal.hari} ${jadwal.jam_mulai} - ${jadwal.jam_selesai}`;
                    jadwalSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = '-- Tidak ada jadwal --';
                jadwalSelect.appendChild(option);
            }
        }
    </script>
</body>
</html>