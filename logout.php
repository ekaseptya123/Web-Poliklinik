<?php
session_start();
session_destroy(); // Menghancurkan session
header("Location: index.php"); // Arahkan kembali ke halaman login
exit();
?>