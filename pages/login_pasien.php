<?php
session_start();
require '../config/db.php'; // Pastikan jalur ini benar

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Mencari pasien di database berdasarkan username dan password
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    $stmt->execute(['username' => $username, 'password' => $password]);
    $pasien = $stmt->fetch();

    if ($pasien) {
        // Set session dan redirect ke halaman dashboard atau halaman lain
        $_SESSION['pasien_id'] = $pasien['id'];
        header("Location: dashboard_pasien.php"); 
        exit();
    } else {
       // $error = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pasien</title>
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

        .register-link {
            margin-top: 15px;
            font-size: 14px;
            color: #2F4F4F;
            text-decoration: none;
        }

        .register-link:hover {
            text-decoration: underline;
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
            <h1>Login Pasien</h1>
        </header>

        <form method="POST" class="login-form">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <?php if ($error): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
        <a href="register_pasien.php" class="register-link">Belum punya akun? Daftar</a>
    </div>
</body>
</html>