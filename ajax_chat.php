<?php
require_once 'db.php';

// Pastikan charset koneksi adalah UTF-8
if (isset($conn) && $conn instanceof mysqli) {
    @$conn->set_charset('utf8mb4');
}

header('Content-Type: application/json; charset=utf-8');

/**
 * Keluarkan output JSON yang selalu aman dari karakter malformed UTF-8
 */
function safe_json_output($data) {
    array_walk_recursive($data, function(&$item) {
        if (is_string($item)) {
            $item = str_replace(["\x95", "ÔÇó", "â€¢", "Â"], ["•", "•", "•", ""], $item);
            $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
        }
    });

    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    if ($json === false) {
        $json = json_encode([
            'status' => 'error',
            'answer' => 'Maaf, terjadi kendala saat menampilkan jawaban. Silakan coba sesaat lagi.'
        ]);
    }
    echo $json;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    safe_json_output(['status' => 'error', 'message' => 'Invalid request']);
}

$input = json_decode(file_get_contents('php://input'), true);
$message_raw = isset($input['message']) ? trim($input['message']) : '';
$message     = strtolower($message_raw);
$is_quick_reply = isset($input['is_quick_reply']) ? $input['is_quick_reply'] : false;
$faq_id      = isset($input['faq_id']) ? (int)$input['faq_id'] : 0;

if (empty($message) && !$is_quick_reply) {
    safe_json_output(['status' => 'success', 'answer' => 'Halo! Ada yang bisa Pustabots bantu?']);
}

/**
 * Panggil API Google Gemini (AI)
 */
function callGeminiAPI($apiKey, $userMessage, $model = 'gemini-3.5-flash-lite', $systemInstruction = '', &$debug = []) {
    if (empty($apiKey) || empty($userMessage)) return false;

    if (empty($systemInstruction)) {
        $systemInstruction = "Kamu adalah Pustabots, asisten kecerdasan buatan resmi dari Perpustakaan Politeknik Keselamatan Transportasi Jalan (PKTJ) Tegal (Kementerian Perhubungan RI).\n"
            . "Tugasmu membantu taruna, dosen, staf, dan pemustaka umum dengan ramah, santun, jelas, dan solutif.\n\n"
            . "Fakta Resmi Kampus & Perpustakaan:\n"
            . "- Direktur PKTJ saat ini: Bapak Dr. Bambang Istiyanto, S.Si.T., A.TD., M.T.\n"
            . "- Kepala Perpustakaan PKTJ saat ini: Ibu Supriatin, S.S.T.\n"
            . "- Lokasi Kampus: Kampus Margadana (Jl. Abdul Syukur No. 17) dan Kampus Perintis (Jl. Perintis Kemerdekaan No. 17, Tegal).\n"
            . "- Program Studi: D4 Manajemen Rekayasa Lalu Lintas (MRL), D4 Teknologi Rekayasa Otomotif (TRO), D3 Keselamatan Transportasi Jalan (KTJ).\n"
            . "- Jam Layanan: Senin - Kamis 08.00 - 16.00 WIB, Jumat 08.30 - 16.30 WIB.\n\n"
            . "Pedoman Hemat Token & Menjawab Efisien:\n"
            . "1. Berikan jawaban yang padat, ringkas, terstruktur (gunakan poin/bullet), dan langsung ke inti jawaban tanpa kalimat pengantar basa-basi bertele-tele.\n"
            . "2. Fokus utama: literasi, referensi akademik/skripsi, materi keselamatan transportasi jalan, rekayasa lalu lintas, otomotif, dan perkeretaapian.\n"
            . "3. Jika pertanyaan di luar konteks perpustakaan atau studi kampus PKTJ, tolak secara halus dan ringkas, lalu arahkan kembali ke topik perpustakaan.\n"
            . "4. Jika membutuhkan administrasi resmi (misal tanda tangan bebas pustaka fisik), arahkan ke WhatsApp resmi Pustakawan.";
    }

    // Daftar model yang sudah ditutup/deprecated oleh Google untuk pengguna baru
    $deprecated = ['gemini-1.5-flash', 'gemini-2.0-flash', 'gemini-2.5-flash', 'gemini-1.5-pro', 'gemini-2.5-pro'];
    if (in_array($model, $deprecated) || empty($model)) {
        $modelClean = 'gemini-3.5-flash-lite';
    } else {
        $modelClean = trim($model);
    }

    $modelsToTry = [$modelClean];
    if ($modelClean !== 'gemini-3.5-flash-lite') {
        $modelsToTry[] = 'gemini-3.5-flash-lite';
    }

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
            "maxOutputTokens" => 450
        ]
    ];
    $jsonPayload = json_encode($payload);

    foreach ($modelsToTry as $mTarget) {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/" . urlencode($mTarget) . ":generateContent?key=" . urlencode($apiKey);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
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

        $debug['last_model'] = $mTarget;
        $debug['http_code'] = $httpCode;
        if ($curlError) $debug['curl_error'] = $curlError;
        if ($httpCode === 429) {
            $debug['quota_exceeded'] = true;
        }

        if (!$curlError && $httpCode === 200 && $response) {
            $data = json_decode($response, true);
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return trim($data['candidates'][0]['content']['parts'][0]['text']);
            }
        }

        $debug['response_snippet'] = substr($response, 0, 150);
    }

    return false;
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

/**
 * Format markdown ringan pada teks FAQ (bold, italic) tanpa merusak tag HTML yang sudah ada
 */
