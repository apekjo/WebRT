<?php 
session_start(); 
include 'db.php'; 
if(isset($_SESSION['user'])) header('Location: dashboard_warga.php');

$msg = $err = '';
if($_POST){
    $nama  = $conn->real_escape_string($_POST['nama']);
    $nik   = $conn->real_escape_string($_POST['nik']);
    $email = $conn->real_escape_string($_POST['email']);
    $pass  = $_POST['password'];
    $pass2 = $_POST['password2'];

    if($pass !== $pass2){
        $err = "Password dan konfirmasi tidak sama!";
    } elseif(strlen($pass) < 6){
        $err = "Password minimal 6 karakter!";
    } else {
        $cek = $conn->query("SELECT id FROM warga WHERE nik='$nik'");
        if($cek->num_rows > 0){
            $err = "NIK sudah terdaftar!";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $conn->query("INSERT INTO warga (nama, nik, email, password) VALUES ('$nama','$nik','$email','$hash')");
            $msg = "Registrasi berhasil! Silakan login sekarang.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Warga - RT 01 RW 05</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        .register-box { width:460px; margin:80px auto; background:white; padding:40px; border-radius:20px; box-shadow:0 20px 60px rgba(0,0,0,0.2); text-align:center; }
        .input-group { position:relative; margin:22px 0; }
        .input-group input { width:100%; padding:15px 50px 15px 15px; border:2px solid #e0e0e0; border-radius:12px; font-size:1.1em; }
        .eye-icon { position:absolute; right:15px; top:50%; transform:translateY(-50%); cursor:pointer; color:#666; font-size:1.3em; }
        .btn-daftar { width:100%; padding:15px; background:#007bff; color:white; border:none; border-radius:12px; font-size:1.2em; cursor:pointer; margin-top:10px; }
        .btn-daftar:hover { background:#034184ff; }
        .kembali { display:inline-block; margin-bottom:20px; color:#007bff; text-decoration:none; font-size:1.1em; }
    </style>
</head>
<body>

<div class="register-box">
    <h2 style="color:rgba(0, 123, 255, 1); margin-bottom:10px;">Daftar Akun Warga Baru</h2>
    <p style="color:#666; margin-bottom:25px;">RT 09 RW 04 - Website Resmi Rukun Tetangga</p>

    <a href="index.php" class="kembali">← Kembali ke Beranda</a>

    <?php if($err): ?>
        <div style="background:#ffebee; color:red; padding:12px; border-radius:10px; margin:20px 0;">
            <?= $err ?>
        </div>
    <?php endif; ?>

    <?php if($msg): ?>
        <div style="background:#d4edda; color:#155724; padding:12px; border-radius:10px; margin:20px 0;">
            <?= $msg ?><br><a href="login.php" style="color:#007bff;">Klik di sini untuk login →</a>
        </div>
    <?php endif; ?>

    <?php if(!$msg): ?>
    <form method="POST">
        <div class="input-group">
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
        </div>
        <div class="input-group">
            <input type="text" name="nik" placeholder="NIK (16 digit)" maxlength="16" required>
        </div>
        <div class="input-group">
            <input type="email" name="email" placeholder="Email (opsional)">
        </div>
        <div class="input-group">
            <input type="password" name="password" id="pass1" placeholder="Buat Password" required>
            <i class="fas fa-eye eye-icon" id="toggle1" onclick="togglePass('pass1','toggle1')"></i>
        </div>
        <div class="input-group">
            <input type="password" name="password2" id="pass2" placeholder="Konfirmasi Password" required>
            <i class="fas fa-eye eye-icon" id="toggle2" onclick="togglePass('pass2','toggle2')"></i>
        </div>
        <button type="submit" class="btn-daftar">DAFTAR SEKARANG</button>
    </form>
    <?php endif; ?>

    <p style="margin-top:25px;">
        Sudah punya akun? <a href="login.php" style="color:#007bff; font-weight:bold;">Login di sini</a>
    </p>
</div>

<script>
function togglePass(id, iconId) {
    let pass = document.getElementById(id);
    let icon = document.getElementById(iconId);
    if (pass.type === "password") {
        pass.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        pass.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}
</script>
</body>
</html>