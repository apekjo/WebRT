// functions.php
<?php
date_default_timezone_set('Asia/Jakarta'); // WIB

function tgl_indo($datetime) {
    $bulan = ['Januari','Februari','Maret','April','Mei','Juni',
              'Juli','Agustus','September','Oktober','November','Desember'];
    $t = strtotime($datetime);
    return date('j', $t) . ' ' . $bulan[date('n', $t)-1] . ' ' . date('Y', $t) . ' pukul ' . date('H:i', $t) . ' WIB';
}
?>