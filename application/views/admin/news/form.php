<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($article) ? 'Edit' : 'Tulis'; ?> Berita - Admin Panel</title>

    <link rel="shortcut icon" href="<?= base_url('assets/logo/logo.ico'); ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/variables.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-style.css'); ?>">

    <style>
        .editor-card-header { background: #fff; border-bottom: 1px solid #eee; padding: 20px 24px; }
        .form-label { font-weight: 600; color: #4a4a4a; font-size: .88rem; }
        .form-control, .form-select { border-radius: 9px; padding: 10px 13px; border-color: #ddd; }
        .form-control:focus, .form-select:focus { border-color: var(--brand-primary); box-shadow: 0 0 0 .2rem rgba(198,0,0,.1); }
        .title-input { font-family: 'Oswald', sans-serif; font-size: 1.35rem; text-transform: none; }
        .editor-toolbar { display: flex; flex-wrap: wrap; gap: 5px; padding: 10px; background: #f8f9fa; border: 1px solid #ddd; border-bottom: 0; border-radius: 10px 10px 0 0; }
        .editor-toolbar .btn { min-width: 36px; color: #555; background: #fff; border-color: #ddd; }
        .editor-toolbar .btn:hover { color: var(--brand-primary); border-color: var(--brand-primary); }
        .news-editor { min-height: 430px; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 0 0 10px 10px; line-height: 1.8; outline: none; overflow-y: auto; }
        .news-editor:focus { border-color: var(--brand-primary); box-shadow: 0 0 0 .2rem rgba(198,0,0,.08); }
        .news-editor:empty::before { content: attr(data-placeholder); color: #aaa; }
        .cover-upload { border: 2px dashed #d8d8d8; border-radius: 12px; padding: 20px; text-align: center; background: #fafafa; transition: .2s; cursor: pointer; }
        .cover-upload:hover, .cover-upload.dragover { border-color: var(--brand-primary); background: #fff7f7; }
        .cover-upload input { display: none; }
        .cover-preview-wrap { position: relative; margin-top: 14px; border-radius: 12px; overflow: hidden; background: #eee; }
        .cover-preview { width: 100%; max-height: 260px; object-fit: cover; display: block; }
        .publish-panel { position: sticky; top: 90px; }
        .panel-section { padding: 20px; border-bottom: 1px solid #eee; }
        .panel-section:last-child { border-bottom: 0; }
        .status-dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; margin-right: 7px; }
        .save-bar { background: #fff; border-top: 1px solid #eee; padding: 16px 20px; }
        .helper-box { background: #fff8e1; border-left: 4px solid #ffc107; border-radius: 8px; padding: 12px 14px; }
        .counter { font-size: .72rem; color: #999; }
        @media (max-width: 991.98px) { .publish-panel { position: static; } }
        @media (max-width: 575.98px) { .navbar-brand { font-size: 1rem; } .navbar-brand img { height: 38px; } .news-editor { min-height: 330px; } }
    </style>
</head>

<body class="admin-body">
    <?php $admin_active_menu = 'news'; include(APPPATH . 'views/admin/_navbar.php'); ?>

    <main class="container py-5">
        <div class="d-md-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-danger fw-bold small text-uppercase"><i class="fas fa-<?= isset($article) ? 'edit' : 'pen-nib'; ?> me-2"></i>Editor Digital Silat News</span>
                <h1 class="h3 fw-bold text-dark font-oswald mt-2 mb-1"><?= isset($article) ? 'EDIT BERITA' : 'TULIS BERITA BARU'; ?></h1>
                <p class="text-muted mb-0"><?= isset($article) ? 'Perbarui informasi dan pengaturan publikasi berita.' : 'Susun informasi yang jelas dan mudah dibaca oleh pengunjung.'; ?></p>
            </div>
            <?php if (isset($article)): ?><a href="<?= base_url('admin/news/preview/' . $article['id']); ?>" target="_blank" class="btn btn-outline-danger mt-3 mt-md-0"><i class="far fa-eye me-2"></i>Preview Berita</a><?php endif; ?>
        </div>

        <?php if ($this->session->flashdata('error')): ?><div class="alert alert-danger rounded-3"><i class="fas fa-exclamation-triangle me-2"></i><?= html_escape($this->session->flashdata('error')); ?></div><?php endif; ?>
        <?php if ($this->session->flashdata('success')): ?><div class="alert alert-success rounded-3"><i class="fas fa-check-circle me-2"></i><?= html_escape($this->session->flashdata('success')); ?></div><?php endif; ?>

        <form method="post" action="<?= base_url('admin/news/save'); ?>" enctype="multipart/form-data" id="newsForm">
            <input type="hidden" name="id" value="<?= isset($article) ? (int) $article['id'] : 0; ?>">
            <input type="hidden" name="content" id="contentInput">

            <div class="row g-4">
                <div class="col-lg-8">
                    <section class="card card-custom mb-4">
                        <div class="editor-card-header"><h2 class="h5 fw-bold mb-1"><i class="fas fa-align-left text-danger me-2"></i>Konten Berita</h2><small class="text-muted">Judul, ringkasan, dan isi utama artikel.</small></div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <div class="d-flex justify-content-between"><label class="form-label">Judul Berita <span class="text-danger">*</span></label><span class="counter"><span id="titleCount">0</span>/255</span></div>
                                <input name="title" id="title" class="form-control title-input" required maxlength="255" value="<?= isset($article) ? html_escape($article['title']) : ''; ?>" placeholder="Contoh: Pendaftaran Kejuaraan Pencak Silat Resmi Dibuka">
                                <small class="text-muted">Slug URL akan dibuat otomatis dari judul berita.</small>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between"><label class="form-label">Ringkasan Berita</label><span class="counter"><span id="excerptCount">0</span>/500</span></div>
                                <textarea name="excerpt" id="excerpt" class="form-control" rows="4" maxlength="500" placeholder="Tulis ringkasan singkat yang akan muncul pada kartu berita..."><?= isset($article) ? html_escape($article['excerpt']) : ''; ?></textarea>
                                <small class="text-muted">Disarankan 120-200 karakter agar nyaman dibaca di landing page.</small>
                            </div>
                            <div>
                                <label class="form-label">Isi Berita <span class="text-danger">*</span></label>
                                <div class="editor-toolbar" role="toolbar" aria-label="Toolbar editor">
                                    <button type="button" class="btn btn-sm" data-command="formatBlock" data-value="p" title="Paragraf"><i class="fas fa-paragraph"></i></button>
                                    <button type="button" class="btn btn-sm" data-command="formatBlock" data-value="h2" title="Judul bagian">H2</button>
                                    <button type="button" class="btn btn-sm" data-command="formatBlock" data-value="h3" title="Subjudul">H3</button>
                                    <span class="border-start mx-1"></span>
                                    <button type="button" class="btn btn-sm" data-command="bold" title="Tebal"><i class="fas fa-bold"></i></button>
                                    <button type="button" class="btn btn-sm" data-command="italic" title="Miring"><i class="fas fa-italic"></i></button>
                                    <button type="button" class="btn btn-sm" data-command="underline" title="Garis bawah"><i class="fas fa-underline"></i></button>
                                    <span class="border-start mx-1"></span>
                                    <button type="button" class="btn btn-sm" data-command="insertUnorderedList" title="Bullet list"><i class="fas fa-list-ul"></i></button>
                                    <button type="button" class="btn btn-sm" data-command="insertOrderedList" title="Numbered list"><i class="fas fa-list-ol"></i></button>
                                    <button type="button" class="btn btn-sm" data-command="formatBlock" data-value="blockquote" title="Kutipan"><i class="fas fa-quote-right"></i></button>
                                    <button type="button" class="btn btn-sm" id="btnLink" title="Tambahkan tautan"><i class="fas fa-link"></i></button>
                                    <span class="border-start mx-1"></span>
                                    <button type="button" class="btn btn-sm" data-command="undo" title="Undo"><i class="fas fa-undo"></i></button>
                                    <button type="button" class="btn btn-sm" data-command="redo" title="Redo"><i class="fas fa-redo"></i></button>
                                </div>
                                <div id="newsEditor" class="news-editor" contenteditable="true" data-placeholder="Mulai tulis isi berita di sini..."><?= isset($article) ? $article['content'] : ''; ?></div>
                                <div class="d-flex justify-content-between mt-2"><small class="text-muted">Gunakan heading untuk membagi artikel menjadi bagian yang mudah dibaca.</small><span class="counter"><span id="wordCount">0</span> kata</span></div>
                            </div>
                        </div>
                    </section>

                    <section class="card card-custom">
                        <div class="editor-card-header"><h2 class="h5 fw-bold mb-1"><i class="far fa-image text-danger me-2"></i>Cover Berita</h2><small class="text-muted">Gambar ini tampil pada landing page, daftar news, dan detail artikel.</small></div>
                        <div class="card-body p-4">
                            <label class="cover-upload" id="coverDrop" for="cover">
                                <input type="file" name="cover" id="cover" accept="image/jpeg,image/png,image/webp,image/gif">
                                <span class="d-block fs-2 text-danger mb-2"><i class="fas fa-cloud-upload-alt"></i></span>
                                <strong class="d-block">Klik atau letakkan gambar di sini</strong>
                                <small class="text-muted">JPG, PNG, GIF, atau WebP maksimal 5 MB. File otomatis dioptimalkan ke WebP.</small>
                            </label>
                            <div id="previewWrap" class="cover-preview-wrap <?= isset($article) && $article['cover_image'] ? '' : 'd-none'; ?>">
                                <?php if (isset($article) && $article['cover_image']): ?><img id="coverPreview" class="cover-preview" src="<?= base_url('assets/uploads/news/covers/' . rawurlencode($article['cover_image'])); ?>" alt="Cover saat ini"><?php else: ?><img id="coverPreview" class="cover-preview" src="" alt="Preview cover"><?php endif; ?>
                            </div>
                            <div class="mt-3"><label class="form-label">Alt Text Gambar</label><input name="image_alt" class="form-control" value="<?= isset($article) ? html_escape($article['image_alt']) : ''; ?>" placeholder="Contoh: Pesilat bertanding di Digital Silat Championship"><small class="text-muted">Jelaskan isi gambar secara singkat untuk aksesibilitas dan SEO.</small></div>
                        </div>
                    </section>
                </div>

                <div class="col-lg-4">
                    <aside class="card card-custom publish-panel">
                        <div class="editor-card-header"><h2 class="h5 fw-bold mb-1"><i class="fas fa-paper-plane text-danger me-2"></i>Publikasi</h2><small class="text-muted">Atur bagaimana berita ditampilkan.</small></div>
                        <div class="panel-section">
                            <label class="form-label">Status Berita</label>
                            <select name="status" id="status" class="form-select">
                                <option value="draft" <?= isset($article) && $article['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?= isset($article) && $article['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                                <option value="archived" <?= isset($article) && $article['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                            </select>
                            <div class="small text-muted mt-2" id="statusHelp"><span class="status-dot bg-warning"></span>Draft hanya dapat dilihat admin.</div>
                        </div>
                        <div class="panel-section">
                            <label class="form-label">Tanggal Terbit</label>
                            <div class="input-group"><span class="input-group-text bg-white"><i class="far fa-calendar-alt text-danger"></i></span><input type="datetime-local" name="published_at" class="form-control" value="<?= isset($article) && $article['published_at'] ? date('Y-m-d\TH:i', strtotime($article['published_at'])) : date('Y-m-d\TH:i'); ?>"></div>
                        </div>
                        <div class="panel-section">
                            <label class="form-label">Penulis</label>
                            <div class="input-group"><span class="input-group-text bg-white"><i class="fas fa-user-edit text-danger"></i></span><input name="author_name" class="form-control" value="<?= isset($article) ? html_escape($article['author_name']) : 'Digital Pencak Silat'; ?>"></div>
                        </div>
                        <div class="panel-section">
                            <label class="form-label">Event Terkait <span class="text-muted fw-normal">(Opsional)</span></label>
                            <select name="related_event_id" class="form-select"><option value="">Tidak ada event terkait</option><?php foreach ($events as $event): ?><option value="<?= $event['id']; ?>" <?= isset($article) && (int) $article['related_event_id'] === (int) $event['id'] ? 'selected' : ''; ?>><?= html_escape($event['judul']); ?></option><?php endforeach; ?></select>
                            <small class="text-muted">Event akan ditampilkan pada detail berita.</small>
                        </div>
                        <div class="panel-section">
                            <div class="form-check form-switch"><input type="checkbox" class="form-check-input" name="is_featured" id="featured" value="1" <?= isset($article) && $article['is_featured'] ? 'checked' : ''; ?>><label for="featured" class="form-check-label fw-semibold"><i class="fas fa-star text-warning me-1"></i>Jadikan berita utama</label></div>
                            <small class="text-muted d-block mt-1">Berita akan mendapat posisi utama pada landing page.</small>
                        </div>
                        <div class="panel-section"><div class="helper-box small"><i class="fas fa-lightbulb text-warning me-2"></i>Gunakan <strong>Draft</strong> jika konten belum siap ditampilkan kepada publik.</div></div>
                        <div class="save-bar"><button class="btn btn-brand w-100 py-2" type="submit" id="saveButton"><i class="fas fa-save me-2"></i><?= isset($article) ? 'Simpan Perubahan' : 'Simpan Berita'; ?></button><a href="<?= base_url('admin/news'); ?>" class="btn btn-link text-muted text-decoration-none w-100 mt-2">Batal dan Kembali</a></div>
                    </aside>
                </div>
            </div>
        </form>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const form = document.getElementById('newsForm');
        const editor = document.getElementById('newsEditor');
        const contentInput = document.getElementById('contentInput');
        const title = document.getElementById('title');
        const excerpt = document.getElementById('excerpt');
        const status = document.getElementById('status');

        function updateCounters() {
            document.getElementById('titleCount').textContent = title.value.length;
            document.getElementById('excerptCount').textContent = excerpt.value.length;
            const words = editor.innerText.trim().split(/\s+/).filter(Boolean);
            document.getElementById('wordCount').textContent = words.length;
        }
        title.addEventListener('input', updateCounters);
        excerpt.addEventListener('input', updateCounters);
        editor.addEventListener('input', updateCounters);
        updateCounters();

        document.querySelectorAll('[data-command]').forEach(button => button.addEventListener('click', function() {
            editor.focus();
            document.execCommand(this.dataset.command, false, this.dataset.value || null);
            updateCounters();
        }));
        document.getElementById('btnLink').addEventListener('click', function() {
            const url = prompt('Masukkan URL lengkap (https://...):');
            if (url) { editor.focus(); document.execCommand('createLink', false, url); }
        });

        function updateStatusHelp() {
            const help = document.getElementById('statusHelp');
            if (status.value === 'published') help.innerHTML = '<span class="status-dot bg-success"></span>Berita dapat dilihat oleh publik.';
            else if (status.value === 'archived') help.innerHTML = '<span class="status-dot bg-secondary"></span>Berita disimpan tetapi tidak tampil di publik.';
            else help.innerHTML = '<span class="status-dot bg-warning"></span>Draft hanya dapat dilihat admin.';
        }
        status.addEventListener('change', updateStatusHelp);
        updateStatusHelp();

        const cover = document.getElementById('cover');
        const drop = document.getElementById('coverDrop');
        function previewCover(file) {
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) { Swal.fire({icon:'error',title:'File terlalu besar',text:'Ukuran cover maksimal 5 MB.',confirmButtonColor:'#C60000'}); cover.value = ''; return; }
            document.getElementById('coverPreview').src = URL.createObjectURL(file);
            document.getElementById('previewWrap').classList.remove('d-none');
        }
        cover.addEventListener('change', () => previewCover(cover.files[0]));
        ['dragenter','dragover'].forEach(name => drop.addEventListener(name, e => { e.preventDefault(); drop.classList.add('dragover'); }));
        ['dragleave','drop'].forEach(name => drop.addEventListener(name, e => { e.preventDefault(); drop.classList.remove('dragover'); }));
        drop.addEventListener('drop', e => { if (e.dataTransfer.files.length) { cover.files = e.dataTransfer.files; previewCover(cover.files[0]); } });

        form.addEventListener('submit', function(e) {
            contentInput.value = editor.innerHTML.trim();
            if (!editor.innerText.trim()) { e.preventDefault(); Swal.fire({icon:'warning',title:'Isi berita masih kosong',text:'Silakan tulis isi berita sebelum menyimpan.',confirmButtonColor:'#C60000'}); editor.focus(); return; }
            document.getElementById('saveButton').disabled = true;
            document.getElementById('saveButton').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        });

        $('#btn-logout').on('click', function(e) { e.preventDefault(); const href = $(this).attr('href'); Swal.fire({title:'Keluar dari Admin?',icon:'question',showCancelButton:true,confirmButtonColor:'#C60000',cancelButtonColor:'#6c757d',confirmButtonText:'Logout',cancelButtonText:'Batal'}).then(result => { if (result.isConfirmed) window.location.href = href; }); });
    </script>
</body>
</html>
