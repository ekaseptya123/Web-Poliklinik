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

// Ambil ID jadwal dari URL
$id_jadwal = $_GET['id'];

// Proses update status jadwal
if (isset($_POST['update_jadwal'])) {
    $is_active = $_POST['is_active'] == '1' ? 1 : 0;

    // Nonaktifkan jadwal lain untuk hari yang sama jika mengaktifkan jadwal baru
    if ($is_active) {
        // Ambil hari dari jadwal yang sedang diedit
        $stmt = $pdo->prepare("SELECT hari FROM jadwal_periksa WHERE id = ? AND id_dokter = ?");
        $stmt->execute([$id_jadwal, $dokter_id]);
        $hari_jadwal = $stmt->fetchColumn();

        // Nonaktifkan jadwal lain yang aktif pada hari yang sama
        $stmt = $pdo->prepare("UPDATE jadwal_periksa SET is_active = 0 WHERE id_dokter = ? AND hari = ? AND id != ?");
        $stmt->execute([$dokter_id, $hari_jadwal, $id_jadwal]);
    }

    // Update status jadwal
    $stmt = $pdo->prepare("UPDATE jadwal_periksa SET is_active = ? WHERE id = ?");
    $stmt->execute([$is_active, $id_jadwal]);

    $msg_jadwal = "Jadwal berhasil diperbarui!";
    header("Location: ../dokter/input_jadwal.php"); // Kembali ke halaman input jadwal
    exit;
}

// Ambil data jadwal untuk diedit
$stmt = $pdo->prepare("SELECT * FROM jadwal_periksa WHERE id = ? AND id_dokter = ?");
$stmt->execute([$id_jadwal, $dokter_id]);
$jadwal = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jadwal Periksa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #2c3e50;
            text-align: center;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="time"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="time"]:focus,
        select:focus {
            border-color: #3498db;
            outline: none; /* Hilangkan outline default */
        }

        button {
            background-color: #3498db; /* Warna tombol */
            color: white; /* Warna teks tombol */
            border: none; /* Tanpa border */
            padding: 10px 15px; /* Padding dalam tombol */
            border-radius: 5px; /* Sudut melengkung */
            cursor: pointer; /* Kursor pointer saat hover */
            transition: background-color 0.3s, transform 0.2s; /* Transisi */
            font-size: 16px; /* Ukuran font */
        }

        button:hover {
            background-color: #2980b9; /* Warna tombol saat hover */
            transform: scale(1.05); /* Efek zoom saat hover */
        }

        p {
            text-align: center; /* Pusatkan pesan */
            color: #e74c3c; /* Warna merah untuk pesan */
        }
    </style>
</head>
<body>
    <h1>Edit Jadwal Periksa</h1>
    <?php if (isset($msg_jadwal)) { echo "<p>$msg_jadwal</p>"; } ?>
    <form method="POST" action="">
        <label for="hari">Hari:</label>
        <input type="text" name="hari" id="hari" value="<?php echo htmlspecialchars($jadwal['hari']); ?>" readonly>

        <label for="jam_mulai">Jam Mulai:</label>
        <input type="time" name="jam_mulai" id="jam_mulai" value="<?php echo htmlspecialchars($jadwal['jam_mulai']); ?>" readonly>

        <label for="jam_selesai">Jam Selesai:</label>
        <input type="time" name="jam_selesai" id="jam_selesai" value="<?php echo htmlspecialchars($jadwal['jam_selesai']); ?>" readonly>

        <label for="is_active">Status:</label>
        <select name="is_active" id="is_active">
            <option value="1" <?php echo $jadwal['is_active'] ? 'selected' : ''; ?>>Aktif</option>
            <option value="0" <?php echo !$jadwal['is_active'] ? 'selected' : ''; ?>>Tidak Aktif</option>
        </select>

        <button type="submit" name="update_jadwal">Update Jadwal</button>
    </form>
</body>
</html>