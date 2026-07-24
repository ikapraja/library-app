<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// TRUNCATE table to start fresh
$conn->query("TRUNCATE TABLE menus");

// Helper to insert menu and return ID
function insert_menu($conn, $parent_id, $title, $url, $order) {
    $p = $parent_id === null ? "NULL" : $parent_id;
    $t = $conn->real_escape_string($title);
    $u = $conn->real_escape_string($url);
    $conn->query("INSERT INTO menus (parent_id, title, url, order_num) VALUES ($p, '$t', '$u', $order)");
    return $conn->insert_id;
}

// 1. BERANDA
insert_menu($conn, null, 'BERANDA', 'index.php', 10);

// 2. PROFIL
$profil_id = insert_menu($conn, null, 'PROFIL', '#', 20);
insert_menu($conn, $profil_id, 'Visi dan Misi', 'visi-misi.php', 10);
insert_menu($conn, $profil_id, 'Struktur Organisasi', 'struktur-organisasi.php', 20);
insert_menu($conn, $profil_id, 'Fasilitas', 'fasilitas.php', 30);
insert_menu($conn, $profil_id, 'Tata Tertib', 'tata-tertib.php', 40);

// 3. LAYANAN
$lay_id = insert_menu($conn, null, 'LAYANAN', '#', 30);
insert_menu($conn, $lay_id, 'Layanan Sirkulasi', 'layanan-sirkulasi.php', 10);
insert_menu($conn, $lay_id, 'Layanan Referensi & Serial', 'layanan-referensi.php', 20);
insert_menu($conn, $lay_id, 'Layanan Bebas Pustaka', 'bebas-pustaka.php', 30);
insert_menu($conn, $lay_id, 'Layanan Penyediaan Repository', 'https://forms.gle/Hzaqgq56KghfYmMZ9', 40);
insert_menu($conn, $lay_id, 'Layanan Delivery Book', 'https://forms.gle/i1RCTshjuUq1tfEn8', 50);
insert_menu($conn, $lay_id, 'Layanan Library Class', 'https://forms.gle/C9wEbt492ppe2Fkj7', 60);

