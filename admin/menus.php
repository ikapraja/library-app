<?php
require_once '../db.php';
include 'includes/header.php';

$error = '';
$success = '';

// Handle CRUD Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'add' || $action == 'edit') {
        $title = $conn->real_escape_string($_POST['title']);
        $url_type = $_POST['url_type'] ?? 'internal';
        
        if ($url_type == 'internal') {
            $slug = $conn->real_escape_string($_POST['internal_slug']);
            // Check if it's a special page that still uses its own file
            $special_files = ['visi-misi', 'tata-tertib', 'layanan-sirkulasi', 'layanan-referensi', 'bebas-pustaka', 'struktur-organisasi', 'fasilitas'];
            if (in_array($slug, $special_files)) {
                $url = $slug . '.php';
            } else {
                $url = 'page.php?slug=' . $slug;
            }
        } else {
            $url = $conn->real_escape_string($_POST['url_custom']);
        }
        
        if ($action == 'add') {
            $sql = "INSERT INTO menus (title, url, parent_id, order_num) VALUES ('$title', '$url', NULL, 999)";
            if ($conn->query($sql)) $success = "Menu baru ditambahkan ke bagian paling bawah. Silakan geser (drag) ke posisi yang diinginkan.";
            else $error = "Gagal menambah menu: " . $conn->error;
        } else {
            $id = intval($_POST['id']);
            $sql = "UPDATE menus SET title='$title', url='$url' WHERE id=$id";
            if ($conn->query($sql)) $success = "Menu berhasil diperbarui.";
            else $error = "Gagal memperbarui menu: " . $conn->error;
        }
    } elseif ($action == 'delete') {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM menus WHERE id=$id";
        if ($conn->query($sql)) $success = "Menu berhasil dihapus.";
        else $error = "Gagal menghapus menu: " . $conn->error;
    }
}

// Fetch all menus to build a tree
$menus_raw = [];
$res = $conn->query("SELECT * FROM menus ORDER BY parent_id, order_num ASC");
while ($row = $res->fetch_assoc()) {
    $menus_raw[] = $row;
}

// Build hierarchical array
function buildTree(array $elements, $parentId = null) {
    $branch = array();
    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
            $children = buildTree($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            } else {
                $element['children'] = [];
            }
            $branch[] = $element;
        }
    }
    return $branch;
}
$menu_tree = buildTree($menus_raw);

// Fetch pages for dropdown
$pages_res = $conn->query("SELECT title, slug FROM pages ORDER BY title ASC");
$pages_list = [];
while($p = $pages_res->fetch_assoc()) $pages_list[] = $p;

// For Edit Mode
$edit_menu = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    foreach ($menus_raw as $m) {
        if ($m['id'] == $edit_id) {
            $edit_menu = $m;
            break;
        }
    }
}

// Function to render nestable list recursively
function renderNestableList($tree) {
    $html = '<ol class="dd-list">';
    foreach ($tree as $node) {
        $html .= '<li class="dd-item dd3-item" data-id="'.$node['id'].'">';
        $html .= '<div class="dd-handle dd3-handle"><i class="fa-solid fa-bars"></i></div>';
        $html .= '<div class="dd3-content" style="display:flex; justify-content:space-between; align-items:center;">';
        $html .= '<span><strong>' . htmlspecialchars($node['title']) . '</strong> <span style="color:#888; font-size:0.85em; margin-left:10px;">' . htmlspecialchars($node['url']) . '</span></span>';
        $html .= '<div>';
        $html .= '<a href="menus?edit='.$node['id'].'" class="btn" style="background: #f39c12; padding: 3px 8px; font-size: 0.8rem; margin-right: 5px; color: white;"><i class="fa-solid fa-pen"></i></a>';
        $html .= '<form method="POST" action="menus" style="display:inline;" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus menu ini?\');">';
        $html .= '<input type="hidden" name="action" value="delete">';
        $html .= '<input type="hidden" name="id" value="'.$node['id'].'">';
        $html .= '<button type="submit" class="btn" style="background: #e74c3c; padding: 3px 8px; font-size: 0.8rem; border:none; color:white; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>';
        $html .= '</form>';
        $html .= '</div></div>';
        
        if (!empty($node['children'])) {
            $html .= renderNestableList($node['children']);
        }
        $html .= '</li>';
    }
    $html .= '</ol>';
    return $html;
}
?>

