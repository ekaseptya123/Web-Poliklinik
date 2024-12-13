<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poliklinik Sehat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e9ecef;
        }

        header {
            background: #007bff;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        header h1 {
            margin: 0;
            font-size: 2.5em;
        }

        .login-menu {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .login-menu a {
            margin: 0 15px;
            padding: 15px 25px;
            background: #2471a3;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
            font-size: 1.1em;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .login-menu a:hover {
            background: #1a5276;
        }

        .content {
            padding: 20px;
            text-align: center;
            max-width: 800px;
            margin: auto;
        }

        .content h2 {
            font-size: 2em;
            color: #343a40;
        }

        .content p {
            font-size: 1.2em;
            color: #495057;
            line-height: 1.5;
        }

        .features {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }

        .feature {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 30%;
            text-align: center;
        }

        .feature i {
            font-size: 3em;
            color: #007bff;
        }

        footer {
            text-align: center;
            padding: 20px 0;
            background: #007bff;
            color: #ffffff;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Selamat Datang di Poliklinik Sehat</h1>
        <p>Pelayanan Kesehatan Terbaik untuk Anda dan Keluarga</p>
    </header>
    

    <div class="content">
        <h2>Tentang Kami</h2>
        <p>Poliklinik Sehat adalah tempat terbaik untuk mendapatkan layanan kesehatan yang berkualitas. Kami memiliki tim dokter yang berpengalaman dan fasilitas yang memadai untuk memenuhi kebutuhan kesehatan Anda.</p>
        <p>Silakan login untuk mengakses layanan kami.</p>

        <div class="login-menu">
            <a href="pages/login_admin.php">Login Admin</a>
            <a href="pages/login_dokter.php">Login Dokter</a>
            <a href="pages/register_pasien.php">Login Pasien</a>
         </div>

        <div class="features">
            <div class="feature">
                <i class="fas fa-user-md"></i>
                <h3>Dokter Berpengalaman</h3>
                <p>Tim dokter kami siap memberikan pelayanan terbaik untuk kesehatan Anda.</p>
            </div>
            <div class="feature">
                <i class="fas fa-hospital"></i>
                <h3>Fasilitas Modern</h3>
                <p>Kami dilengkapi dengan fasilitas medis yang modern dan nyaman.</p>
            </div>
            <div class="feature">
                <i class="fas fa-calendar-check"></i>
                <h3>Jadwal Fleksibel</h3>
                <p>Jadwal kunjungan yang fleksibel untuk memenuhi kebutuhan Anda
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; // Menyertakan footer ?>
</body>
</html>