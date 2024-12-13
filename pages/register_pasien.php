<?php
session_start();
require '../config/db.php'; // Pastikan jalur ini benar

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : null;
    $alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : null;
    $no_ktp = isset($_POST['no_ktp']) ? trim($_POST['no_ktp']) : null;
    $no_hp = isset($_POST['no_hp']) ? trim($_POST['no_hp']) : null;

    // Validasi input
    if (!empty($nama) && !empty($alamat) && !empty($no_ktp) && !empty($no_hp)) {
        // Cek apakah pasien sudah terdaftar
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM pasien WHERE no_ktp = :no_ktp");
        $stmt->execute(['no_ktp' => $no_ktp]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $error = "Pasien dengan nomor KTP ini sudah terdaftar.";
        } else {
            // Generate No RM
            // Ambil tahun dan bulan saat ini
            $year_month = date('Ym');

            // Hitung jumlah pasien yang terdaftar pada bulan dan tahun ini
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM pasien WHERE DATE_FORMAT(tanggal_daftar, '%Y%m') = :year_month");
            $stmt->execute(['year_month' => $year_month]);
            $pasien_count = $stmt->fetchColumn() + 1; // Tambah 1 untuk urutan

            // Generate No RM
            $no_rm = $year_month . '-' . str_pad($pasien_count, 3, '0', STR_PAD_LEFT);
                        // Simpan data pasien
                        $stmt = $pdo->prepare("INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm) VALUES (:nama, :alamat, :no_ktp, :no_hp, :no_rm)");
                        $stmt->execute(['nama' => $nama, 'alamat' => $alamat, 'no_ktp' => $no_ktp, 'no_hp' => $no_hp, 'no_rm' => $no_rm]);

            $success = "Pendaftaran berhasil! No Rekam Medis Anda adalah: $no_rm.";
        }
    } else {
        $error = "Semua field harus diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pasien</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f5cba7;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
            transition: transform 0.3s;
        }

        .register-container:hover {
            transform: scale(1.02);
        }

        .register-container h1 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #333;
        }

        .register-form {
            display: flex;
            flex-direction: column;
        }

        .register-form input {
            margin-bottom:             15px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .register-form input:focus {
            border-color: #4facfe;
            outline: none;
        }

        .register-form button {
            padding: 12px;
            background: #E9967A;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        .register-form button:hover {
            background: #CD5C5C;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }

        .success-message {
            color: green;
            margin-top: 10px;
            font-size: 14px;
        }

        @media (max-width: 400px) {
            .register-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <header>
            <h1>Pendaftaran Pasien</h1>
        </header>

        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form class="register-form" method="POST" action="">
            <input type="text" name="nama" placeholder="Nama" required>
            <input type="text" name="alamat" placeholder="Alamat" required>
            <input type="text" name="no_ktp" placeholder="No KTP" required>
            <input type="text" name="no_hp" placeholder="No HP" required>
            <button type="submit">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login_pasien.php">Login di sini</a></p>
    </div>
</body>
</html>