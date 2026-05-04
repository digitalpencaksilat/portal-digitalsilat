<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfigurasi Kontak - Admin Panel</title>

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/variables.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-style.css'); ?>">
</head>

<body class="admin-body">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('admin/dashboard'); ?>">
                <img src="<?= base_url('assets/logo/logo.png'); ?>" alt="Logo">
                ADMIN PANEL
            </a>
            <div class="d-flex align-items-center">
                <a href="<?= base_url('admin/dashboard'); ?>" class="btn btn-outline-dark btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
                <span class="d-none d-md-block me-3 text-muted">Hai, <strong><?= $this->session->userdata('nama'); ?></strong></span>
                <a href="<?= base_url('admin/logout'); ?>" class="btn btn-outline-danger btn-sm" id="btn-logout">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-custom p-4 p-md-5">
                    <h4 class="fw-bold mb-4 text-dark">PENGATURAN KONTAK</h4>
                    <p class="text-muted small mb-4">Informasi ini akan muncul secara otomatis di Footer Landing Page dan Tombol WhatsApp pendaftaran.</p>

                    <?= form_open('admin/update_settings'); ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nomor WhatsApp</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fab fa-whatsapp text-success"></i></span>
                            <input type="text" name="whatsapp" class="form-control border-start-0" value="<?= $s['whatsapp']; ?>" placeholder="628..." required>
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">Gunakan format 628xxx (tanpa + atau 0 di depan)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Email Resmi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="far fa-envelope text-danger"></i></span>
                            <input type="email" name="email" class="form-control border-start-0" value="<?= $s['email']; ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Username Instagram</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fab fa-instagram text-danger"></i></span>
                            <input type="text" name="instagram" class="form-control border-start-0" value="<?= $s['instagram']; ?>" placeholder="username_tanpa_@" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">Link Channel YouTube</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fab fa-youtube text-danger"></i></span>
                            <input type="url" name="youtube" class="form-control border-start-0" value="<?= $s['youtube']; ?>" placeholder="https://youtube.com/..." required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-brand px-4 rounded-pill">Simpan Perubahan</button>
                    </div>
                    <?= form_close(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
        <?php if ($this->session->flashdata('success')): ?>
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '<?= $this->session->flashdata('success'); ?>', confirmButtonColor: '#C60000', timer: 2000, showConfirmButton: false });
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
            }).then((result) => { if (result.isConfirmed) window.location.href = href; });
        });
    </script>
</body>
</html>
