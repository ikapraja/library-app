<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$new_content = '<i class="fa-solid fa-magnifying-glass-chart" style="font-size: 5rem; color: var(--accent-gold); margin-bottom: 20px;"></i>

<div class="content-card">
    <h3><i class="fa-solid fa-book-journal-whills" style="color: var(--accent-gold);"></i> Layanan Referensi</h3>
    <p>Layanan Referensi adalah pusat rujukan informasi bagi Anda yang membutuhkan data spesifik, cepat, dan akurat. Kami menyediakan koleksi referensi berkualitas tinggi seperti kamus, ensiklopedia, direktori, hingga buku panduan khusus. Melalui layanan ini, pustakawan kami siap mendampingi Anda dalam menelusuri literatur rujukan dengan cakupan subjek yang lebih luas dan mendalam. Mengingat sifatnya sebagai rujukan utama, seluruh koleksi referensi ini eksklusif hanya dapat dibaca di tempat dan tidak dipinjamkan ke luar perpustakaan.</p>
</div>

<div class="content-card">
    <h3><i class="fa-solid fa-newspaper" style="color: var(--accent-gold);"></i> Layanan Terbitan Berkala (Serial)</h3>
    <p>Layanan Terbitan Berkala menghadirkan sumber informasi terbaru dan paling mutakhir yang diterbitkan secara periodik. Anda dapat mengakses berbagai jurnal ilmiah tercetak, majalah populer, surat kabar harian, hingga koleksi literatur standar khusus dari Badan Standardisasi Nasional (BSN). Layanan ini sangat ideal untuk mengikuti perkembangan ilmu pengetahuan dan berita terkini. Sama seperti koleksi referensi, seluruh terbitan berkala hanya diperuntukkan untuk dibaca di ruang baca perpustakaan.</p>
</div>

<div class="content-card">
    <h3><i class="fa-solid fa-circle-exclamation" style="color: var(--accent-gold);"></i> Syarat dan Ketentuan</h3>
    <ul>
        <li>Tercatat secara resmi sebagai bagian dari sivitas akademika PKTJ.</li>
        <li>Anda dipersilakan untuk menelusuri dan mengambil sendiri bahan pustaka yang dibutuhkan langsung dari rak koleksi (sistem layanan terbuka).</li>
        <li>Seluruh koleksi Referensi dan Serial memiliki status <strong>Baca di Tempat</strong>. Pemustaka tidak diperkenankan membawa koleksi ini ke luar area perpustakaan.</li>
    </ul>
</div>

<div class="content-card">
    <h3><i class="fa-solid fa-shoe-prints" style="color: var(--accent-gold);"></i> Prosedur Layanan</h3>
    <ol>
        <li>Sesampainya di perpustakaan, pastikan Anda telah mengisi daftar hadir atau buku kunjungan tamu yang tersedia di area resepsionis.</li>
        <li>Silakan langsung menuju area rak koleksi Referensi atau Terbitan Berkala untuk mencari informasi yang Anda butuhkan. (Pustakawan kami selalu bersedia membantu penelusuran Anda).</li>
        <li>Gunakan area ruang baca (<i>reading area</i>) nyaman yang telah kami sediakan untuk membaca dan mencatat informasi dari bahan pustaka tersebut.</li>
        <li>Setelah selesai membaca, Anda <strong>tidak perlu</strong> mengembalikan buku atau jurnal ke rak semula. Cukup tinggalkan koleksi tersebut di atas meja baca, dan petugas kami yang akan merapikannya kembali untuk Anda.</li>
    </ol>
</div>';

$stmt = $conn->prepare("UPDATE pages SET content = ? WHERE slug = 'layanan-referensi'");
$stmt->bind_param("s", $new_content);
if ($stmt->execute()) {
    echo "Content updated successfully!";
} else {
    echo "Error updating content: " . $conn->error;
}
$stmt->close();
$conn->close();
?>
