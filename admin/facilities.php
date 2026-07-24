<?php
require_once '../db.php';
include 'includes/header.php';

$sql = "SELECT * FROM facilities ORDER BY order_num ASC, id ASC";
$result = $conn->query($sql);
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i class="fa-solid fa-building"></i> Manajemen Fasilitas</h2>
        <a href="create_facility" class="btn"><i class="fa-solid fa-plus"></i> Tambah Fasilitas</a>
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
                    <th style="width: 150px;">Foto</th>
                    <th>Nama Fasilitas</th>
                    <th>Lokasi</th>
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
                        <?php if ($row['image_url']): ?>
                            <img src="<?php echo htmlspecialchars(strpos($row['image_url'], 'http') === 0 ? $row['image_url'] : '../' . $row['image_url']); ?>" alt="Foto" style="width: 120px; height: 80px; object-fit: cover; border-radius: 8px;">
                        <?php else: ?>
                            <div style="width: 120px; height: 80px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-image" style="color: #999;"></i></div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td>
                        <a href="edit_facility?id=<?php echo $row['id']; ?>" class="btn btn-warning" style="padding: 5px 10px; font-size: 12px;"><i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="delete_facility?id=<?php echo $row['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Yakin ingin menghapus fasilitas ini?');"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php 
                    endwhile; 
                } else {
                    echo '<tr><td colspan="5" style="text-align:center;">Belum ada data fasilitas.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
