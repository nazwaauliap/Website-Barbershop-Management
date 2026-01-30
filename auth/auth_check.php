<?php
// File ini digunakan untuk memproteksi halaman yang memerlukan login
// Include file ini di awal setiap halaman yang memerlukan autentikasi

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Jika belum login, redirect ke halaman login
    header('Location: login.php');
    exit();
}

// Optional: Cek timeout session (30 menit)
$timeout_duration = 1800; // 30 menit dalam detik

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session timeout, hapus session dan redirect ke login
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit();
}

// Update waktu aktivitas terakhir
$_SESSION['last_activity'] = time();
?>