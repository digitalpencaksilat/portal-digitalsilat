<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($event) ? 'Edit' : 'Tambah'; ?> Event - Admin Panel</title>

    <!-- Bootstrap & Icons -->
    <link rel="shortcut icon" href="<?= base_url('assets/logo/logo.ico'); ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/variables.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-style.css'); ?>">
    <style>
        .card-header-custom {
            background-color: white;
            border-bottom: 2px solid #eee;
            padding: 20px 25px;
            border-radius: 15px 15px 0 0 !important;
        }
        .form-label { font-weight: 500; color: #555; font-size: 0.9rem; }
        .form-control, .form-select { border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd; }
        .form-control:focus { border-color: var(--brand-primary); box-shadow: 0 0 0 0.2rem rgba(198, 0, 0, 0.1); }
    </style>
</head>

<body class="admin-body">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center mb-3">
                    <a href="<?= base_url('admin/dashboard'); ?>" class="text-decoration-none text-muted me-2"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>

                <div class="card card-custom">
                    <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-<?= isset($event) ? 'edit' : 'plus-circle'; ?> text-danger me-2"></i>
                            <?= isset($event) ? 'Edit Data Event' : 'Tambah Event Baru'; ?>
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger rounded-3"><i class="fas fa-exclamation-triangle me-2"></i> <?= $this->session->flashdata('error'); ?></div>
                        <?php endif; ?>

                        <?= form_open_multipart(isset($event) ? 'admin/update/' . $event['id'] : 'admin/simpan', ['id' => 'eventForm']); ?>
                        <div class="mb-3">
                            <label class="form-label">Judul Event <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" placeholder="Masukkan nama kejuaraan..." value="<?= isset($event) ? $event['judul'] : ''; ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="far fa-calendar-alt text-muted"></i></span>
                                    <input type="text" name="tanggal_pelaksanaan" class="form-control" placeholder="Contoh: 20 - 22 Oktober 2024" value="<?= isset($event) ? $event['tanggal_pelaksanaan'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Batas Pendaftaran <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="far fa-clock text-muted"></i></span>
                                    <input type="text" name="batas_pendaftaran" class="form-control" placeholder="Contoh: 10 Oktober 2024" value="<?= isset($event) ? $event['batas_pendaftaran'] : ''; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tempat / Lokasi <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                <input type="text" name="tempat" class="form-control" placeholder="Nama GOR atau Gedung..." value="<?= isset($event) ? $event['tempat'] : ''; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link Website Pendaftaran (Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-link text-muted"></i></span>
                                <input type="url" name="link_pendaftaran" class="form-control" placeholder="https://..." value="<?= isset($event) && isset($event['link_pendaftaran']) ? $event['link_pendaftaran'] : ''; ?>">
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Jika diisi, tombol pendaftaran akan mengarah ke link ini. Jika kosong, akan mengarah ke WhatsApp Panitia.</small>
                        </div>

                        <div class="row align-items-center mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Event</label>
                                <select name="status" class="form-select">
                                    <?php $options = ['Segera Hadir', 'Open Registration', 'Ditutup', 'Selesai']; $selected = isset($event) ? $event['status'] : ''; ?>
                                    <?php foreach ($options as $opt): ?>
                                        <option value="<?= $opt; ?>" <?= ($selected == $opt) ? 'selected' : ''; ?>><?= $opt; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Poster Event</label>
                                <input type="file" name="poster" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <?php if (isset($event)): ?>
                            <div class="mb-4 bg-white p-3 rounded border">
                                <p class="small fw-bold mb-2">Poster Saat Ini:</p>
                                <?php $img_src = $event['poster']; if (strpos($img_src, 'http') !== 0) $img_src = base_url('assets/uploads/posters/' . $img_src); ?>
                                <img src="<?= $img_src; ?>" alt="Preview" style="height: 120px;" class="rounded shadow-sm">
                            </div>
                        <?php endif; ?>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-brand py-2"><i class="fas fa-save me-2"></i> Simpan Data Event</button>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            Swal.fire({ title: 'Menyimpan Data...', text: 'Mohon tunggu sebentar', icon: 'info', allowOutsideClick: false, showConfirmButton: false, willOpen: () => { Swal.showLoading() } });
        });
    </script>
</body>
</html>
