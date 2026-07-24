<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$new_content = '<i class="fa-solid fa-book-open-reader" style="font-size: 5rem; color: var(--accent-gold); margin-bottom: 20px;"></i>
<h2>Layanan Sirkulasi Perpustakaan</h2>
<p>Selamat datang di Layanan Sirkulasi! Melalui layanan ini, kami memfasilitasi seluruh pemustaka untuk melakukan peminjaman, perpanjangan, dan pengembalian koleksi buku. Kami berkomitmen untuk memberikan pelayanan yang cepat, ramah, dan solutif guna mendukung kebutuhan literasi serta kelancaran kegiatan akademik Anda di lingkungan PKTJ.</p>

<div class="content-card">
    <h3><i class="fa-solid fa-circle-exclamation" style="color: var(--accent-gold);"></i> Ketentuan Peminjaman</h3>
    <p>Untuk memastikan pemerataan akses buku bagi seluruh pemustaka, kami menerapkan kebijakan peminjaman sebagai berikut:</p>
    <ul>
        <li><strong>Batas Maksimal Peminjaman:</strong> Setiap pemustaka diperkenankan meminjam hingga maksimal 2 (dua) judul buku secara bersamaan.</li>
        <li><strong>Durasi Peminjaman (Taruna):</strong> Masa pinjam berlaku selama 7 hari kalender.</li>
        <li><strong>Durasi Peminjaman (Dosen / Tenaga Pendidik):</strong> Masa pinjam berlaku lebih lama, yaitu selama 14 hari kalender.</li>
    </ul>
</div>

<div class="content-card">
    <h3><i class="fa-solid fa-list-check" style="color: var(--accent-gold);"></i> Persyaratan Administrasi</h3>
    <p>Sebelum memanfaatkan layanan ini, pastikan Anda telah memenuhi beberapa persyaratan ringan berikut ini:</p>
    
    <h4>Syarat Peminjaman Koleksi</h4>
    <ol>
        <li>Tercatat secara resmi sebagai bagian dari sivitas akademika PKTJ.</li>
        <li>Wajib membawa dan menunjukkan Kartu Anggota Perpustakaan yang masih aktif (penggunaan kartu tidak dapat diwakilkan kepada orang lain).</li>
        <li>Tidak sedang memiliki tanggungan pinjaman buku yang melebihi batas kuota (maksimal 2 buku).</li>
        <li>Telah menyelesaikan seluruh tanggungan denda keterlambatan (jika ada) pada transaksi sebelumnya.</li>
    </ol>

    <h4 style="margin-top: 25px;">Syarat Pengembalian Koleksi</h4>
    <ol>
        <li>Membawa fisik buku yang dipinjam dalam keadaan baik seperti semula.</li>
        <li>Menyerahkan Kartu Anggota Perpustakaan yang digunakan pada saat proses transaksi peminjaman.</li>
    </ol>
</div>

<div class="content-card">
    <h3><i class="fa-solid fa-shoe-prints" style="color: var(--accent-gold);"></i> Prosedur Layanan</h3>
    <p>Berikut adalah langkah-langkah praktis yang perlu Anda ikuti saat berkunjung ke area sirkulasi:</p>
    
    <h4>Alur Peminjaman Buku</h4>
    <ol>
        <li>Silakan telusuri area koleksi kami dan ambil langsung buku yang ingin Anda pinjam dari rak. (Jangan ragu untuk meminta bantuan pustakawan jika Anda kesulitan mencari buku!).</li>
        <li>Bawa buku pilihan Anda beserta Kartu Anggota menuju meja sirkulasi.</li>
        <li>Petugas kami akan memindai (<i>scan</i>) dan mencatat data peminjaman Anda secara terintegrasi ke dalam sistem.</li>
        <li>Setelah proses selesai, petugas akan menyerahkan kembali Kartu Anggota beserta buku Anda. Selamat membaca!</li>
    </ol>

    <h4 style="margin-top: 25px;">Alur Pengembalian Buku</h4>
    <ol>
        <li>Bawa buku yang ingin dikembalikan beserta Kartu Anggota Anda ke meja sirkulasi.</li>
        <li>Petugas kami akan melakukan pengecekan fisik buku dan menyesuaikannya dengan catatan di dalam sistem.</li>
        <li>Apabila buku dikembalikan tepat waktu, sistem akan langsung membebaskan status pinjaman Anda saat itu juga.</li>
        <li>Apabila Anda melewati batas waktu pengembalian yang disepakati, petugas akan menginformasikan nominal denda keterlambatan yang harus diselesaikan.</li>
        <li>Setelah seluruh proses administrasi selesai, petugas akan mengembalikan Kartu Anggota Anda dan menyusun kembali buku tersebut ke rak agar siap dipinjam oleh pemustaka berikutnya.</li>
    </ol>
</div>';

$stmt = $conn->prepare("UPDATE pages SET content = ? WHERE slug = 'layanan-sirkulasi'");
$stmt->bind_param("s", $new_content);
if ($stmt->execute()) {
    echo "Content updated successfully!";
} else {
    echo "Error updating content: " . $conn->error;
}
$stmt->close();
$conn->close();
?>
