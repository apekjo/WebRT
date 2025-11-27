<?php 
session_start(); 
include 'db.php';
include 'functions.php';
if(!isset($_SESSION['user']) || !in_array($_SESSION['user']['nik'], ['admin','0000000000000000'])) {
    header('Location: login.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ADMIN PANEL - RT 01 RW 05</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',Arial,sans-serif; }
        body { background:#f0f2f5; }
        header {
            background:#034184ff; color:white; padding:20px 0; position:fixed; width:100%; top:0; z-index:1000;
            box-shadow:0 4px 20px rgba(0,0,0,0.2);
        }
        .header-container { max-width:1200px; margin:0 auto; padding:0 30px; display:flex; justify-content:space-between; align-items:center; }
        .header-container h1 { font-size:1.8em; font-weight:bold; }
        .logout-btn { background:white; color:#007bff; padding:12px 30px; border-radius:50px; text-decoration:none; font-weight:bold; font-size:1.1em; box-shadow:0 5px 15px rgba(0,0,0,0.2); transition:0.3s; }
        .logout-btn:hover { transform:translateY(-3px); box-shadow:0 8px 25px rgba(0,0,0,0.3); }
        .main-container { max-width:1200px; margin:120px auto 50px; padding:0 30px; }
        .card { background:white; padding:30px; border-radius:18px; box-shadow:0 10px 40px rgba(0,0,0,0.1); margin-bottom:30px; }
        .card h2 { color:#333; margin-bottom:20px; font-size:1.5em; }
        input, textarea, select { width:100%; padding:15px; margin:12px 0; border:2px solid #e0e0e0; border-radius:12px; font-size:1em; }
        textarea { height:120px; resize:vertical; }
        .btn { padding:12px 25px; border:none; border-radius:12px; color:white; font-weight:bold; cursor:pointer; transition:0.3s; }
        .btn-green { background:#28a745; } .btn-green:hover { background:#218838; }
        .btn-blue { background:#007bff; } .btn-blue:hover { background:#0056b3; }
        .btn-red { background:#dc3545; } .btn-red:hover { background:#c82333; }
        table { width:100%; border-collapse:collapse; margin-top:15px; }
        table th { background:#007bff; color:white; padding:18px; text-align:center; font-weight:bold; }
        table td { padding:18px; text-align:center; border-bottom:1px solid #eee; }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <h1>ADMIN PANEL - RT 09 RW 04</h1>
        <a href="logout.php" class="logout-btn">LOGOUT</a>
    </div>
</header>

<div class="main-container">

    <!-- Daftar & Hapus Pemberitahuan -->
    <div class="card">
        <h2>Kelola Pemberitahuan Resmi</h2>
    <div class="card">
        <h2>Upload Dokumentasi Kegiatan (Admin)</h2>
        
        <form action="proses/admin_act.php?act=tambah_galeri" method="POST" enctype="multipart/form-data">

            <input type="text" name="judul" placeholder="Judul Kegiatan / Acara" required>

            <textarea name="deskripsi" placeholder="Deskripsi singkat kegiatan..." required style="height:80px;"></textarea>

            <label style="font-weight:bold; display:block; margin-top:10px;">Pilih Foto:</label>
            <input type="file" name="gambar" accept="image/*" required style="border:none; padding:10px 0;">

            <button type="submit" class="btn btn-green">Upload Dokumentasi</button>
        </form>
        <h3 style="margin-top:30px; color:#555;">Galeri Warga</h3>
        
        <p style="margin-bottom:15px; color:#666;">
            Berikut adalah foto-foto kegiatan yang diupload oleh warga. Anda dapat menghapusnya jika konten tidak sesuai.
        </p>

        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Foto</th>
                    <th style="width: 20%;">Nama Pengupload</th>
                    <th style="width: 25%;">Judul & Deskripsi</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Query mengambil galeri KHUSUS yang ada pemiliknya (Warga)
                // Kita gunakan JOIN untuk memastikan hanya mengambil data yang terhubung dengan warga
                $q_galeri = $conn->query("SELECT g.*, w.nama FROM galeri g JOIN warga w ON g.warga_id = w.id ORDER BY g.tanggal DESC");
                
                if($q_galeri->num_rows > 0):
                    while($g = $q_galeri->fetch_assoc()): 
                ?>
                <tr>
                    <td>
                        <a href="assets/uploads/<?= htmlspecialchars($g['gambar']) ?>" target="_blank">
                            <img src="assets/uploads/<?= htmlspecialchars($g['gambar']) ?>" 
                                 style="width:80px; height:80px; object-fit:cover; border-radius:8px; border:1px solid #ddd;">
                        </a>
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($g['nama']) ?></strong>
                    </td>
                    <td style="text-align:left;">
                        <strong style="color:#black;"><?= htmlspecialchars($g['judul']) ?></strong><br>
                        <small style="color:#555;"><?= substr(htmlspecialchars($g['deskripsi']), 0, 50) ?>...</small>
                    </td>
                    <td>
                        <?= date('d/m/Y H:i', strtotime($g['tanggal'])) ?>
                    </td>
                    <td>
                        <a href="proses/admin_act.php?act=hapus_galeri&id=<?= $g['id'] ?>&file=<?= $g['gambar'] ?>" 
                       class="btn btn-red" style="padding:5px 10px; font-size:0.8em;"
                       onclick="return confirm('Hapus foto ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; 
                else: ?>
                <tr>
                    <td colspan="5" style="padding:30px; color:#888;">Belum ada warga yang mengupload kegiatan.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

        <h3 style="margin-top:30px; color:#555;">Daftar Galeri Admin</h3>
        <table>
            <tr>
                <th>Foto</th>
                <th>Judul</th>
                <th>Aksi</th>
            </tr>
            <?php 
            // Ambil data galeri yang diupload admin (warga_id = 0 atau NULL)
            $galeri = $conn->query("SELECT * FROM galeri WHERE warga_id = 0 OR warga_id IS NULL ORDER BY tanggal DESC");
            while($g = $galeri->fetch_assoc()): ?>
            <tr>
                <td>
                    <img src="assets/uploads/<?= htmlspecialchars($g['gambar']) ?>" width="80" style="border-radius:5px;">
                </td>
                <td><?= htmlspecialchars($g['judul']) ?></td>
                <td>
                    <a href="proses/admin_act.php?act=hapus_galeri&id=<?= $g['id'] ?>&file=<?= $g['gambar'] ?>" 
                       class="btn btn-red" style="padding:5px 10px; font-size:0.8em;"
                       onclick="return confirm('Hapus foto ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    
    <!-- Form Kirim Baru -->
    <form action="proses/admin_act.php?act=tambah_info" method="POST" style="margin-bottom:30px;">
        <input type="text" name="judul" placeholder="Judul Pengumuman Baru" required>
        <textarea name="isi" placeholder="Tulis isi pengumuman..." required></textarea>
        <button type="submit" class="btn btn-green">Kirim Pemberitahuan Baru</button>
    </form>

    <!-- Daftar Semua Pemberitahuan -->
    <table>
        <!-- MODAL EDIT PEMBERITAHUAN -->
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999;">
    <div style="background:white; width:90%; max-width:600px; margin:50px auto; padding:30px; border-radius:15px; position:relative;">
        <h2 style="margin-bottom:20px; color:#333;">Edit Pemberitahuan</h2>
        <form action="proses/admin_act.php?act=edit_info" method="POST">
            <input type="hidden" name="id" id="edit_id">
            <input type="text" name="judul" id="edit_judul" placeholder="Judul" required style="width:100%; padding:15px; margin:10px 0; border:2px solid #ddd; border-radius:10px;">
            <textarea name="isi" id="edit_isi" placeholder="Isi pemberitahuan..." required style="width:100%; height:150px; padding:15px; margin:10px 0; border:2px solid #ddd; border-radius:10px; resize:vertical;"></textarea>
            <div style="text-align:right;">
                <button type="button" onclick="document.getElementById('editModal').style.display='none'" 
                        class="btn btn-red" style="margin-right:10px;">Batal</button>
                <button type="submit" class="btn btn-green">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editInfo(id, judul, isi) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_judul').value = judul;
    document.getElementById('edit_isi').value = isi;
    document.getElementById('editModal').style.display = 'block';
}
</script>
    <tr>
        <th>No</th>
        <th>Judul</th>
        <th>Dibuat</th>
        <th>Diperbarui</th>
        <th>Aksi</th>
    </tr>
    <?php 
    $no = 1;
    $info = $conn->query("SELECT * FROM informasi ORDER BY tanggal DESC");
    while($i = $info->fetch_assoc()): ?>
    <tr style="background:#<?= $i['updated_at'] && $i['updated_at'] > $i['tanggal'] ? 'fff3cd' : 'ffffff' ?>;">
        <td><?= $no++ ?></td>
        <td><strong><?= htmlspecialchars($i['judul']) ?></strong></td>
        <td><?= tgl_indo($i['tanggal']) ?></td>
        <td>
            <?php if($i['updated_at'] && $i['updated_at'] > $i['tanggal']): ?>
                <span style="color:#e67e22; font-weight:bold;">
                    Diperbarui<br><?= tgl_indo($i['updated_at']) ?>
                </span>
            <?php else: ?>
                <span style="color:#95a5a6;">—</span>
            <?php endif; ?>
        </td>
        <td>
            <!-- TOMBOL EDIT -->
            <button onclick="editInfo(<?= $i['id'] ?>, `<?= addslashes(htmlspecialchars($i['judul'])) ?>`, `<?= addslashes(htmlspecialchars($i['isi'])) ?>`)" 
                    class="btn btn-blue" style="padding:8px 15px; font-size:0.9em; margin-right:5px;">
                Edit
            </button>
            <!-- TOMBOL HAPUS -->
            <a href="proses/admin_act.php?act=hapus_info&id=<?= $i['id'] ?>" 
               class="btn btn-red" style="padding:8px 15px; font-size:0.9em;"
               onclick="return confirm('Yakin hapus:\n<?= addslashes($i['judul']) ?>?')">
               Hapus
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
    </table>

    <!-- Pengajuan Surat dari Warga -->
    <div class="card">
        <h2>Pengajuan Surat dari Warga</h2>
        <table>
            <tr>
                <th>Nama</th>
                <th>Jenis Surat</th>
                <th>Keterangan</th>
                <th>Status</th>
            </tr>
            <?php 
            $q = $conn->query("SELECT p.*, w.nama FROM pengajuan p JOIN warga w ON p.warga_id=w.id ORDER BY p.tanggal DESC");
            while($r = $q->fetch_assoc()): ?>
            <tr id="pengajuan-<?= $r['id'] ?>">
                <td><?= htmlspecialchars($r['nama']) ?></td>
                <td><?= htmlspecialchars($r['jenis']) ?></td>
                <td><?= htmlspecialchars($r['keterangan']) ?></td>
                <td>
                    <select onchange="ubahStatus(<?= $r['id'] ?>, this.value)" 
                            style="padding:10px 15px; border-radius:8px; font-weight:bold;">
                        <option value="Menunggu" <?= $r['status']=='Menunggu'?'selected':'' ?>>Menunggu</option>
                        <option value="Diproses" <?= $r['status']=='Diproses'?'selected':'' ?>>Sedang Diproses</option>
                        <option value="Selesai" <?= $r['status']=='Selesai'?'selected':'' ?>>Sudah Selesai</option>
                    </select>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Pengaduan Warga -->
    <div class="card">
        <h2>Pengaduan Warga</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Nama Warga</th>
                    <th style="width: 10%;">Kategori</th> <th style="width: 45%;">Isi Pengaduan</th>
                    <th style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $q = $conn->query("SELECT pd.*, w.nama FROM pengaduan pd JOIN warga w ON pd.warga_id=w.id ORDER BY pd.tanggal DESC");
                while($r = $q->fetch_assoc()): 

                    // Logika Warna Badge Kategori
                    $badgeColor = '#6c757d'; // Default (Abu-abu) untuk Lainnya
                    if($r['jenis'] == 'Kritik') $badgeColor = '#dc3545'; // Merah
                    if($r['jenis'] == 'Saran')  $badgeColor = '#17a2b8'; // Cyan/Biru Muda
                    if($r['jenis'] == 'Keluhan')$badgeColor = '#ffc107'; // Kuning
                ?>
                <tr id="pengaduan-<?= $r['id'] ?>">
                    <td style="font-weight:bold;"><?= htmlspecialchars($r['nama']) ?></td>

                    <td>
                        <span style="background:<?= $badgeColor ?>; color:<?= ($r['jenis']=='Keluhan')?'#000':'#fff' ?>; padding:5px 10px; border-radius:15px; font-size:0.85em; font-weight:bold; display:inline-block;">
                            <?= htmlspecialchars($r['jenis']) ?>
                        </span>
                    </td>

                    <td style="text-align:center;"><?= nl2br(htmlspecialchars($r['isi'])) ?></td>

                    <td>
                        <a href="proses/admin_act.php?act=hapus_pengaduan&id=<?= $r['id'] ?>" 
                           class="btn btn-red" 
                           style="padding:8px 15px; font-size:0.9em;"
                           onclick="return confirm('Hapus pengaduan ini?')">
                           <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

<script>
// FUNGSI UBAH STATUS PENGAJUAN (SURAT)
function ubahStatus(id, statusBaru) {
    // Gunakan FormData agar data terbaca sempurna oleh $_POST PHP
    const formData = new FormData();
    formData.append('act', 'update_status');
    formData.append('id', id);
    formData.append('status', statusBaru);

    fetch('proses/admin_act.php', {
        method: 'POST',
        body: formData // FormData otomatis mengatur header yang benar
    })
    .then(res => res.text())
    .then(result => {
        // Kita cek apakah responnya benar-benar "success"
        if (result.trim() === "success") {
            // Opsional: Beri feedback visual kecil saja, tidak perlu alert mengganggu
            console.log("Status ID " + id + " berhasil diubah ke " + statusBaru);
        } else {
            // Jika gagal, kembalikan dropdown ke posisi semula (karena DB gagal update)
            alert("Gagal menyimpan ke Database!\nRespon Server: " + result);
            location.reload(); // Refresh agar admin sadar data belum berubah
        }
    })
    .catch(err => {
        alert("Error Jaringan: " + err);
    });
}
</script>

</body>
</html>