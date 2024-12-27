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
$stmt = $pdo->prepare("SELECT d.*, p.nama_poli FROM dokter d JOIN poli p ON d.id_poli = p.id_poli WHERE d.id = :id");
$stmt->execute([':id' => $dokter_id]);
$dokter = $stmt->fetch(PDO::FETCH_ASSOC);

// Cek apakah dokter ditemukan
if (!$dokter) {
    die("Dokter tidak ditemukan. Silakan periksa ID dokter.");
}

// Proses pembaruan data dokter
if (isset($_POST['update_data'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $id_poli = $_POST['id_poli'];
    $password = $_POST['password'];

    // Update data dokter
    $stmt = $pdo->prepare("UPDATE dokter SET nama = ?, alamat = ?, no_hp = ?, id_poli = ? WHERE id = ?");
    $stmt->execute([$nama, $alamat, $no_hp, $id_poli, $dokter_id]);

    // Update password jika diisi
    if (!empty($password)) {
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $dokter_id]);
    }

    // Ambil data dokter yang diperbarui
    $stmt = $pdo->prepare("SELECT d.*, p.nama_poli FROM dokter d JOIN poli p ON d.id_poli = p.id_poli WHERE d.id = :id");
    $stmt->execute([':id' => $dokter_id]);
    $dokter = $stmt->fetch(PDO::FETCH_ASSOC);

    $msg = "Data berhasil diperbarui!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perbarui Data Diri</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
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
            margin-left: 270px; /* Menyesuaikan margin untuk sidebar */
            padding: 20px;
        }
        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        .form-container h2 {
            margin-bottom: 20px;
            color: #2c3e50;
            text-align: center;
        }
        .form-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .form-container th, .form-container td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .form-container th {
            background-color: #2c3e50;
            color: white;
        }
        .form-container tr:hover {
            background-color: #f1f1;
        }
        .form-container input, .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s;
        }
        .form-container input:focus, .form-container select:focus {
            border-color: #3498db; /* Warna border saat fokus */
            outline: none;
        }
        .form-container button {
            background-color: #2c3e50;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
            border-radius: 5px;
        }
        .form-container button:hover {
            background-color: #34495e;
        }
        .success-message {
            color: green;
            margin-bottom: 20px;
            text-align: center;
        }
        .error-message {
            color: red;
            margin-bottom: 20px;
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
        <?php if (isset($msg)) { echo "<p class='success-message'>$msg</p>"; } ?>
        <?php if (isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>
        
        <div class="form-container">
            <h2>Data Dokter Saat Ini</h2>
            <table>
                <tr>
                    <th>Nama</th>
                    <td><?php echo htmlspecialchars($dokter['nama']); ?></td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td><?php echo htmlspecialchars($dokter['alamat']); ?></td>
                </tr>
                <tr>
                    <th>No HP</th>
                    <td><?php echo htmlspecialchars($dokter['no_hp']); ?></td>
                </tr>
                <tr>
                    <th>Poli</th>
                    <td><?php echo htmlspecialchars($dokter['nama_poli']); ?></td>
                </tr>
            </table>

            <h2>Perbarui Data Diri</h2>
            <form method="POST" action="">
                <label for="nama">Nama:</label>
                <input type="text" name="nama" id="nama" value="<?php echo htmlspecialchars($dokter['nama']); ?>" required>

                <label for="alamat">Alamat:</label>
                <input type="text" name="alamat" id="alamat" value="<?php echo htmlspecialchars($dokter['alamat']); ?>" required>

                <label for="no_hp">No HP:</label>
                <input type="text" name="no_hp" id="no_hp" value="<?php echo htmlspecialchars($dokter['no_hp']); ?>" required>

                <label for="id_poli">Poli:</label>
                <select name="id_poli" id="id_poli" required>
                    <?php
                    // Ambil daftar poli untuk dropdown
                    $poliQuery = $pdo->query("SELECT * FROM poli");
                    while ($poli = $poliQuery->fetch(PDO::FETCH_ASSOC)) {
                        $selected = ($poli['id_poli'] == $dokter['id_poli']) ? 'selected' : '';
                        echo "<option value='{$poli['id_poli']}' $selected>{$poli['nama_poli']}</option>";
                    }
                    ?>
                </select>

                <label for="password">Password Baru (kosongkan jika tidak ingin mengubah):</label>
                <input type="password" name="password" id="password">

                <button type="submit" name="update_data">Perbarui Data</button>
            </form>
        </div>
    </div>
</body>
</html>