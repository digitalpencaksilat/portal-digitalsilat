<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Pencak Silat - Event List</title>

    <!-- Assets -->
    <link rel="shortcut icon" href="<?= base_url('assets/logo/logo.ico'); ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/variables.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/main-style.css'); ?>">
</head>

<body id="home">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="<?= base_url('assets/logo/logo.png'); ?>" alt="Logo Brand" class="img-fluid">
                DIGITAL PENCAK SILAT
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#heroCarousel">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="#events">Jadwal Event</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Kontak</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Carousel -->
    <header id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="<?= base_url('assets/carousel/carousel-1.jpg'); ?>" class="d-block w-100" alt="Silat 1">
                <div class="carousel-caption">
                    <h1 class="hero-title">BANGKITKAN JIWA <br><span class="text-highlight">PENDEKAR</span></h1>
                    <p class="lead">Platform informasi terpusat event Pencak Silat Indonesia dengan Digital Pencak Silat.</p>
                    <a href="#events" class="btn btn-brand">Lihat Jadwal</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="<?= base_url('assets/carousel/carousel-2.jpg'); ?>" class="d-block w-100" alt="Silat 2">
                <div class="carousel-caption">
                    <h1 class="hero-title">LESTARIKAN <br><span class="text-highlight">BUDAYA BANGSA</span></h1>
                    <p class="lead">Dari tradisi menuju prestasi dunia.</p>
                    <a href="#about" class="btn btn-brand">Tentang Kami</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="<?= base_url('assets/carousel/carousel-3.jpg'); ?>" class="d-block w-100" alt="Silat 3">
                <div class="carousel-caption">
                    <h1 class="hero-title">RAIH <br><span class="text-highlight">PRESTASI TERTINGGI</span></h1>
                    <p class="lead">Daftarkan perguruanmu di kejuaraan bergengsi.</p>
                    <a href="#contact" class="btn btn-brand">Kontak Kami</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </header>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-lg-2">
                    <div class="about-image-stack">
                        <!-- Update Image to carousel-4.jpg -->
                        <img src="<?= base_url('assets/carousel/carousel-4.jpg'); ?>" alt="Tentang Kami" class="about-img img-fluid" onerror="this.src='https://images.unsplash.com/photo-1599058945522-28d584b6f0ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                        <div class="stats-badge">
                            <div class="stats-number">100+</div>
                            <div class="stats-text"><span>Event<br>Terlaksana</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1">
                    <h6 class="text-danger fw-bold text-uppercase mb-3" style="letter-spacing: 2px;">Tentang Platform Ini</h6>
                    <h2 class="display-6 fw-bold mb-4 text-dark">Transformasi Digital <br> Dunia Persilatan</h2>
                    <p class="text-secondary lead mb-4" style="font-size: 1.1rem;">Digital Pencak Silat hadir sebagai solusi manajemen modern untuk mengelola ekosistem kejuaraan Pencak Silat secara lebih profesional.</p>

                    <!-- 6 Poin Keunggulan Baru -->
                    <div class="row mt-4">
                        <!-- 1. User Friendly UI/UX -->
                        <div class="col-md-6 mb-4">
                            <div class="feature-item d-flex align-items-center">
                                <div class="feature-icon-box me-3 text-danger"><i class="fas fa-mobile-alt fa-lg"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold">User Friendly UI/UX</h6><small class="text-muted">Antarmuka mudah & intuitif.</small>
                                </div>
                            </div>
                        </div>
                        <!-- 2. Performa Stabil -->
                        <div class="col-md-6 mb-4">
                            <div class="feature-item d-flex align-items-center">
                                <div class="feature-icon-box me-3 text-danger"><i class="fas fa-rocket fa-lg"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Performa Stabil</h6><small class="text-muted">Sistem cepat & handal.</small>
                                </div>
                            </div>
                        </div>
                        <!-- 3. Kebutuhan Penyelenggara -->
                        <div class="col-md-6 mb-4">
                            <div class="feature-item d-flex align-items-center">
                                <div class="feature-icon-box me-3 text-danger"><i class="fas fa-sliders-h fa-lg"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Fleksibel & Adaptif</h6><small class="text-muted">Sesuai kebutuhan event.</small>
                                </div>
                            </div>
                        </div>
                        <!-- 4. Mempermudah Penyelenggara -->
                        <div class="col-md-6 mb-4">
                            <div class="feature-item d-flex align-items-center">
                                <div class="feature-icon-box me-3 text-danger"><i class="fas fa-hands-helping fa-lg"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Manajemen Efisien</h6><small class="text-muted">Permudah kerja panitia.</small>
                                </div>
                            </div>
                        </div>
                        <!-- 5. Generate ID Card, Jadwal, Bagan -->
                        <div class="col-md-6 mb-4">
                            <div class="feature-item d-flex align-items-center">
                                <div class="feature-icon-box me-3 text-danger"><i class="fas fa-print fa-lg"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Generator Otomatis</h6><small class="text-muted">ID Card, Jadwal & Bagan.</small>
                                </div>
                            </div>
                        </div>
                        <!-- 6. Budget Friendly -->
                        <div class="col-md-6 mb-4">
                            <div class="feature-item d-flex align-items-center">
                                <div class="feature-icon-box me-3 text-danger"><i class="fas fa-wallet fa-lg"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Budget Friendly</h6><small class="text-muted">Solusi hemat biaya.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="#events" class="btn btn-brand mt-4 px-4 py-3 shadow-sm">Jelajahi Event</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Event List Section -->
    <section id="events" class="container py-5 position-relative">
        <div class="section-title">
            <h2>KALENDER EVENT</h2>
            <p>Daftar kejuaraan Pencak Silat mendatang</p>
        </div>

        <div class="search-container">
            <div class="input-group">
                <input type="text" class="form-control search-input" placeholder="Cari nama event atau kota..." aria-label="Search">
                <button class="btn text-muted d-none" type="button" id="btn-clear-search" style="border: none; background: transparent;">
                    <i class="fas fa-times"></i>
                </button>
                <button class="btn btn-brand search-btn" type="button"><i class="fas fa-search me-2"></i> Cari</button>
            </div>
        </div>

        <div id="loader" class="text-center py-5 d-none">
            <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;"></div>
        </div>

        <div class="row g-4" id="event-list-container">
            <?php if (empty($events)): ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Belum ada event yang tersedia saat ini.</p>
                </div>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <?php
                    $badge_class = 'bg-secondary text-white';
                    if ($event['status'] == 'Open Registration') $badge_class = 'bg-warning text-dark';
                    elseif ($event['status'] == 'Selesai') $badge_class = 'bg-success text-white';
                    elseif ($event['status'] == 'Ditutup') $badge_class = 'bg-danger text-white';

                    $img_src = $event['poster'];
                    if (strpos($img_src, 'http') !== 0) $img_src = base_url('assets/uploads/posters/' . $img_src);

                    $link_daftar = isset($event['link_pendaftaran']) ? $event['link_pendaftaran'] : '';
                    ?>

                    <div class="col-lg-4 col-md-6 fade-in-item">
                        <div class="event-card">
                            <div class="event-poster-container">
                                <span class="event-status <?= $badge_class ?>"><?= $event['status']; ?></span>
                                <img src="<?= $img_src; ?>" alt="Pamflet Event" class="event-poster" onerror="this.src='https://via.placeholder.com/800x400?text=No+Image'">
                            </div>
                            <div class="card-body">
                                <h3 class="event-title"><?= $event['judul']; ?></h3>
                                <ul class="info-list">
                                    <li><i class="far fa-calendar-alt"></i>
                                        <div><span class="label-text">Pelaksanaan</span><br><span class="value-text"><?= $event['tanggal_pelaksanaan']; ?></span></div>
                                    </li>
                                    <li><i class="fas fa-map-marker-alt"></i>
                                        <div><span class="label-text">Tempat</span><br><span class="value-text"><?= $event['tempat']; ?></span></div>
                                    </li>
                                    <li><i class="far fa-clipboard"></i>
                                        <div><span class="label-text">Batas Pendaftaran</span><br><span class="value-text"><?= $event['batas_pendaftaran']; ?></span></div>
                                    </li>
                                </ul>
                                <div class="d-grid gap-2">
                                    <?php if ($event['status'] == 'Selesai'): ?>
                                        <a href="<?= base_url('event/detail/' . $event['id']); ?>" class="btn btn-outline-danger mb-1">
                                            <i class="fas fa-trophy me-2"></i> Lihat Hasil Juara
                                        </a>
                                    <?php endif; ?>
                                    <button class="btn btn-brand btn-detail"
                                        data-title="<?= htmlspecialchars($event['judul']); ?>"
                                        data-date="<?= htmlspecialchars($event['tanggal_pelaksanaan']); ?>"
                                        data-place="<?= htmlspecialchars($event['tempat']); ?>"
                                        data-deadline="<?= htmlspecialchars($event['batas_pendaftaran']); ?>"
                                        data-poster="<?= $img_src; ?>"
                                        data-status="<?= $event['status']; ?>"
                                        data-link="<?= $link_daftar; ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#eventDetailModal">
                                        Info Pendaftaran
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-12" id="pagination-container">
                <?= $pagination; ?>
            </div>
        </div>
    </section>

    <!-- Footer Dinamis -->
    <footer id="contact">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="footer-brand">
                        <img src="<?= base_url('assets/logo/logo.png'); ?>" alt="Logo">
                        <span class="brand-text">DIGITAL PENCAK SILAT</span>
                    </div>
                    <p class="text-muted">Mendukung digitalisasi event pencak silat untuk pelestarian budaya dan prestasi atlet Indonesia.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="text-uppercase fw-bold mb-3" style="color: var(--brand-primary);">Navigasi</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#home" class="text-dark text-decoration-none hover-underline">Beranda</a></li>
                        <li class="mb-2"><a href="#about" class="text-dark text-decoration-none hover-underline">Tentang Kami</a></li>
                        <li class="mb-2"><a href="#events" class="text-dark text-decoration-none hover-underline">Jadwal Event</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="text-uppercase fw-bold mb-3" style="color: var(--brand-primary);">Kontak Panitia</h5>
                    <ul class="list-unstyled">
                        <!-- MENAMPILKAN KONTAK DARI DATABASE (VARIABEL $s) -->
                        <li class="mb-2 text-dark"><i class="fab fa-whatsapp me-2 text-success"></i> +<?= $s['whatsapp']; ?></li>
                        <li class="mb-2 text-dark"><i class="far fa-envelope me-2 text-danger"></i> <?= $s['email']; ?></li>
                        <li class="mb-2"><a href="https://instagram.com/<?= $s['instagram']; ?>" target="_blank" class="text-dark text-decoration-none hover-underline"><i class="fab fa-instagram me-2 text-danger"></i> @<?= $s['instagram']; ?></a></li>
                        <li class="mb-2"><a href="<?= $s['youtube']; ?>" target="_blank" class="text-dark text-decoration-none hover-underline"><i class="fab fa-youtube me-2 text-danger"></i> Youtube Channel</a></li>
                    </ul>
                </div>
            </div>
            <hr style="border-color: #eee;">
            <div class="text-center pb-3 pt-2">
                <small class="text-muted">&copy; <?= date('Y'); ?> Digital Pencak Silat. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button type="button" class="btn btn-danger btn-floating" id="btn-back-to-top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Modal Detail Event -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-bold font-oswald text-uppercase" id="modalEventTitle">Detail Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-md-5">
                            <img src="" id="modalEventPoster" class="img-fluid h-100 object-fit-cover" style="min-height: 300px;" alt="Poster">
                        </div>
                        <div class="col-md-7 p-4">
                            <span class="badge bg-danger mb-2" id="modalEventStatus">Status</span>
                            <h3 class="fw-bold mb-3" id="modalEventName">Nama Event</h3>

                            <ul class="list-unstyled text-muted mb-4">
                                <li class="mb-2 d-flex"><i class="far fa-calendar-alt text-danger mt-1 me-3"></i> <span id="modalEventDate">-</span></li>
                                <li class="mb-2 d-flex"><i class="fas fa-map-marker-alt text-danger mt-1 me-3"></i> <span id="modalEventPlace">-</span></li>
                                <li class="mb-2 d-flex"><i class="far fa-clock text-danger mt-1 me-3"></i> Deadline: <span id="modalEventDeadline">-</span></li>
                            </ul>

                            <p class="small text-muted mb-4">
                                Segera daftarkan kontingen Anda sebelum kuota penuh. Pastikan seluruh persyaratan administrasi telah terpenuhi.
                            </p>

                            <div class="d-grid">
                                <button class="btn btn-brand" id="btnDaftarModal">Daftar Sekarang</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // --- 1. MODAL DETAIL LOGIC (UPDATED DINAMIS) ---
        const eventModal = document.getElementById('eventDetailModal');
        if (eventModal) {
            eventModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;

                const title = button.getAttribute('data-title');
                const date = button.getAttribute('data-date');
                const place = button.getAttribute('data-place');
                const deadline = button.getAttribute('data-deadline');
                const poster = button.getAttribute('data-poster');
                const status = button.getAttribute('data-status');
                const link = button.getAttribute('data-link');

                document.getElementById('modalEventName').textContent = title;
                document.getElementById('modalEventDate').textContent = date;
                document.getElementById('modalEventPlace').textContent = place;
                document.getElementById('modalEventDeadline').textContent = deadline;
                document.getElementById('modalEventPoster').src = poster;
                document.getElementById('modalEventStatus').textContent = status;

                const btnDaftar = document.getElementById('btnDaftarModal');

                // Clone button untuk reset event listener
                const newBtn = btnDaftar.cloneNode(true);
                btnDaftar.parentNode.replaceChild(newBtn, btnDaftar);

                if (link && link !== '') {
                    // JIKA ADA LINK WEBSITE
                    newBtn.innerHTML = '<i class="fas fa-globe me-2"></i> Daftar Sekarang via Website';
                    newBtn.classList.remove('btn-success');
                    newBtn.classList.add('btn-brand');
                    newBtn.onclick = function() {
                        window.open(link, '_blank');
                    };
                } else {
                    // JIKA LINK KOSONG, GUNAKAN WA DARI DATABASE
                    newBtn.innerHTML = '<i class="fab fa-whatsapp me-2"></i> Daftar Sekarang via WhatsApp';
                    newBtn.classList.remove('btn-brand');
                    newBtn.classList.add('btn-success'); // Hijau WA
                    newBtn.onclick = function() {
                        // Mengambil nomor WA dari PHP Variable $s['whatsapp']
                        const phone = '<?= $s['whatsapp']; ?>';
                        const text = `Halo Panitia, saya ingin mendaftar untuk event: ${title}`;
                        window.open(`https://wa.me/${phone}?text=${encodeURIComponent(text)}`, '_blank');
                    };
                }
            });
        }

        // --- 2. BACK TO TOP BUTTON LOGIC ---
        let mybutton = document.getElementById("btn-back-to-top");
        window.onscroll = function() {
            scrollFunction();
            navbarScroll();
        };

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        }
        mybutton.addEventListener("click", () => {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        });

        // --- 3. AJAX PAGINATION LOGIC ---
        document.addEventListener('DOMContentLoaded', function() {
            const eventContainer = document.getElementById('event-list-container');
            const paginationContainer = document.getElementById('pagination-container');
            const loader = document.getElementById('loader');

            if (paginationContainer) {
                paginationContainer.addEventListener('click', function(e) {
                    let target = e.target.closest('.ajax-pagination');
                    if (target) {
                        e.preventDefault();
                        const url = target.getAttribute('href');
                        if (!url || url === '#') return;

                        loader.classList.remove('d-none');
                        eventContainer.style.opacity = '0.3';

                        fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                eventContainer.innerHTML = data.html_events;
                                paginationContainer.innerHTML = data.html_pagination;
                                document.getElementById('events').scrollIntoView({
                                    behavior: 'smooth'
                                });
                            })
                            .catch(error => console.error('Error:', error))
                            .finally(() => {
                                loader.classList.add('d-none');
                                eventContainer.style.opacity = '1';
                            });
                    }
                });
            }

        // --- 4. SEARCH LOGIC ---
        const searchInput = document.querySelector('.search-input');
        const searchBtn = document.querySelector('.search-btn');
        const btnClearSearch = document.getElementById('btn-clear-search');
        let searchTimeout;

        const performSearch = (keyword, shouldScroll = false) => {
            const url = `<?= base_url('event/index'); ?>?keyword=${encodeURIComponent(keyword)}`;

            if (keyword.length > 0) {
                btnClearSearch.classList.remove('d-none');
            } else {
                btnClearSearch.classList.add('d-none');
            }

            loader.classList.remove('d-none');
            eventContainer.style.opacity = '0.3';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    eventContainer.innerHTML = data.html_events;
                    paginationContainer.innerHTML = data.html_pagination;
                    if (shouldScroll) {
                        document.getElementById('events').scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    eventContainer.innerHTML = '<div class="col-12 text-center text-danger"><p>Terjadi kesalahan saat mencari event. Silakan coba lagi.</p></div>';
                })
                .finally(() => {
                    loader.classList.add('d-none');
                    eventContainer.style.opacity = '1';
                });
        };

        if (searchBtn) {
            searchBtn.addEventListener('click', () => performSearch(searchInput.value, true));
        }

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const keyword = e.target.value;
                if (keyword.length > 0) {
                    btnClearSearch.classList.remove('d-none');
                } else {
                    btnClearSearch.classList.add('d-none');
                }

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch(keyword, false);
                }, 500);
            });

            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimeout);
                    performSearch(searchInput.value, true);
                }
            });
        }

        if (btnClearSearch) {
            btnClearSearch.addEventListener('click', () => {
                searchInput.value = '';
                btnClearSearch.classList.add('d-none');
                performSearch('', false);
                searchInput.focus();
            });
        }
    });

        // --- 4. DYNAMIC NAVBAR & SCROLLSPY ---
        const navLinks = document.querySelectorAll('.nav-link');
        const sections = document.querySelectorAll('header, section, footer');

        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId.startsWith('#')) {
                    e.preventDefault();
                    const targetSection = document.querySelector(targetId);
                    if (targetSection) {
                        targetSection.scrollIntoView({
                            behavior: 'smooth'
                        });
                        const navbarCollapse = document.getElementById('navbarNav');
                        if (navbarCollapse.classList.contains('show')) {
                            document.querySelector('.navbar-toggler').click();
                        }
                    }
                }
            });
        });

        function navbarScroll() {
            let current = '';
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 50) {
                current = 'contact';
            } else {
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    if (window.scrollY >= (sectionTop - 150)) {
                        current = section.getAttribute('id');
                    }
                });
            }
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) link.classList.add('active');
            });

            var navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
                navbar.style.padding = '10px 0';
            } else {
                navbar.style.boxShadow = '0 4px 12px rgba(0,0,0,0.05)';
                navbar.style.padding = '15px 0';
            }
        }
    </script>
</body>

</html>