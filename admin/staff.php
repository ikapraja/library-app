<?php
require_once '../db.php';
include 'includes/header.php';

$sql = "SELECT * FROM staff_members ORDER BY order_num ASC, id ASC";
$result = $conn->query($sql);
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i class="fa-solid fa-user-tie"></i> Manajemen Tim Pustakawan</h2>
        <a href="create_staff" class="btn"><i class="fa-solid fa-plus"></i> Tambah Anggota</a>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr>
                    <th style="width: 50px;">Urutan</th>
                    <th>Foto</th>
                    <th>Nama Lengkap</th>
                    <th>Jabatan</th>
                    <th>Email</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo $row['order_num']; ?></td>
                    <td>
                        <?php if ($row['photo_url']): ?>
                            <img src="../<?php echo htmlspecialchars($row['photo_url']); ?>" alt="Foto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                        <?php else: ?>
                            <div style="width: 50px; height: 50px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-user" style="color: #999;"></i></div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <a href="edit_staff?id=<?php echo $row['id']; ?>" class="btn btn-warning" style="padding: 5px 10px; font-size: 12px;"><i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="delete_staff?id=<?php echo $row['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Yakin ingin menghapus anggota ini?');"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php 
                    endwhile; 
                } else {
                    echo '<tr><td colspan="6" style="text-align:center;">Belum ada data anggota.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