<!-- Nestable CSS -->
<style>
.dd { position: relative; display: block; margin: 0; padding: 0; max-width: 600px; list-style: none; font-size: 13px; line-height: 20px; }
.dd-list { display: block; position: relative; margin: 0; padding: 0; list-style: none; }
.dd-list .dd-list { padding-left: 30px; }
.dd-item, .dd-empty, .dd-placeholder { display: block; position: relative; margin: 0; padding: 0; min-height: 20px; font-size: 13px; line-height: 20px; }
.dd-handle { display: block; height: 30px; margin: 5px 0; padding: 5px 10px; color: #333; text-decoration: none; font-weight: bold; border: 1px solid #ccc; background: #fafafa; border-radius: 3px; box-sizing: border-box; cursor: move; }
.dd-item > button { display: block; position: relative; cursor: pointer; float: left; width: 25px; height: 20px; margin: 5px 0; padding: 0; text-indent: 100%; white-space: nowrap; overflow: hidden; border: 0; background: transparent; font-size: 12px; line-height: 1; text-align: center; font-weight: bold; }
.dd-item > button:before { content: '+'; display: block; position: absolute; width: 100%; text-align: center; text-indent: 0; }
.dd-item > button[data-action="collapse"]:before { content: '-'; }
.dd-placeholder, .dd-empty { margin: 5px 0; padding: 0; min-height: 30px; background: #f2fbff; border: 1px dashed #b6bcbf; box-sizing: border-box; }
.dd3-content { display: block; height: 40px; margin: 5px 0; padding: 10px 10px 10px 40px; color: #333; text-decoration: none; border: 1px solid #ccc; background: #fafafa; border-radius: 3px; box-sizing: border-box; }
.dd3-handle { position: absolute; margin: 0; left: 0; top: 0; cursor: move; width: 30px; text-align: center; padding: 10px 0; height: 40px; border-radius: 3px 0 0 3px; border: 1px solid #aaa; background: #ddd; color: #777; }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--primary-bg); margin: 0;">Manajemen Menu</h1>
        <p style="color: #666; margin-top: 5px;">Atur susunan menu navigasi utama dengan menggeser (drag & drop).</p>
    </div>
    <?php if ($edit_menu): ?>
        <a href="menus" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Batal Edit & Tambah Baru</a>
    <?php endif; ?>
</div>

<?php if($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>
<div id="ajax-msg" style="display:none;" class="alert alert-success"></div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
    <!-- Kiri: Form -->
    <div>
        <div style="background: white; border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
            <h3 style="margin-top:0; color: var(--primary-blue); font-size: 1.1rem; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                <?php echo $edit_menu ? '<i class="fa-solid fa-pen-to-square"></i> Edit Menu' : '<i class="fa-solid fa-plus"></i> Tambah Menu Baru'; ?>
            </h3>
            
            <form method="POST" action="menus">
                <input type="hidden" name="action" value="<?php echo $edit_menu ? 'edit' : 'add'; ?>">
                <?php if ($edit_menu): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_menu['id']; ?>">
                <?php endif; ?>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Judul Menu</label>
                    <input type="text" name="title" class="form-control" value="<?php echo $edit_menu ? htmlspecialchars($edit_menu['title']) : ''; ?>" placeholder="Contoh: Berita" required>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Tipe Tautan (URL)</label>
                    <div style="display:flex; gap: 15px; margin-bottom: 10px;">
                        <label>
                            <input type="radio" name="url_type" value="internal" <?php echo (!$edit_menu || strpos($edit_menu['url'], 'http') !== 0 && $edit_menu['url'] != '#') ? 'checked' : ''; ?> onchange="toggleUrlType()"> 
                            Pilih Halaman (Internal)
                        </label>
                        <label>
                            <input type="radio" name="url_type" value="custom" <?php echo ($edit_menu && (strpos($edit_menu['url'], 'http') === 0 || $edit_menu['url'] == '#')) ? 'checked' : ''; ?> onchange="toggleUrlType()"> 
                            Tautan Luar / Kustom
                        </label>
                    </div>
                </div>

                <div class="form-group" id="group_internal" style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Pilih Halaman</label>
                    <select name="internal_slug" class="form-control">
                        <option value="">-- Pilih Halaman --</option>
                        <?php foreach($pages_list as $p): 
                            // Try to match current edit_menu URL to determine selected
                            $selected = '';
                            if ($edit_menu) {
                                if (strpos($edit_menu['url'], 'page.php?slug='.$p['slug']) !== false || $edit_menu['url'] == $p['slug'].'.php') {
                                    $selected = 'selected';
                                }
                            }
                        ?>
                            <option value="<?php echo $p['slug']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($p['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" id="group_custom" style="margin-bottom: 20px; display: none;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">URL Kustom</label>
                    <input type="text" name="url_custom" class="form-control" value="<?php echo $edit_menu ? htmlspecialchars($edit_menu['url']) : ''; ?>" placeholder="contoh: https://forms.gle/... atau #">
                    <small style="color: #777;">Gunakan <b>#</b> jika menu ini hanya ditujukan untuk membuka sub-menu (dropdown).</small>
                </div>

                <button type="submit" class="btn" style="width: 100%; padding: 12px; background: <?php echo $edit_menu ? '#f39c12' : '#27ae60'; ?>; color:white; font-weight:bold; border:none; cursor:pointer; border-radius:4px;">
                    <?php echo $edit_menu ? 'SIMPAN PERUBAHAN' : 'TAMBAHKAN MENU'; ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Kanan: Drag and Drop -->
    <div>
        <div style="background: white; border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom:15px;">
                <h3 style="margin:0; color: var(--primary-blue); font-size: 1.1rem;">
                    <i class="fa-solid fa-list"></i> Struktur Menu
                </h3>
                <button id="save-menu-order" class="btn btn-primary" style="background:var(--accent); color:var(--primary-bg); font-weight:bold; padding:8px 15px; border-radius:5px; cursor:pointer; border:none;">Simpan Urutan</button>
            </div>
            
            <p style="font-size: 0.9em; color:#666;">Silakan seret (drag) kotak di bawah ini ke atas/bawah untuk mengurutkan, atau seret menjorok ke kanan untuk menjadikannya Sub-Menu.</p>
            
            <div class="dd" id="nestable">
                <?php echo renderNestableList($menu_tree); ?>
            </div>
            
        </div>
    </div>
</div>

<!-- jQuery and Nestable JS from CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js"></script>
<script>
    function toggleUrlType() {
        var type = document.querySelector('input[name="url_type"]:checked').value;
        if (type === 'internal') {
            document.getElementById('group_internal').style.display = 'block';
            document.getElementById('group_custom').style.display = 'none';
        } else {
            document.getElementById('group_internal').style.display = 'none';
            document.getElementById('group_custom').style.display = 'block';
        }
    }
    
    $(document).ready(function() {
        // Initialize nestable
        $('#nestable').nestable({
            maxDepth: 3
        });
        
        // Initial run for toggle
        toggleUrlType();
        
        // Save order button
        $('#save-menu-order').on('click', function() {
            var btn = $(this);
            btn.text('Menyimpan...').prop('disabled', true);
            
            var serializedData = window.JSON.stringify($('#nestable').nestable('serialize'));
            
            $.ajax({
                url: 'ajax_update_menu.php',
                method: 'POST',
                data: { menu_data: serializedData },
                dataType: 'json',
                success: function(response) {
                    btn.text('Simpan Urutan').prop('disabled', false);
                    if(response.status === 'success') {
                        $('#ajax-msg').removeClass('alert-error').addClass('alert-success').text(response.message).show();
                        setTimeout(function(){ $('#ajax-msg').fadeOut(); }, 3000);
                    } else {
                        $('#ajax-msg').removeClass('alert-success').addClass('alert-error').text(response.message).show();
                    }
                },
                error: function() {
                    btn.text('Simpan Urutan').prop('disabled', false);
                    $('#ajax-msg').removeClass('alert-success').addClass('alert-error').text('Terjadi kesalahan koneksi.').show();
                }
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
