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

// Ambil ID pasien dari URL
$id_daftar_poli = $_GET['id_daftar_poli'] ?? null;

if ($id_daftar_poli) {
    // Ambil data pasien berdasarkan ID
    $pasienQuery = $pdo->prepare("SELECT p.* FROM pasien p JOIN daftar_poli dp ON p.id = dp.id_pasien WHERE dp.id = ?");
    $pasienQuery->execute([$id_daftar_poli]);
    $pasien = $pasienQuery->fetch(PDO::FETCH_ASSOC);
}

// Proses pemeriksaan
if (isset($_POST['periksa'])) {
    $catatan = $_POST['catatan'];
    $id_obat = $_POST['id_obat'];
    $tgl_periksa = $_POST['tgl_periksa'];

    // Biaya jasa dokter
    $biaya_jasa_dokter = 150000;

    // Ambil harga obat yang dipilih
    $stmt = $pdo->prepare("SELECT harga FROM obat WHERE id = ?");
    $stmt->execute([$id_obat]);
    $obat = $stmt->fetch(PDO::FETCH_ASSOC);
    $biaya_obat = $obat['harga'] ?? 0; // Jika tidak ada, set biaya obat ke 0

    // Hitung total biaya pemeriksaan
    $biaya_periksa = $biaya_obat + $biaya_jasa_dokter;

    // Simpan data pemeriksaan
    try {
        // Simpan ke tabel periksa
        $stmt = $pdo->prepare("INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa) VALUES (?, CURDATE(), ?, ?)");
        $stmt->execute([$id_daftar_poli, $catatan, $biaya_periksa]);
        $id_periksa = $pdo->lastInsertId();

        // Simpan detail obat jika ada
        if (!empty($id_obat)) {
            $stmt = $pdo->prepare("INSERT INTO detail_periksa (id_periksa, id_obat) VALUES (?, ?)");
            $stmt->execute([$id_periksa, $id_obat]);
        }

        // Update status menjadi "Sudah Diperiksa"
        $stmt = $pdo->prepare("UPDATE daftar_poli SET status = 'Sudah Diperiksa' WHERE id = :id");
        $stmt->execute([':id' => $id_daftar_poli]);

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
        /* CSS yang sudah ada */
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
        <?php if ($pasien): ?>
            <div class="form-container">
                <form method="POST" action="">
                    <input type="hidden" name="id_daftar_poli" value="<?php echo $id_daftar_poli; ?>">
                    
                    <label for="nama_pasien">Nama Pasien:</label>
                    <input type="text" id="nama_pasien" value="<?php echo htmlspecialchars($pasien['nama']); ?>" readonly>

                    <label for="tgl_periksa">Tanggal Pemeriksaan:</label>
                    <input type="date" name="tgl_periksa" id="tgl_periksa" required>

                    <label for="catatan">Catatan Kesehatan:</label>
                    <textarea name="catatan" id="catatan" rows="4" required></textarea>

                    <label for="biaya_periksa">Biaya Pemeriksaan:</label>
                    <input type="number" name="biaya_periksa" id="biaya_periksa" value="<?php echo $biaya_jasa_dokter; ?>" readonly>

                    <label for="id_obat">Pilih Obat:</label>
                    <select name="id_obat" id="id_obat" required onchange="updateBiaya();">
                        <option value="">-- Pilih Obat --</option>
                        <?php
                        // Ambil daftar obat dari database
                        $obatQuery = $pdo->query("SELECT * FROM obat");
                        while ($obat = $obatQuery->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . $obat['id'] . '" data-harga="' . $obat['harga'] . '">' . htmlspecialchars($obat['nama_obat']) . '</option>';
                        }
                        ?>
                    </select>

                    <button type="submit" name="periksa" class="submit-button">Simpan Pemeriksaan</button>
                </form>
            </div>
        <?php else: ?>
            <p>Data pasien tidak ditemukan.</p>
            <?php endif; ?>
    </div>

    <script>
        function updateBiaya() {
            const obatSelect = document.getElementById('id_obat');
            const selectedOption = obatSelect.options[obatSelect.selectedIndex];
            const harga = selectedOption.getAttribute('data-harga');
            const biayaJasaDokter = 150000; // Biaya jasa dokter
            const totalBiaya = (parseInt(harga) || 0) + biayaJasaDokter; // Hitung total biaya
            document.getElementById('biaya_periksa').value = totalBiaya; // Set biaya pemeriksaan
        }
    </script>
</body>
</html>