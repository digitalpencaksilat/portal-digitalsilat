<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen API - Admin DPS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root { --brand-primary: #C60000; --brand-dark: #1a1a1a; }
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        h4 { font-family: 'Oswald', sans-serif; text-transform: uppercase; color: var(--brand-primary); }
        .navbar { background-color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .btn-brand { background-color: var(--brand-primary); color: white; border-radius: 50px; }
        .btn-brand:hover { background-color: #a00000; color: white; }
        .api-key-box { background: #f8f9fa; border: 1px dashed #ccc; padding: 5px 15px; border-radius: 8px; font-family: monospace; font-size: 0.9rem; }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger" href="<?= base_url('admin/dashboard'); ?>">DPS ADMIN</a>
            <div class="ms-auto">
                <a href="<?= base_url('admin/dashboard'); ?>" class="btn btn-outline-secondary btn-sm me-2"><i class="fas fa-arrow-left me-1"></i> Dashboard</a>
                <a href="<?= base_url('admin/logout'); ?>" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-2">Manajemen API Kejuaraan</h4>
                <p class="text-muted">Gunakan API Key berikut untuk mengintegrasikan sistem scoring dengan Portal Digital Silat.</p>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4">Event ID</th>
                                <th>Nama Event</th>
                                <th>Status Event</th>
                                <th>API Key Access</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($events as $e): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <code class="me-2 fw-bold text-dark"><?= $e['id']; ?></code>
                                        <button class="btn btn-xs btn-outline-secondary py-0 px-1" style="font-size: 0.7rem;" onclick="copyText('<?= $e['id']; ?>', 'ID Event')"><i class="far fa-copy"></i></button>
                                    </div>
                                </td>
                                <td class="fw-bold"><?= $e['judul']; ?></td>
                                <td>
                                    <?php 
                                        $badge = 'bg-secondary';
                                        if($e['status'] == 'Open Registration') $badge = 'bg-warning text-dark';
                                        elseif($e['status'] == 'Selesai') $badge = 'bg-success';
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= $e['status']; ?></span>
                                </td>
                                <td>
                                    <?php if($e['api_key']): ?>
                                        <div class="d-flex align-items-center">
                                            <div class="api-key-box me-2" id="key-<?= $e['id']; ?>"><?= $e['api_key']; ?></div>
                                            <button class="btn btn-sm btn-outline-primary" onclick="copyText('<?= $e['api_key']; ?>', 'API Key')"><i class="far fa-copy"></i></button>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small"><em>Belum di-generate</em></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="<?= base_url('admin/generate_api_key/'.$e['id']); ?>" class="btn btn-sm <?= $e['api_key'] ? 'btn-outline-danger' : 'btn-brand' ?>">
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
        
        <div class="mt-4 info-box bg-white p-4 rounded-4 shadow-sm">
            <h6 class="fw-bold"><i class="fas fa-info-circle text-primary me-2"></i> Petunjuk Penggunaan</h6>
            <ul class="text-muted small mb-0">
                <li>Setiap Event memiliki <strong>API Key</strong> unik untuk keamanan data.</li>
                <li>Gunakan header <code>X-API-KEY</code> saat melakukan request POST ke endpoint API.</li>
                <li>Endpoint API: <code><?= base_url('api/push_results'); ?></code></li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function copyText(text, label) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Disalin!',
                    text: label + ' telah disalin ke clipboard.',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }
        
        <?php if($this->session->flashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '<?= $this->session->flashdata('success'); ?>',
            timer: 2000,
            showConfirmButton: false
        });
        <?php endif; ?>
    </script>
</body>
</html>
