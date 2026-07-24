<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$new_content = '<i class="fa-solid fa-clipboard-list" style="font-size: 5rem; color: var(--accent-gold); margin-bottom: 20px;"></i>
<p>Tata tertib ini disusun sebagai pedoman bersama untuk menciptakan suasana akademik yang nyaman, tertib, dan kondusif bagi seluruh sivitas akademika PKTJ yang menggunakan fasilitas perpustakaan.</p>

<div class="content-card">
    <h3><i class="fa-solid fa-scale-balanced" style="color: var(--accent-gold);"></i> Ketentuan Umum</h3>
    <p>Demi kenyamanan bersama, setiap pengunjung perpustakaan diharapkan untuk mematuhi etika berikut:</p>
    <ol>
        <li>Senantiasa menjaga ketenangan, kebersihan, kerapian, serta kesopanan (baik dari segi berpakaian maupun bertutur kata) selama berada di lingkungan perpustakaan.</li>
        <li>Wajib mengisi daftar hadir kunjungan secara mandiri pada komputer resepsionis yang telah disediakan di pintu masuk.</li>
        <li>Menghargai koleksi dan kebersihan ruangan dengan <strong style="color: #d9534f;">tidak membawa atau mengonsumsi makanan maupun minuman</strong> di dalam area baca.</li>
        <li>Merawat seluruh fasilitas dan koleksi bahan pustaka dengan <strong style="color: #d9534f;">tidak</strong> merobek, melipat, mencorat-coret, atau merusaknya dalam bentuk apa pun.</li>
    </ol>
</div>

<div class="content-card">
    <h3><i class="fa-solid fa-book-open-reader" style="color: var(--accent-gold);"></i> Tata Tertib Sirkulasi (Peminjaman & Pengembalian)</h3>
    <p>Terkait dengan hak akses peminjaman koleksi, berikut adalah tata tertib yang wajib diperhatikan:</p>
    <ol>
        <li>Setiap transaksi layanan sirkulasi wajib disertai dengan menunjukkan Kartu Anggota Perpustakaan milik sendiri.</li>
        <li>Batas maksimal peminjaman adalah <strong>2 (dua) eksemplar</strong> buku per transaksi.</li>
        <li>Durasi masa pinjam ditetapkan maksimal <strong>7 hari</strong> bagi Taruna, dan <strong>14 hari</strong> bagi Dosen serta Tenaga Kependidikan.</li>
        <li>Perpanjangan masa pinjam hanya dapat dilakukan sebanyak 1 (satu) kali sebelum batas waktu jatuh tempo berakhir.</li>
        <li><strong>Seluruh koleksi wajib dikembalikan</strong> apabila masa jatuh tempo bertepatan dengan masa Izin Bermalam (IB) panjang atau masa libur semester.</li>
        <li>Keterlambatan pengembalian koleksi akan dikenakan sanksi denda administratif sebesar <strong>Rp1.000,00 per hari</strong>, yang nantinya akan dikonversikan dalam bentuk sumbangan bahan pustaka.</li>
        <li>Apabila terjadi kerusakan atau kehilangan buku saat masa peminjaman, pemustaka <strong>wajib menggantinya dengan judul buku yang persis sama</strong> dan dalam kondisi fisik baru.</li>
        <li>Peminjaman baru tidak dapat dilayani apabila pemustaka bersangkutan masih memiliki tanggungan pinjaman sebelumnya yang belum dikembalikan.</li>
        <li>Khusus bagi Taruna tingkat akhir, diwajibkan untuk memenuhi syarat <strong style="color: #d9534f;">"Bebas Pustaka"</strong> sebagai dokumen kelengkapan wisuda.</li>
    </ol>
</div>

<div class="content-card">
    <h3><i class="fa-solid fa-gavel" style="color: var(--accent-gold);"></i> Sanksi Pelanggaran</h3>
    <p>Untuk menjaga ketertiban dan kelancaran layanan bagi semua pihak, segala bentuk pelanggaran terhadap tata tertib perpustakaan akan ditindaklanjuti dengan tahapan sanksi edukatif berikut:</p>
    <ol>
        <li>Pemberian teguran secara lisan oleh petugas perpustakaan yang berjaga.</li>
        <li>Penangguhan (pencabutan sementara) hak akses peminjaman dan larangan penggunaan seluruh fasilitas perpustakaan.</li>
        <li>Penerapan sanksi administratif dan akademik tingkat lanjut, sesuai dengan pedoman kedisiplinan resmi yang berlaku di lingkungan PKTJ.</li>
    </ol>
</div>';

$stmt = $conn->prepare("UPDATE pages SET content = ? WHERE slug = 'tata-tertib'");
$stmt->bind_param("s", $new_content);
if ($stmt->execute()) {
    echo "Content updated successfully!";
} else {
    echo "Error updating content: " . $conn->error;
}
$stmt->close();
$conn->close();
?>
