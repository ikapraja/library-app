<?php
require_once '../db.php';
session_start();

// Simple auth check
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['menu_data'])) {
    $menu_data = json_decode($_POST['menu_data'], true);
    
    if (is_array($menu_data)) {
        // Function to recursively update menu positions
        function updateMenuHierarchy($items, $parent_id = "NULL") {
            global $conn;
            $order = 10; // Start order at 10, increment by 10
            
            foreach ($items as $item) {
                $id = intval($item['id']);
                
                // Update this item's parent and order
                $sql = "UPDATE menus SET parent_id = $parent_id, order_num = $order WHERE id = $id";
                $conn->query($sql);
                
                // If this item has children, recursively update them with this item's ID as parent
                if (isset($item['children']) && is_array($item['children'])) {
                    updateMenuHierarchy($item['children'], $id);
                }
                
                $order += 10;
            }
        }
        
        // Start the recursive update from the root level
        updateMenuHierarchy($menu_data);
        
        echo json_encode(['status' => 'success', 'message' => 'Susunan menu berhasil disimpan.']);
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Data tidak valid.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
}
?>
