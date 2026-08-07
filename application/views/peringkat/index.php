<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringkat Atlet - Digital Pencak Silat</title>

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
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>#peringkat">Peringkat</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Peringkat Lengkap</li>
                </ol>
            </nav>
            <div class="row align-items-center mt-4">
                <div class="col-12">
                    <span class="badge bg-warning text-dark mb-2 px-3 py-2 fw-bold text-uppercase">Klasemen Atlet</span>
                    <h1 class="display-5 fw-bold"><i class="fas fa-trophy me-2"></i> Peringkat Atlet</h1>
                    <div class="d-flex flex-wrap gap-4 mt-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users text-danger me-2 fa-lg"></i>
                            <span><?= number_format($total); ?> Atlet Terdata</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-star text-danger me-2 fa-lg"></i>
                            <span>Poin: Emas 3 &middot; Perak 2 &middot; Perunggu 1</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-12">

                <!-- Info Box + Search -->
                <div class="info-box shadow-sm mb-4">
                    <form method="get" action="<?= base_url('peringkat'); ?>" class="row align-items-center g-3">
                        <div class="col-md-5">
                            <h4 class="mb-2"><i class="fas fa-list-ol text-warning me-2"></i> Klasemen Akumulasi</h4>
                            <p class="text-muted mb-0">Akumulasi Prestasi Atlet dari seluruh event yang berkerjasama dengan Digital Pencak Silat.</p>
                        </div>
                        <div class="col-md-3">
                            <select name="kategori" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Kategori</option>
                                <option value="tanding" <?= ($filter['category_main'] === 'tanding') ? 'selected' : ''; ?>>Tanding</option>
                                <option value="seni" <?= ($filter['category_main'] === 'seni') ? 'selected' : ''; ?>>Seni</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="cari" value="<?= htmlspecialchars($filter['keyword'] ?? ''); ?>" class="form-control border-start-0" placeholder="Cari nama atlet...">
                                <button class="btn btn-brand" type="submit">Cari</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tabel Peringkat -->
                <?php if (empty($list)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada atlet yang cocok dengan pencarian.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th class="text-center">Peringkat</th>
                                    <th>Nama Atlet</th>
                                    <th>Kontingen</th>
                                    <th class="text-center">Emas</th>
                                    <th class="text-center">Perak</th>
                                    <th class="text-center">Perunggu</th>
                                    <th class="text-center">Event</th>
                                    <th class="text-center">Total Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list as $i => $a):
                                    $rank_num = $offset + $i + 1;
                                    $rank_badge = $rank_num === 1 ? 'rank-emas' : ($rank_num === 2 ? 'rank-perak' : ($rank_num === 3 ? 'rank-perunggu' : ''));
                                ?>
                                    <tr class="result-row">
                                        <td class="text-center">
                                            <?php if ($rank_num <= 3): ?>
                                                <span class="badge-rank <?= $rank_badge; ?>"><?= $rank_num; ?></span>
                                            <?php else: ?>
                                                <span class="fw-bold text-muted"><?= $rank_num; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold winner-name">
                                            <!-- NEW: Use clean URL with row_id -->
                                             <a class="text-decoration-none text-dark" href="<?= base_url('peringkat/atlet/' . ($a['row_id'] ?? urlencode($a['name_key']))); ?>">
                                                <?= htmlspecialchars(ucwords(strtolower($a['display_name']))); ?>
                                            </a>
                                        </td>
                                        <td class="contingent-name text-uppercase"><?= htmlspecialchars(strtoupper($a['last_contingent'])); ?></td>
                                        <td class="text-center"><i class="fas fa-medal" style="color:#FFD700;"></i> <?= $a['emas']; ?></td>
                                        <td class="text-center"><i class="fas fa-medal" style="color:#C0C0C0;"></i> <?= $a['perak']; ?></td>
                                        <td class="text-center"><i class="fas fa-medal" style="color:#CD7F32;"></i> <?= $a['perunggu']; ?></td>
                                        <td class="text-center"><?= $a['event_count']; ?></td>
                                        <td class="text-center">
                                            <span class="badge-rank" style="background:var(--brand-primary);color:#fff;"><?= $a['poin']; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($last_page > 1):
                        $qs = $_GET;
                        $mk = function ($p) use ($qs) { $qs['page'] = $p; return base_url('peringkat') . '?' . http_build_query($qs); };
                        $start = max(1, $page - 2);
                        $end = min($last_page, $page + 2);
                    ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>"><a class="page-link" href="<?= $mk(max(1, $page - 1)); ?>">&laquo;</a></li>
                                <?php if ($start > 1): ?>
                                    <li class="page-item"><a class="page-link" href="<?= $mk(1); ?>">1</a></li>
                                    <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                                <?php endif; ?>
                                <?php for ($p = $start; $p <= $end; $p++): ?>
                                    <li class="page-item <?= $p === $page ? 'active' : ''; ?>"><a class="page-link" href="<?= $mk($p); ?>"><?= $p; ?></a></li>
                                <?php endfor; ?>
                                <?php if ($end < $last_page): ?>
                                    <?php if ($end < $last_page - 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                                    <li class="page-item"><a class="page-link" href="<?= $mk($last_page); ?>"><?= $last_page; ?></a></li>
                                <?php endif; ?>
                                <li class="page-item <?= $page >= $last_page ? 'disabled' : ''; ?>"><a class="page-link" href="<?= $mk(min($last_page, $page + 1)); ?>">&raquo;</a></li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>

                <p class="text-center text-muted small mt-4 mb-5">
                    <i class="fas fa-info-circle me-1"></i>
                    Peringkat mencocokkan atlet berdasarkan kesamaan nama. Atlet dengan ejaan nama berbeda antar event mungkin terhitung terpisah.
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
