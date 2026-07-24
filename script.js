document.addEventListener('DOMContentLoaded', () => {
    
    // --- Mobile Menu Toggle ---
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.getElementById('navLinks');
    
    if (mobileMenuBtn && navLinks) {
        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('show');
        });
    }

    // --- Mobile Dropdown Toggle ---
    const dropdowns = document.querySelectorAll('.dropdown, .dropdown-submenu');
    dropdowns.forEach(dropdown => {
        const link = dropdown.children[0]; // Only get the direct child link
        if (link && link.tagName === 'A') {
            link.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    e.stopPropagation(); // Prevent bubbling to parent dropdowns
                    dropdown.classList.toggle('active');
                }
            });
        }
    });

    // --- Search Tab Switching ---
    window.switchSearch = function(type) {
        const tabs = document.querySelectorAll('.tab-btn');
        const formBuku = document.getElementById('formBuku');
        const formRepo = document.getElementById('formRepo');
        const formEjournal = document.getElementById('formEjournal');
        const formEbook = document.getElementById('formEbook');
        
        tabs.forEach(tab => tab.classList.remove('active'));
        formBuku.style.display = 'none';
        formRepo.style.display = 'none';
        formEjournal.style.display = 'none';
        formEbook.style.display = 'none';
        
        if (type === 'buku') {
            tabs[0].classList.add('active');
            formBuku.style.display = 'block';
        } else if (type === 'repositori') {
            tabs[1].classList.add('active');
            formRepo.style.display = 'block';
        } else if (type === 'ejournal') {
            tabs[2].classList.add('active');
            formEjournal.style.display = 'block';
        } else if (type === 'ebook') {
            tabs[3].classList.add('active');
            formEbook.style.display = 'block';
        }
    };

    // --- FAQ Accordion ---
    const accordionBtns = document.querySelectorAll('.accordion-btn');
    
    accordionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const item = this.parentElement;
            const content = this.nextElementSibling;
            
            // Close other items
            document.querySelectorAll('.accordion-item').forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                    otherItem.querySelector('.accordion-content').style.maxHeight = null;
                }
            });
            
            // Toggle current item
            item.classList.toggle('active');
            
            if (item.classList.contains('active')) {
                content.style.maxHeight = content.scrollHeight + "px";
            } else {
                content.style.maxHeight = null;
            }
        });
    });

    // --- Navbar Scroll Effect ---
    const header = document.getElementById('header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.style.backgroundColor = 'rgba(30, 58, 138, 0.95)';
            header.style.backdropFilter = 'blur(10px)';
            header.style.boxShadow = '0 4px 15px rgba(0,0,0,0.2)';
        } else {
            header.style.backgroundColor = '#1E3A8A';
            header.style.backdropFilter = 'none';
            header.style.boxShadow = 'var(--shadow-md)';
        }
    });
});
