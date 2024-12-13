<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil username dan password dari form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ganti dengan logika autentikasi Anda
    if ($username == 'Agha' && $password == 'dokter') {
        // Simpan username ke dalam session
        $_SESSION['username'] = $username;
        // Arahkan ke dashboard
        header("Location: dashboard_dokter.php");
        exit();
    } else {
        $error = 'Username atau password salah!';
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dokter</title>
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

        .login-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
            transition: transform 0.3s;
        }

        .login-container:hover {
            transform: scale(1.02);
        }

        .login-container h1 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #333;
        }

        .login-form {
            display: flex;
            flex-direction: column;
        }

        .login-form input {
            margin-bottom: 15px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .login-form input:focus {
            border-color: #4facfe;
            outline: none;
        }

        .login-form button {
            padding: 12px;
            background: #E9967A;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        .login-form button:hover {
            background: #CD5C5C;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }

        @media (max-width: 400px) {
            .login-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <header>
            <h1>Login Dokter</h1>
        </header>

        <form method="POST" class="login-form">
            <input type="text" name="username" placeholder="Username Dokter" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <?php if ($error): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
    </div>

    
</body>
</html>