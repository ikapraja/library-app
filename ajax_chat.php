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

/**
 * Panggil API Google Gemini (AI)
 */
function callGeminiAPI($apiKey, $userMessage, $model = 'gemini-3.5-flash-lite', $systemInstruction = '') {
    if (empty($apiKey) || empty($userMessage)) return false;

    if (empty($systemInstruction)) {
        $systemInstruction = "Kamu adalah Pustabots, asisten kecerdasan buatan resmi dari Perpustakaan Politeknik Keselamatan Transportasi Jalan (PKTJ) Tegal (Kementerian Perhubungan RI).\n"
            . "Tugasmu membantu taruna, dosen, staf, dan pemustaka umum dengan ramah, santun, jelas, dan solutif.\n"
            . "Fokus: literasi, layanan perpustakaan, pencarian referensi/skripsi, materi keselamatan jalan, rekayasa lalu lintas, otomotif, dan perkeretaapian.\n"
            . "Jika ditanya hal di luar konteks perpustakaan atau studi kampus PKTJ, tolak dengan sopan dan kembalikan ke topik perpustakaan.\n"
            . "Bila membutuhkan administrasi resmi (tanda tangan berkas bebas pustaka, dll), arahkan ke WhatsApp resmi Pustakawan.\n"
            . "Berikan jawaban ringkas, informatif, dan terstruktur rapi (gunakan bullet/poin bila perlu).";
    }

    $modelClean = (!empty($model) && $model !== 'gemini-1.5-flash' && $model !== 'gemini-2.0-flash') ? trim($model) : 'gemini-3.5-flash-lite';
    $url = "https://generativelanguage.googleapis.com/v1beta/models/" . urlencode($modelClean) . ":generateContent?key=" . urlencode($apiKey);

    $payload = [
        "contents" => [
            [
                "role" => "user",
                "parts" => [
                    ["text" => $userMessage]
                ]
            ]
        ],
        "system_instruction" => [
            "parts" => [
                ["text" => $systemInstruction]
            ]
        ],
        "generationConfig" => [
            "temperature" => 0.7,
            "maxOutputTokens" => 800
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError || $httpCode !== 200 || !$response) {
        return false;
    }

    $data = json_decode($response, true);
    if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        return false;
    }

    return trim($data['candidates'][0]['content']['parts'][0]['text']);
}

/**
 * Format markdown teks dari Gemini menjadi HTML yang rapi & aman
 */
