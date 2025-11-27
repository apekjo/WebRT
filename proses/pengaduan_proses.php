<?php
session_start();
include '../db.php';

if(!isset($_SESSION['user'])){
    header('Location: ../login.php');
    exit;
}

// Ambil data dari Form
$warga_id = $_SESSION['user']['id'];
$jenis    = $conn->real_escape_string($_POST['jenis']); // Tangkap Jenis
$isi      = $conn->real_escape_string($_POST['isi']);   // Tangkap Isi

// Query Insert (Sekarang memasukkan kolom 'jenis')
$query = "INSERT INTO pengaduan (warga_id, jenis, isi, status, tanggal) 
          VALUES ('$warga_id', '$jenis', '$isi', 'belum_dibaca', NOW())";

if($conn->query($query)){
    header('Location: ../dashboard_warga.php?pengaduan=success');
} else {
    echo "Gagal mengirim pengaduan: " . $conn->error;
}
?>