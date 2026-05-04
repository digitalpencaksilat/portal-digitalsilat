<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Digital Pencak Silat</title>

    <!-- Bootstrap 5 & Icons -->
    <link rel="shortcut icon" href="<?= base_url('assets/logo/logo.ico'); ?>" type="image/x-icon">
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

<body class="login-body">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="card-header-custom">
                        <img src="<?= base_url('assets/logo/logo.png'); ?>" alt="Logo" class="logo-img">
                        <h4 class="app-title">ADMIN PANEL</h4>
                        <p class="app-subtitle">Digital Pencak Silat Event Manager</p>
                    </div>

                    <div class="card-body-custom">
                        <!-- Flashdata Error (Fallback jika JS error) -->
                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger text-center small rounded-3 mb-4">
                                <i class="fas fa-exclamation-circle me-1"></i> <?= $this->session->flashdata('error'); ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('admin/auth'); ?>" method="POST" id="loginForm">
                            <div class="mb-4">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text"><i class="far fa-user"></i></span>
                                    <input type="text" name="username" class="form-control" placeholder="Username" required autofocus style="font-size: 0.95rem;">
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="Password" required style="font-size: 0.95rem;">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-brand btn-login">
                                Masuk Dashboard <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </form>

                        <a href="<?= base_url(); ?>" class="back-link">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Website Utama
                        </a>
                    </div>
                </div>

                <div class="text-center mt-4 text-muted small">
                    &copy; <?= date('Y'); ?> Digital Pencak Silat. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Notifikasi Error Login dengan SweetAlert
        <?php if ($this->session->flashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: '<?= $this->session->flashdata('error'); ?>',
                confirmButtonColor: '#C60000',
                confirmButtonText: 'Coba Lagi'
            });
        <?php endif; ?>

        // Notifikasi Logout Sukses
        <?php if ($this->session->flashdata('success_logout')): ?>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: 'success',
                title: 'Anda telah berhasil logout'
            })
        <?php endif; ?>
    </script>
</body>

</html>
