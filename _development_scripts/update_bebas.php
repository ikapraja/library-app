<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$new_content = '<i class="fa-solid fa-file-circle-check" style="font-size: 5rem; color: var(--accent-gold); margin-bottom: 20px;"></i>
<p>Surat Keterangan Bebas Pustaka adalah dokumen resmi yang menerangkan bahwa seorang taruna telah menyelesaikan seluruh kewajiban administrasi di Unit Perpustakaan, termasuk tidak memiliki tanggungan pinjaman buku maupun denda. Surat keterangan ini merupakan salah satu dokumen prasyarat wajib untuk mengikuti prosesi wisuda kelulusan.</p>

<div class="content-card">
    <h3><i class="fa-solid fa-list-check" style="color: var(--accent-gold);"></i> Persyaratan Mendapatkan Surat</h3>
    <p>Untuk mengajukan Surat Keterangan Bebas Pustaka, pastikan Anda telah memenuhi langkah-langkah berikut ini:</p>
    <ol>
        <li>Telah mengembalikan seluruh pinjaman koleksi pustaka dan melunasi denda keterlambatan (jika ada).</li>
        <li>Telah melakukan prosedur Unggah Mandiri (Upload Mandiri) untuk dokumen digital laporan magang, skripsi, dan artikel ilmiah Anda.</li>
        <li>Mengunduh dan mengisi formulir-formulir berikut ini:
            <div class="download-grid" style="margin-top: 15px; margin-bottom: 20px;">
                <a href="https://docs.google.com/document/d/1EHa1PY7etBXU7c1M4_GU87KNqcRTBcaae7A7cfUzfIM/edit?usp=sharing" target="_blank" class="download-btn"><i class="fa-solid fa-file-pdf"></i> Formulir Skripsi / KKW</a>
                <a href="https://docs.google.com/document/d/1sodrzsxyywe3Q9V1k5EHJ3TlAVME9D_THJUVOZ4vXi8/edit?usp=sharing" target="_blank" class="download-btn"><i class="fa-solid fa-file-pdf"></i> Formulir Laporan Magang</a>
                <a href="https://docs.google.com/document/d/1Mjsq2iSmnA6mcPnmlyLRFMA-pM0t_NnzDCUUDNJsVsM/edit?usp=sharing" target="_blank" class="download-btn"><i class="fa-solid fa-file-signature"></i> Formulir Persetujuan Publikasi</a>
                <a href="https://docs.google.com/document/d/1QYzjhXJUqePiuRxVAos1GjzZLlrJoOTE_o_0KjveCqI/edit?usp=sharing" target="_blank" class="download-btn"><i class="fa-solid fa-book"></i> Formulir Sumbangan Buku</a>
            </div>
        </li>
        <li>Datang secara langsung ke Unit Perpustakaan Kampus Perintis untuk menyerahkan kelengkapan fisik:
            <ul>
                <li>Seluruh berkas formulir fisik yang sudah diisi dan ditandatangani.</li>
                <li>Keping CD berisi laporan magang (1 keping).</li>
                <li>Keping CD berisi skripsi dan produk hasil penelitian (1 keping).</li>
                <li>Fisik buku asli yang akan disumbangkan kepada Unit Perpustakaan.</li>
            </ul>
        </li>
    </ol>
</div>

