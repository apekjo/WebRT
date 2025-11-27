<?php 
session_start(); 
include 'db.php'; 

$err = '';
if($_POST){
    $nik = trim($_POST['nik']);
    $pass = $_POST['password'];

    // KHUSUS UNTUK ADMIN: NIK = "admin"
    if($nik === 'admin' && $pass === 'admin123'){
        // Buat session admin langsung
        $_SESSION['user'] = [
            'id' => 0,
            'nama' => 'Administrator',
            'nik' => 'admin',
            'email' => 'admin@rt01rw05.com',
            'role' => 'admin'
        ];
        header('Location: dashboard_warga.php');
        exit;
    }

    // Login biasa untuk warga
    $nik = $conn->real_escape_string($nik);
    $res = $conn->query("SELECT * FROM warga WHERE nik='$nik'");
    if($res->num_rows > 0){
        $user = $res->fetch_assoc();
        if(password_verify($pass, $user['password'])){
            $_SESSION['user'] = $user;
            header('Location: dashboard_warga.php'); 
            exit;
        } else $err = "Password salah!";
    } else $err = "NIK tidak ditemukan!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - RT 09 RW 04</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-box { width:420px; margin:100px auto; background:white; padding:40px; border-radius:20px; box-shadow:0 20px 60px rgba(0,0,0,0.2); text-align:center; }
        .input-group { position:relative; margin:25px 0; }
        .input-group input { width:100%; padding:15px 50px 15px 15px; border:2px solid #e0e0e0; border-radius:12px; font-size:1.1em; }
        .eye-icon { position:absolute; right:15px; top:50%; transform:translateY(-50%); cursor:pointer; color:#666; font-size:1.3em; }
        .btn-login { width:100%; padding:15px; background:#007bff; color:white; border:none; border-radius:12px; font-size:1.2em; cursor:pointer; }
        .btn-login:hover { background:#0056b3; }
    </style>
</head>
<body>
<div class="login-box">
    <h2 style="color:#007bff;">RT 09 RW 04</h2>
    <p style="color:#666; margin-bottom:30px;">Website Resmi Rukun Tetangga</p>

    <a href="index.php" style="color:#007bff; text-decoration:none; display:block; margin-bottom:20px;">
        ← Kembali ke Beranda
    </a>

    <?php if($err): ?>
        <div style="background:#ffebee; color:red; padding:12px; border-radius:10px; margin:20px 0;">
            <?= $err ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <input type="text" name="nik" placeholder="NIK atau ketik: admin" required autofocus>
        </div>
        <div class="input-group">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <i class="fas fa-eye eye-icon" id="togglePass" onclick="togglePassword()"></i>
        </div>
        <button type="submit" class="btn-login">MASUK</button>
    </form>

    <div style="margin-top:25px; padding:15px; background:#f8f9fa; border-radius:10px; font-size:0.95em;">
        <strong>Login Admin:</strong><br>
        NIK: <code style="background:#e9ecef; padding:2px 8px;">admin</code><br>
        Password: <code style="background:#e9ecef; padding:2px 8px;">admin123</code>
    </div>

    <p style="margin-top:20px;">
        Belum punya akun? <a href="register.php" style="color:#007bff;">Daftar di sini</a>
    </p>
</div>

<script>
function togglePassword() {
    let p = document.getElementById("password");
    let i = document.getElementById("togglePass");
    if (p.type === "password") {
        p.type = "text";
        i.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        p.type = "password";
        i.classList.replace("fa-eye-slash", "fa-eye");
    }
}
</script>
</body>
</html>