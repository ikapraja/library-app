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
        $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : "NULL";
        
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
            // Hitung order_num tertinggi di parent tersebut
            $max_order_query = "SELECT MAX(order_num) as max_o FROM menus WHERE parent_id " . ($parent_id === "NULL" ? "IS NULL" : "= $parent_id");
            $max_res = $conn->query($max_order_query);
            $next_order = 10;
            if ($max_res && $row = $max_res->fetch_assoc()) {
                $next_order = intval($row['max_o']) + 10;
            }
            
            $sql = "INSERT INTO menus (title, url, parent_id, order_num) VALUES ('$title', '$url', $parent_id, $next_order)";
            if ($conn->query($sql)) {
                $success = "Menu baru berhasil ditambahkan.";
            } else {
                $error = "Gagal menambah menu: " . $conn->error;
            }
        } else {
            $id = intval($_POST['id']);
            $sql = "UPDATE menus SET title='$title', url='$url', parent_id=$parent_id WHERE id=$id";
            if ($conn->query($sql)) {
                $success = "Menu berhasil diperbarui.";
            } else {
                $error = "Gagal memperbarui menu: " . $conn->error;
            }
        }
    } elseif ($action == 'delete') {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM menus WHERE id=$id";
        if ($conn->query($sql)) {
            $success = "Menu berhasil dihapus.";
        } else {
            $error = "Gagal menghapus menu: " . $conn->error;
        }
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

// Helper dropdown pilihan Parent Menu
function renderParentOptions($tree, $selected_id = null, $current_edit_id = null, $depth = 0) {
    $html = '';
    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth);
    $prefix = $depth > 0 ? '↳ ' : '';
    foreach ($tree as $node) {
        // Jangan tampilkan diri sendiri jika sedang edit
        if ($current_edit_id && $node['id'] == $current_edit_id) {
            continue;
        }
        $selected = ($selected_id !== null && $selected_id == $node['id']) ? 'selected' : '';
        $html .= '<option value="' . $node['id'] . '" ' . $selected . '>' . $indent . $prefix . htmlspecialchars($node['title']) . '</option>';
        if (!empty($node['children'])) {
            $html .= renderParentOptions($node['children'], $selected_id, $current_edit_id, $depth + 1);
        }
    }
    return $html;
}

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
        $html .= '<div class="dd-handle dd3-handle" title="Tahan & geser untuk memindahkan"><i class="fa-solid fa-grip-vertical"></i></div>';
        $html .= '<div class="dd3-content">';
        $html .= '<div style="display:flex; align-items:center; gap:8px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; padding-right:10px;">';
        $html .= '<span style="font-weight:600; color:#1e293b; font-size:13px;">' . htmlspecialchars($node['title']) . '</span>';
        $html .= '<span style="color:#64748b; font-size:11px; background:#f1f5f9; padding:2px 6px; border-radius:3px; max-width:220px; overflow:hidden; text-overflow:ellipsis;">' . htmlspecialchars($node['url']) . '</span>';
        $html .= '</div>';
        
        $html .= '<div style="display:flex; align-items:center; gap:4px; flex-shrink:0;">';
        // Tombol Geser Naik / Turun
        $html .= '<button type="button" class="btn-move-up" title="Geser ke Atas" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; border-radius:4px; padding:3px 7px; cursor:pointer; font-size:11px;"><i class="fa-solid fa-arrow-up"></i></button>';
        $html .= '<button type="button" class="btn-move-down" title="Geser ke Bawah" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; border-radius:4px; padding:3px 7px; cursor:pointer; font-size:11px;"><i class="fa-solid fa-arrow-down"></i></button>';
        
        // Tombol Edit
        $html .= '<a href="menus?edit='.$node['id'].'" title="Edit Menu" style="background:#f59e0b; color:white; border-radius:4px; padding:3px 7px; font-size:11px; text-decoration:none; display:inline-flex; align-items:center;"><i class="fa-solid fa-pen"></i></a>';
        
        // Tombol Hapus
        $html .= '<form method="POST" action="menus" style="display:inline; margin:0;" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus menu ini beserta seluruh sub-menunya?\');">';
        $html .= '<input type="hidden" name="action" value="delete">';
        $html .= '<input type="hidden" name="id" value="'.$node['id'].'">';
        $html .= '<button type="submit" title="Hapus Menu" style="background:#ef4444; color:white; border:none; border-radius:4px; padding:4px 7px; font-size:11px; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</div>';
        
        if (!empty($node['children'])) {
            $html .= renderNestableList($node['children']);
        }
        $html .= '</li>';
    }
    $html .= '</ol>';
    return $html;
}
?>

