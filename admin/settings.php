<?php
require_once '../db.php';
include 'includes/header.php';

$error = '';
$success = '';

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $settings_to_update = [
        'site_name', 'site_name_long', 'npp', 
        'contact_email', 'contact_wa', 'contact_phone',
        'address_1_name', 'address_1_text', 'address_1_map',
        'address_2_name', 'address_2_text', 'address_2_map',
        'hero_type', 'hero_video',
        'gemini_ai_enabled', 'gemini_api_key', 'gemini_model', 'gemini_system_instruction'
    ];
    
    $all_success = true;

    // Handle File Upload for Logo
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {
        $allowed = ['png', 'jpg', 'jpeg', 'webp'];
        $filename = $_FILES['site_logo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'logo_' . time() . '.' . $ext;
            $upload_path = '../uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $upload_path)) {
                $image_url = 'uploads/' . $new_filename;
                $sql = "UPDATE settings SET setting_value = '$image_url' WHERE setting_key = 'site_logo'";
                $conn->query($sql);
            }
        }
    }

    // Handle File Upload for Hero Image
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] == 0) {
        $allowed = ['png', 'jpg', 'jpeg', 'webp'];
        $filename = $_FILES['hero_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'hero_' . time() . '.' . $ext;
            $upload_path = '../uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $upload_path)) {
                $image_url = 'uploads/' . $new_filename;
                $sql = "UPDATE settings SET setting_value = '$image_url' WHERE setting_key = 'hero_image'";
                $conn->query($sql);
            }
        }
    }

    // Handle Text Inputs
    foreach ($settings_to_update as $key) {
        if (isset($_POST[$key])) {
            $val = $conn->real_escape_string($_POST[$key]);
            $sql = "INSERT INTO settings (setting_key, setting_value) VALUES ('$key', '$val') 
                    ON DUPLICATE KEY UPDATE setting_value = '$val'";
            if (!$conn->query($sql)) {
                $all_success = false;
            }
        }
    }
    
    // Handle Dynamic Jam Layanan
    if (isset($_POST['jam_day']) && isset($_POST['jam_time'])) {
        $jam_array = [];
        for ($i = 0; $i < count($_POST['jam_day']); $i++) {
            if (!empty(trim($_POST['jam_day'][$i])) || !empty(trim($_POST['jam_time'][$i]))) {
                $jam_array[] = [
                    'day' => trim($_POST['jam_day'][$i]),
                    'time' => trim($_POST['jam_time'][$i])
                ];
            }
        }
        $json_jam = $conn->real_escape_string(json_encode($jam_array));
        $conn->query("UPDATE settings SET setting_value = '$json_jam' WHERE setting_key = 'jam_layanan_json'");
    }

    // Handle Dynamic Pusat Informasi
    if (isset($_POST['pusat_title']) && isset($_POST['pusat_link'])) {
        $pusat_array = [];
        for ($i = 0; $i < count($_POST['pusat_title']); $i++) {
            if (!empty(trim($_POST['pusat_title'][$i]))) {
                $pusat_array[] = [
                    'title' => trim($_POST['pusat_title'][$i]),
                    'url' => trim($_POST['pusat_link'][$i])
                ];
            }
        }
        $json_pusat = $conn->real_escape_string(json_encode($pusat_array));
        $conn->query("UPDATE settings SET setting_value = '$json_pusat' WHERE setting_key = 'pusat_informasi_json'");
    }

    // Handle Dynamic Social Media
    if (isset($_POST['social_url'])) {
        $social_array = [];
        for ($i = 0; $i < count($_POST['social_url']); $i++) {
            $url = trim($_POST['social_url'][$i]);
            if (empty($url)) continue; // skip empty rows

            $icon_type = $_POST['social_icon_type'][$i] ?? 'fa';
            $icon_val = $_POST['social_icon_val'][$i] ?? 'fa-link';

            // Check if a new file was uploaded for this row
            if (isset($_FILES['social_icon']['error'][$i]) && $_FILES['social_icon']['error'][$i] == 0) {
                $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
                $filename = $_FILES['social_icon']['name'][$i];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $new_filename = 'social_' . time() . '_' . $i . '.' . $ext;
                    $upload_path = '../uploads/' . $new_filename;
                    
                    if (move_uploaded_file($_FILES['social_icon']['tmp_name'][$i], $upload_path)) {
                        $icon_type = 'img';
                        $icon_val = 'uploads/' . $new_filename;
                    }
                }
            }

            $social_array[] = [
                'icon_type' => $icon_type,
                'icon_val' => $icon_val,
                'url' => $url
            ];
        }
        $json_social = $conn->real_escape_string(json_encode($social_array));
        $conn->query("UPDATE settings SET setting_value = '$json_social' WHERE setting_key = 'social_media_json'");
    }

    if ($all_success) {
        $success = "Pengaturan berhasil diperbarui!";
    } else {
        $error = "Terjadi kesalahan saat menyimpan pengaturan.";
    }
}

