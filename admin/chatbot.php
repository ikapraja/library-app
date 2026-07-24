<?php
require_once '../db.php';
include 'includes/header.php';

$error = '';
$success = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn->query("DELETE FROM chatbot_faq WHERE id = $id")) {
        $success = "Data chatbot berhasil dihapus.";
    } else {
        $error = "Gagal menghapus data.";
    }
}

// Handle Add/Edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $conn->real_escape_string($_POST['question']);
    $keywords = $conn->real_escape_string(strtolower($_POST['keywords']));
    $answer = $conn->real_escape_string($_POST['answer']);
    $show_on_homepage = isset($_POST['show_on_homepage']) ? 1 : 0;
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $id = (int)$_POST['id'];
        $sql = "UPDATE chatbot_faq SET question='$question', keywords='$keywords', answer='$answer', show_on_homepage=$show_on_homepage WHERE id=$id";
        if ($conn->query($sql)) {
            $success = "Data chatbot berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui data: " . $conn->error;
        }
    } else {
        // Insert
        $sql = "INSERT INTO chatbot_faq (question, keywords, answer, show_on_homepage) VALUES ('$question', '$keywords', '$answer', $show_on_homepage)";
        if ($conn->query($sql)) {
            $success = "Data chatbot berhasil ditambahkan.";
        } else {
            $error = "Gagal menambahkan data: " . $conn->error;
        }
    }
}

// Fetch all FAQs
$faqs = $conn->query("SELECT * FROM chatbot_faq ORDER BY id DESC");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2><i class="fa-solid fa-robot"></i> Manajemen Pustabots (Chatbot FAQ)</h2>
    <button onclick="document.getElementById('faqModal').style.display='flex'" class="btn-primary"><i class="fa-solid fa-plus"></i> Tambah Pertanyaan</button>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="card" style="padding: 0; overflow: hidden;">
    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Pertanyaan (Tombol Cepat)</th>
                <th width="20%">Kata Kunci (Keywords)</th>
                <th width="35%">Jawaban Robot</th>
                <th width="10%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($faqs && $faqs->num_rows > 0): ?>
                <?php $no = 1; while($row = $faqs->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['question']); ?></strong>
                            <?php if(isset($row['show_on_homepage']) && $row['show_on_homepage'] == 1): ?>
                                <span style="display: inline-block; margin-left: 10px; font-size: 0.7rem; padding: 2px 6px; background: var(--accent); color: var(--primary-bg); border-radius: 4px; font-weight: bold;"><i class="fa-solid fa-home"></i> Beranda</span>
                            <?php endif; ?>
                        </td>
                        <td><small style="color: #666; background: #eee; padding: 2px 5px; border-radius: 4px;"><?php echo htmlspecialchars($row['keywords']); ?></small></td>
                        <td><?php echo nl2br(htmlspecialchars(substr($row['answer'], 0, 100))) . (strlen($row['answer']) > 100 ? '...' : ''); ?></td>
                        <td>
                            <button onclick="editFaq(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="btn-action btn-edit" title="Edit"><i class="fa-solid fa-edit"></i></button>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus pertanyaan ini?')" title="Hapus"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Belum ada data pertanyaan chatbot.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Dialog -->
<div id="faqModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="card" style="width: 100%; max-width: 600px; position: relative;">
        <span onclick="document.getElementById('faqModal').style.display='none'" style="position: absolute; right: 20px; top: 20px; cursor: pointer; font-size: 20px; color: #888;"><i class="fa-solid fa-times"></i></span>
        
        <h3 id="modalTitle" style="margin-top: 0; margin-bottom: 20px;"><i class="fa-solid fa-plus"></i> Tambah Pertanyaan Baru</h3>
        
        <form method="POST" action="chatbot">
            <input type="hidden" name="id" id="faqId" value="">
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: 600; display: block; margin-bottom: 8px;">Pertanyaan (Sebagai Teks Tombol)</label>
                <input type="text" name="question" id="faqQuestion" class="form-control" required placeholder="Contoh: Kapan jam buka perpustakaan?" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                <small style="color: #666; display: block; margin-top: 5px;">Pertanyaan ini akan muncul sebagai tombol pilihan ganda di awal chat.</small>
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: 600; display: block; margin-bottom: 8px;">Kata Kunci (Pemisah Koma)</label>
                <input type="text" name="keywords" id="faqKeywords" class="form-control" required placeholder="Contoh: jam,buka,tutup,waktu" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                <small style="color: #666; display: block; margin-top: 5px;">Jika pengunjung mengetik salah satu kata ini, robot akan membalas dengan jawaban di bawah.</small>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: 600; display: block; margin-bottom: 8px;">Jawaban Robot</label>
                <textarea name="answer" id="faqAnswer" class="form-control" rows="5" required placeholder="Tuliskan jawaban dari perpustakaan di sini..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; resize: vertical;"></textarea>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px; padding: 10px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px;">
                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" name="show_on_homepage" id="faqShowOnHomepage" value="1" style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                    Tampilkan pertanyaan ini di Beranda Website
                </label>
                <small style="color: #666; display: block; margin-top: 5px; margin-left: 28px;">Jika dicentang, pertanyaan ini akan muncul pada bagian Akordion FAQ di Halaman Utama.</small>
            </div>
            
            <div style="text-align: right;">
                <button type="button" onclick="document.getElementById('faqModal').style.display='none'" class="btn-outline" style="margin-right: 10px;">BATAL</button>
                <button type="submit" class="btn-primary">SIMPAN</button>
            </div>
        </form>
    </div>
</div>

<script>
function editFaq(data) {
    document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-edit"></i> Edit Pertanyaan';
    document.getElementById('faqId').value = data.id;
    document.getElementById('faqQuestion').value = data.question;
    document.getElementById('faqKeywords').value = data.keywords;
    document.getElementById('faqAnswer').value = data.answer;
    document.getElementById('faqShowOnHomepage').checked = (data.show_on_homepage == 1);
    document.getElementById('faqModal').style.display = 'flex';
}

// Reset form when clicking add new
document.querySelector('.btn-primary[onclick]').addEventListener('click', function() {
    document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-plus"></i> Tambah Pertanyaan Baru';
    document.getElementById('faqId').value = '';
    document.getElementById('faqQuestion').value = '';
    document.getElementById('faqKeywords').value = '';
    document.getElementById('faqAnswer').value = '';
    document.getElementById('faqShowOnHomepage').checked = false;
});
</script>

<?php include 'includes/footer.php'; ?>
