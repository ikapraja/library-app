<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$message = isset($input['message']) ? trim(strtolower($input['message'])) : '';
$is_quick_reply = isset($input['is_quick_reply']) ? $input['is_quick_reply'] : false;
$faq_id = isset($input['faq_id']) ? (int)$input['faq_id'] : 0;

if (empty($message) && !$is_quick_reply) {
    echo json_encode(['status' => 'success', 'answer' => 'Halo! Ada yang bisa Pustabots bantu?']);
    exit;
}

// Fetch WhatsApp Number from Settings
$wa_number = '';
$settings_res = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'contact_wa'");
if ($settings_res && $settings_res->num_rows > 0) {
    $wa_number = $settings_res->fetch_assoc()['setting_value'];
    // Format WA number to international format (remove leading 0 or +)
    $wa_number = preg_replace('/[^0-9]/', '', $wa_number);
    if (strpos($wa_number, '0') === 0) {
        $wa_number = '62' . substr($wa_number, 1);
    }
}

$response_text = "";
$found = false;

if ($is_quick_reply && $faq_id > 0) {
    // Quick reply clicked, fetch exact answer by ID
    $sql = "SELECT answer FROM chatbot_faq WHERE id = $faq_id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $response_text = $result->fetch_assoc()['answer'];
        $found = true;
    }
} else {
    // Keyword matching
    // Tokenize user message
    $words = explode(' ', preg_replace('/[^a-z0-9 ]/', '', $message));
    
    // Fetch all FAQs
    $sql = "SELECT id, keywords, answer FROM chatbot_faq";
    $result = $conn->query($sql);
    
    $best_match_score = 0;
    $best_match_answer = "";
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $db_keywords = explode(',', strtolower($row['keywords']));
            $db_keywords = array_map('trim', $db_keywords);
            
            $score = 0;
            foreach ($db_keywords as $dk) {
                if (strlen($dk) > 2) {
                    $dk_words = explode(' ', $dk);
                    $all_words_exist = true;
                    
                    foreach ($dk_words as $w) {
                        if (strlen($w) > 2) {
                            // Menggunakan strpos agar mendeteksi imbuhan bahasa Indonesia (misal: pinjam = meminjam)
                            if (strpos($message, $w) === false) {
                                $all_words_exist = false;
                                break;
                            }
                        }
                    }
                    
                    if ($all_words_exist) {
                        // Frase dengan kata lebih banyak mendapat bobot skor sangat tinggi (prioritas)
                        $score += count($dk_words) * 10;
                    }
                }
            }
            
            if ($score > $best_match_score) {
                $best_match_score = $score;
                $best_match_answer = $row['answer'];
            }
        }
    }
    
    if ($best_match_score > 0) {
        $response_text = $best_match_answer;
        $found = true;
    }
}

if ($found) {
    echo json_encode(['status' => 'success', 'answer' => nl2br($response_text)]);
} else {
    // Fallback message
    $fallback = "Maaf, Pustabots belum mengerti pertanyaan Anda terkait <strong>\"".htmlspecialchars($message)."\"</strong>.<br><br>Agar lebih jelas, silakan mengobrol langsung dengan Pustakawan kami melalui WhatsApp.";
    
    $wa_link = "";
    if (!empty($wa_number)) {
        $encoded_msg = urlencode("Halo Admin Perpustakaan PKTJ, saya ingin bertanya terkait: " . $message);
        $wa_link = "<br><br><a href='https://wa.me/{$wa_number}?text={$encoded_msg}' target='_blank' style='display:inline-block; background:#25D366; color:white; padding:8px 15px; border-radius:20px; text-decoration:none; font-weight:bold; font-size:0.9em;'><i class='fa-brands fa-whatsapp'></i> Chat via WhatsApp</a>";
    }
    
    echo json_encode(['status' => 'success', 'answer' => $fallback . $wa_link]);
}
?>
