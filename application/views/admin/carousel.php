<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Carousel - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/variables.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-style.css'); ?>">
</head>

<body class="admin-body">

    <?php $admin_active_menu = 'event'; include(APPPATH . 'views/admin/_navbar.php'); ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h4 class="fw-bold mb-4">Tambah Slider Baru</h4>
                <div class="card card-custom p-4">
                    <?= form_open_multipart('admin/simpan_carousel'); ?>
                    <div class="mb-3">
                        <label class="form-label">Pilih Gambar Slider</label>
                        <input type="file" name="image_file" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul Utama (H1)</label>
                        <input type="text" name="judul_h1" class="form-control" placeholder="Contoh: RAIH PRESTASI">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan Singkat</label>
                        <textarea name="subjudul_p" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Urutan Tampil</label>
                        <input type="number" name="urutan" class="form-control" value="1">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-brand">Tambah Slider</button>
                    </div>
                    <?= form_close(); ?>
                </div>
            </div>

            <div class="col-lg-8">
                <h4 class="fw-bold mb-4">Daftar Slider Aktif</h4>
                <div class="card card-custom">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Urutan</th>
                                    <th>Gambar</th>
                                    <th>Info Slider</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sliders as $sl): ?>
                                    <tr>
                                        <td class="ps-4"><?= $sl['urutan']; ?></td>
                                        <td>
                                            <img src="<?= base_url('assets/uploads/carousel/' . $sl['image_file']); ?>" class="rounded" style="width: 120px; height: 70px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <small class="fw-bold d-block"><?= $sl['judul_h1']; ?></small>
                                            <small class="text-muted"><?= $sl['subjudul_p']; ?></small>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('admin/hapus_carousel/' . $sl['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus slider ini?')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if ($this->session->flashdata('success')): ?>
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '<?= $this->session->flashdata('success'); ?>', confirmButtonColor: '#C60000' });
        <?php endif; ?>
    </script>
</body>
</html>