function formatFaqText($text) {
    // 1. Ubah **teks** menjadi <strong>teks</strong>
    $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);
    
    // 2. Ubah *teks* menjadi <em>teks</em>
    $text = preg_replace('/(?<!\*)\*(?!\*)(.*?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $text);

    // 3. Newline ke <br>
    return nl2br($text);
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
    safe_json_output([
        'status' => 'success', 
        'answer' => formatFaqText($response_text),
        'source' => 'faq_database'
    ]);
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

$raw_enabled = trim($ai_cfg['gemini_ai_enabled'] ?? '0');
$ai_enabled = ($raw_enabled === '1' || $raw_enabled === 'true' || $raw_enabled === 'on');
$api_key = trim($ai_cfg['gemini_api_key'] ?? '');
$model = (!empty($ai_cfg['gemini_model']) && $ai_cfg['gemini_model'] !== 'gemini-1.5-flash') ? trim($ai_cfg['gemini_model']) : 'gemini-3.5-flash-lite';
$system_instruction = trim($ai_cfg['gemini_system_instruction'] ?? '');

$ai_debug = [
    'ai_enabled' => $ai_enabled,
    'raw_enabled_val' => $raw_enabled,
    'has_api_key' => !empty($api_key),
    'api_key_len' => strlen($api_key),
    'model' => $model
];

if ($ai_enabled && !empty($api_key)) {
    $ai_answer = callGeminiAPI($api_key, $message_raw, $model, $system_instruction, $ai_debug);
    if ($ai_answer !== false && !empty($ai_answer)) {
        $formatted_answer = formatAiMarkdown($ai_answer);
        $badge = '<div style="margin-top:10px; font-size:0.75rem; color:#888; border-top:1px dashed #e2e8f0; padding-top:6px;"><i class="fa-solid fa-wand-magic-sparkles" style="color:#f59e0b;"></i> Dijawab oleh AI Pustabots</div>';
        
        safe_json_output([
            'status' => 'success',
            'answer' => $formatted_answer . $badge,
            'source' => 'gemini_ai'
        ]);
    }
}

// =============================================
// JALUR 3: SMART FALLBACK (FAQ POPULER + WHATSAPP)
// =============================================
$is_quota_exceeded = !empty($ai_debug['quota_exceeded']) || (($ai_debug['http_code'] ?? 0) === 429);

// Ambil pertanyaan FAQ populer dari database untuk ditampilkan sebagai tombol langsung di bubble chat
$faq_suggestions_html = "";
$sug_res = $conn->query("SELECT id, question FROM chatbot_faq WHERE show_on_homepage = 1 ORDER BY id ASC LIMIT 4");
if ($sug_res && $sug_res->num_rows > 0) {
    $faq_buttons = "";
    while ($sr = $sug_res->fetch_assoc()) {
        $q_id = (int)$sr['id'];
        $q_text = htmlspecialchars($sr['question'], ENT_QUOTES, 'UTF-8');
        $q_js = addslashes($sr['question']);
        $faq_buttons .= "<button type=\"button\" onclick=\"sendQuickReply({$q_id}, '{$q_js}')\" style=\"width:100%; text-align:left; background:#ffffff; border:1px solid #bfdbfe; color:#1e40af; border-radius:10px; padding:7px 10px; font-size:0.83rem; cursor:pointer; margin-bottom:5px; transition:0.2s;\"><i class=\"fa-regular fa-circle-question\" style=\"color:#3b82f6; margin-right:5px;\"></i> {$q_text}</button>";
    }
    
    $faq_suggestions_html = "
    <div style=\"margin-top:12px; padding-top:10px; border-top:1px dashed #cbd5e1;\">
        <div style=\"font-size:0.82rem; font-weight:600; color:#475569; margin-bottom:8px;\">
            <i class=\"fa-solid fa-list-check\" style=\"color:#2563eb;\"></i> Pertanyaan Populer yang Sering Diajukan:
        </div>
        {$faq_buttons}
    </div>";
}

$wa_button = "";
if (!empty($wa_number)) {
    $encoded_msg = urlencode("Halo Admin Perpustakaan PKTJ, saya ingin bertanya terkait: " . $message_raw);
    $wa_button = "
    <div style=\"margin-top:10px;\">
        <a href=\"https://wa.me/{$wa_number}?text={$encoded_msg}\" target=\"_blank\" style=\"display:inline-flex; align-items:center; gap:6px; background:#25D366; color:white; padding:8px 15px; border-radius:20px; text-decoration:none; font-weight:bold; font-size:0.85rem; box-shadow:0 2px 5px rgba(37,211,102,0.3);\">
            <i class=\"fa-brands fa-whatsapp\" style=\"font-size:1.1rem;\"></i> Chat Pustakawan via WhatsApp
        </a>
    </div>";
}

if ($is_quota_exceeded) {
    $fallback = "Maaf, kuota harian asisten AI cerdas saat ini sedang mencapai batas maksimum atau dalam pemulihan.<br><br>Jangan khawatir, Anda dapat memilih topik pertanyaan populer di bawah ini, atau langsung mengobrol dengan Pustakawan kami:";
} else {
    $fallback = "Maaf, Pustabots belum menemukan jawaban otomatis untuk <strong>\"" . htmlspecialchars($message_raw) . "\"</strong>.<br><br>Anda dapat memilih topik bantuan perpustakaan di bawah ini, atau menanyakan langsung kepada Pustakawan kami:";
}

safe_json_output([
    'status' => 'success', 
    'answer' => $fallback . $faq_suggestions_html . $wa_button,
    'source' => $is_quota_exceeded ? 'quota_exceeded_fallback' : 'fallback_wa',
    'debug' => $ai_debug
]);
?>