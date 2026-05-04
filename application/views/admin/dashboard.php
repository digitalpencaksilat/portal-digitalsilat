<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Digital Pencak Silat</title>

    <!-- Bootstrap 5 & Icons -->
    <link rel="shortcut icon" href="<?= base_url('assets/logo/logo.ico'); ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/variables.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-style.css'); ?>">
</head>

<body class="admin-body">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="<?= base_url('assets/logo/logo.png'); ?>" alt="Logo">
                ADMIN PANEL
            </a>
            <div class="d-flex align-items-center">
                <a href="<?= base_url('admin/api_management'); ?>" class="btn btn-outline-danger btn-sm me-2"><i class="fas fa-key me-1"></i> Manajemen API</a>
                <a href="<?= base_url('admin/pengaturan'); ?>" class="btn btn-outline-dark btn-sm me-2"><i class="fas fa-cog me-1"></i> Pengaturan Kontak</a>
                <span class="d-none d-md-block me-3 text-muted">Hai, <strong><?= $this->session->userdata('nama'); ?></strong></span>
                <a href="<?= base_url('admin/logout'); ?>" class="btn btn-outline-danger btn-sm" id="btn-logout">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5">

        <!-- --- SECTION STATISTIK PENGUNJUNG --- -->
        <h5 class="fw-bold mb-3 font-oswald text-secondary"><i class="fas fa-chart-line me-2"></i> STATISTIK PENGUNJUNG</h5>
        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-6">
                <div class="card stat-card border-start border-4 border-primary">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Pengunjung Hari Ini</span>
                            <h2 class="mb-0 fw-bold text-primary"><?= number_format($visitor_today); ?></h2>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-user-clock"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-6">
                <div class="card stat-card border-start border-4 border-dark">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Total Pengunjung</span>
                            <h2 class="mb-0 fw-bold text-dark"><?= number_format($visitor_total); ?></h2>
                        </div>
                        <div class="stat-icon bg-dark bg-opacity-10 text-dark">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- --- SECTION STATISTIK EVENT --- -->
        <h5 class="fw-bold mb-3 font-oswald text-secondary"><i class="fas fa-calendar-alt me-2"></i> DATA EVENT</h5>
        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card border-start border-4 border-danger">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Total Event</span>
                            <h2 class="mb-0 fw-bold text-dark"><?= $total_events; ?></h2>
                        </div>
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card stat-card border-start border-4 border-warning">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Pendaftaran Buka</span>
                            <h2 class="mb-0 fw-bold text-dark"><?= $active_events; ?></h2>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card stat-card border-start border-4 border-info">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Segera Hadir</span>
                            <h2 class="mb-0 fw-bold text-dark"><?= $coming_soon; ?></h2>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card stat-card border-start border-4 border-success">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Selesai</span>
                            <h2 class="mb-0 fw-bold text-dark"><?= $finished_events; ?></h2>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-flag-checkered"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-0">Manajemen Event</h3>
                <p class="text-muted small">Kelola data kejuaraan dan jadwal.</p>
            </div>
            <a href="<?= base_url('admin/tambah'); ?>" class="btn btn-brand rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Tambah Event
            </a>
        </div>

        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 w-100" id="tableEvents">
                        <thead class="table-head-custom">
                            <tr>
                                <th class="ps-4 py-3">#</th>
                                <th>Poster</th>
                                <th>Informasi Event</th>
                                <th>Pelaksanaan</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($events)): ?>
                                <?php $no = 1; foreach ($events as $ev): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold"><?= $no++; ?></td>
                                        <td>
                                            <?php
                                            $img_src = $ev['poster'];
                                            if (strpos($img_src, 'http') !== 0) $img_src = base_url('assets/uploads/posters/' . $img_src);
                                            ?>
                                            <div class="ratio ratio-4x3 rounded overflow-hidden shadow-sm" style="width: 80px;">
                                                <img src="<?= $img_src; ?>" class="object-fit-cover" alt="Poster" onerror="this.src='https://via.placeholder.com/800x600?text=No+Img'">
                                            </div>
                                        </td>
                                        <td>
                                            <h6 class="fw-bold mb-1 text-dark"><?= $ev['judul']; ?></h6>
                                            <small class="text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?= $ev['tempat']; ?></small>
                                            <?php if (!empty($ev['link_pendaftaran'])): ?>
                                                <div class="mt-1"><span class="badge bg-light text-primary border border-primary"><i class="fas fa-link me-1"></i> Link Website Aktif</span></div>
                                            <?php else: ?>
                                                <div class="mt-1"><span class="badge bg-light text-success border border-success"><i class="fab fa-whatsapp me-1"></i> via WhatsApp</span></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="d-block fw-bold"><?= $ev['tanggal_pelaksanaan']; ?></small>
                                            <small class="text-muted" style="font-size: 0.75rem;">Deadline: <?= $ev['batas_pendaftaran']; ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $badge = 'secondary';
                                            if ($ev['status'] == 'Open Registration') $badge = 'warning text-dark';
                                            elseif ($ev['status'] == 'Selesai') $badge = 'success';
                                            elseif ($ev['status'] == 'Ditutup') $badge = 'danger';
                                            ?>
                                            <span class="badge bg-<?= $badge; ?> rounded-pill px-3"><?= $ev['status']; ?></span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="<?= base_url('admin/edit/' . $ev['id']); ?>" class="btn btn-sm btn-light text-primary border me-1" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="<?= base_url('admin/hapus/' . $ev['id']); ?>" class="btn btn-sm btn-light text-danger border btn-hapus" title="Hapus"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="<?= base_url(); ?>" target="_blank" class="text-decoration-none text-muted fw-bold small">
                <i class="fas fa-external-link-alt me-1"></i> Lihat Website Utama
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tableEvents').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
                columnDefs: [{ orderable: false, targets: [1, 5] }],
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            });
        });

        <?php if ($this->session->flashdata('success')): ?>
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '<?= $this->session->flashdata('success'); ?>', confirmButtonColor: '#C60000', timer: 2000, showConfirmButton: false });
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            Swal.fire({ icon: 'error', title: 'Gagal!', text: '<?= $this->session->flashdata('error'); ?>', confirmButtonColor: '#C60000' });
        <?php endif; ?>

        $(document).on('click', '.btn-hapus', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');
            Swal.fire({
                title: 'Yakin hapus event ini?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#C60000',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => { if (result.isConfirmed) window.location.href = href; });
        });

        $('#btn-logout').on('click', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');
            Swal.fire({
                title: 'Keluar dari Admin?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#C60000',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Logout'
            }).then((result) => { if (result.isConfirmed) window.location.href = href; });
        });
    </script>
</body>

</html>
