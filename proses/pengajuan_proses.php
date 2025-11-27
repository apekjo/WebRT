<?php
session_start();
include '../db.php';
if(!isset($_SESSION['user'])) die("Akses ditolak");

$warga_id = $_SESSION['user']['id'];
$jenis = $conn->real_escape_string($_POST['jenis']);
$keterangan = $conn->real_escape_string($_POST['keterangan']);

$conn->query("INSERT INTO pengajuan (warga_id, jenis, keterangan, status, tanggal) 
              VALUES ('$warga_id', '$jenis', '$keterangan', 'menunggu', NOW())");

header("Location: ../dashboard_warga.php?pengajuan=success");
?>