<?php 
session_start(); 
include 'db.php'; 
include 'functions.php';

// Cek sesi login
if(!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];

// Redirect jika admin mencoba akses halaman warga
if($user['nik'] === 'admin' || $user['nik'] === '0000000000000000'){
    header('Location: admin_panel.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard Warga - <?= htmlspecialchars($user['nama']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Style CSS Bawaan */
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',Arial,sans-serif; }
        body { background:#f8f9fc; color:#333; }
        header { background:white; padding:15px 0; box-shadow:0 4px 20px rgba(0,0,0,0.1); position:fixed; width:100%; top:0; z-index:1000; }
        .container { max-width:1100px; margin:0 auto; padding:0 20px; }
        .header-content { display:flex; justify-content:space-between; align-items:center; }
        .logo { font-size:1.8em; font-weight:bold; color:#007bff; }
        .logout-btn { background:#dc3545; color:white; padding:10px 20px; border-radius:50px; text-decoration:none; margin-left:15px; font-weight:600; }
        .main { margin-top:100px; padding:20px; }
        .welcome { background:linear-gradient(135deg,#667eea,#034184ff); color:white; padding:40px; border-radius:20px; text-align:center; margin-bottom:30px; box-shadow:0 10px 30px rgba(0,0,0,0.2); }
        .tabs { display:flex; background:white; border-radius:15px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.1); margin-bottom:30px; flex-wrap: wrap;}
        .tab-btn { flex:1; padding:18px; border:none; background:none; font-weight:600; cursor:pointer; transition:0.3s; white-space: nowrap; }
        .tab-btn.active { background:#034184ff; color:white; }
        .tab-content { display:none; background:white; padding:35px; border-radius:20px; box-shadow:0 15px 40px rgba(0,0,0,0.1); }
        .tab-content.active { display:block; animation:fadeIn 0.5s; }
        @keyframes fadeIn { from{opacity:0; transform:translateY(20px);} to{opacity:1; transform:none;} }
        .notif-item { background:#f8f9fa; padding:25px; border-left:6px solid #007bff; margin:20px 0; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.05); }
        .notif-item h4 { color:#007bff; margin-bottom:8px; }
        .form-group { margin:20px 0; }
        .form-group input, .form-group textarea, .form-group select { width:100%; padding:15px; border:2px solid #e0e0e0; border-radius:12px; font-size:1em; }
        .form-group textarea { height:140px; resize:vertical; }
        .btn-submit { background:#007bff; color:white; padding:15px 30px; border:none; border-radius:12px; font-size:1.1em; cursor:pointer; width:100%; margin-top:10px; }
        .btn-submit:hover { background:#034184ff; }
        .no-data { text-align:center; color:#888; font-size:1.3em; padding:60px 20px; }
        .success-msg { background:#d4edda; color:#155724; padding:15px; border-radius:10px; margin:20px 0; text-align:center; font-weight:bold; }
    </style>
</head>
<body>

<header>
    <div class="container header-content">
        <div class="logo">RT 09 RW 04</div>
        <div>
            Halo, <strong><?= htmlspecialchars($user['nama']) ?></strong>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
</header>

<div class="main">
    <div class="container">
        <div class="welcome">
            <h1>Selamat Datang Kembali!</h1>
            <p>Kelola kegiatan RT, ajukan surat, dan laporkan pengaduan dengan mudah.</p>
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="openTab('pemberitahuan')">Pemberitahuan</button>
            <button class="tab-btn" onclick="openTab('upload')">Upload Kegiatan</button>
            <button class="tab-btn" onclick="openTab('pengajuan')">Pengajuan Surat</button>
            <button class="tab-btn" onclick="openTab('pengaduan')">Pengaduan</button>
            <button class="tab-btn" onclick="openTab('status-pengajuan')">Status Pengajuan</button>
        </div>

    <div id="pemberitahuan" class="tab-content active">
        <h2 style="color:#007bff; margin-bottom:25px;">Pemberitahuan Resmi & Kegiatan Warga</h2>

        <?php 
        // Query Union untuk menggabungkan Informasi (Teks) dan Galeri (Gambar)
        $notif = $conn->query("
            SELECT 'info' AS tipe, i.id AS item_id, i.judul, i.isi, i.tanggal, NULL AS nama_warga, NULL AS gambar
            FROM informasi i
            UNION ALL
            SELECT 'kegiatan' AS tipe, g.id AS item_id, g.judul, g.deskripsi AS isi, g.tanggal, w.nama AS nama_warga, g.gambar
            FROM galeri g LEFT JOIN warga w ON g.warga_id = w.id
            ORDER BY tanggal DESC LIMIT 50
        ");

        if($notif && $notif->num_rows > 0):
            while($n = $notif->fetch_assoc()): 
                
                // LOGIKA BARU: Menentukan Penulis (Admin atau Warga)
                // Jika nama_warga NULL, berarti upload dari Admin
                $penulis = $n['nama_warga'] ? $n['nama_warga'] : 'Admin / Pengurus RT';
                $label_color = $n['nama_warga'] ? '#28a745' : '#007bff'; // Hijau Warga, Biru Admin
            ?>
                <div class="notif-item" style="border-left-color: <?= $n['tipe']=='info'?'#dc3545':'#007bff' ?>;">
                    <h4>
                        <?= $n['tipe']=='info' ? '<i class="fas fa-bullhorn"></i>' : '<i class="fas fa-camera"></i>' ?> 
                        <?= htmlspecialchars($n['judul']) ?>
                    </h4>

                    <?php if($n['tipe'] == 'kegiatan' && $n['gambar']): ?>
                        <div style="margin:15px 0; text-align:center;">
                            <img src="assets/uploads/<?= htmlspecialchars($n['gambar']) ?>" 
                                 alt="Foto kegiatan" 
                                 style="max-width:100%; max-height:400px; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.2);">
                        </div>
                    <?php endif; ?>
                    
                    <p><?= nl2br(htmlspecialchars($n['isi'])) ?></p>
                    
                    <?php if($n['tipe'] == 'kegiatan'): ?>
                        <small style="color:<?= $label_color ?>; font-weight:bold; display:block; margin-top:5px;">
                            Diposting oleh: <?= htmlspecialchars($penulis) ?>
                        </small>
                    <?php endif; ?>
                    
                    <small style="color:#666; display:block; margin-top:5px;">
                        <i class="far fa-clock"></i> <?= tgl_indo($n['tanggal']) ?>
                    </small>
                </div>
            <?php endwhile;
        else: ?>
            <div class="no-data">Belum ada pemberitahuan atau kegiatan.</div>
        <?php endif; ?>
    </div>

        <div id="upload" class="tab-content">
            <h2 style="color:#007bff;">Upload Kegiatan RT</h2>
            <?php if(isset($_GET['upload'])): ?><div class="success-msg">Kegiatan berhasil diupload!</div><?php endif; ?>
            <form action="proses/upload_galeri.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" name="judul" placeholder="Judul Kegiatan" required>
                </div>
                <div class="form-group">
                    <textarea name="deskripsi" placeholder="Ceritakan kegiatan ini..." required></textarea>
                </div>
                <div class="form-group">
                    <input type="file" name="gambar" accept="image/*">
                </div>
                <button type="submit" class="btn-submit">Bagikan ke Warga</button>
            </form>
        </div>

        <div id="pengajuan" class="tab-content">
            <h2 style="color:#007bff;">Ajukan Surat Administrasi</h2>
            <?php if(isset($_GET['pengajuan'])): ?><div class="success-msg">Pengajuan surat berhasil dikirim ke admin, Silahkan mendatangi kantor RT untuk menyelesaikan keperluan!</div><?php endif; ?>
            <form action="proses/pengajuan_proses.php" method="POST">
                <dl style="margin: top 20px;">Bahan-bahan yang diperlukan :
                <dd>1. Keterangan Domisili      : Foto kopi KTP dan Kartu keluarga, Surat keterangan Domisili daerah asal</dd>
                <dd>2. Keterangan Usaha         : KTP, Kartu keluarga asli dan foto kopi, Pajak Bumi dan Bangunan</dd>
                <dd>3. Pengantar SKCK           : Foto kopi KTP, Akta kelahiran/Ijazah dan Kartu keluarga, Pas foto</dd>
                <dd>4. Keterangan Belum Menikah : Foto kopi KTP dan Kartu keluarga, Surat pernyataan belum menikah, Materai Rp10.000</dd>
                <dd></dd>
            </dl>

                <div class="form-group">
                    <select name="jenis" required>
                        <option value="">-- Pilih Jenis Surat --</option>
                        <option>Surat Keterangan Domisili</option>
                        <option>Surat Keterangan Usaha</option>
                        <option>Surat Pengantar SKCK</option>
                        <option>Surat Keterangan Belum Menikah</option>
                        <option>Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <textarea name="keterangan" placeholder="Jelaskan keperluan surat..." required></textarea>
                </div>
                <button type="submit" class="btn-submit">Kirim Pengajuan</button>
            </form>
        </div>

        <div id="status-pengajuan" class="tab-content">
            <h2 style="color:#007bff; margin-bottom:25px;">Status Pengajuan Surat Anda</h2>
            <div style="margin-top:20px; padding:20px; background:#f8f9fa; border-radius:15px;">
                <?php 
                $pengajuan = $conn->query("SELECT * FROM pengajuan WHERE warga_id='".$user['id']."' ORDER BY tanggal DESC");
                if($pengajuan->num_rows > 0):
                    while($p = $pengajuan->fetch_assoc()): 
                        if($p['status'] == 'Selesai') {
                            $ket = "Sudah Selesai"; $warna = "#27ae60"; $icon = "✓";
                        } elseif($p['status'] == 'Diproses') {
                            $ket = "Sedang Diproses"; $warna = "#3498db"; $icon = "⏳";
                        } else {
                            $ket = "Menunggu Konfirmasi"; $warna = "#f39c12"; $icon = "⏱";
                        }
                    ?>
                        <div style="background:white; padding:20px; margin:15px 0; border-radius:15px; border-left:8px solid <?= $warna ?>; box-shadow:0 5px 20px rgba(0,0,0,0.1);">
                            <h3 style="color:<?= $warna ?>; margin-bottom:10px;"><?= $icon ?> <?= $ket ?></h3>
                            <strong><?= htmlspecialchars($p['jenis']) ?></strong><br>
                            <small>Keperluan: <?= htmlspecialchars($p['keterangan']) ?></small><br><br>
                            <small style="color:#7f8c8d;">Diajukan pada <?= date('d F Y H:i', strtotime($p['tanggal'])) ?> WIB</small>
                        </div>
                    <?php endwhile;
                else: ?>
                    <div class="no-data">Belum ada pengajuan surat.</div>
                <?php endif; ?>
            </div>
        </div>

        <div id="pengaduan" class="tab-content">
            <h2 style="color:#007bff;">Laporkan Pengaduan</h2>
            
            <?php if(isset($_GET['pengaduan'])): ?>
                <div class="success-msg">Pengaduan berhasil dikirim!</div>
            <?php endif; ?>

            <form action="proses/pengaduan_proses.php" method="POST">
                <div class="form-group">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Kategori Laporan:</label>
                    <select name="jenis" required style="width:100%; padding:15px; border:2px solid #e0e0e0; border-radius:12px;">
                        <option value="">-- Pilih Jenis Pengaduan --</option>
                        <option value="Saran">Saran</option>
                        <option value="Kritik">Kritik</option>
                        <option value="Keluhan">Keluhan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Isi Laporan:</label>
                    <textarea name="isi" placeholder="Tulis detail pengaduan Anda di sini..." required style="height:150px;"></textarea>
                </div>
                
                <button type="submit" class="btn-submit">Kirim Pengaduan</button>
            </form>
        </div>
    </div>
</div>

<script>
function openTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabName).classList.add('active');
    document.querySelector(`button[onclick="openTab('${tabName}')"]`).classList.add('active');
}
</script>
</body>
</html>