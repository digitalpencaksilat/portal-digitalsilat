<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen API - Digital Pencak Silat</title>

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

    <style>
        .api-key-box {
            background: #f8f9fa;
            border: 1px dashed #ccc;
            padding: 5px 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.9rem;
            color: #333;
        }
        .info-box {
            transition: all 0.3s ease;
        }
        .info-box:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body class="admin-body">

    <?php $admin_active_menu = 'api'; include(APPPATH . 'views/admin/_navbar.php'); ?>

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="fw-bold mb-1 text-dark font-oswald">MANAJEMEN API KEJUARAAN</h3>
                <p class="text-muted small">Gunakan API Key berikut untuk mengintegrasikan sistem scoring dengan Portal Digital Silat.</p>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 w-100" id="tableApi">
                        <thead class="table-head-custom">
                            <tr>
                                <th class="ps-4">Event ID</th>
                                <th>Nama Event</th>
                                <th>Status Event</th>
                                <th>API Key Access</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $e): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <code class="me-2 fw-bold text-dark"><?= $e['id']; ?></code>
                                            <button class="btn btn-xs btn-outline-secondary py-0 px-1" style="font-size: 0.7rem;" onclick="copyText('<?= $e['id']; ?>', 'ID Event')"><i class="far fa-copy"></i></button>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="fw-bold mb-0 text-dark"><?= $e['judul']; ?></h6>
                                    </td>
                                    <td>
                                        <?php
                                        $badge = 'secondary';
                                        if ($e['status'] == 'Open Registration') $badge = 'warning text-dark';
                                        elseif ($e['status'] == 'Selesai') $badge = 'success';
                                        ?>
                                        <span class="badge bg-<?= $badge ?> rounded-pill px-3"><?= $e['status']; ?></span>
                                    </td>
                                    <td>
                                        <?php if ($e['api_key']): ?>
                                            <div class="d-flex align-items-center">
                                                <div class="api-key-box me-2" id="key-<?= $e['id']; ?>"><?= $e['api_key']; ?></div>
                                                <button class="btn btn-sm btn-outline-primary" onclick="copyText('<?= $e['api_key']; ?>', 'API Key')"><i class="far fa-copy"></i></button>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted small"><em>Belum di-generate</em></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <a href="<?= base_url('admin/generate_api_key/' . $e['id']); ?>" class="btn btn-sm <?= $e['api_key'] ? 'btn-outline-danger' : 'btn-brand' ?>">
                                            <?= $e['api_key'] ? '<i class="fas fa-sync-alt me-1"></i> Regenerate' : '<i class="fas fa-plus me-1"></i> Generate Key' ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-5 info-box bg-white p-4 rounded-4 shadow-sm" style="border-left: 5px solid var(--brand-primary);">
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle text-primary me-2"></i> Petunjuk Penggunaan Integrasi</h6>
            <div class="row">
                <div class="col-md-6">
                    <ul class="text-muted small mb-0">
                        <li class="mb-2">Setiap Event memiliki <strong>API Key</strong> unik untuk keamanan data.</li>
                        <li class="mb-2">Gunakan header <code>X-API-KEY</code> saat melakukan request POST ke endpoint API.</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="text-muted small mb-0">
                        <li class="mb-2">Endpoint API: <code class="text-primary"><?= base_url('api/push_results'); ?></code></li>
                        <li class="mb-2">Pastikan status event dalam keadaan <strong>Aktif</strong> atau <strong>Selesai</strong> untuk sinkronisasi.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="<?= base_url('admin/dashboard'); ?>" class="text-decoration-none text-muted fw-bold small">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
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
            $('#tableApi').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                },
                columnDefs: [{
                    orderable: false,
                    targets: [3, 4]
                }]
            });
        });

        function copyText(text, label) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Disalin!',
                    text: label + ' telah disalin ke clipboard.',
                    timer: 1500,
                    showConfirmButton: false,
                    confirmButtonColor: '#C60000'
                });
            });
        }

        <?php if ($this->session->flashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= $this->session->flashdata('success'); ?>',
                confirmButtonColor: '#C60000',
                timer: 2000,
                showConfirmButton: false
            });
        <?php endif; ?>

        $('#btn-logout').on('click', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');
            Swal.fire({
                title: 'Keluar dari Admin?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#C60000',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = href;
            });
        });
    </script>
</body>

</html>
