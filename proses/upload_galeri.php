<?php
session_start();
include '../db.php';
if(!isset($_SESSION['user'])) die("Akses ditolak");

$warga_id = $_SESSION['user']['id'];
$judul = $conn->real_escape_string($_POST['judul']);
$deskripsi = $conn->real_escape_string($_POST['deskripsi']);

$target = "../assets/uploads/";
$gambar = time()."_".$_FILES['gambar']['name'];
move_uploaded_file($_FILES['gambar']['tmp_name'], $target.$gambar);

$conn->query("INSERT INTO galeri (warga_id, judul, deskripsi, gambar, tanggal) 
              VALUES ('$warga_id', '$judul', '$deskripsi', '$gambar', NOW())");

header("Location: ../dashboard_warga.php?upload=success");
?>