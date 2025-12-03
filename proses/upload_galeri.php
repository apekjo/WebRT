<?php
session_start();
include '../db.php';

// === 1. Cek login (tetap dipertahankan) ===
if (!isset($_SESSION['user'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

$warga_id  = $_SESSION['user']['id'];
$judul     = $conn->real_escape_string($_POST['judul']);
$deskripsi = $conn->real_escape_string($_POST['deskripsi']);

// === 2. Inisialisasi variabel gambar (PENTING!) ===
$gambar = ''; // Default: kosong (akan disimpan sebagai NULL di DB)

// === 3. Hanya proses upload jika ADA file yang dikirim dan tidak error ===
if (
    isset($_FILES['gambar']) && 
    $_FILES['gambar']['error'] === UPLOAD_ERR_OK // Artinya: file berhasil diupload
) {
    $target_dir = "../assets/uploads/";
    $file_name  = $_FILES['gambar']['name'];
    $file_tmp   = $_FILES['gambar']['tmp_name'];
    $file_size  = $_FILES['gambar']['size'];

    // Validasi ekstensi (hanya izinkan gambar)
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext     = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Validasi ukuran maksimal 5MB (bisa disesuaikan)
    if (in_array($ext, $allowed) && $file_size <= 5 * 1024 * 1024) {
        // Buat nama file unik agar tidak bentrok
        $gambar = time() . "_" . rand(1000, 9999) . "." . $ext;
        $target_path = $target_dir . $gambar;

        // Pindahkan file
        if (move_uploaded_file($file_tmp, $target_path)) {
            // Berhasil upload → $gambar tetap berisi nama file
        } else {
            // Gagal pindah file (misal permission folder)
            $gambar = ''; // tetap kosong
            // Bisa ditambah notif error jika mau
        }
    }
    // Jika ekstensi tidak diizinkan atau terlalu besar → $gambar tetap kosong
}

// === 4. Query INSERT yang aman (pakai prepared statement lebih baik, tapi minimal seperti ini dulu) ===
$sql = "INSERT INTO galeri (warga_id, judul, deskripsi, gambar, tanggal) 
        VALUES ('$warga_id', '$judul', '$deskripsi', " . ($gambar ? "'$gambar'" : "NULL") . ", NOW())";

$conn->query($sql);

// === 5. Redirect dengan pesan sukses ===
header("Location: ../dashboard_warga.php?upload=success");
exit;
?>