<div class="content-card">
    <h3><i class="fa-solid fa-circle-info" style="color: var(--accent-gold);"></i> Ketentuan Lebih Lanjut</h3>

    <h4 style="margin-top: 20px;">A. Ketentuan Format File Unggah Mandiri</h4>
    <ul>
        <li>File harus berformat PDF dengan ukuran maksimal 3 MB.</li>
        <li>Format penamaan file laporan magang:<br> <code style="background: #f1f5f9; color: var(--primary-blue); padding: 4px 8px; border-radius: 4px; font-size: 0.95em; font-weight: 600; font-family: monospace;">NOTAR-JENIS LAPORAN-BAGIAN LAPORAN (Contoh: 1801000_1801001_1801002_1801003-MAGANG 1-BAB_1)</code></li>
        <li>Format penamaan file skripsi:<br> <code style="background: #f1f5f9; color: var(--primary-blue); padding: 4px 8px; border-radius: 4px; font-size: 0.95em; font-weight: 600; font-family: monospace;">NOTAR-JENIS LAPORAN-BAGIAN LAPORAN (Contoh: 1801000-SKRIPSI-BAB_1)</code></li>
        <li>Pastikan file yang diunggah sudah menyertakan halaman persetujuan dan pengesahan yang lengkap dengan tanda tangan asli.</li>
        <li>Pemecahan file harus diunggah terpisah menjadi: ABSTRAK – BAB 1 – BAB 2 – BAB 3 – BAB 4 – BAB 5 – LAMPIRAN.</li>
    </ul>

    <h4 style="margin-top: 25px;">B. Prosedur Unggah Mandiri</h4>
    <p>Silakan akses folder penyimpanan terpusat kami pada <a href="https://drive.google.com/drive/folders/1bflR2M3L5HwUDZVojrtptMtFtBdT9Yeb?usp=sharing" target="_blank" style="color: #0056b3; font-weight: 600;">Tautan Unggah Mandiri Berikut Ini</a>.</p>

    <h4 style="margin-top: 25px;">C. Ketentuan Sumbangan Buku</h4>
    <ul>
        <li><strong>Pengecekan Duplikasi Buku (Wajib):</strong> Sebelum membeli, Anda wajib mengecek judul buku melalui sistem <a href="https://opac.pktj.ac.id/" target="_blank" style="color: #0056b3; font-weight: 600;">OPAC perpustakaan</a>. Apabila judul tersebut sudah tersedia lebih dari 5 (lima) eksemplar di perpustakaan, Anda dimohon untuk menyumbangkan judul yang berbeda.</li>
        <li><strong>Kategori Buku (Pilih Salah Satu):</strong>
            <ul style="list-style-type: circle;">
                <li>Buku Program Studi: Wajib relevan dengan kurikulum prodi (Harap berkoordinasi dengan petugas perpustakaan terlebih dahulu).</li>
                <li>Buku Penunjang: Fiksi (Novel, Sastra), Non-Fiksi & Keterampilan (Pemrograman, Desain), atau Motivasi (Pengembangan Diri).</li>
            </ul>
        </li>
        <li><strong>Kondisi dan Kebaruan:</strong>
            <ul style="list-style-type: circle;">
                <li>Buku harus diserahkan dalam kondisi fisik yang sepenuhnya baru.</li>
                <li>Tahun terbit buku maksimal 5 tahun terakhir dari tahun berjalan.</li>
                <li>Wajib melampirkan Nota Pembelian Asli dari toko.</li>
            </ul>
        </li>
        <li><strong>Legalitas dan Konten:</strong>
            <ul style="list-style-type: circle;">
                <li>Buku wajib Original/Asli (Bukan buku bajakan, hasil fotokopi, atau cetakan tidak resmi).</li>
                <li>Isi buku tidak diperbolehkan mengandung unsur SARA, pornografi, radikalisme, atau pelanggaran norma hukum.</li>
            </ul>
        </li>
    </ul>

    <h4 style="margin-top: 25px;">D. Ketentuan Fisik CD</h4>
    <ul>
        <li>Format file di dalam CD harus sama persis dengan aturan format file pada unggah mandiri (Poin A).</li>
        <li>CD khusus laporan magang hanya berisi softcopy laporan magang.</li>
        <li>CD khusus skripsi berisi softcopy skripsi dan dokumentasi produk hasil penelitian (jika ada).</li>
        <li>Kepingan CD harus dilengkapi dengan cover standar (Desain cover dapat diunduh di bawah).</li>
    </ul>
    
    <div style="margin-top: 25px; display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="https://docs.google.com/document/d/1NLU-n4iGZ7TMCSrQzKTE5w6FPDEHkAbU/edit?usp=sharing&ouid=104543204542922209954&rtpof=true&sd=true" target="_blank" class="download-btn"><i class="fa-solid fa-download"></i> Contoh Cover Skripsi</a>
        <a href="https://docs.google.com/document/d/1L1v_Oj_IExeZWXQwGsmD1Q-Bzu4hQOaL/edit?usp=sharing&ouid=104543204542922209954&rtpof=true&sd=true" target="_blank" class="download-btn"><i class="fa-solid fa-download"></i> Contoh Cover Laporan Magang</a>
    </div>
</div>';

$stmt = $conn->prepare("UPDATE pages SET content = ? WHERE slug = 'bebas-pustaka'");
$stmt->bind_param("s", $new_content);
if ($stmt->execute()) {
    echo "Content updated successfully!";
} else {
    echo "Error updating content: " . $conn->error;
}
$stmt->close();
$conn->close();
?>