// 4. RESOURCES
$res_id = insert_menu($conn, null, 'RESOURCES', '#', 40);
    // E-Journal & Database
    $ejournal_id = insert_menu($conn, $res_id, 'E-Journal & Database', '#', 10);
        // Jurnal PKTJ
        $pktj_id = insert_menu($conn, $ejournal_id, 'Jurnal PKTJ', '#', 10);
        insert_menu($conn, $pktj_id, 'Jurnal Keselamatan Transportasi Jalan', 'https://ktj.pktj.ac.id/index.php/ktj', 10);
        insert_menu($conn, $pktj_id, 'Jurnal Abdimas Transjaya', 'https://ktj.pktj.ac.id/index.php/jat', 20);
        insert_menu($conn, $pktj_id, 'Jurnal Perintis', 'https://ejournal.pktj.ac.id/index.php/perintis/beranda', 30);
        
        // Jurnal Nasional & Internasional
        $ni_id = insert_menu($conn, $ejournal_id, 'Jurnal Nasional & Internasional', '#', 20);
            // Internasional
            $inter_id = insert_menu($conn, $ni_id, 'Jurnal Internasional (OA)', '#', 10);
            insert_menu($conn, $inter_id, 'Emerald', 'https://www.emerald.com/insight/', 10);
            insert_menu($conn, $inter_id, 'IEEE', 'https://ieeexplore.ieee.org/Xplore/home.jsp', 20);
            insert_menu($conn, $inter_id, 'Oxford Academic', 'https://academic.oup.com/', 30);
            insert_menu($conn, $inter_id, 'Scientific Research', 'https://www.scirp.org/index', 40);
            insert_menu($conn, $inter_id, 'Springer Nature', 'https://www.springernature.com/gp', 50);
            insert_menu($conn, $inter_id, 'Wiley', 'https://onlinelibrary.wiley.com/', 60);
            insert_menu($conn, $inter_id, 'Taylor & Francis', 'https://authorservices.taylorandfrancis.com/choose-open/publishing-open-access/open-access-cost-finder/?category=all&journal=all&fulloa=1&openselect=0&notavailable=0&dove=1&routledge=1&tandf=1&numberofresultsperpage=5&pagenumber=1', 70);
            
            // Nasional
            $nas_id = insert_menu($conn, $ni_id, 'Jurnal Nasional (OA)', '#', 20);
            insert_menu($conn, $nas_id, 'Garuda', 'https://garuda.kemdiktisaintek.go.id/', 10);
            insert_menu($conn, $nas_id, 'Sinta', 'https://sinta.kemdiktisaintek.go.id/journals', 20);
            
        // Database
        $db_id = insert_menu($conn, $ejournal_id, 'Database', '#', 30);
        insert_menu($conn, $db_id, 'Gale: Science', 'https://link.gale.com/apps/PPGS?u=fjatsci', 10);
        insert_menu($conn, $db_id, 'Gale: Humanities', 'https://link.gale.com/apps/SPJ.SP00?u=fjathum', 20);
        insert_menu($conn, $db_id, 'Gale: Business', 'https://link.gale.com/apps/ITBC?u=fjatbus', 30);
        
    // E-Book
    $ebook_id = insert_menu($conn, $res_id, 'E-Book', '#', 20);
    insert_menu($conn, $ebook_id, 'E-Book OPAC PKTJ', 'https://opac.pktj.ac.id/index.php?title=&author=&subject=&isbn=&colltype=E-book&location=0&gmd=0&search=search', 10);
    insert_menu($conn, $ebook_id, 'E-Book Digido', 'https://pktj.perpustakaan.co.id/home.ks', 20);
    insert_menu($conn, $ebook_id, 'DOAB', 'https://www.doabooks.org/en', 30);
    insert_menu($conn, $ebook_id, 'Project Gutenberg', 'https://www.gutenberg.org/', 40);
    insert_menu($conn, $ebook_id, 'Open Library', 'https://openlibrary.org/', 50);
    insert_menu($conn, $ebook_id, 'OAPEN', 'https://oapen.org/', 60);
    insert_menu($conn, $ebook_id, 'Intechopen', 'https://www.intechopen.com/', 70);
    insert_menu($conn, $ebook_id, 'Taylor & Francis', 'https://www.taylorfrancis.com/search?sortBy=relevance&key=&openAccess=true&fullyOABook=true&OAChapter=true', 80);
    
    // Repositori Institusi
    insert_menu($conn, $res_id, 'Repositori Institusi', 'http://eprints.pktj.ac.id/', 30);
    
    // Jejaring & Mitra
    $mitra_id = insert_menu($conn, $res_id, 'Jejaring & Mitra', '#', 40);
    insert_menu($conn, $mitra_id, 'Perpusnas', 'https://www.perpusnas.go.id/layanan', 10);
    insert_menu($conn, $mitra_id, 'FPPTI Jawa Tengah', 'https://fppti-jateng.or.id/', 20);
    insert_menu($conn, $mitra_id, 'Integrated Transport Resource Catalog (Perpustakaan Kemenhub)', 'https://elibrary.dephub.go.id/', 30);

// 5. RUANG PEMUSTAKA
$rp_id = insert_menu($conn, null, 'RUANG PEMUSTAKA', '#', 50);
insert_menu($conn, $rp_id, 'Survei Kebutuhan Pemustaka', 'https://forms.gle/UGY3949cTwuh4Egz8', 10);
insert_menu($conn, $rp_id, 'Survei Kepuasan Pemustaka', 'https://forms.gle/H5XaT3Hr3GFV4Vjb9', 20);
insert_menu($conn, $rp_id, 'Peminjaman Ruang', 'https://forms.gle/NRymXWjqsyBeYa469', 30);
insert_menu($conn, $rp_id, 'Tanya Pustakawan', 'whatsapp_tanya', 40);

// 6. INFORMASI
$info_id = insert_menu($conn, null, 'INFORMASI', '#', 60);
insert_menu($conn, $info_id, 'Berita', 'berita.php', 10);
insert_menu($conn, $info_id, 'Galeri', 'galeri.php', 20);

echo "Database re-seeded successfully!";
$conn->close();
?>
