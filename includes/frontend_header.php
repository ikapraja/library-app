<?php
require_once __DIR__ . '/../db.php';

// Fetch settings
$settings = [];
$settings_sql = "SELECT setting_key, setting_value FROM settings";
$settings_res = $conn->query($settings_sql);
if ($settings_res) {
    while ($row = $settings_res->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Helper to safely get setting
function get_setting($key, $default = '') {
    global $settings;
    return isset($settings[$key]) ? htmlspecialchars($settings[$key]) : $default;
}

// Get raw setting (for HTML content)
function get_raw_setting($key, $default = '') {
    global $settings;
    return isset($settings[$key]) ? $settings[$key] : $default;
}
?>
<?php
$is_subfolder = (strpos($_SERVER['REQUEST_URI'], '/library-app/') !== false);
$app_base = $is_subfolder ? '/library-app/' : '/';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo $app_base; ?>">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . get_setting('site_name') : get_setting('site_name') . ' - ' . get_setting('site_name_long'); ?></title>
    <!-- Favicon -->
    <link rel="icon" href="<?php echo $app_base; ?>LOGO PERPUS PKTJ.png" type="image/png">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- GLightbox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <span class="npp-info"><i class="fa-solid fa-id-card"></i> NPP <?php echo get_setting('npp'); ?></span>
            <span class="akreditasi"><i class="fa-solid fa-award"></i> Akreditasi A</span>
        </div>
    </div>

    <!-- Header & Navigation -->
    <header id="header" class="header">
        <div class="container header-container">
            <a href="./" class="logo-container" style="text-decoration: none; cursor: pointer;">
                <div class="logo-icon">
                    <?php 
                    $logo = get_setting('site_logo');
                    if(strpos($logo, 'http') !== 0) $logo = $logo; // Assuming logo is in root or uploads
                    ?>
                    <img src="<?php echo $logo; ?>" alt="Logo <?php echo get_setting('site_name'); ?>" style="height: 45px; width: auto; display: block;">
                </div>
                <div class="logo-text">
                    <h1><?php echo strtoupper(get_setting('site_name')); ?></h1>
                    <p><?php echo strtoupper(get_setting('site_name_long')); ?></p>
                </div>
            </a>
            
            <nav class="main-nav">
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <?php
                // Fetch all menus
                $menus_sql = "SELECT * FROM menus WHERE is_active=1 ORDER BY parent_id, order_num ASC";
                $menus_res = $conn->query($menus_sql);
                $all_menus = [];
                if ($menus_res) {
                    while($m_row = $menus_res->fetch_assoc()){
                        $all_menus[] = $m_row;
                    }
                }

                // Helper to check if a menu or its children is active
                function is_menu_active($menu_id, $all_menus, $current_file) {
                    foreach ($all_menus as $m) {
                        if ($m['id'] == $menu_id && $m['url'] != '#' && basename($m['url']) == $current_file) {
                            return true;
                        }
                        if ($m['parent_id'] == $menu_id) {
                            if (is_menu_active($m['id'], $all_menus, $current_file)) {
                                return true;
                            }
                        }
                    }
                    return false;
                }

                function render_frontend_menu($parent_id, $all_menus) {
                    $children = array_filter($all_menus, function($m) use ($parent_id) {
                        return $m['parent_id'] == $parent_id;
                    });
                    
                    if (empty($children)) return;
                    
                    if ($parent_id === null) {
                        echo '<ul class="nav-links" id="navLinks">';
                    } else {
                        echo '<ul class="dropdown-menu">';
                    }
                    
                    $current_file = basename($_SERVER['PHP_SELF']);

                    foreach ($children as $child) {
                        $has_children = false;
                        foreach ($all_menus as $m) {
                            if ($m['parent_id'] == $child['id']) {
                                $has_children = true; break;
                            }
                        }
                        
                        $url = $child['url'];
                        if ($url === 'whatsapp_tanya') {
                            $url = 'https://api.whatsapp.com/send/?phone=' . get_setting('contact_wa') . '&text=Halo+Admin+Perpustakaan+PKTJ%2C+saya+ingin+bertanya+seputar+layanan+perpustakaan&type=phone_number&app_absent=0';
                        }
                        $url_safe = htmlspecialchars($url);
                        $title = htmlspecialchars($child['title']);
                        
                        // Check active status including children
                        $active_class = is_menu_active($child['id'], $all_menus, $current_file) ? 'active' : '';
                        
                        if ($parent_id === null) {
                            // Top level
                            if ($has_children) {
                                // For the INFORMASI menu, the original had 'publikasi-btn' but active should just be 'active'
                                $extra_class = ($title === 'INFORMASI') ? 'publikasi-btn ' : '';
                                echo '<li class="dropdown">';
                                echo '<a href="'.$url_safe.'" class="'.$extra_class.$active_class.'">'.$title.' <i class="fa-solid fa-chevron-down"></i></a>';
                                render_frontend_menu($child['id'], $all_menus);
                                echo '</li>';
                            } else {
                                echo '<li><a href="'.$url_safe.'" class="'.$active_class.'">'.$title.'</a></li>';
                            }
                        } else {
                            // Sub level
                            if ($has_children) {
                                echo '<li class="dropdown-submenu">';
                                echo '<a href="'.$url_safe.'">'.$title.' <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>';
                                render_frontend_menu($child['id'], $all_menus);
                                echo '</li>';
                            } else {
                                $target = (strpos($url, 'http') === 0) ? ' target="_blank" rel="noopener noreferrer"' : '';
                                echo '<li><a href="'.$url_safe.'"'.$target.'>'.$title.'</a></li>';
                            }
                        }
                    }
                    echo '</ul>';
                }
                
                render_frontend_menu(null, $all_menus);
                ?>
            </nav>
        </div>
    </header>
