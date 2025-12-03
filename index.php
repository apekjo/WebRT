<?php session_start(); include 'db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>RT 09 RW 04 - Website Resmi</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<header>
    <div class="container" style="display:flex; justify-content:space-between; align-items:center;">
        <div class="logo">
            <i class="fas fa-home"></i> RT 09 RW 04
        </div>
        <nav>
            <?php if(isset($_SESSION['user'])): ?>
                <a href="dashboard_warga.php" class="btn" style="background:#28a745;">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="logout.php" class="btn" style="background:#dc3545;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="register.php" class="btn" style="background:#ffc107; color:#212529;">
                    <i class="fas fa-user-plus"></i> Daftar Warga
                </a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<div class="hero" style="background: linear-gradient(rgba(3, 65, 132, 1),rgba(3, 65, 132, 1)), url('assets/images/hero.jpg') center/cover no-repeat; color:white; text-align:center; padding:180px 20px;">
    <h1 style="font-size:3.5em; margin-bottom:20px;">Selamat Datang di Website Resmi</h1>
    <p style="font-size:1.5em;">RT 09 RW 04 - Kelurahan Cadika, Muara Bungo</p>
</div>

<div class="container" style="margin-top:-80px;">
    <section class="info">
        <h2 style="text-align:center; color:white; margin-bottom:30px; font-size:2.2em;">
            <i class="fas fa-bullhorn"></i> Informasi Terbaru
        </h2>
        <?php $res = $conn->query("SELECT * FROM informasi ORDER BY tanggal DESC LIMIT 6");
        while($row = $res->fetch_assoc()): ?>
        <div class="card">
            <?php if($row['gambar'] && file_exists("assets/images/".$row['gambar'])): ?>
                <img src="assets/images/<?= $row['gambar'] ?>" alt="" style="width:100%;height:200px;object-fit:cover;border-radius:10px;">
            <?php endif; ?>
            <h3><?= htmlspecialchars($row['judul']) ?></h3>
            <p><?= nl2br(htmlspecialchars($row['isi'])) ?></p>
            <small><i class="fas fa-calendar"></i> <?= date('d F Y', strtotime($row['tanggal'])) ?></small>
        </div>
        <?php endwhile; ?>
    </section>

    <section class="galeri" style="margin-top:60px;">
    <h2 style="text-align:center; color:rgba(0, 123, 255, 1); margin-bottom:30px; font-size:2.2em;">
        <i class="fas fa-images"></i> Galeri Kegiatan Warga
    </h2>
    <div class="grid">
        <?php 
        // QUERY BARU: Hanya ambil kegiatan yang ADA gambarnya (gambar NOT NULL dan tidak kosong)
        $gal = $conn->query("
            SELECT g.*, w.nama 
            FROM galeri g 
            LEFT JOIN warga w ON g.warga_id = w.id 
            WHERE g.gambar IS NOT NULL 
              AND g.gambar != '' 
            ORDER BY g.tanggal DESC 
            LIMIT 12
        ");

        // Jika tidak ada yang punya foto, tampilkan pesan ramah
        if ($gal->num_rows == 0): ?>
            <div style="grid-column: 1 / -1; text-align:center; padding:40px; color:#666;">
                <i class="fas fa-images fa-3x" style="margin-bottom:20px; opacity:0.3;"></i>
                <p>Belum ada foto kegiatan yang diunggah. <br>
                   Yuk jadi yang pertama bagikan momen seru bersama warga!</p>
            </div>
        <?php endif; ?>

        <?php while($g = $gal->fetch_assoc()): 
            // Pastikan file benar-benar ada (keamanan tambahan)
            $img_path = "assets/uploads/" . $g['gambar'];
            $img = file_exists($img_path) ? $g['gambar'] : 'placeholder.jpg';

            // Logika penulis (Admin atau Warga)
            $uploader = $g['nama'] ?? "Admin / Pengurus RT";
            $labelStyle = empty($g['nama']) ? 'color:#007bff; font-weight:bold;' : 'color:#28a745; font-weight:bold;';
        ?>
            <div class="item" style="background:white;border-radius:15px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.2);">
                <img src="assets/uploads/<?= htmlspecialchars($img) ?>" alt="Kegiatan <?= htmlspecialchars($g['judul']) ?>">
                <div style="padding:15px;">
                    <h4><?= htmlspecialchars($g['judul']) ?></h4>
                    <p style="font-size:0.9em;color:#666;">
                        <?= substr(htmlspecialchars($g['deskripsi']), 0, 80) ?>...
                    </p>
                    <small>
                        Diposting oleh 
                        <span style="<?= $labelStyle ?>">
                            <?= htmlspecialchars($uploader) ?>
                        </span> | 
                        <?= date('d/m/Y', strtotime($g['tanggal'])) ?>
                    </small>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>
</div>

<footer style="background:rgba(3, 65, 132, 1);color:white;text-align:center;padding:40px;margin-top:100px;">
    <p>&copy; <?= date('Y') ?> RT 09 RW 04 - Website Resmi Rukun Tetangga. All rights reserved.</p>
</footer>

</body>
</html>