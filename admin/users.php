<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login");
    exit;
}
if ($_SESSION['admin_role'] !== 'superadmin') {
    header("Location: index");
    exit;
}

require_once '../db.php';
require_once 'includes/header.php';

// Fetch all users
$sql = "SELECT id, username, name, email, role, created_at FROM users ORDER BY created_at ASC";
$result = $conn->query($sql);
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 style="margin: 0;">Manajemen Pengguna</h2>
        <div>
            <input type="text" id="searchUser" class="form-control" placeholder="Cari nama atau email..." style="display: inline-block; width: 250px; margin-right: 10px; padding: 10px 15px;">
            <a href="create_user" class="btn">+ Tambah Pengguna Baru</a>
        </div>
    </div>
    
    <table id="userTable">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Email</th>
                <th>Peran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><span style="background: #e1f5fe; color: #0288d1; padding: 3px 8px; border-radius: 20px; font-size: 12px; font-weight: bold;"><?php echo htmlspecialchars($row['role']); ?></span></td>
                    <td>
                        <a href="edit_user?id=<?php echo $row['id']; ?>" class="btn" style="background: #f6a821; color: #fff; padding: 5px 10px; font-size: 13px; text-decoration: none; border-radius: 4px; display: inline-block;"><i class="fa-solid fa-pen"></i> Edit</a>
                        <?php if($row['id'] != 1 && $row['id'] != $_SESSION['admin_id']): ?>
                            <a href="delete_user?id=<?php echo $row['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 13px; display: inline-block; margin-left: 5px;" onclick="return confirm('Yakin ingin menghapus pengguna ini?');"><i class="fa-solid fa-trash"></i> Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding: 30px;">Belum ada pengguna tambahan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('searchUser').addEventListener('keyup', function() {
    var searchValue = this.value.toLowerCase();
    var tableRows = document.querySelectorAll('#userTable tbody tr');
    
    tableRows.forEach(function(row) {
        if (row.children.length > 1) { // abaikan baris "belum ada pengguna"
            var textContent = row.textContent.toLowerCase();
            if (textContent.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
