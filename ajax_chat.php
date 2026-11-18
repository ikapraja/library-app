<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$message_raw = isset($input['message']) ? trim($input['message']) : '';
$message     = strtolower($message_raw);
$is_quick_reply = isset($input['is_quick_reply']) ? $input['is_quick_reply'] : false;
$faq_id      = isset($input['faq_id']) ? (int)$input['faq_id'] : 0;

if (empty($message) && !$is_quick_reply) {
    echo json_encode(['status' => 'success', 'answer' => 'Halo! Ada yang bisa Pustabots bantu?']);
    exit;
}

// Fetch WhatsApp Number from Settings
$wa_number = '';
$settings_res = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'contact_wa'");
if ($settings_res && $settings_res->num_rows > 0) {
    $wa_number = $settings_res->fetch_assoc()['setting_value'];
    $wa_number = preg_replace('/[^0-9]/', '', $wa_number);
    if (strpos($wa_number, '0') === 0) {
        $wa_number = '62' . substr($wa_number, 1);
    }
}

$response_text = "";
$found = false;

if ($is_quick_reply && $faq_id > 0) {
    // Quick reply clicked – fetch exact answer by ID
    $result = $conn->query("SELECT answer FROM chatbot_faq WHERE id = $faq_id");
    if ($result && $result->num_rows > 0) {
        $response_text = $result->fetch_assoc()['answer'];
        $found = true;
    }
} else {
    // =============================================
    // ALGORITMA PENCOCOKAN LEBIH CERDAS
    // =============================================
    // Bersihkan pesan: huruf kecil, hapus tanda baca
    $clean_message = preg_replace('/[^a-z0-9 ]/', ' ', $message);
    $clean_message = preg_replace('/\s+/', ' ', trim($clean_message));

    // Pecah menjadi token kata
    $msg_words = array_filter(explode(' ', $clean_message), function($w) {
        return strlen($w) >= 2;
    });
    $msg_words = array_values($msg_words);

    // Ambil semua FAQ dari database
    $result = $conn->query("SELECT id, question, keywords, answer FROM chatbot_faq");

    $best_score  = 0;
    $best_answer = "";
    $THRESHOLD   = 1; // Minimal skor 1 agar terjawab

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $score = 0;

            // --- Metode 1: Pencocokan kata kunci dari kolom keywords ---
            $raw_keywords = strtolower($row['keywords'] ?? '');
            $keyword_list = array_map('trim', explode(',', $raw_keywords));

            foreach ($keyword_list as $kw) {
                if (strlen($kw) < 2) continue;

                // Kata kunci adalah satu kata
                if (strpos($kw, ' ') === false) {
                    // Cocok substring di pesan (menangkap imbuhan: pinjam = meminjam, dipinjam, peminjaman)
                    if (strpos($clean_message, $kw) !== false) {
                        $score += 5;  // satu kata cocok
                    }
                } else {
                    // Kata kunci adalah frase multi-kata
                    $kw_words = array_filter(explode(' ', $kw), function($w){ return strlen($w) >= 2; });

                    $matched_in_phrase = 0;
                    foreach ($kw_words as $kw_w) {
                        if (strpos($clean_message, $kw_w) !== false) {
                            $matched_in_phrase++;
                        }
                    }
                    $kw_word_count = count($kw_words);
                    if ($kw_word_count > 0 && $matched_in_phrase === $kw_word_count) {
                        // Semua kata dalam frase cocok → skor tinggi (proporsional panjang frase)
                        $score += $kw_word_count * 10;
                    } elseif ($kw_word_count > 1 && $matched_in_phrase >= ceil($kw_word_count * 0.6)) {
                        // >=60% kata frase cocok → skor sedang
                        $score += $matched_in_phrase * 4;
                    }
                }
            }

            // --- Metode 2: Pencocokan langsung ke teks pertanyaan (question) ---
            // Ini membantu ketika admin belum memasang kata kunci lengkap
            $q_clean = preg_replace('/[^a-z0-9 ]/', ' ', strtolower($row['question'] ?? ''));
            $q_clean = preg_replace('/\s+/', ' ', trim($q_clean));
            $q_words = array_filter(explode(' ', $q_clean), function($w){ return strlen($w) >= 3; });

            $q_match = 0;
            foreach ($q_words as $qw) {
                if (strpos($clean_message, $qw) !== false) {
                    $q_match++;
                }
            }
            $q_word_count = count($q_words);
            if ($q_word_count > 0) {
                $q_ratio = $q_match / $q_word_count;
                if ($q_ratio >= 0.5) {
                    // Setidaknya 50% kata dari pertanyaan cocok → tambah skor
                    $score += (int)($q_match * 3);
                }
            }

            // --- Metode 3: Pencocokan kata pengguna ke kata kunci (reverse check) ---
            // Membantu ketika pengguna pakai variasi kata
            foreach ($msg_words as $mw) {
                if (strlen($mw) < 3) continue;
                foreach ($keyword_list as $kw) {
                    // Jika kata pengguna ada di dalam salah satu keyword, atau keyword ada di kata pengguna
                    if (strpos($kw, $mw) !== false || strpos($mw, $kw) !== false) {
                        $score += 2;
                    }
                }
            }

            if ($score > $best_score) {
                $best_score  = $score;
                $best_answer = $row['answer'];
            }
        }
    }

    if ($best_score >= $THRESHOLD) {
        $response_text = $best_answer;
        $found = true;
    }
}

if ($found) {
    echo json_encode(['status' => 'success', 'answer' => nl2br($response_text)]);
} else {
    // Fallback message
    $fallback = "Maaf, Pustabots belum mengerti pertanyaan Anda terkait <strong>\"" . htmlspecialchars($message_raw) . "\"</strong>.<br><br>Agar lebih jelas, silakan mengobrol langsung dengan Pustakawan kami melalui WhatsApp.";

    $wa_link = "";
    if (!empty($wa_number)) {
        $encoded_msg = urlencode("Halo Admin Perpustakaan PKTJ, saya ingin bertanya terkait: " . $message_raw);
        $wa_link = "<br><br><a href='https://wa.me/{$wa_number}?text={$encoded_msg}' target='_blank' style='display:inline-block; background:#25D366; color:white; padding:8px 15px; border-radius:20px; text-decoration:none; font-weight:bold; font-size:0.9em;'><i class='fa-brands fa-whatsapp'></i> Chat via WhatsApp</a>";
    }

    echo json_encode(['status' => 'success', 'answer' => $fallback . $wa_link]);
}
?>
