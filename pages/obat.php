<?php
// Mulai session untuk menyimpan data sementara
session_start();

// Koneksi ke database
include_once('../config/db.php'); 

// Menangani aksi tambah obat
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $nama_obat = $_POST['nama_obat'];
    $kemasan = $_POST['kemasan'];
    $harga = $_POST['harga'];

    // Query untuk memasukkan data ke dalam database
    $sql = "INSERT INTO obat (nama_obat, kemasan, harga) VALUES ('$nama_obat', '$kemasan', '$harga')";

    if ($conn->query($sql) === TRUE) {
        header("Location: obat.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menangani aksi edit obat
if (isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = $_POST['id'];
    $nama_obat = $_POST['nama_obat'];
    $kemasan = $_POST['kemasan'];
    $harga = $_POST['harga'];

    // Query untuk memperbarui data obat
    $sql = "UPDATE obat SET nama_obat='$nama_obat', kemasan='$kemasan', harga='$harga' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: obat.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menangani aksi hapus obat
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id_obat'])) {
    $id_obat = $_GET['id_obat'];

    // Query untuk menghapus data obat
    $sql = "DELETE FROM obat WHERE id_obat=$id_obat";

    if ($conn->query($sql) === TRUE) {
        header("Location: obat.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Mengambil data obat dari database
$sql = "SELECT * FROM obat";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Obat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4; /* Warna latar belakang halaman */
        }

        .sidebar {
            width: 250px;
            background-color: #34495e; /* Warna sidebar */
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px; /* Ukuran font judul */
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
            background-color: #2980b9; /* Ubah warna saat hover */
            padding-left: 15px; /* Efek geser saat hover */
        }

        .content {
            margin-left: 270px; /* Memberikan ruang untuk sidebar */
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        form {
            background-color: #ecf0f1;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 100%;
            max-width: 500px;
        }

        form input {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: calc(100% - 22px);
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
            background-color: #2980b9; /* Warna saat hover */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            max-width: 800px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #2980b9; /* Warna latar belakang header tabel */
            color: white; /* Warna teks header */
        }

        tr:hover {
            background-color: #f2f2f2; /* Warna saat hover pada baris tabel */
        }

        .action-links {
            display: flex;
            gap: 10px; /* Jarak antar tombol aksi */
        }

        .action-links a {
            padding: 10px 15px;
            background-color: #3498db; /* Warna tombol */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s, box-shadow 0.2s; /* Transisi untuk efek hover */
        }

        .action-links a:hover {
            background-color: #2980b9; /* Warna saat hover */
            transform: translateY(-2px); /* Efek angkat saat hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Bayangan saat hover */
        }

        .action-links a.btn-danger {
            background-color: #e74c3c; /* Warna tombol untuk hapus */
        }

        .action-links a.btn-danger:hover {
            background-color: #c0392b; /* Warna saat hover untuk hapus */
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
        <h1>Kelola Obat</h1>
        
        <form method="POST" action="">
            <?php if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])): ?>
                <?php
                    $id = $_GET['id'];
                    $edit_obat = null;
                    $sql_edit = "SELECT * FROM obat WHERE id = $id";
                    $result_edit = $conn->query($sql_edit);
                    if ($result_edit->num_rows > 0) {
                        $edit_obat = $result_edit->fetch_assoc();
                    }
                ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?php echo $edit_obat['id']; ?>">
                <label>Nama Obat:</label>
                <input type="text" name="nama_obat" value="<?php echo htmlspecialchars($edit_obat['nama_obat']); ?>" required>
                <label>Kemasan:</label>
                <input type="text" name="kemasan" value="<?php echo htmlspecialchars($edit_obat['kemasan']); ?>" required>
                <label>Harga:</label>
                <input type="number" name="harga" value="<?php echo htmlspecialchars($edit_obat['harga']); ?>" required step="0.01">
                <button type="submit">Perbarui Obat</button>
            <?php else: ?>
                <input type="hidden" name="action" value="add">
                <label>Nama Obat:</label>
                <input type="text" name="nama_obat" required>
                <label>Kemasan:</label>
                <input type="text" name="kemasan" required>
                <label>Harga:</label>
                <input type="number" name="harga" required step="0.01">
                <button type="submit">Tambah Obat</button>
            <?php endif; ?>
        </form>

        <table>
            <tr>
                <th>No</th>
                <th>Nama Obat</th>
                <th>Kemasan</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($obat = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $obat['id'] . "</td>
                            <td>" . htmlspecialchars($obat['nama_obat']) . "</td>
                            <td>" . htmlspecialchars($obat['kemasan']) . "</td>
                            <td>" . htmlspecialchars($obat['harga']) . "</td>
                            <td>
                                <div class='action-links'>
                                    <a href='?action=edit&id=" . $obat['id'] . "' class='btn'>
                                        Edit
                                    </a>
                                    <a href='?action=delete&id_obat=" . $obat['id'] . "' class='btn btn-danger' onclick=\"return confirm('Apakah Anda yakin ingin menghapus obat ini?');\">
                                        Hapus
                                    </a>
                                </div>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Tidak ada data obat.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>

<?php
// Menutup koneksi database
$conn->close();
?>