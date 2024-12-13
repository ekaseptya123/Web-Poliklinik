<?php
session_start();
require '../config/db.php'; // Pastikan jalur ini benar

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Mengambil data dokter dengan join untuk mendapatkan nama poli
$stmt = $pdo->query("SELECT dokter.*, poli.nama_poli FROM dokter JOIN poli ON dokter.id_poli = poli.id_poli");
$dokter = $stmt->fetchAll();

// Mengambil data poli untuk dropdown
$stmt_poli = $pdo->query("SELECT * FROM poli");
$poli = $stmt_poli->fetchAll();

// Menangani penambahan dokter
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $id_poli = $_POST['id_poli'];

    $stmt = $pdo->prepare("INSERT INTO dokter (id, nama, alamat, no_hp, id_poli) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id, $nama, $alamat, $no_hp, $id_poli]);
    header("Location: dokter.php");
    exit();
}

// Menangani pengeditan dokter
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM dokter WHERE id = ?");
    $stmt->execute([$id]);
    $dokter_edit = $stmt->fetch();
}

// Menangani pembaruan dokter
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $id_poli = $_POST['id_poli'];

    $stmt = $pdo->prepare("UPDATE dokter SET nama = ?, alamat = ?, no_hp = ?, id_poli = ? WHERE id = ?");
    $stmt->execute([$nama, $alamat, $no_hp, $id_poli, $id]);
    header("Location: dokter.php");
    exit();
}

// Menangani penghapusan dokter
// Menangani penghapusan dokter
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Hapus semua jadwal yang terkait dengan dokter ini
    $stmt = $pdo->prepare("DELETE FROM jadwal_periksa WHERE id_dokter = ?");
    $stmt->execute([$id]);

    // Hapus dokter
    $stmt = $pdo->prepare("DELETE FROM dokter WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: dokter.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Dokter</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Gaya CSS yang sama seperti sebelumnya */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #f4f4f4; /* Warna latar belakang halaman */
        }

        .sidebar {
            width: 250px;
            background-color: #34495e; /* Warna sidebar yang lebih gelap */
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* Bayangan untuk sidebar */
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
            margin: 5px 0;
            border-radius: 5px;
            transition: background-color 0.3s, padding-left 0.3s; /* Transisi untuk efek hover */
        }

        .sidebar a:hover {
            background-color: #2980b9; /* Warna saat hover */
            padding-left: 15px; /* Efek geser saat hover */
        }

        .content {
            margin-left: 270px; /* Memberikan ruang untuk sidebar */
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center; /* Memusatkan konten */
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        form {
            background-color: #ecf0f1; /* Warna latar belakang form */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Bayangan untuk form */
            margin-bottom: 20px;
            width: 100%; /* Mengatur lebar form */
            max-width: 500px; /* Maksimal lebar form */
        }

        form input, form select {
            padding: 10px;
            margin: 10px 0; /* Memberikan jarak antar input */
            border: 1px solid #ccc;
            border-radius: 5px;
            width: calc(100% - 22px); /* Mengatur lebar input */
            transition: border-color 0.3s; /* Transisi untuk border */
        }

        form input:focus, form select:focus {
            border-color: #2980b9; /* Warna border saat fokus */
        }

        form button {
            padding: 10px 15px;
            background-color: #3498db; /* Warna tombol */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #219653; /* Warna tombol saat hover */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            max-width: 800px; /* Maksimal lebar tabel */
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #3498db; /* Warna latar belakang header tabel */
            color: white;
        }
        

        tr:hover {
            background-color: #f2f2f2; /* Warna saat hover pada baris tabel */
        }

        .action-links {
            display: flex;
            gap: 10px; /* Jarak antar tautan aksi */
        }

        .action-links a {
            padding: 8px 12px; /* Padding untuk tombol */
            background-color: #3498db; /* Warna tombol */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s; /* Transisi untuk efek hover */
            display: flex; /* Flexbox untuk ikon dan teks */
            align-items: center; /* Vertikal center */
        }

        .action-links a:hover {
            background-color: #2980b9; /* Warna saat hover */
            transform: translateY(-2px); /* Efek angkat saat hover */
        }

        .action-links a.btn-danger {
            background-color: #e74c3c; /* Warna tombol untuk hapus */
        }

        .action-links a.btn-danger:hover {
            background-color: #c0392b; /* Warna saat hover untuk hapus */
        }

        .action-links i {
            margin-right: 5px; /* Jarak antara ikon dan teks */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Menu Admin</h2>
        <a href="dashboard_admin.php">Dashboard</a>
        <a href="dokter.php">Dokter</a>
        <a href="pasien.php">Pasien</a>
        <a href="obat.php">Obat</a>
        <a href="poli.php">Poli</a>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="content">
        <h1>Kelola Dokter</h1>

        <!-- Form untuk menambah atau mengedit dokter -->
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo isset($dokter_edit) ? htmlspecialchars($dokter_edit['id']) : ''; ?>">
            <input type="text" name="nama" placeholder="Nama Dokter" required value="<?php echo isset($dokter_edit) ? htmlspecialchars($dokter_edit['nama']) : ''; ?>">
            <input type="text" name="alamat" placeholder="Alamat" required value="<?php echo isset($dokter_edit) ? htmlspecialchars($dokter_edit['alamat']) : ''; ?>">
            <input type="text" name="no_hp" placeholder="No HP" required value="<?php echo isset($dokter_edit) ? htmlspecialchars($dokter_edit['no_hp']) : ''; ?>">
            <select name="id_poli" required>
                <option value="" disabled <?php echo !isset($dokter_edit) ? 'selected' : ''; ?>>Pilih Poli</option>
                <?php foreach ($poli as $p): ?>
                    <option value="<?php echo htmlspecialchars($p['id_poli']); ?>" <?php echo isset($dokter_edit) && $dokter_edit['id_poli'] == $p['id_poli'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($p['nama_poli']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="<?php echo isset($dokter_edit) ? 'update' : 'tambah'; ?>">
                <?php echo isset($dokter_edit) ? 'Update Dokter' : 'Tambah Dokter'; ?>
            </button>
        </form>

        <h2>Daftar Dokter</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>No HP</th>
                <th>Poli</th>
                <th>Aksi</th>
            </tr>
            <?php foreach ($dokter as $d): ?>
            <tr>
                <td><?php echo htmlspecialchars($d['id']); ?></td>
                <td><?php echo htmlspecialchars($d['nama']); ?></td>
                <td><?php echo htmlspecialchars($d['alamat']); ?></td>
                <td><?php echo htmlspecialchars($d['no_hp']); ?></td>
                <td><?php echo htmlspecialchars($d['nama_poli']); ?></td>
                <td class="action-links">
                    <a href="?edit=<?php echo htmlspecialchars($d['id']); ?>" class="btn">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="?delete=<?php echo htmlspecialchars($d['id']); ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus dokter ini?');">
                        <i class="fas fa-trash-alt"></i> Hapus
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>






