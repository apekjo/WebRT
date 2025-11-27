<?php
session_start();
// Gunakan dirname(__FILE__) untuk memastikan path include selalu benar
include dirname(__FILE__) . '/../db.php';

// Pastikan koneksi database ada
if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

// Cek Login Admin
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['nik'], ['admin', '0000000000000000'])) {
    die("Akses ditolak! Anda bukan admin.");
}

// AMBIL act DARI GET ATAU POST
$act = $_GET['act'] ?? $_POST['act'] ?? '';

// ========== 1. UPDATE STATUS PENGAJUAN SURAT ==========
if ($act === 'update_status') {
    // Validasi Input
    if (!isset($_POST['id']) || !isset($_POST['status'])) {
        die("Data ID atau Status tidak dikirim oleh Browser.");
    }

    $id     = (int)$_POST['id'];
    $status = $conn->real_escape_string($_POST['status']);
    
    // Cek apakah ID valid (bukan 0)
    if ($id === 0) {
        die("ID Pengajuan tidak valid (0).");
    }

    // Jalankan Query
    $query  = "UPDATE pengajuan SET status = '$status' WHERE id = '$id'";
    $update = $conn->query($query);
    
    if ($update) {
        echo "success";
    } else {
        echo "Gagal Query MySQL: " . $conn->error;
    }
    exit;
}

// ========== 2. TANDAI PENGADUAN DIBACA ==========
if ($act === 'tandai_dibaca') {
    $id = (int)$_GET['id'];
    
    if ($conn->query("UPDATE pengaduan SET status = 'dibaca' WHERE id = '$id'")) {
        echo "ok";
    } else {
        echo "Gagal Query: " . $conn->error;
    }
    exit;
}

// ========== 3. HAPUS INFO ==========
if ($act === 'hapus_info') {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM informasi WHERE id = '$id'");
    header("Location: ../admin_panel.php");
    exit;
}

// ========== 4. HAPUS PENGADUAN ==========
if ($act === 'hapus_pengaduan') {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM pengaduan WHERE id = '$id'");
    header("Location: ../admin_panel.php");
    exit;
}

// ========== 5. EDIT INFO ==========
if ($act === 'edit_info') {
    $id    = (int)$_POST['id'];
    $judul = $conn->real_escape_string($_POST['judul']);
    $isi   = $conn->real_escape_string($_POST['isi']);
    $conn->query("UPDATE informasi SET judul='$judul', isi='$isi', updated_at=NOW() WHERE id='$id'");
    header("Location: ../admin_panel.php?edit=success");
    exit;
}

// ========== 6. TAMBAH INFO ==========
if ($act === 'tambah_info') {
    $judul = $conn->real_escape_string($_POST['judul']);
    $isi   = $conn->real_escape_string($_POST['isi']);
    $conn->query("INSERT INTO informasi (judul, isi, tanggal, updated_at) VALUES ('$judul', '$isi', NOW(), NOW())");
    header("Location: ../admin_panel.php?info=success");
    exit;
}

// ========== 7. TAMBAH GALERI (ADMIN) ==========
if ($act === 'tambah_galeri') {
    $judul     = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    
    // Logika Upload Gambar
    $rand = rand();
    $ekstensi =  array('png','jpg','jpeg','gif');
    $filename = $_FILES['gambar']['name'];
    $ukuran = $_FILES['gambar']['size'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    
    if(!in_array(strtolower($ext),$ekstensi) ) {
        die("Gagal: Ekstensi file harus png, jpg, jpeg, atau gif.");
    }
    
    if($ukuran < 1044070){ // Max 1 MB
        $xx = $rand.'_'.$filename;
        // Pastikan folder assets/uploads/ sudah ada
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../assets/uploads/'.$xx);
        
        // Insert ke database (warga_id = 0 menandakan ini dari Admin)
        $query = "INSERT INTO galeri (warga_id, judul, deskripsi, gambar, tanggal) VALUES (NULL, '$judul', '$deskripsi', '$xx', NOW())";
        
        if($conn->query($query)){
            header("Location: ../admin_panel.php?upload=success");
        } else {
            echo "Gagal insert DB: " . $conn->error;
        }
    } else {
        echo "Gagal: Ukuran file terlalu besar (Max 1MB).";
    }
    exit;
}

// ========== 8. HAPUS GALERI ==========
if ($act === 'hapus_galeri') {
    $id   = (int)$_GET['id'];
    $file = $_GET['file']; // Nama file gambar
    
    // Hapus data di database
    $conn->query("DELETE FROM galeri WHERE id = '$id'");
    
    // Hapus file fisik di folder (opsional tapi disarankan agar server tidak penuh)
    $path = "../assets/uploads/" . $file;
    if(file_exists($path)){
        unlink($path);
    }
    
    header("Location: ../admin_panel.php?hapus=success");
    exit;
}

echo "Aksi ($act) tidak dikenali!";
exit;
?>