function formatAiMarkdown($text) {
    // 1. Escape HTML dasar untuk keamanan
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    
    // 2. Bold: **teks**
    $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);
    
    // 3. Italic: *teks*
    $text = preg_replace('/(?<!\*)\*(?!\*)(.*?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $text);
    
    // 4. Inline code: `code`
    $text = preg_replace('/`([^`]+)`/', '<code style="background:#edf2f7; padding:2px 5px; border-radius:3px; font-size:0.9em;">$1</code>', $text);
    
    // 5. Bullet list: baris diawali dengan * atau -
    $text = preg_replace('/^[ \t]*[\*\-][ \t]+(.+)$/m', '<div style="margin-left:10px; margin-bottom:4px;">• $1</div>', $text);
    
    // 6. Numbered list: baris diawali dengan 1. 2. dst
    $text = preg_replace('/^[ \t]*(\d+)\.[ \t]+(.+)$/m', '<div style="margin-left:10px; margin-bottom:4px;"><strong>$1.</strong> $2</div>', $text);
    
    // 7. Ubah newline menjadi <br>
    $text = nl2br($text);
    
    // 8. Rapikan break berlebih setelah div
    $text = str_replace(["</div><br />", "</div><br>"], "</div>", $text);
    
    return $text;
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

// =============================================
// JALUR 1: TOMBOL PILIHAN CEPAT (QUICK REPLY)
// =============================================
if ($is_quick_reply && $faq_id > 0) {
    // Quick reply clicked – fetch exact answer by ID
    $result = $conn->query("SELECT answer FROM chatbot_faq WHERE id = $faq_id");
    if ($result && $result->num_rows > 0) {
        $response_text = $result->fetch_assoc()['answer'];
        $found = true;
    }
} else {
    // =============================================
    // JALUR 1B: ALGORITMA PENCOCOKAN FAQ INTERNAL
    // =============================================
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
    $THRESHOLD   = 6; // Minimal skor 6 agar terjawab oleh FAQ internal

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
                    // Gunakan batas kata (word boundary) dan imbuhan bahasa Indonesia
                    // agar tidak salah mencocokkan substring (contoh: 'neliti' di dalam kata 'penelitian')
                    $special_exact = ['neliti', 'opac', 'garuda', 'perpusnas', 'moraref', 'turnitin', 'kkw', 'ojs', 'buku'];
                    if (in_array($kw, $special_exact)) {
                        $kw_pattern = '/\b' . preg_quote($kw, '/') . '\b/i';
                    } else {
                        $kw_pattern = '/\b(me|di|pe|ter|ber)?' . preg_quote($kw, '/') . '(an|i|kan|nya)?\b/i';
                    }

                    if (preg_match($kw_pattern, $clean_message)) {
                        $score += 6;
                    }
                } else {
                    // Kata kunci adalah frase multi-kata
                    $kw_words = array_filter(explode(' ', $kw), function($w){ return strlen($w) >= 2; });

                    $matched_in_phrase = 0;
                    foreach ($kw_words as $kw_w) {
                        if (preg_match('/\b' . preg_quote($kw_w, '/') . '\b/i', $clean_message)) {
                            $matched_in_phrase++;
                        }
                    }
                    $kw_word_count = count($kw_words);
                    if ($kw_word_count > 0 && $matched_in_phrase === $kw_word_count) {
                        // Semua kata dalam frase cocok → skor tinggi
                        $score += $kw_word_count * 10;
                    } elseif ($kw_word_count > 1 && $matched_in_phrase >= ceil($kw_word_count * 0.7)) {
                        // >=70% kata frase cocok → skor sedang
                        $score += $matched_in_phrase * 4;
                    }
                }
            }

            // --- Metode 2: Pencocokan langsung ke teks pertanyaan (question) ---
            $q_clean = preg_replace('/[^a-z0-9 ]/', ' ', strtolower($row['question'] ?? ''));
            $q_clean = preg_replace('/\s+/', ' ', trim($q_clean));
            $q_words = array_filter(explode(' ', $q_clean), function($w){ return strlen($w) >= 3; });

            $q_match = 0;
            foreach ($q_words as $qw) {
                if (preg_match('/\b' . preg_quote($qw, '/') . '\b/i', $clean_message)) {
                    $q_match++;
                }
            }
            $q_word_count = count($q_words);
            if ($q_word_count > 0) {
                $q_ratio = $q_match / $q_word_count;
                if ($q_ratio >= 0.5) {
                    $score += (int)($q_match * 3);
                }
            }

            // --- Metode 3: Pencocokan kata pengguna ke kata kunci (exact token match) ---
            foreach ($msg_words as $mw) {
                if (strlen($mw) < 3) continue;
                foreach ($keyword_list as $kw) {
                    if ($kw === $mw) {
                        $score += 3;
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

// Jika jawaban ditemukan dari FAQ resmi kampus
if ($found) {
    echo json_encode([
        'status' => 'success', 
        'answer' => nl2br($response_text),
        'source' => 'faq_database'
    ]);
    exit;
}

// =============================================
// JALUR 2: OTAK AI (GOOGLE GEMINI)
// =============================================
$ai_settings_res = $conn->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('gemini_ai_enabled', 'gemini_api_key', 'gemini_model', 'gemini_system_instruction')");
$ai_cfg = [];
if ($ai_settings_res && $ai_settings_res->num_rows > 0) {
    while ($r = $ai_settings_res->fetch_assoc()) {
        $ai_cfg[$r['setting_key']] = $r['setting_value'];
    }
}

$ai_enabled = ($ai_cfg['gemini_ai_enabled'] ?? '0') === '1';
$api_key = trim($ai_cfg['gemini_api_key'] ?? '');
$model = (!empty($ai_cfg['gemini_model']) && $ai_cfg['gemini_model'] !== 'gemini-1.5-flash') ? trim($ai_cfg['gemini_model']) : 'gemini-3.5-flash-lite';
$system_instruction = trim($ai_cfg['gemini_system_instruction'] ?? '');

if ($ai_enabled && !empty($api_key)) {
    $ai_answer = callGeminiAPI($api_key, $message_raw, $model, $system_instruction);
    if ($ai_answer !== false && !empty($ai_answer)) {
        $formatted_answer = formatAiMarkdown($ai_answer);
        $badge = '<div style="margin-top:10px; font-size:0.75rem; color:#888; border-top:1px dashed #e2e8f0; padding-top:6px;"><i class="fa-solid fa-wand-magic-sparkles" style="color:#f59e0b;"></i> Dijawab oleh AI Pustabots</div>';
        
        echo json_encode([
            'status' => 'success',
            'answer' => $formatted_answer . $badge,
            'source' => 'gemini_ai'
        ]);
        exit;
    }
}

// =============================================
// JALUR 3: WHATSAPP ADMIN (FALLBACK)
// =============================================
$fallback = "Maaf, Pustabots belum mengerti pertanyaan Anda terkait <strong>\"" . htmlspecialchars($message_raw) . "\"</strong>.<br><br>Agar lebih jelas, silakan mengobrol langsung dengan Pustakawan kami melalui WhatsApp.";

$wa_link = "";
if (!empty($wa_number)) {
    $encoded_msg = urlencode("Halo Admin Perpustakaan PKTJ, saya ingin bertanya terkait: " . $message_raw);
    $wa_link = "<br><br><a href='https://wa.me/{$wa_number}?text={$encoded_msg}' target='_blank' style='display:inline-block; background:#25D366; color:white; padding:8px 15px; border-radius:20px; text-decoration:none; font-weight:bold; font-size:0.9em;'><i class='fa-brands fa-whatsapp'></i> Chat via WhatsApp</a>";
}

echo json_encode([
    'status' => 'success', 
    'answer' => $fallback . $wa_link,
    'source' => 'fallback_wa'
]);
?>