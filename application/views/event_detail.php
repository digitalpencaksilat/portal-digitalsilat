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

    <style>
        :root {
            --brand-primary: #C60000;
            --brand-secondary: #FFD700;
            --brand-dark: #1a1a1a;
            --brand-light: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            color: #333;
            padding-top: 80px;
        }

        h1, h2, h3, h4, h5, .nav-tabs .nav-link {
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
        }

        .navbar {
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 10px 0;
        }

        .navbar-brand {
            color: var(--brand-primary) !important;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        .event-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('<?= base_url('assets/carousel/carousel-1.jpg') ?>');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
            border-bottom: 5px solid var(--brand-primary);
        }

        .breadcrumb-item a {
            color: var(--brand-secondary);
            text-decoration: none;
        }

        .event-poster-detail {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: 4px solid white;
        }

        .card-result {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 20px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #666;
            font-weight: 600;
            padding: 12px 25px;
            transition: all 0.3s;
        }

        .nav-tabs .nav-link.active {
            color: var(--brand-primary);
            border-bottom: 3px solid var(--brand-primary);
            background: transparent;
        }

        .table-custom {
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .table-custom thead th {
            border: none;
            background-color: var(--brand-dark);
            color: white;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 15px;
        }

        .table-custom tbody tr {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            transition: transform 0.2s;
        }

        .table-custom tbody tr:hover {
            transform: scale(1.01);
            background-color: #fff9f9;
        }

        .table-custom td {
            padding: 15px;
            vertical-align: middle;
            border: none;
        }

        .badge-rank {
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .rank-emas { background-color: #FFD700; color: #000; }
        .rank-perak { background-color: #C0C0C0; color: #000; }
        .rank-perunggu { background-color: #CD7F32; color: #fff; }

        .info-box {
            background: white;
            padding: 20px;
            border-radius: 15px;
            border-left: 5px solid var(--brand-primary);
            margin-bottom: 20px;
        }

        footer {
            background-color: #ffffff;
            padding: 40px 0 20px;
            border-top: 1px solid #eee;
            margin-top: 60px;
        }

        .btn-brand {
            background-color: var(--brand-primary);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-brand:hover {
            background-color: #a00000;
            color: white;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url(); ?>">
                <img src="<?= base_url('assets/logo/logo.png'); ?>" alt="Logo">
                DIGITAL PENCAK SILAT
            </a>
            <a href="<?= base_url(); ?>" class="btn btn-brand btn-sm ms-auto">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Beranda
            </a>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="event-header">
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
                            <span><?= $event['tanggal_pelaksanaan'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="info-box shadow-sm mb-5">
                    <h4 class="mb-3"><i class="fas fa-trophy text-warning me-2"></i> Hasil Akhir Pertandingan</h4>
                    <p class="text-muted mb-0">Berikut adalah daftar pemenang resmi untuk kategori Tanding dan Seni pada kejuaraan ini. Data disinkronkan langsung dari sistem Digital Scoring.</p>
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
                                <table class="table table-custom">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Atlet</th>
                                            <th>Kontingen</th>
                                            <th>Kategori</th>
                                            <th>Kelas</th>
                                            <th>Medali</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach($results_tanding as $res): ?>
                                            <?php 
                                                $rank_class = 'rank-' . strtolower($res['rank_label']);
                                            ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td class="fw-bold"><?= $res['winner_name'] ?></td>
                                                <td><?= $res['contingent'] ?></td>
                                                <td><?= $res['age_category'] ?></td>
                                                <td><span class="badge bg-light text-dark"><?= $res['category_detail'] ?></span></td>
                                                <td width="150">
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
                                <table class="table table-custom">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Atlet / Kelompok</th>
                                            <th>Kontingen</th>
                                            <th>Kategori</th>
                                            <th>Jenis Seni</th>
                                            <th>Medali</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach($results_seni as $res): ?>
                                            <?php 
                                                $rank_class = 'rank-' . strtolower($res['rank_label']);
                                            ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td class="fw-bold"><?= $res['winner_name'] ?></td>
                                                <td><?= $res['contingent'] ?></td>
                                                <td><?= $res['age_category'] ?></td>
                                                <td><span class="badge bg-light text-dark"><?= $res['category_detail'] ?></span></td>
                                                <td width="150">
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

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p class="text-muted small">&copy; <?= date('Y'); ?> Digital Pencak Silat. All rights reserved.</p>
            <div class="d-flex justify-content-center gap-3 mt-3">
                <a href="https://instagram.com/<?= $s['instagram'] ?>" class="text-danger"><i class="fab fa-instagram fa-lg"></i></a>
                <a href="<?= $s['youtube'] ?>" class="text-danger"><i class="fab fa-youtube fa-lg"></i></a>
                <a href="mailto:<?= $s['email'] ?>" class="text-danger"><i class="far fa-envelope fa-lg"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
