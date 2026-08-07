<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($summary['display_name']); ?> - Profil Atlet</title>

    <!-- Assets -->
    <link rel="shortcut icon" href="<?= base_url('assets/logo/logo.ico'); ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/variables.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/main-style.css'); ?>">
    <style>
        body { padding-top: 80px; }
        .stat-tile { background:#fff; border-radius:var(--border-radius-md); box-shadow:var(--box-shadow); padding:22px; text-align:center; }
        .stat-tile .num { font-family:'Oswald',sans-serif; font-size:2.2rem; font-weight:700; line-height:1; }
        .profile-badge { background-color:var(--brand-primary); color:#fff; }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url(); ?>">
                <img src="<?= base_url('assets/logo/logo.png'); ?>" alt="Logo Brand" class="img-fluid">
                DIGITAL PENCAK SILAT
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>#heroCarousel">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>#about">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>#events">Jadwal Event</a></li>
                    <li class="nav-item"><a class="nav-link active" href="<?= base_url(); ?>#peringkat">Peringkat</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>#contact">Kontak</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="event-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('<?= base_url('assets/carousel/carousel-1.jpg') ?>');">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('peringkat'); ?>">Peringkat</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Profil Atlet</li>
                </ol>
            </nav>
            <div class="row align-items-center mt-4">
                <div class="col-12">
                    <span class="badge profile-badge mb-2 px-3 py-2 fw-bold text-uppercase">Profil Atlet</span>
                    <h1 class="display-5 fw-bold"><?= htmlspecialchars(ucwords(strtolower($summary['display_name']))); ?></h1>
                    <?php if (!empty($summary['kontingen'])): ?>
                        <div class="d-flex flex-wrap gap-4 mt-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-shield-alt text-danger me-2 fa-lg"></i>
                                <span><?= htmlspecialchars(implode(' · ', $summary['kontingen'])); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-12">

                <!-- Statistik Medali -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3"><div class="stat-tile"><div class="num" style="color:#d4a000;"><?= $summary['emas']; ?></div><div class="text-muted mt-1"><i class="fas fa-medal" style="color:#FFD700;"></i> Emas</div></div></div>
                    <div class="col-6 col-md-3"><div class="stat-tile"><div class="num" style="color:#9a9a9a;"><?= $summary['perak']; ?></div><div class="text-muted mt-1"><i class="fas fa-medal" style="color:#C0C0C0;"></i> Perak</div></div></div>
                    <div class="col-6 col-md-3"><div class="stat-tile"><div class="num" style="color:#a06a30;"><?= $summary['perunggu']; ?></div><div class="text-muted mt-1"><i class="fas fa-medal" style="color:#CD7F32;"></i> Perunggu</div></div></div>
                    <div class="col-6 col-md-3"><div class="stat-tile"><div class="num" style="color:var(--brand-primary);"><?= $summary['poin']; ?></div><div class="text-muted mt-1"><i class="fas fa-star"></i> Total Poin</div></div></div>
                </div>

                <!-- Info box riwayat -->
                <div class="info-box shadow-sm mb-4">
                    <h4 class="mb-2"><i class="fas fa-history text-warning me-2"></i> Riwayat Prestasi</h4>
                    <p class="text-muted mb-0">Daftar capaian atlet di seluruh event yang pernah diikuti.</p>
                </div>

                <!-- Tabel Riwayat -->
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Event</th>
                                <th class="text-center">Kategori</th>
                                <th>Kelas / Nomor</th>
                                <th class="text-center">Kelompok</th>
                                <th>Kontingen</th>
                                <th class="text-center">Medali</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($history as $h):
                                $age_gender_display = trim($h['age_category'] . (isset($h['gender']) && !empty($h['gender']) ? ' - ' . strtoupper($h['gender']) : ''));
                                if (empty($age_gender_display)) {
                                    $age_gender_display = '-';
                                }
                            ?>
                                <tr class="result-row">
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($h['event_judul']); ?></td>
                                    <td class="text-center text-capitalize"><?= htmlspecialchars($h['category_main']); ?></td>
                                    <td><span class="badge bg-light text-dark"><?= htmlspecialchars($h['category_detail']); ?></span></td>
                                    <td class="text-center"><?= htmlspecialchars($age_gender_display); ?></td>
                                    <td class="contingent-name text-uppercase"><?= htmlspecialchars(strtoupper($h['contingent'])); ?></td>
                                    <td class="text-center" width="150">
                                        <span class="badge-rank rank-<?= strtolower(trim($h['rank_label'])); ?>"><?= htmlspecialchars($h['rank_label']); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <p class="text-center text-muted small mt-4 mb-5">
                    <i class="fas fa-info-circle me-1"></i>
                    Data dikelompokkan berdasarkan kesamaan nama ternormalisasi. Jika ada nama yang sama persis milik orang berbeda, prestasinya bisa tergabung di sini.
                </p>

            </div>
        </div>
    </div>

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
                        <li class="mb-2"><a href="<?= base_url(); ?>#home" class="text-dark text-decoration-none hover-underline">Beranda</a></li>
                        <li class="mb-2"><a href="<?= base_url(); ?>#about" class="text-dark text-decoration-none hover-underline">Tentang Kami</a></li>
                        <li class="mb-2"><a href="<?= base_url(); ?>#events" class="text-dark text-decoration-none hover-underline">Jadwal Event</a></li>
                        <li class="mb-2"><a href="<?= base_url(); ?>#peringkat" class="text-dark text-decoration-none hover-underline">Peringkat Atlet</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="text-uppercase fw-bold mb-3" style="color: var(--brand-primary);">Kontak Panitia</h5>
                    <ul class="list-unstyled">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
