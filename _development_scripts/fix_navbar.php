<?php
$correct_nav = <<<EOD
            <nav class="main-nav">
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <ul class="nav-links" id="navLinks">
                    <li><a href="index">BERANDA</a></li>
                    
                    <li class="dropdown">
                        <a href="#">PROFIL <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="visi-misi">Visi dan Misi</a></li>
                            <li><a href="struktur-organisasi">Struktur Organisasi</a></li>
                            <li><a href="fasilitas">Fasilitas</a></li>
                            <li><a href="tata-tertib">Tata Tertib</a></li>
                        </ul>
                    </li>
                    
                    <li class="dropdown">
                        <a href="#">LAYANAN <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="layanan-sirkulasi">Layanan Sirkulasi</a></li>
                            <li><a href="layanan-referensi">Layanan Referensi & Serial</a></li>
                            <li><a href="bebas-pustaka">Layanan Bebas Pustaka</a></li>
                            <li><a href="https://forms.gle/Hzaqgq56KghfYmMZ9" target="_blank">Layanan Penyediaan Repository</a></li>
                            <li><a href="https://forms.gle/i1RCTshjuUq1tfEn8" target="_blank">Layanan Delivery Book</a></li>
                            <li><a href="https://forms.gle/C9wEbt492ppe2Fkj7" target="_blank">Layanan Library Class</a></li>
                        </ul>
                    </li>
                    
                    <li class="dropdown">
                        <a href="#">RESOURCES <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-submenu">
                                <a href="#">E-Journal & Database <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-submenu">
                                        <a href="#">Jurnal PKTJ <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="https://ktj.pktj.ac.id/index.php/ktj" target="_blank">Jurnal Keselamatan Transportasi Jalan</a></li>
                                            <li><a href="https://ktj.pktj.ac.id/index.php/jat" target="_blank">Jurnal Abdimas Transjaya</a></li>
                                            <li><a href="https://ejournal.pktj.ac.id/index.php/perintis/beranda" target="_blank">Jurnal Perintis</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a href="#">Jurnal Nasional & Internasional <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-submenu">
                                                <a href="#">Jurnal Internasional (OA) <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="https://www.emerald.com/insight/" target="_blank">Emerald</a></li>
                                                    <li><a href="https://ieeexplore.ieee.org/Xplore/home.jsp" target="_blank">IEEE</a></li>
                                                    <li><a href="https://academic.oup.com/" target="_blank">Oxford Academic</a></li>
                                                    <li><a href="https://www.scirp.org/index" target="_blank">Scientific Research</a></li>
                                                    <li><a href="https://www.springernature.com/gp" target="_blank">Springer Nature</a></li>
                                                    <li><a href="https://onlinelibrary.wiley.com/" target="_blank">Wiley</a></li>
                                                    <li><a href="https://authorservices.taylorandfrancis.com/choose-open/publishing-open-access/open-access-cost-finder/?category=all&journal=all&fulloa=1&openselect=0&notavailable=0&dove=1&routledge=1&tandf=1&numberofresultsperpage=5&pagenumber=1" target="_blank">Taylor & Francis</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown-submenu">
                                                <a href="#">Jurnal Nasional (OA) <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="https://garuda.kemdiktisaintek.go.id/" target="_blank">Garuda</a></li>
                                                    <li><a href="https://sinta.kemdiktisaintek.go.id/journals" target="_blank">Sinta</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a href="#">Database <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="https://link.gale.com/apps/PPGS?u=fjatsci" target="_blank">Gale: Science</a></li>
                                            <li><a href="https://link.gale.com/apps/SPJ.SP00?u=fjathum" target="_blank">Gale: Humanities</a></li>
                                            <li><a href="https://link.gale.com/apps/ITBC?u=fjatbus" target="_blank">Gale: Business</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a href="#">E-Book <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="https://opac.pktj.ac.id/index.php?title=&author=&subject=&isbn=&colltype=E-book&location=0&gmd=0&search=search" target="_blank">E-Book OPAC PKTJ</a></li>
                                    <li><a href="https://pktj.perpustakaan.co.id/home.ks" target="_blank">E-Book Digido</a></li>
                                    <li><a href="https://www.doabooks.org/en" target="_blank">DOAB</a></li>
                                    <li><a href="https://www.gutenberg.org/" target="_blank">Project Gutenberg</a></li>
                                    <li><a href="https://openlibrary.org/" target="_blank">Open Library</a></li>
                                    <li><a href="https://oapen.org/" target="_blank">OAPEN</a></li>
                                    <li><a href="https://www.intechopen.com/" target="_blank">Intechopen</a></li>
                                    <li><a href="https://www.taylorfrancis.com/search?sortBy=relevance&key=&openAccess=true&fullyOABook=true&OAChapter=true" target="_blank">Taylor & Francis</a></li>
                                </ul>
                            </li>
                            <li><a href="http://eprints.pktj.ac.id/" target="_blank">Repositori Institusi</a></li>
                            <li class="dropdown-submenu">
                                <a href="#">Jejaring & Mitra <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="https://www.perpusnas.go.id/layanan" target="_blank">Perpusnas</a></li>
                                    <li><a href="https://fppti-jateng.or.id/" target="_blank">FPPTI Jawa Tengah</a></li>
                                    <li><a href="https://elibrary.dephub.go.id/" target="_blank">Integrated Transport Resource Catalog (Perpustakaan Kemenhub)</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="dropdown">
                        <a href="#">RUANG PEMUSTAKA <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="https://forms.gle/UGY3949cTwuh4Egz8" target="_blank" rel="noopener noreferrer">Survei Kebutuhan Pemustaka</a></li>
                            <li><a href="https://forms.gle/H5XaT3Hr3GFV4Vjb9" target="_blank" rel="noopener noreferrer">Survei Kepuasan Pemustaka</a></li>
                            <li><a href="https://forms.gle/NRymXWjqsyBeYa469" target="_blank" rel="noopener noreferrer">Peminjaman Ruang</a></li>
                            <li><a href="https://api.whatsapp.com/send/?phone=6281292760076&text=Halo+Admin+Perpustakaan+PKTJ%2C+saya+ingin+bertanya+seputar+layanan+perpustakaan&type=phone_number&app_absent=0" target="_blank" rel="noopener noreferrer">Tanya Pustakawan</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="berita">BERITA</a></li>
                </ul>
            </nav>
EOD;

$files = ['index.php', 'bebas-pustaka.php', 'berita.php', 'detail-berita.php', 'fasilitas.php', 'layanan-referensi.php', 'layanan-sirkulasi.php', 'struktur-organisasi.php', 'tata-tertib.php', 'visi-misi.php'];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // Add active class for index.php or berita.php
        $nav_to_insert = $correct_nav;
        if ($file === 'index.php') {
            $nav_to_insert = str_replace('<a href="index">BERANDA</a>', '<a href="index" class="active">BERANDA</a>', $nav_to_insert);
        } else if ($file === 'berita.php') {
            $nav_to_insert = str_replace('<a href="berita">BERITA</a>', '<a href="berita" class="active">BERITA</a>', $nav_to_insert);
        }
        
        $content = preg_replace('/<nav class="main-nav">.*?<\/nav>/s', $nav_to_insert, $content);
        file_put_contents($file, $content);
        echo "Fixed nav in $file\n";
    }
}
?>
