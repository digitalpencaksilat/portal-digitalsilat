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
        .publisher-status { min-width: 82px; }
        .publisher-log-message { max-width: 260px; white-space: normal; }
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
                                            <code class="me-2 fw-bold text-dark"><?= (int) $e['id']; ?></code>
                                            <button class="btn btn-xs btn-outline-secondary py-0 px-1" style="font-size: 0.7rem;" onclick="copyText(<?= html_escape(json_encode((string) $e['id'])); ?>, 'ID Event')"><i class="far fa-copy"></i></button>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="fw-bold mb-0 text-dark"><?= html_escape($e['judul']); ?></h6>
                                    </td>
                                    <td>
                                        <?php
                                        $badge = 'secondary';
                                        if ($e['status'] == 'Open Registration') $badge = 'warning text-dark';
                                        elseif ($e['status'] == 'Selesai') $badge = 'success';
                                        ?>
                                        <span class="badge bg-<?= $badge ?> rounded-pill px-3"><?= html_escape($e['status']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($e['api_key']): ?>
                                            <div class="d-flex align-items-center">
                                                <div class="api-key-box me-2" id="key-<?= (int) $e['id']; ?>"><?= html_escape($e['api_key']); ?></div>
                                                <button class="btn btn-sm btn-outline-primary" onclick="copyText(<?= html_escape(json_encode($e['api_key'])); ?>, 'API Key')"><i class="far fa-copy"></i></button>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted small"><em>Belum di-generate</em></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <?= form_open('admin/generate_api_key/' . $e['id'], ['class' => 'd-inline']); ?><button type="submit" class="btn btn-sm <?= $e['api_key'] ? 'btn-outline-danger' : 'btn-brand' ?>"><?= $e['api_key'] ? '<i class="fas fa-sync-alt me-1"></i> Regenerate' : '<i class="fas fa-plus me-1"></i> Generate Key' ?></button><?= form_close(); ?>
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

        <section id="publishing-api" class="mt-5">
            <div class="d-lg-flex justify-content-between align-items-end mb-4">
                <div>
                    <span class="text-danger small fw-bold text-uppercase"><i class="fas fa-pen-nib me-2"></i>Draft-only Integration</span>
                    <h3 class="fw-bold mb-1 mt-2 text-dark font-oswald">PUBLISHING API NEWS</h3>
                    <p class="text-muted small mb-0">Token hanya dapat mencari event dan membuat draft. Publikasi tetap dilakukan oleh admin.</p>
                </div>
                <?php if ($can_manage_publisher): ?>
                    <button class="btn btn-brand mt-3 mt-lg-0" data-bs-toggle="modal" data-bs-target="#generatePublisherToken"><i class="fas fa-plus me-2"></i>Generate Token</button>
                <?php else: ?>
                    <span class="badge bg-secondary mt-3 mt-lg-0">Hanya superadmin dapat mengelola token</span>
                <?php endif; ?>
            </div>

            <?php if ($can_manage_publisher): ?>
            <div class="card card-custom mb-4">
                <div class="card-header bg-white border-0 px-4 py-3"><h5 class="fw-bold mb-1">Token Publisher</h5><small class="text-muted">Token asli tidak disimpan dan tidak ditampilkan pada daftar ini.</small></div>
                <div class="table-responsive"><table class="table table-hover align-middle mb-0">
                    <thead class="table-head-custom"><tr><th class="ps-4 py-3">Nama Token</th><th>Status</th><th>Terakhir Digunakan</th><th>Kedaluwarsa</th><th>Dibuat</th><th class="text-end pe-4">Aksi</th></tr></thead>
                    <tbody>
                    <?php if (empty($publisher_keys)): ?><tr><td colspan="6" class="text-center py-5 text-muted"><i class="fas fa-key fa-2x mb-3 d-block"></i>Belum ada Publishing Token.</td></tr><?php endif; ?>
                    <?php foreach ($publisher_keys as $key): ?>
                        <?php $expired = !empty($key['expires_at']) && strtotime($key['expires_at']) <= time(); ?>
                        <tr>
                            <td class="ps-4"><strong><?= html_escape($key['key_name']); ?></strong><small class="d-block text-muted">ID #<?= (int) $key['id']; ?></small></td>
                            <td><span class="badge rounded-pill publisher-status bg-<?= (int) $key['is_active'] && !$expired ? 'success' : 'secondary'; ?>"><?= (int) $key['is_active'] && !$expired ? 'Aktif' : ($expired ? 'Expired' : 'Nonaktif'); ?></span></td>
                            <td><?= $key['last_used_at'] ? date('d M Y H:i', strtotime($key['last_used_at'])) : '<span class="text-muted">Belum pernah</span>'; ?></td>
                            <td><?= $key['expires_at'] ? date('d M Y', strtotime($key['expires_at'])) : '-'; ?></td>
                            <td><small><?= date('d M Y', strtotime($key['created_at'])); ?></small><small class="d-block text-muted"><?= html_escape($key['creator_name'] ?: 'Administrator'); ?></small></td>
                            <td class="text-end pe-4"><?php if ($can_manage_publisher && (int) $key['is_active']): ?><?= form_open('admin/publisher-token/revoke/' . $key['id'], ['class' => 'd-inline revoke-token-form']); ?><button class="btn btn-sm btn-outline-danger" type="submit"><i class="fas fa-ban me-1"></i>Nonaktifkan</button><?= form_close(); ?><?php else: ?><span class="text-muted small"><?= (int) $key['is_active'] ? 'Tanpa akses' : 'Tidak aktif'; ?></span><?php endif; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table></div>
            </div>

            <div class="card card-custom">
                <div class="card-header bg-white border-0 px-4 py-3"><h5 class="fw-bold mb-1">Aktivitas Publishing API</h5><small class="text-muted">30 aktivitas terbaru tanpa menyimpan token atau isi artikel.</small></div>
                <div class="table-responsive"><table class="table table-hover align-middle mb-0">
                    <thead class="table-head-custom"><tr><th class="ps-4 py-3">Waktu</th><th>Token</th><th>Aksi</th><th>Status</th><th>Artikel / Event</th><th>IP</th><th>Catatan</th></tr></thead>
                    <tbody>
                    <?php if (empty($publisher_logs)): ?><tr><td colspan="7" class="text-center py-5 text-muted">Belum ada aktivitas Publishing API.</td></tr><?php endif; ?>
                    <?php foreach ($publisher_logs as $log): ?>
                        <tr><td class="ps-4"><small><?= date('d M Y H:i:s', strtotime($log['created_at'])); ?></small></td><td><?= html_escape($log['key_name'] ?: 'Unknown'); ?></td><td><code><?= html_escape($log['action']); ?></code></td><td><span class="badge bg-<?= $log['status'] === 'success' ? 'success' : 'danger'; ?>"><?= ucfirst($log['status']); ?></span></td><td><small><?= $log['article_id'] ? 'Artikel #' . (int) $log['article_id'] : '-'; ?><?= $log['event_slug'] ? '<br>' . html_escape($log['event_slug']) : ''; ?></small></td><td><small><?= html_escape($log['ip_address']); ?></small></td><td class="publisher-log-message"><small class="text-muted"><?= html_escape($log['error_message'] ?: '-'); ?></small></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table></div>
            </div>
            <?php endif; ?>
        </section>

        <?php if ($can_manage_publisher): ?>
            <div class="modal fade" id="generatePublisherToken" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 rounded-4"><div class="modal-header"><h5 class="modal-title fw-bold">Generate Publishing Token</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><?= form_open('admin/publisher-token/generate'); ?><div class="modal-body"><div class="alert alert-warning small"><i class="fas fa-shield-alt me-2"></i>Token hanya ditampilkan satu kali dan hanya dapat membuat draft.</div><div class="mb-3"><label class="form-label fw-bold">Nama Token</label><input name="key_name" class="form-control" maxlength="100" required placeholder="Contoh: News Publisher Laptop Utama"></div><div class="mb-3"><label class="form-label fw-bold">Masa Berlaku</label><select name="valid_days" class="form-select"><option value="90">90 hari</option><option value="180" selected>180 hari</option><option value="365">365 hari</option></select></div><div><label class="form-label fw-bold">Password Admin Saat Ini</label><input type="password" name="current_password" class="form-control" autocomplete="current-password" required><small class="text-muted">Konfirmasi diperlukan sebelum token dibuat.</small></div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-brand"><i class="fas fa-key me-2"></i>Generate Token</button></div><?= form_close(); ?></div></div>
            </div>
        <?php endif; ?>

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

        $('.revoke-token-form').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({ title: 'Nonaktifkan token?', text: 'Token tidak dapat digunakan lagi setelah dinonaktifkan.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#C60000', cancelButtonColor: '#6c757d', confirmButtonText: 'Ya, Nonaktifkan', cancelButtonText: 'Batal' }).then(result => { if (result.isConfirmed) form.submit(); });
        });

        <?php if ($this->session->flashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: <?= json_encode($this->session->flashdata('success')); ?>,
                confirmButtonColor: '#C60000',
                timer: 2000,
                showConfirmButton: false
            });
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: <?= json_encode($this->session->flashdata('error')); ?>,
                confirmButtonColor: '#C60000'
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
