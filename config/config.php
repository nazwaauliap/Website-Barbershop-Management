<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_PORT', '3308'); // Sesuaikan port Anda (3306 untuk default, 3308 untuk yang di SQL)
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'nz_barbershop');

// Koneksi Database
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    // Cek koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    
    // Set charset
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Fungsi untuk mencegah SQL Injection
function clean($data) {
    global $conn;
    return $conn->real_escape_string(strip_tags(trim($data)));
}

// Fungsi untuk format rupiah
function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Fungsi untuk format tanggal Indonesia
function tanggal_indo($tanggal) {
    if(empty($tanggal) || $tanggal == '0000-00-00') return "-";
    
    $bulan = array (
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $pecahkan = explode('-', $tanggal);
    
    // Tambahkan pengecekan apakah explode menghasilkan 3 bagian
    if(count($pecahkan) == 3) {
        return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
    }
    return $tanggal;
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>