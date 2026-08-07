<?php
$months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$format_date = function ($value) use ($months) {
    if (!$value) return '-';
    $timestamp = strtotime($value);
    return date('d', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
};
$format_event_date = function ($start, $end, $legacy) use ($format_date) {
    if ($start && $end && $start !== $end) return $format_date($start) . ' - ' . $format_date($end);
    if ($start || $end) return $format_date($start ?: $end);
    return $legacy ?: '-';
};
$cover = $article['cover_image'] ?: $article['cover_image_fallback'];
$word_count = str_word_count(strip_tags($article['content']));
$reading_time = max(1, (int) ceil($word_count / 200));
$event_poster = !empty($article['event_poster']) ? $article['event_poster'] : NULL;
if ($event_poster && strpos($event_poster, 'http') !== 0) $event_poster = base_url('assets/uploads/posters/' . rawurlencode($event_poster));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($article['title']); ?> - Digital Silat News</title>
    <meta name="description" content="<?= html_escape($article['excerpt']); ?>">
    <meta property="og:title" content="<?= html_escape($article['title']); ?>">
    <meta property="og:description" content="<?= html_escape($article['excerpt']); ?>">
    <?php if ($cover): ?><meta property="og:image" content="<?= base_url('assets/uploads/news/covers/' . rawurlencode($cover)); ?>"><?php endif; ?>
    <link rel="shortcut icon" href="<?= base_url('assets/logo/logo.ico'); ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/variables.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/main-style.css'); ?>">
    <style>
        body { padding-top: 80px; background: #f7f7f7; }
        .article-header { background: #fff; border-bottom: 1px solid #ececec; padding: 55px 0 42px; }
        .breadcrumb-news { display: flex; flex-wrap: wrap; align-items: center; gap: 9px; margin-bottom: 28px; color: #888; font-size: .8rem; }
        .breadcrumb-news a { color: #666; text-decoration: none; }
        .breadcrumb-news a:hover { color: var(--brand-primary); }
        .article-heading { max-width: 980px; }
        .article-kicker { color: var(--brand-primary); font-size: .76rem; font-weight: 700; letter-spacing: 1.6px; text-transform: uppercase; }
        .article-title { margin: 12px 0 18px; color: #181818; font-size: clamp(2.15rem, 5vw, 4rem); line-height: 1.08; text-transform: uppercase; }
        .article-lead { max-width: 850px; color: #666; font-size: 1.05rem; line-height: 1.75; }
        .article-meta { display: flex; flex-wrap: wrap; gap: 9px 22px; margin-top: 24px; color: #777; font-size: .82rem; }
        .article-meta span { display: inline-flex; align-items: center; gap: 7px; }
        .article-meta i { color: var(--brand-primary); }
        .article-shell { max-width: 1160px; }
        .article-cover-wrap { overflow: hidden; margin-top: 34px; background: #e9ecef; border-radius: 20px; box-shadow: 0 15px 38px rgba(0,0,0,.12); }
        .article-cover { display: block; width: 100%; aspect-ratio: 16/9; object-fit: cover; }
        .cover-caption { padding: 11px 16px; color: #777; background: #fff; font-size: .75rem; }
        .article-layout { display: grid; grid-template-columns: 68px minmax(0, 790px); justify-content: center; gap: 30px; padding: 54px 0; }
        .share-rail { position: sticky; top: 110px; align-self: start; display: flex; flex-direction: column; align-items: center; gap: 9px; }
        .share-rail-label { color: #888; font-size: .65rem; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; writing-mode: vertical-rl; margin-bottom: 5px; }
        .share-button { display: grid; place-items: center; width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 50%; color: #555; background: #fff; transition: .2s; }
        button.share-button { cursor: pointer; }
        .share-button:hover { color: #fff; border-color: var(--brand-primary); background: var(--brand-primary); }
        .article-content { color: #343434; font-family: 'Poppins', sans-serif; font-size: 1.04rem; line-height: 1.95; text-align: justify; text-justify: inter-word; }
        .article-content p { margin-bottom: 1.45rem; }
        .article-content h2 { margin: 2.6rem 0 1rem; font-size: 1.8rem; line-height: 1.3; }
        .article-content h3 { margin: 2.1rem 0 .9rem; font-size: 1.35rem; line-height: 1.35; }
        .article-content ul, .article-content ol { margin-bottom: 1.5rem; padding-left: 1.4rem; }
        .article-content li { margin-bottom: .55rem; }
        .article-content blockquote { position: relative; margin: 2.2rem 0; padding: 24px 28px 24px 34px; color: #343434; background: #fff; border-left: 5px solid var(--brand-primary); border-radius: 0 12px 12px 0; box-shadow: 0 5px 18px rgba(0,0,0,.05); font-size: 1.08rem; font-style: italic; }
        .article-content a { color: var(--brand-primary); }
        .mobile-share { display: none; padding-top: 28px; border-top: 1px solid #ddd; }
        .event-related { display: grid; grid-template-columns: 170px minmax(0,1fr) auto; gap: 25px; align-items: center; padding: 24px; background: #1b1b1b; color: #fff; border-radius: 18px; overflow: hidden; box-shadow: 0 12px 28px rgba(0,0,0,.12); }
        .event-related-image { width: 170px; height: 125px; object-fit: cover; border-radius: 12px; background: #333; }
        .event-related-kicker { color: #ffd04a; font-size: .72rem; font-weight: 700; letter-spacing: 1.4px; text-transform: uppercase; }
        .event-related h2 { margin: 7px 0 8px; font-size: 1.55rem; }
        .event-related-meta { display: flex; flex-wrap: wrap; gap: 10px 22px; color: rgba(255,255,255,.7); font-size: .8rem; }
        .related-section { padding: 72px 0; }
        .related-heading { border-bottom: 1px solid #ddd; padding-bottom: 14px; margin-bottom: 25px; }
        .related-heading h2 { margin: 0; font-size: 2rem; }
        .related-heading h2::after { content: ''; display: block; width: 52px; height: 4px; margin-top: 10px; background: var(--brand-primary); }
        .related-card { display: block; overflow: hidden; height: 100%; color: #222; text-decoration: none; background: #fff; border-radius: 16px; box-shadow: 0 6px 22px rgba(0,0,0,.055); transition: transform .25s, box-shadow .25s; }
        .related-card:hover { color: #222; transform: translateY(-5px); box-shadow: 0 13px 30px rgba(0,0,0,.1); }
        .related-card-image { aspect-ratio: 16/9; overflow: hidden; }
        .related-card img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
        .related-card:hover img { transform: scale(1.04); }
        .related-card-body { padding: 19px; }
        .related-card h3 { margin: 8px 0 0; font-size: 1.14rem; line-height: 1.35; }
        .news-footer { margin-top: 10px; }
        @media (max-width: 991.98px) { .event-related { grid-template-columns: 135px minmax(0,1fr); } .event-related .btn { grid-column: 2; justify-self: start; } .event-related-image { width: 135px; height: 135px; grid-row: span 2; } }
        @media (max-width: 767.98px) { body { padding-top: 70px; } .article-header { padding: 40px 0 30px; } .article-cover-wrap { margin-top: 25px; border-radius: 14px; } .article-layout { display: block; padding: 38px 0; } .share-rail { display: none; } .mobile-share { display: block; } .event-related { grid-template-columns: 100px minmax(0,1fr); gap: 16px; } .event-related-image { width: 100px; height: 120px; } }
        @media (max-width: 575.98px) { .navbar-brand { font-size: 1rem; } .navbar-brand img { height: 40px; } .article-title { font-size: 2.05rem; } .article-lead { font-size: .95rem; } .article-content { font-size: .97rem; line-height: 1.85; } .article-content blockquote { padding: 20px; } .event-related { grid-template-columns: 1fr; padding: 20px; } .event-related-image { width: 100%; height: 190px; grid-row: auto; } .event-related .btn { grid-column: 1; width: 100%; } }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top"><div class="container"><a class="navbar-brand" href="<?= base_url(); ?>"><img src="<?= base_url('assets/logo/logo.png'); ?>" alt="Logo Digital Pencak Silat">DIGITAL PENCAK SILAT</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNews" aria-controls="navbarNews" aria-expanded="false" aria-label="Buka navigasi"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNews"><ul class="navbar-nav ms-auto"><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>#heroCarousel">Beranda</a></li><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>#about">Tentang Kami</a></li><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>#events">Jadwal Event</a></li><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>#peringkat">Peringkat</a></li><li class="nav-item"><a class="nav-link active" href="<?= base_url('news'); ?>">News</a></li><li class="nav-item"><a class="nav-link" href="#contact">Kontak</a></li></ul></div></div></nav>

    <header class="article-header">
        <div class="container article-shell">
            <nav class="breadcrumb-news" aria-label="Breadcrumb"><a href="<?= base_url(); ?>">Beranda</a><i class="fas fa-chevron-right small"></i><a href="<?= base_url('news'); ?>">Digital Silat News</a><i class="fas fa-chevron-right small"></i><span>Detail Berita</span></nav>
            <div class="article-heading"><span class="article-kicker"><i class="far fa-newspaper me-2"></i>Digital Silat News</span><h1 class="article-title"><?= html_escape($article['title']); ?></h1><?php if ($article['excerpt']): ?><p class="article-lead"><?= html_escape($article['excerpt']); ?></p><?php endif; ?><div class="article-meta"><span><i class="far fa-calendar-alt"></i><?= $article['published_at'] ? $format_date($article['published_at']) : 'Preview Draft'; ?></span><span><i class="far fa-user"></i><?= html_escape($article['author_name']); ?></span><span><i class="far fa-clock"></i><?= $reading_time; ?> menit membaca</span></div></div>
            <?php if ($cover): ?><figure class="article-cover-wrap mb-0"><img class="article-cover" src="<?= base_url('assets/uploads/news/covers/' . rawurlencode($cover)); ?>" alt="<?= html_escape($article['image_alt']); ?>"><figcaption class="cover-caption"><i class="fas fa-camera me-2 text-danger"></i><?= html_escape($article['image_alt']); ?></figcaption></figure><?php endif; ?>
        </div>
    </header>

    <main>
        <div class="container article-shell">
            <article class="article-layout">
                <aside class="share-rail" aria-label="Bagikan berita"><span class="share-rail-label">Bagikan</span><button type="button" class="share-button" onclick="shareWhatsApp()" title="Bagikan ke WhatsApp"><i class="fab fa-whatsapp"></i></button><button type="button" class="share-button" onclick="copyLink(this)" title="Salin tautan"><i class="far fa-copy"></i></button></aside>
                <div><div class="article-content"><?= $article['content']; ?></div><div class="mobile-share"><h2 class="h6 fw-bold text-uppercase">Bagikan Berita</h2><button type="button" class="btn btn-success me-2" onclick="shareWhatsApp()"><i class="fab fa-whatsapp me-2"></i>WhatsApp</button><button type="button" class="btn btn-outline-dark" onclick="copyLink(this)"><i class="far fa-copy me-2"></i>Salin Link</button></div></div>
            </article>

            <?php if (!empty($article['related_event_id'])): ?>
            <section class="event-related"><img class="event-related-image" src="<?= $event_poster ?: base_url('assets/carousel/carousel-1.webp'); ?>" alt="Poster <?= html_escape($article['event_title']); ?>"><div><span class="event-related-kicker"><i class="fas fa-link me-1"></i>Event Terkait</span><h2><?= html_escape($article['event_title']); ?></h2><div class="event-related-meta"><span><i class="far fa-calendar-alt me-1"></i><?= html_escape($format_event_date($article['tanggal_mulai'], $article['tanggal_selesai'], $article['tanggal_pelaksanaan'])); ?></span><span><i class="fas fa-map-marker-alt me-1"></i><?= html_escape($article['tempat']); ?></span></div></div><a class="btn btn-brand px-4" href="<?= base_url('event/detail/' . (int) $article['related_event_id']); ?>">Lihat Detail Event <i class="fas fa-arrow-right ms-2"></i></a></section>
            <?php endif; ?>

            <?php if ($related): ?>
            <section class="related-section"><div class="related-heading"><small class="text-danger fw-bold text-uppercase">Lanjut Membaca</small><h2>Berita Terkait</h2></div><div class="row g-4"><?php foreach ($related as $item): ?><?php $related_image = $item['thumbnail_image'] ?: $item['thumbnail_image_fallback'] ?: $item['cover_image'] ?: $item['cover_image_fallback']; ?><div class="col-md-6 col-lg-4"><a class="related-card" href="<?= base_url('news/detail/' . rawurlencode($item['slug'])); ?>"><div class="related-card-image"><img src="<?= $related_image ? base_url('assets/uploads/news/covers/' . rawurlencode($related_image)) : base_url('assets/carousel/carousel-1.webp'); ?>" alt="<?= html_escape($item['image_alt']); ?>" loading="lazy"></div><div class="related-card-body"><small class="text-danger fw-semibold"><i class="far fa-calendar-alt me-1"></i><?= $format_date($item['published_at']); ?></small><h3><?= html_escape($item['title']); ?></h3></div></a></div><?php endforeach; ?></div></section>
            <?php endif; ?>
        </div>
    </main>

    <footer id="contact" class="news-footer"><div class="container"><div class="row"><div class="col-md-4 mb-4"><div class="footer-brand"><img src="<?= base_url('assets/logo/logo.png'); ?>" alt="Logo"><span class="brand-text">DIGITAL PENCAK SILAT</span></div><p class="text-muted">Mendukung digitalisasi event pencak silat untuk pelestarian budaya dan prestasi atlet Indonesia.</p></div><div class="col-md-4 mb-4"><h5 class="text-uppercase fw-bold mb-3 text-danger">Navigasi</h5><ul class="list-unstyled"><li class="mb-2"><a href="<?= base_url(); ?>" class="text-dark text-decoration-none">Beranda</a></li><li class="mb-2"><a href="<?= base_url(); ?>#events" class="text-dark text-decoration-none">Jadwal Event</a></li><li class="mb-2"><a href="<?= base_url('peringkat'); ?>" class="text-dark text-decoration-none">Peringkat Atlet</a></li><li class="mb-2"><a href="<?= base_url('news'); ?>" class="text-dark text-decoration-none">Digital Silat News</a></li></ul></div><div class="col-md-4 mb-4"><h5 class="text-uppercase fw-bold mb-3 text-danger">Kontak</h5><ul class="list-unstyled"><li class="mb-2"><i class="fab fa-whatsapp me-2 text-success"></i>+<?= html_escape(isset($s['whatsapp']) ? $s['whatsapp'] : ''); ?></li><li class="mb-2"><i class="far fa-envelope me-2 text-danger"></i><?= html_escape(isset($s['email']) ? $s['email'] : ''); ?></li><li class="mb-2"><a href="https://instagram.com/<?= html_escape(isset($s['instagram']) ? $s['instagram'] : ''); ?>" target="_blank" rel="noopener" class="text-dark text-decoration-none"><i class="fab fa-instagram me-2 text-danger"></i>@<?= html_escape(isset($s['instagram']) ? $s['instagram'] : ''); ?></a></li></ul></div></div><hr><div class="text-center pb-3 pt-2"><small class="text-muted">&copy; <?= date('Y'); ?> Digital Pencak Silat. All rights reserved.</small></div></div></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function shareWhatsApp() { window.open('https://wa.me/?text=' + encodeURIComponent(document.title + '\n' + window.location.href), '_blank', 'noopener'); }
        function copyLink(button) { navigator.clipboard.writeText(window.location.href).then(function() { const original = button.innerHTML; button.innerHTML = '<i class="fas fa-check"></i>'; setTimeout(function() { button.innerHTML = original; }, 1800); }); }
    </script>
</body>
</html>