<!-- Nestable CSS Enhanced -->
<style>
.dd { position: relative; display: block; margin: 0; padding: 0; max-width: 100%; width: 100%; list-style: none; font-size: 13px; line-height: 20px; }
.dd-list { display: block; position: relative; margin: 0; padding: 0; list-style: none; }
.dd-list .dd-list { padding-left: 28px; border-left: 2px dashed #cbd5e1; margin-left: 12px; }
.dd-item, .dd-empty, .dd-placeholder { display: block; position: relative; margin: 0; padding: 0; min-height: 20px; font-size: 13px; line-height: 20px; }
.dd-item > button { display: block; position: relative; cursor: pointer; float: left; width: 25px; height: 28px; margin: 6px 0; padding: 0; text-indent: 100%; white-space: nowrap; overflow: hidden; border: 0; background: transparent; font-size: 13px; line-height: 1; text-align: center; font-weight: bold; color: #475569; z-index: 10; }
.dd-item > button:before { content: '+'; display: block; position: absolute; width: 100%; text-align: center; text-indent: 0; background: #e2e8f0; border-radius: 3px; line-height: 20px; height: 20px; }
.dd-item > button[data-action="collapse"]:before { content: '−'; }
.dd-placeholder, .dd-empty { margin: 6px 0; padding: 0; min-height: 42px; background: #eff6ff; border: 2px dashed #3b82f6 !important; border-radius: 6px; box-sizing: border-box; }
.dd3-content { display: flex; align-items: center; justify-content: space-between; min-height: 42px; margin: 6px 0; padding: 6px 10px 6px 45px; color: #1e293b; text-decoration: none; border: 1px solid #cbd5e1; background: #ffffff; border-radius: 6px; box-sizing: border-box; box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: border-color 0.2s, background 0.2s; }
.dd3-content:hover { border-color: #94a3b8; background: #f8fafc; }
.dd3-handle { position: absolute; margin: 0; left: 0; top: 0; cursor: grab; width: 36px; text-align: center; display: flex; align-items: center; justify-content: center; height: 100%; border-radius: 6px 0 0 6px; border: 1px solid #cbd5e1; border-right: none; background: #f1f5f9; color: #64748b; font-size: 14px; }
.dd3-handle:hover { background: #e2e8f0; color: #0f172a; }
.dd-dragel { position: absolute; pointer-events: none; z-index: 9999; opacity: 0.95; }
.dd-dragel > .dd-item .dd3-content { margin: 0; box-shadow: 0 12px 28px rgba(0,0,0,0.2) !important; border-color: #3b82f6 !important; background: #eff6ff !important; }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--primary-bg); margin: 0;">
            <i class="fa-solid fa-bars-staggered" style="color: var(--accent); margin-right: 8px;"></i> Manajemen Menu
        </h1>
        <p style="color: #666; margin-top: 5px;">Atur menu navigasi website dengan mudah melalui tombol panah (⬆️ ⬇️), geser (drag & drop), atau pilih menu induk.</p>
    </div>
    <?php if ($edit_menu): ?>
        <a href="menus" class="btn btn-secondary" style="background:#64748b; color:white; padding:8px 15px; border-radius:5px; text-decoration:none; font-size:13px; font-weight:600;"><i class="fa-solid fa-arrow-left"></i> Batal Edit & Tambah Baru</a>
    <?php endif; ?>
</div>

<?php if($success): ?>
    <div class="alert alert-success" style="background:#dcfce7; color:#166534; padding:12px 15px; border-radius:6px; margin-bottom:15px; border-left:4px solid #22c55e;"><i class="fa-solid fa-check"></i> <?php echo $success; ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="alert alert-error" style="background:#fee2e2; color:#991b1b; padding:12px 15px; border-radius:6px; margin-bottom:15px; border-left:4px solid #ef4444;"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?></div>
<?php endif; ?>
<div id="ajax-msg" style="display:none; background:#dcfce7; color:#166534; padding:12px 15px; border-radius:6px; margin-bottom:15px; border-left:4px solid #22c55e;"></div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
    <!-- Kiri: Form Tambah / Edit -->
    <div>
        <div style="background: white; border: 1px solid #e2e8f0; padding: 22px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="margin-top:0; color: var(--primary-blue); font-size: 1.1rem; border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom:18px;">
                <?php echo $edit_menu ? '<i class="fa-solid fa-pen-to-square"></i> Edit Menu' : '<i class="fa-solid fa-plus"></i> Tambah Menu Baru'; ?>
            </h3>
            
            <form method="POST" action="menus">
                <input type="hidden" name="action" value="<?php echo $edit_menu ? 'edit' : 'add'; ?>">
                <?php if ($edit_menu): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_menu['id']; ?>">
                <?php endif; ?>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#1e293b; font-size:13px;">Judul Menu</label>
                    <input type="text" name="title" class="form-control" value="<?php echo $edit_menu ? htmlspecialchars($edit_menu['title']) : ''; ?>" placeholder="Contoh: Neliti, Berita, dll." required style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:6px; box-sizing:border-box;">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#1e293b; font-size:13px;">Menu Induk (Posisi Sub-Menu)</label>
                    <select name="parent_id" class="form-control" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:6px; box-sizing:border-box; background:#f8fafc; font-size:13px;">
                        <option value="">-- Menu Utama (Tingkat Paling Luar) --</option>
                        <?php echo renderParentOptions($menu_tree, $edit_menu['parent_id'] ?? null, $edit_menu['id'] ?? null); ?>
                    </select>
                    <small style="color:#64748b; font-size:11px; display:block; margin-top:4px;">
                        <i class="fa-solid fa-circle-info"></i> Pilih menu induk agar menu ini langsung masuk ke dalam sub-menu yang Anda tuju (tanpa perlu repot menggeser).
                    </small>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#1e293b; font-size:13px;">Tipe Tautan (URL)</label>
                    <div style="display:flex; gap: 15px; margin-bottom: 10px;">
                        <label style="font-size:13px; cursor:pointer;">
                            <input type="radio" name="url_type" value="internal" <?php echo (!$edit_menu || (strpos($edit_menu['url'], 'http') !== 0 && $edit_menu['url'] != '#')) ? 'checked' : ''; ?> onchange="toggleUrlType()"> 
                            Pilih Halaman (Internal)
                        </label>
                        <label style="font-size:13px; cursor:pointer;">
                            <input type="radio" name="url_type" value="custom" <?php echo ($edit_menu && (strpos($edit_menu['url'], 'http') === 0 || $edit_menu['url'] == '#')) ? 'checked' : ''; ?> onchange="toggleUrlType()"> 
                            Tautan Luar / Kustom
                        </label>
                    </div>
                </div>

                <div class="form-group" id="group_internal" style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#1e293b; font-size:13px;">Pilih Halaman</label>
                    <select name="internal_slug" class="form-control" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:6px; box-sizing:border-box; font-size:13px;">
                        <option value="">-- Pilih Halaman --</option>
                        <?php foreach($pages_list as $p): 
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
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#1e293b; font-size:13px;">URL Kustom</label>
                    <input type="text" name="url_custom" class="form-control" value="<?php echo $edit_menu ? htmlspecialchars($edit_menu['url']) : ''; ?>" placeholder="contoh: https://www.neliti.com/id/ atau #" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:6px; box-sizing:border-box;">
                    <small style="color: #64748b; font-size:11px; display:block; margin-top:4px;">Gunakan tanda <b>#</b> jika menu ini hanya berfungsi sebagai pembuka dropdown (tanpa link).</small>
                </div>

                <button type="submit" class="btn" style="width: 100%; padding: 11px; background: <?php echo $edit_menu ? '#f59e0b' : '#16a34a'; ?>; color:white; font-weight:700; border:none; cursor:pointer; border-radius:6px; font-size:14px; display:flex; align-items:center; justify-content:center; gap:8px;">
                    <i class="fa-solid <?php echo $edit_menu ? 'fa-floppy-disk' : 'fa-plus'; ?>"></i>
                    <?php echo $edit_menu ? 'SIMPAN PERUBAHAN' : 'TAMBAHKAN MENU'; ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Kanan: Pohon Menu & Pengurutan Cepat -->
    <div>
        <div style="background: white; border: 1px solid #e2e8f0; padding: 22px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom:15px; flex-wrap:wrap; gap:10px;">
                <div>
                    <h3 style="margin:0; color: var(--primary-blue); font-size: 1.1rem;">
                        <i class="fa-solid fa-sitemap"></i> Struktur & Urutan Menu
                    </h3>
                    <p style="margin:4px 0 0; font-size:0.85rem; color:#64748b;">
                        Gunakan tombol panah ⬆️ ⬇️ atau geser (drag & drop) untuk mengatur urutan.
                    </p>
                </div>
                <div style="display:flex; gap:8px; align-items:center;">
                    <button type="button" id="btn-expand-all" class="btn" style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; font-size:12px; padding:6px 10px; border-radius:5px; cursor:pointer;" title="Buka Semua Sub-Menu">
                        <i class="fa-solid fa-angles-down"></i> Buka Semua
                    </button>
                    <button type="button" id="btn-collapse-all" class="btn" style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; font-size:12px; padding:6px 10px; border-radius:5px; cursor:pointer;" title="Tutup Semua Sub-Menu">
                        <i class="fa-solid fa-angles-right"></i> Tutup Semua
                    </button>
                    <button id="save-menu-order" class="btn" style="background:var(--accent); color:var(--primary-bg); font-weight:700; padding:7px 15px; border-radius:5px; cursor:pointer; border:none; font-size:13px; display:inline-flex; align-items:center; gap:6px;">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Urutan
                    </button>
                </div>
            </div>
            
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
        // Initialize nestable dengan kedalaman hingga 6 level
        $('#nestable').nestable({
            maxDepth: 6,
            threshold: 20
        });
        
        // Initial run for toggle
        toggleUrlType();
        
        // Buka / Tutup Semua
        $('#btn-expand-all').on('click', function() {
            $('#nestable').nestable('expandAll');
        });
        
        $('#btn-collapse-all').on('click', function() {
            $('#nestable').nestable('collapseAll');
        });
        
        // Simpan Urutan Function
        function saveMenuOrder(silent) {
            var btn = $('#save-menu-order');
            if (!silent) {
                btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);
            }
            
            var serializedData = window.JSON.stringify($('#nestable').nestable('serialize'));
            
            $.ajax({
                url: 'ajax_update_menu.php',
                method: 'POST',
                data: { menu_data: serializedData },
                dataType: 'json',
                success: function(response) {
                    btn.html('<i class="fa-solid fa-floppy-disk"></i> Simpan Urutan').prop('disabled', false);
                    if(response.status === 'success') {
                        $('#ajax-msg').removeClass('alert-error').addClass('alert-success').html('<i class="fa-solid fa-check"></i> ' + response.message).fadeIn();
                        setTimeout(function(){ $('#ajax-msg').fadeOut(); }, 2500);
                    } else {
                        $('#ajax-msg').removeClass('alert-success').addClass('alert-error').text(response.message).show();
                    }
                },
                error: function() {
                    btn.html('<i class="fa-solid fa-floppy-disk"></i> Simpan Urutan').prop('disabled', false);
                    $('#ajax-msg').removeClass('alert-success').addClass('alert-error').text('Terjadi kesalahan koneksi saat menyimpan.').show();
                }
            });
        }
        
        $('#save-menu-order').on('click', function() {
            saveMenuOrder(false);
        });
        
        // Notifikasi visual saat item digeser
        $('#nestable').on('change', function() {
            $('#ajax-msg').removeClass('alert-error').addClass('alert-success').html('<i class="fa-solid fa-circle-info"></i> Urutan menu berubah. Jangan lupa klik <strong>Simpan Urutan</strong> jika sudah selesai.').fadeIn();
        });
        
        // Tombol Geser Naik ⬆️
        $(document).on('click', '.btn-move-up', function(e) {
            e.preventDefault();
            var item = $(this).closest('.dd-item');
            var prev = item.prev('.dd-item');
            if (prev.length > 0) {
                item.insertBefore(prev);
                saveMenuOrder(true);
            }
        });
        
        // Tombol Geser Turun ⬇️
        $(document).on('click', '.btn-move-down', function(e) {
            e.preventDefault();
            var item = $(this).closest('.dd-item');
            var next = item.next('.dd-item');
            if (next.length > 0) {
                item.insertAfter(next);
                saveMenuOrder(true);
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
