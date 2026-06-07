<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $event['judul']; ?> - Detail Hasil Kejuaraan</title>

    <!-- Assets -->
    <link rel="shortcut icon" href="<?= base_url('assets/logo/logo.ico'); ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/variables.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/main-style.css'); ?>">
    <style>
        body { padding-top: 80px; }
        /* DataTables Custom Styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
            color: white !important;
            border-radius: 5px;
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 8px;
            padding: 5px;
        }
        .dataTables_wrapper .dataTables_filter {
            display: none;
        }
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
            <a href="<?= base_url(); ?>" class="btn btn-brand btn-sm ms-auto">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Beranda
            </a>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="event-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('<?= base_url('assets/carousel/carousel-1.jpg') ?>');">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>#events">Events</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Hasil Kejuaraan</li>
                </ol>
            </nav>
            <div class="row align-items-center mt-4">
                <div class="col-md-3 mb-4 mb-md-0">
                    <?php 
                        $img_src = $event['poster'];
                        if (strpos($img_src, 'http') !== 0) $img_src = base_url('assets/uploads/posters/' . $img_src);
                    ?>
                    <img src="<?= $img_src ?>" class="img-fluid event-poster-detail" alt="Poster">
                </div>
                <div class="col-md-9">
                    <span class="badge bg-warning text-dark mb-2 px-3 py-2 fw-bold text-uppercase"><?= $event['status'] ?></span>
                    <h1 class="display-5 fw-bold"><?= $event['judul'] ?></h1>
                    <div class="d-flex flex-wrap gap-4 mt-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-map-marker-alt text-danger me-2 fa-lg"></i>
                            <span><?= $event['tempat'] ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="far fa-calendar-alt text-danger me-2 fa-lg"></i>
                            <span><?= $event['tanggal_pelaksanaan_display'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="info-box shadow-sm mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h4 class="mb-2"><i class="fas fa-trophy text-warning me-2"></i> Hasil Akhir Pertandingan</h4>
                            <p class="text-muted mb-md-0">Berikut adalah daftar pemenang resmi yang disinkronkan langsung dari sistem Digital Scoring.</p>
                        </div>
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" id="searchResult" class="form-control border-start-0" placeholder="Cari nama atlet atau kontingen...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs Kategori -->
                <ul class="nav nav-tabs" id="resultTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tanding-tab" data-bs-toggle="tab" data-bs-target="#tanding" type="button" role="tab">
                            <i class="fas fa-user-ninja me-2"></i> Kategori Tanding
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="seni-tab" data-bs-toggle="tab" data-bs-target="#seni" type="button" role="tab">
                            <i class="fas fa-magic me-2"></i> Kategori Seni
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="resultTabsContent">
                    <!-- Tab Tanding -->
                    <div class="tab-pane fade show active" id="tanding" role="tabpanel">
                        <?php if(empty($results_tanding)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Data pemenang kategori tanding belum tersedia.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-custom" id="tableTanding">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th>Nama Atlet</th>
                                            <th>Kontingen</th>
                                            <th>Asal Sekolah</th>
                                            <th class="text-center">Kategori</th>
                                            <th class="text-center">Kelas</th>
                                            <th class="text-center">Medali</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach($results_tanding as $res): ?>
                                            <?php 
                                                $rank_class = 'rank-' . strtolower($res['rank_label']);
                                                $school_value = isset($res['school']) ? trim((string) $res['school']) : '';
                                            ?>
                                            <tr class="result-row">
                                                <td class="text-center"><?= $no++; ?></td>
                                                <td class="fw-bold winner-name"><?= ucwords(strtolower($res['winner_name'])) ?></td>
                                                <td class="contingent-name text-uppercase"><?= strtoupper($res['contingent']) ?></td>
                                                <td class="school-name">
                                                    <?php if ($school_value !== ''): ?>
                                                        <?= htmlspecialchars($school_value, ENT_QUOTES, 'UTF-8') ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?= $res['age_category'] . (isset($res['gender']) && $res['gender'] ? ' - ' . strtoupper($res['gender']) : '') ?></td>
                                                <td class="text-center"><span class="badge bg-light text-dark"><?= $res['category_detail'] ?></span></td>
                                                <td class="text-center" width="150">
                                                    <span class="badge-rank <?= $rank_class ?>"><?= $res['rank_label'] ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab Seni -->
                    <div class="tab-pane fade" id="seni" role="tabpanel">
                        <?php if(empty($results_seni)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Data pemenang kategori seni belum tersedia.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-custom" id="tableSeni">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th>Nama Atlet / Kelompok</th>
                                            <th>Kontingen</th>
                                            <th>Asal Sekolah</th>
                                            <th class="text-center">Kategori</th>
                                            <th class="text-center">Jenis Seni</th>
                                            <th class="text-center">Medali</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach($results_seni as $res): ?>
                                            <?php 
                                                $rank_class = 'rank-' . strtolower($res['rank_label']);
                                                $school_value = isset($res['school']) ? trim((string) $res['school']) : '';
                                            ?>
                                            <tr class="result-row">
                                                <td class="text-center"><?= $no++; ?></td>
                                                <td class="fw-bold winner-name"><?= ucwords(strtolower($res['winner_name'])) ?></td>
                                                <td class="contingent-name text-uppercase"><?= strtoupper($res['contingent']) ?></td>
                                                <td class="school-name">
                                                    <?php if ($school_value !== ''): ?>
                                                        <?= htmlspecialchars($school_value, ENT_QUOTES, 'UTF-8') ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?= $res['age_category'] . (isset($res['gender']) && $res['gender'] ? ' - ' . strtoupper($res['gender']) : '') ?></td>
                                                <td class="text-center"><span class="badge bg-light text-dark"><?= $res['category_detail'] ?></span></td>
                                                <td class="text-center" width="150">
                                                    <span class="badge-rank <?= $rank_class ?>"><?= $res['rank_label'] ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            const tableTanding = $('#tableTanding').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                },
                "dom": "<'row'<'col-sm-12'tr>>" + "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>" // Sembunyikan search default
            });

            const tableSeni = $('#tableSeni').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                },
                "dom": "<'row'<'col-sm-12'tr>>" + "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>"
            });

            // Hubungkan search bar custom ke DataTables
            $('#searchResult').on('keyup', function() {
                const val = $(this).val();
                tableTanding.search(val).draw();
                tableSeni.search(val).draw();
            });

            // Tambahkan dropdown jumlah data di atas tabel secara manual agar rapi
            $('.info-box').after(`
                <div class="container mb-3">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <span class="small text-muted">Tampilkan:</span>
                        <select class="form-select form-select-sm w-auto" id="changeLength">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            `);

            $('#changeLength').on('change', function() {
                const len = $(this).val();
                tableTanding.page.len(len).draw();
                tableSeni.page.len(len).draw();
            });
        });
    </script>
</body>

</html>
