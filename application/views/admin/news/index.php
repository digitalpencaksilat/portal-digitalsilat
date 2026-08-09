<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen News - Digital Pencak Silat</title>

    <link rel="shortcut icon" href="<?= base_url('assets/logo/logo.ico'); ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/variables.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-style.css'); ?>">

    <style>
        .news-page-header {
            background: linear-gradient(135deg, #fff 0%, #fff7f7 100%);
            border-left: 5px solid var(--brand-primary);
            border-radius: 15px;
            padding: 24px 28px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.04);
        }
        .news-stat-card { border: 0; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,.045); }
        .news-stat-icon { width: 44px; height: 44px; border-radius: 11px; display: grid; place-items: center; }
        .filter-card { border: 0; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,.05); }
        .news-cover { width: 108px; height: 68px; object-fit: cover; border-radius: 10px; background: #eee; }
        .news-cover-empty { width: 108px; height: 68px; border-radius: 10px; background: #f1f3f5; color: #adb5bd; display: grid; place-items: center; font-size: 1.4rem; }
        .news-title-cell { min-width: 300px; }
        .news-event-cell { max-width: 190px; }
        .action-buttons { white-space: nowrap; }
        .table-news thead th { color: #fff; background: var(--brand-primary); border: 0; font-size: .78rem; letter-spacing: .45px; text-transform: uppercase; padding: 15px 12px; }
        .table-news tbody td { padding: 14px 12px; border-color: #f0f0f0; }
        .status-badge { min-width: 92px; padding: 7px 10px; }
        @media (max-width: 991.98px) { .news-page-header { padding: 20px; } }
        @media (max-width: 575.98px) {
            .navbar-brand { font-size: 1rem; }
            .navbar-brand img { height: 38px; }
            .news-page-header .btn { width: 100%; margin-top: 16px; }
        }
    </style>
</head>

<body class="admin-body">
    <?php $admin_active_menu = 'news'; include(APPPATH . 'views/admin/_navbar.php'); ?>

    <main class="container py-5">
        <section class="news-page-header d-lg-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="text-danger fw-bold small text-uppercase"><i class="fas fa-newspaper me-2"></i>Digital Pencak Silat News</span>
                <h1 class="h3 fw-bold text-dark font-oswald mt-2 mb-1">MANAJEMEN NEWS</h1>
                <p class="text-muted mb-0">Tulis, tinjau, dan publikasikan informasi kejuaraan serta kegiatan Digital Pencak Silat.</p>
            </div>
            <a href="<?= base_url('admin/news/create'); ?>" class="btn btn-brand rounded-pill px-4 py-2">
                <i class="fas fa-plus me-2"></i>Tulis Berita
            </a>
        </section>

        <section class="row g-3 mb-4">
            <?php
            $stats = [
                ['label' => 'Total Berita', 'value' => $news_counts['all'], 'icon' => 'newspaper', 'color' => 'dark'],
                ['label' => 'Published', 'value' => $news_counts['published'], 'icon' => 'check-circle', 'color' => 'success'],
                ['label' => 'Draft', 'value' => $news_counts['draft'], 'icon' => 'pen', 'color' => 'warning'],
                ['label' => 'Archived', 'value' => $news_counts['archived'], 'icon' => 'archive', 'color' => 'secondary'],
            ];
            ?>
            <?php foreach ($stats as $stat): ?>
                <div class="col-6 col-lg-3">
                    <div class="card news-stat-card h-100"><div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div><small class="text-muted text-uppercase fw-bold"><?= $stat['label']; ?></small><h3 class="fw-bold mb-0 mt-1"><?= number_format($stat['value']); ?></h3></div>
                        <div class="news-stat-icon bg-<?= $stat['color']; ?> bg-opacity-10 text-<?= $stat['color']; ?>"><i class="fas fa-<?= $stat['icon']; ?>"></i></div>
                    </div></div>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="card filter-card mb-4">
            <div class="card-body p-3 p-md-4">
                <form class="row g-3 align-items-end" method="get">
                    <div class="col-lg-6">
                        <label class="form-label fw-bold small text-uppercase">Cari Berita</label>
                        <div class="input-group"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span><input class="form-control" name="keyword" value="<?= html_escape($keyword); ?>" placeholder="Cari judul atau isi berita..."></div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label fw-bold small text-uppercase">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <?php foreach (['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'] as $value => $label): ?>
                                <option value="<?= $value; ?>" <?= $status_filter === $value ? 'selected' : ''; ?>><?= $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 d-flex gap-2">
                        <button class="btn btn-brand flex-grow-1"><i class="fas fa-filter me-2"></i>Terapkan</button>
                        <?php if ($keyword !== '' || $status_filter !== ''): ?><a href="<?= base_url('admin/news'); ?>" class="btn btn-outline-secondary" title="Reset filter"><i class="fas fa-undo"></i></a><?php endif; ?>
                    </div>
                </form>
            </div>
        </section>

        <section class="card card-custom">
            <div class="card-header bg-white border-0 px-4 py-3 d-flex justify-content-between align-items-center">
                <div><h2 class="h5 fw-bold mb-1">Daftar Berita</h2><small class="text-muted">Menampilkan <?= count($articles); ?> berita pada halaman ini</small></div>
                <a href="<?= base_url('news'); ?>" target="_blank" class="btn btn-sm btn-outline-dark"><i class="fas fa-external-link-alt me-1"></i> Lihat Portal</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-news">
                    <thead><tr><th class="ps-4">Cover</th><th>Informasi Berita</th><th>Status</th><th>Publikasi</th><th>Event Terkait</th><th class="text-end pe-4">Aksi</th></tr></thead>
                    <tbody>
                    <?php if (!$articles): ?>
                        <tr><td colspan="6"><div class="text-center py-5"><div class="text-danger fs-1 mb-3"><i class="far fa-newspaper"></i></div><h5 class="fw-bold">Belum ada berita ditemukan</h5><p class="text-muted mb-3"><?= $keyword !== '' || $status_filter !== '' ? 'Coba ubah kata kunci atau filter status.' : 'Mulai publikasikan informasi terbaru Digital Pencak Silat.'; ?></p><a href="<?= base_url('admin/news/create'); ?>" class="btn btn-brand"><i class="fas fa-plus me-2"></i>Tulis Berita</a></div></td></tr>
                    <?php endif; ?>
                    <?php foreach ($articles as $article): ?>
                        <?php $cover = $article['thumbnail_image'] ?: $article['thumbnail_image_fallback'] ?: $article['cover_image']; ?>
                        <tr>
                            <td class="ps-4"><?php if ($cover): ?><img src="<?= base_url('assets/uploads/news/covers/' . rawurlencode($cover)); ?>" alt="<?= html_escape($article['image_alt']); ?>" class="news-cover"><?php else: ?><div class="news-cover-empty"><i class="far fa-image"></i></div><?php endif; ?></td>
                            <td class="news-title-cell"><div class="d-flex align-items-center flex-wrap gap-2 mb-1"><h3 class="h6 fw-bold text-dark mb-0"><?= html_escape($article['title']); ?></h3><?php if ($article['is_featured']): ?><span class="badge bg-warning text-dark"><i class="fas fa-star me-1"></i>Utama</span><?php endif; ?></div><p class="small text-muted mb-1"><?= html_escape(character_limiter(strip_tags($article['excerpt']), 110, '…')); ?></p><small class="text-muted"><i class="fas fa-user-edit me-1"></i><?= html_escape($article['author_name']); ?></small></td>
                            <td><?php $status_class = $article['status'] === 'published' ? 'success' : ($article['status'] === 'archived' ? 'secondary' : 'warning text-dark'); ?><span class="badge rounded-pill bg-<?= $status_class; ?> status-badge"><?= ucfirst($article['status']); ?></span></td>
                            <td><small class="fw-semibold d-block"><?= $article['published_at'] ? date('d M Y', strtotime($article['published_at'])) : 'Belum diterbitkan'; ?></small><small class="text-muted"><?= $article['published_at'] ? date('H:i', strtotime($article['published_at'])) . ' WIB' : '-'; ?></small></td>
                            <td class="news-event-cell"><?php if ($article['event_title']): ?><span class="small fw-semibold"><i class="fas fa-calendar-alt text-danger me-1"></i><?= html_escape($article['event_title']); ?></span><?php else: ?><span class="text-muted small">Tidak terkait event</span><?php endif; ?></td>
                            <td class="text-end pe-4 action-buttons"><a href="<?= base_url('admin/news/preview/' . $article['id']); ?>" target="_blank" class="btn btn-sm btn-light border" title="Preview"><i class="far fa-eye"></i></a> <a href="<?= base_url('admin/news/edit/' . $article['id']); ?>" class="btn btn-sm btn-light text-primary border" title="Edit"><i class="fas fa-edit"></i></a> <?= form_open('admin/news/delete/' . $article['id'], ['class' => 'd-inline news-delete-form']); ?><button type="submit" class="btn btn-sm btn-light text-danger border" title="Hapus"><i class="fas fa-trash"></i></button><?= form_close(); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <?php if ($last_page > 1): ?><nav class="mt-4"><ul class="pagination justify-content-center"><?php for ($i = 1; $i <= $last_page; $i++): ?><li class="page-item <?= $i === $page ? 'active' : ''; ?>"><a class="page-link" href="?page=<?= $i; ?>&keyword=<?= urlencode($keyword); ?>&status=<?= urlencode($status_filter); ?>"><?= $i; ?></a></li><?php endfor; ?></ul></nav><?php endif; ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if ($this->session->flashdata('success')): ?>Swal.fire({icon:'success',title:'Berhasil!',text:<?= json_encode($this->session->flashdata('success')); ?>,timer:2200,showConfirmButton:false});<?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>Swal.fire({icon:'error',title:'Gagal!',text:<?= json_encode($this->session->flashdata('error')); ?>,confirmButtonColor:'#C60000'});<?php endif; ?>
        $('.news-delete-form').on('submit', function(e) { e.preventDefault(); const form = this; Swal.fire({title:'Hapus berita ini?',text:'Data dan gambar berita akan dihapus permanen.',icon:'warning',showCancelButton:true,confirmButtonColor:'#C60000',cancelButtonColor:'#6c757d',confirmButtonText:'Ya, Hapus',cancelButtonText:'Batal'}).then(result => { if (result.isConfirmed) form.submit(); }); });
        $('#btn-logout').on('click', function(e) { e.preventDefault(); const href = $(this).attr('href'); Swal.fire({title:'Keluar dari Admin?',icon:'question',showCancelButton:true,confirmButtonColor:'#C60000',cancelButtonColor:'#6c757d',confirmButtonText:'Logout',cancelButtonText:'Batal'}).then(result => { if (result.isConfirmed) window.location.href = href; }); });
    </script>
</body>
</html>