// Fetch current settings
$settings = [];
$settings_sql = "SELECT setting_key, setting_value FROM settings";
$settings_res = $conn->query($settings_sql);
if ($settings_res) {
    while ($row = $settings_res->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

function get_set($key) {
    global $settings;
    return isset($settings[$key]) ? htmlspecialchars($settings[$key]) : '';
}
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
        <h2 style="margin-bottom: 0;"><i class="fa-solid fa-cogs"></i> Pengaturan Sistem</h2>
        <a href="backup_db" class="btn btn-warning" style="background: #27ae60; color: white; padding: 10px 15px; border-radius: 5px; font-weight: bold;"><i class="fa-solid fa-download"></i> Backup Database (.sql)</a>
    </div>
    <p style="margin-bottom: 20px; color: #666;">Ubah profil perpustakaan, alamat, jam layanan, dan sosial media di sini. Perubahan akan langsung terlihat di seluruh halaman website.</p>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="settings" enctype="multipart/form-data">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Kolom Kiri -->
            <div>
                <fieldset style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <legend style="font-weight: bold; color: var(--primary-blue); padding: 0 10px;">Identitas & Logo</legend>
                    
                    <div class="form-group">
                        <label>Nama Perpustakaan Singkat</label>
                        <input type="text" name="site_name" class="form-control" value="<?php echo get_set('site_name'); ?>" placeholder="Misal: Perpustakaan PKTJ">
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Perpustakaan Lengkap</label>
                        <input type="text" name="site_name_long" class="form-control" value="<?php echo get_set('site_name_long'); ?>" placeholder="Misal: POLITEKNIK KESELAMATAN TRANSPORTASI JALAN">
                    </div>

                    <div class="form-group">
                        <label>Nomor Pokok Perpustakaan (NPP)</label>
                        <input type="text" name="npp" class="form-control" value="<?php echo get_set('npp'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Logo Perpustakaan Baru (Kosongkan jika tidak diganti)</label>
                        <input type="file" name="site_logo" class="form-control" accept="image/*">
                        <?php 
                        $logo = get_set('site_logo');
                        if($logo): 
                            $logo_src = strpos($logo, 'http') === 0 ? $logo : '../' . $logo;
                        ?>
                            <div style="margin-top: 10px; padding: 10px; background: var(--primary-blue); display: inline-block; border-radius: 8px;">
                                <img src="<?php echo htmlspecialchars($logo_src); ?>" alt="Logo Current" style="height: 50px;">
                            </div>
                        <?php endif; ?>
                    </div>
                </fieldset>

                <fieldset style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <legend style="font-weight: bold; color: var(--primary-blue); padding: 0 10px;">Pengaturan Header (Hero)</legend>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Tipe Tampilan Header</label>
                        <div style="display: flex; gap: 20px; margin-top: 10px;">
                            <label style="font-weight: normal; cursor: pointer;">
                                <input type="radio" name="hero_type" value="image" <?php echo get_set('hero_type') == 'image' ? 'checked' : ''; ?>> 
                                Tampilkan Foto
                            </label>
                            <label style="font-weight: normal; cursor: pointer;">
                                <input type="radio" name="hero_type" value="video" <?php echo get_set('hero_type') == 'video' ? 'checked' : ''; ?>> 
                                Tampilkan Video YouTube
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Ganti Foto Gedung (Kosongkan jika tidak diganti)</label>
                        <input type="file" name="hero_image" class="form-control" accept="image/*">
                        <?php 
                        $hero_img = get_set('hero_image');
                        if($hero_img): 
                            $hero_src = strpos($hero_img, 'http') === 0 ? $hero_img : '../' . $hero_img;
                        ?>
                            <div style="margin-top: 10px; padding: 10px; border: 1px solid #eee; display: inline-block; border-radius: 8px;">
                                <img src="<?php echo htmlspecialchars($hero_src); ?>" alt="Hero Current" style="height: 60px; border-radius: 4px; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Link Video YouTube (Untuk Header Video)</label>
                        <input type="url" name="hero_video" class="form-control" value="<?php echo get_set('hero_video'); ?>" placeholder="Contoh: https://www.youtube.com/watch?v=xyz123">
                        <small style="color: #666; display: block; margin-top: 5px;">Video akan diputar otomatis tanpa suara (Muted) dan berulang-ulang (Loop).</small>
                    </div>
                </fieldset>

                <fieldset style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <legend style="font-weight: bold; color: var(--primary-blue); padding: 0 10px;">Jam Layanan Terstruktur</legend>
                    <p style="font-size: 0.85rem; color: #777; margin-bottom: 15px;">Teks akan dirapikan (rata kiri-kanan) secara otomatis oleh sistem agar titik duanya sejajar.</p>
                    
                    <div id="jam-container">
                        <?php
                        $jam_json = get_set('jam_layanan_json');
                        $jam_array = $jam_json ? json_decode(htmlspecialchars_decode($jam_json), true) : [];
                        
                        if (empty($jam_array) || !is_array($jam_array)) {
                            // Default empty row
                            $jam_array = [['day' => '', 'time' => '']];
                        }

                        foreach ($jam_array as $index => $jam) {
                        ?>
                        <div class="jam-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <input type="text" name="jam_day[]" class="form-control" value="<?php echo htmlspecialchars($jam['day']); ?>" placeholder="Hari (Misal: Senin - Kamis)">
                            <input type="text" name="jam_time[]" class="form-control" value="<?php echo htmlspecialchars($jam['time']); ?>" placeholder="Jam (Misal: 08.00 - 16.00 WIB)">
                            <button type="button" class="btn btn-danger btn-remove-jam" style="padding: 10px 15px;"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        <?php } ?>
                    </div>
                    
                    <button type="button" id="btn-add-jam" class="btn" style="background: #27ae60; margin-top: 10px; font-size: 0.9rem;"><i class="fa-solid fa-plus"></i> Tambah Baris Jam</button>
                </fieldset>
            </div>

            <!-- Kolom Kanan -->
            <div>
                <fieldset style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <legend style="font-weight: bold; color: var(--primary-blue); padding: 0 10px;">Alamat & Kontak</legend>
                    
                    <div class="form-group">
                        <label><i class="fa-solid fa-building"></i> Nama Cabang 1 (Misal: Perpustakaan Margadana)</label>
                        <input type="text" name="address_1_name" class="form-control" value="<?php echo get_set('address_1_name'); ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fa-solid fa-location-dot"></i> Teks Alamat Cabang 1</label>
                        <input type="text" name="address_1_text" class="form-control" value="<?php echo get_set('address_1_text'); ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fa-solid fa-map-location-dot"></i> Link Google Maps Cabang 1</label>
                        <input type="url" name="address_1_map" class="form-control" value="<?php echo get_set('address_1_map'); ?>" placeholder="https://maps.google.com/...">
                    </div>

                    <hr style="border:0; border-top: 1px dashed #ddd; margin: 20px 0;">

                    <div class="form-group">
                        <label><i class="fa-solid fa-building"></i> Nama Cabang 2 (Misal: Perpustakaan Perintis)</label>
                        <input type="text" name="address_2_name" class="form-control" value="<?php echo get_set('address_2_name'); ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fa-solid fa-location-dot"></i> Teks Alamat Cabang 2</label>
                        <input type="text" name="address_2_text" class="form-control" value="<?php echo get_set('address_2_text'); ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fa-solid fa-map-location-dot"></i> Link Google Maps Cabang 2</label>
                        <input type="url" name="address_2_map" class="form-control" value="<?php echo get_set('address_2_map'); ?>" placeholder="https://maps.google.com/...">
                    </div>

                    <hr style="border:0; border-top: 1px dashed #ddd; margin: 20px 0;">
                    
                    <div class="form-group">
                        <label><i class="fa-solid fa-envelope"></i> Email Utama</label>
                        <input type="email" name="contact_email" class="form-control" value="<?php echo get_set('contact_email'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fa-solid fa-phone"></i> Nomor Telepon Kantor</label>
                        <input type="text" name="contact_phone" class="form-control" value="<?php echo get_set('contact_phone'); ?>">
                    </div>

                    <div class="form-group">
                        <label><i class="fa-brands fa-whatsapp"></i> Nomor Chat WhatsApp (Hanya angka, contoh: 62812...)</label>
                        <input type="text" name="contact_wa" class="form-control" value="<?php echo get_set('contact_wa'); ?>">
                    </div>
                </fieldset>

                <fieldset style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <legend style="font-weight: bold; color: var(--primary-blue); padding: 0 10px;">Sosial Media Dinamis</legend>
                    <p style="font-size: 0.85rem; color: #777; margin-bottom: 15px;">Tambahkan tautan sosial media baru dan unggah ikonnya (PNG/JPG/SVG).</p>
                    
                    <div id="social-container">
                        <?php
                        $social_json = get_set('social_media_json');
                        $social_array = $social_json ? json_decode(htmlspecialchars_decode($social_json), true) : [];
                        
                        if (empty($social_array) || !is_array($social_array)) {
                            $social_array = [['icon_type' => 'fa', 'icon_val' => 'fa-link', 'url' => '']];
                        }

                        foreach ($social_array as $index => $social) {
                        ?>
                        <div class="social-row" style="display: flex; gap: 10px; margin-bottom: 15px; align-items: center; border: 1px solid #eee; padding: 10px; border-radius: 6px;">
                            <div style="width: 50px; text-align: center;">
                                <?php if ($social['icon_type'] == 'fa'): ?>
                                    <i class="fa-brands <?php echo htmlspecialchars($social['icon_val']); ?> fa-2x" style="color: var(--primary-bg);"></i>
                                <?php else: ?>
                                    <img src="../<?php echo htmlspecialchars($social['icon_val']); ?>" alt="Icon" style="width: 32px; height: 32px; object-fit: contain;">
                                <?php endif; ?>
                            </div>
                            <div style="flex: 1;">
                                <input type="hidden" name="social_icon_type[]" value="<?php echo htmlspecialchars($social['icon_type']); ?>">
                                <input type="hidden" name="social_icon_val[]" value="<?php echo htmlspecialchars($social['icon_val']); ?>">
                                <input type="url" name="social_url[]" class="form-control" value="<?php echo htmlspecialchars($social['url']); ?>" placeholder="Link URL" style="margin-bottom: 5px;">
                                <input type="file" name="social_icon[]" class="form-control" accept="image/*" style="font-size: 0.8rem;">
                            </div>
                            <button type="button" class="btn btn-danger btn-remove-social" style="padding: 10px 15px; align-self: stretch;"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        <?php } ?>
                    </div>
                    
                    <button type="button" id="btn-add-social" class="btn" style="background: #27ae60; font-size: 0.9rem;"><i class="fa-solid fa-plus"></i> Tambah Sosial Media</button>
                </fieldset>

                <fieldset style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-top: 20px;">
                    <legend style="font-weight: bold; color: var(--primary-blue); padding: 0 10px;">Pusat Informasi Dinamis (Footer)</legend>
                    <p style="font-size: 0.85rem; color: #777; margin-bottom: 15px;">Tambahkan teks judul dan URL untuk menu Pusat Informasi di footer.</p>
                    
                    <div id="pusat-container">
                        <?php
                        $pusat_json = get_set('pusat_informasi_json');
                        $pusat_array = $pusat_json ? json_decode(htmlspecialchars_decode($pusat_json), true) : [];
                        
                        if (empty($pusat_array) || !is_array($pusat_array)) {
                            $pusat_array = [['title' => '', 'url' => '']];
                        }

                        foreach ($pusat_array as $index => $pusat) {
                        ?>
                        <div class="pusat-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <input type="text" name="pusat_title[]" class="form-control" value="<?php echo htmlspecialchars($pusat['title']); ?>" placeholder="Judul Tautan">
                            <input type="url" name="pusat_link[]" class="form-control" value="<?php echo htmlspecialchars($pusat['url']); ?>" placeholder="URL Tujuan">
                            <button type="button" class="btn btn-danger btn-remove-pusat" style="padding: 10px 15px;"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        <?php } ?>
                    </div>
                    
                    <button type="button" id="btn-add-pusat" class="btn" style="background: #27ae60; margin-top: 10px; font-size: 0.9rem;"><i class="fa-solid fa-plus"></i> Tambah Tautan</button>
                </fieldset>
            </div>
        </div>

        <!-- Bagian Konfigurasi AI Pustabots (Google Gemini) -->
        <fieldset style="border: 2px solid #3b82f6; padding: 25px; border-radius: 10px; background: #f8fafc; margin-top: 30px; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.08);">
            <legend style="font-weight: 700; color: #1e40af; padding: 0 12px; font-size: 1.15rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-brain" style="color: #2563eb;"></i> Integrasi Otak AI Pustabots (Google Gemini)
            </legend>
            
            <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; color: #1e3a8a; line-height: 1.5;">
                <strong>💡 Konsep Hibrida Cerdas:</strong> Pustabots akan selalu memprioritaskan jawaban resmi dari daftar FAQ internal. Namun jika pengunjung bertanya hal umum, rekomendasi topik skripsi, atau pertanyaan lain di luar FAQ, Pustabots akan otomatis dijawab secara cerdas dan luwes oleh <strong>Google Gemini AI</strong> (100% Gratis dari Google AI Studio)!
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;"><i class="fa-solid fa-power-off"></i> Status Kecerdasan Buatan (AI)</label>
                    <select name="gemini_ai_enabled" class="form-control" style="font-weight: 600;">
                        <option value="1" <?php echo get_set('gemini_ai_enabled') === '1' ? 'selected' : ''; ?>>🟢 AKTIF (Gunakan Otak AI saat FAQ Tidak Menjawab)</option>
                        <option value="0" <?php echo get_set('gemini_ai_enabled') !== '1' ? 'selected' : ''; ?>>⚪ NONAKTIF (Gunakan Pesan Standar & WhatsApp)</option>
                    </select>
                    <small style="color: #64748b; display: block; margin-top: 5px;">Pilih <strong>AKTIF</strong> setelah menempelkan Kunci API di bawah.</small>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;"><i class="fa-solid fa-microchip"></i> Model AI yang Digunakan</label>
                    <select name="gemini_model" class="form-control">
                        <option value="gemini-3.5-flash-lite" <?php echo (get_set('gemini_model') != 'gemini-3.8-flash') ? 'selected' : ''; ?>>Gemini 3.5 Flash Lite (Rekomendasi - Super Cepat & Kuota Melimpah)</option>
                        <option value="gemini-3.8-flash" <?php echo get_set('gemini_model') == 'gemini-3.8-flash' ? 'selected' : ''; ?>>Gemini 3.8 Flash (Model Generasi Baru)</option>
                    </select>
                    <small style="color: #64748b; display: block; margin-top: 5px;">Model <strong>Gemini 3.5 Flash Lite</strong> memiliki kuota gratis melimpah dan respons super cepat (1-2 detik).</small>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: 600; display: block; margin-bottom: 8px;"><i class="fa-solid fa-key"></i> Kunci API Google Gemini (Google AI Studio Key)</label>
                <div style="display: flex; gap: 10px;">
                    <input type="password" id="gemini_api_key_input" name="gemini_api_key" class="form-control" value="<?php echo get_set('gemini_api_key'); ?>" placeholder="AIzaSy..." style="font-family: monospace;">
                    <button type="button" class="btn" onclick="toggleApiKeyVisibility()" style="background: #64748b; white-space: nowrap; padding: 0 15px;"><i class="fa-solid fa-eye" id="toggleApiKeyIcon"></i> Lihat</button>
                </div>
                <div style="margin-top: 10px; font-size: 0.85rem; color: #475569; background: #fff; padding: 12px 15px; border-radius: 6px; border: 1px solid #e2e8f0;">
                    <strong>Cara Mendapatkan Kunci API Gratis (Hanya butuh 2 Menit):</strong>
                    <ol style="margin: 6px 0 0 18px; padding: 0;">
                        <li>Buka tautan <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener" style="color: #2563eb; font-weight: bold; text-decoration: underline;">Google AI Studio (Klik di Sini) <i class="fa-solid fa-arrow-up-right-from-square"></i></a> lalu login menggunakan akun Google Anda.</li>
                        <li>Klik tombol biru <strong>"Create API key"</strong> di pojok kanan atas.</li>
                        <li>Pilih proyek default atau klik <em>"Create API key in new project"</em>.</li>
                        <li>Salin kode kunci yang muncul (berawalan <code>AIzaSy...</code>) lalu tempel (Paste) ke dalam kotak isian di atas.</li>
                    </ol>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="font-weight: 600; display: block; margin-bottom: 8px;"><i class="fa-solid fa-user-gear"></i> Instruksi Watak & Kepribadian AI (System Prompt)</label>
                <textarea name="gemini_system_instruction" class="form-control" rows="6" placeholder="Tulis instruksi kepribadian AI..." style="height: 140px; font-size: 0.9rem; line-height: 1.5; font-family: inherit;"><?php 
                    $curr_instruction = get_set('gemini_system_instruction');
                    if (empty($curr_instruction)) {
                        echo "Kamu adalah Pustabots, asisten kecerdasan buatan resmi dari Perpustakaan Politeknik Keselamatan Transportasi Jalan (PKTJ) Tegal (Kementerian Perhubungan RI).\nTugas utamamu adalah membantu pemustaka (taruna, dosen, staf, dan pengunjung) dengan ramah, santun, jelas, dan solutif.\n\nFakta Resmi Kampus & Perpustakaan:\n- Direktur PKTJ saat ini: Bapak Bambang Istiyanto\n- Kepala Perpustakaan PKTJ saat ini: Ibu Supriatin, S.S.T.\n- Lokasi Kampus: Kampus Margadana (Jl. Abdul Syukur No. 17) dan Kampus Perintis (Jl. Perintis Kemerdekaan No. 17, Tegal).\n- Program Studi: D4 Manajemen Rekayasa Lalu Lintas (MRL), D4 Teknologi Rekayasa Otomotif (TRO), D3 Keselamatan Transportasi Jalan (KTJ).\n\nPedoman Menjawab:\n1. Gunakan bahasa Indonesia yang baik, sopan, dan hangat.\n2. Berikan bantuan seputar informasi referensi akademik, literatur keselamatan transportasi jalan, rekayasa lalu lintas, otomotif, perkeretaapian, dan tips penulisan skripsi/karya ilmiah.\n3. Jika pemustaka menanyakan hal di luar topik perpustakaan atau studi kampus PKTJ, tolaklah secara halus dan kembalikan obrolan ke seputar perpustakaan.\n4. Jika pertanyaan membutuhkan konfirmasi atau penanganan administratif langsung dari pustakawan (seperti validasi tanda tangan bebas pustaka langsung), arahkan untuk menghubungi WhatsApp resmi Pustakawan PKTJ.\n5. Berikan jawaban yang ringkas, terstruktur rapi dengan poin/bullet agar nyaman dibaca pada layar obrolan.";
                    } else {
                        echo $curr_instruction;
                    }
                ?></textarea>
                <small style="color: #64748b; display: block; margin-top: 5px;">Instruksi ini mengatur kepribadian bot. Anda dapat mengubah atau menambahkan aturan khusus perpustakaan di sini kapan saja.</small>
            </div>
        </fieldset>
        
        <div style="margin-top: 30px; text-align: center; border-top: 1px solid #ddd; padding-top: 20px;">
            <button type="submit" class="btn" style="padding: 15px 40px; font-size: 1.1rem;"><i class="fa-solid fa-save"></i> SIMPAN SEMUA PENGATURAN</button>
        </div>
    </form>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Jam Layanan JS
        const containerJam = document.getElementById('jam-container');
        const btnAddJam = document.getElementById('btn-add-jam');

        btnAddJam.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'jam-row';
            row.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px;';
            row.innerHTML = `
                <input type="text" name="jam_day[]" class="form-control" placeholder="Hari (Misal: Senin - Kamis)">
                <input type="text" name="jam_time[]" class="form-control" placeholder="Jam (Misal: 08.00 - 16.00 WIB)">
                <button type="button" class="btn btn-danger btn-remove-jam" style="padding: 10px 15px;"><i class="fa-solid fa-trash"></i></button>
            `;
            containerJam.appendChild(row);
        });

        containerJam.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-jam')) {
                const row = e.target.closest('.jam-row');
                if (containerJam.querySelectorAll('.jam-row').length > 1) {
                    row.remove();
                } else {
                    alert('Minimal harus ada 1 baris jam layanan.');
                }
            }
        });

        // Pusat Informasi JS
        const containerPusat = document.getElementById('pusat-container');
        const btnAddPusat = document.getElementById('btn-add-pusat');

        btnAddPusat.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'pusat-row';
            row.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px;';
            row.innerHTML = `
                <input type="text" name="pusat_title[]" class="form-control" placeholder="Judul Tautan">
                <input type="url" name="pusat_link[]" class="form-control" placeholder="URL Tujuan">
                <button type="button" class="btn btn-danger btn-remove-pusat" style="padding: 10px 15px;"><i class="fa-solid fa-trash"></i></button>
            `;
            containerPusat.appendChild(row);
        });

        containerPusat.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-pusat')) {
                const row = e.target.closest('.pusat-row');
                if (containerPusat.querySelectorAll('.pusat-row').length > 1) {
                    row.remove();
                } else {
                    alert('Minimal harus ada 1 baris pusat informasi.');
                }
            }
        });

        // Social Media JS
        const containerSocial = document.getElementById('social-container');
        const btnAddSocial = document.getElementById('btn-add-social');

        btnAddSocial.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'social-row';
            row.style.cssText = 'display: flex; gap: 10px; margin-bottom: 15px; align-items: center; border: 1px solid #eee; padding: 10px; border-radius: 6px;';
            row.innerHTML = `
                <div style="width: 50px; text-align: center;">
                    <i class="fa-solid fa-link fa-2x" style="color: #ccc;"></i>
                </div>
                <div style="flex: 1;">
                    <input type="hidden" name="social_icon_type[]" value="fa">
                    <input type="hidden" name="social_icon_val[]" value="fa-link">
                    <input type="url" name="social_url[]" class="form-control" placeholder="Link URL" style="margin-bottom: 5px;">
                    <input type="file" name="social_icon[]" class="form-control" accept="image/*" style="font-size: 0.8rem;">
                </div>
                <button type="button" class="btn btn-danger btn-remove-social" style="padding: 10px 15px; align-self: stretch;"><i class="fa-solid fa-trash"></i></button>
            `;
            containerSocial.appendChild(row);
        });

        containerSocial.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-social')) {
                const row = e.target.closest('.social-row');
                row.remove();
            }
        });
    });

    function toggleApiKeyVisibility() {
        const input = document.getElementById('gemini_api_key_input');
        const icon = document.getElementById('toggleApiKeyIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
