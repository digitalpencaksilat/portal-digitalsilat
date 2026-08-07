<?php
$format_news_date = function ($value) {
    $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $timestamp = strtotime($value);
    return date('d', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
};
$image_url = function ($article, $thumbnail = FALSE) {
    $name = $thumbnail
        ? ($article['thumbnail_image'] ?: $article['thumbnail_image_fallback'] ?: $article['cover_image'] ?: $article['cover_image_fallback'])
        : ($article['cover_image'] ?: $article['cover_image_fallback']);
    return $name ? base_url('assets/uploads/news/covers/' . rawurlencode($name)) : base_url('assets/carousel/carousel-1.webp');
};
$featured = !empty($articles) && $keyword === '' && $page === 1 ? $articles[0] : NULL;
$latest = $featured ? array_slice($articles, 1, 2) : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Silat News - Digital Pencak Silat</title>
    <meta name="description" content="Berita terbaru seputar kejuaraan dan kegiatan Digital Pencak Silat.">
    <link rel="shortcut icon" href="<?= base_url('assets/logo/logo.ico'); ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/variables.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/main-style.css'); ?>">
    <style>
        body { background: #f7f7f7; padding-top: 80px; }
        .news-masthead { position: relative; overflow: hidden; background: #171717; color: #fff; padding: 74px 0 78px; }
        .news-masthead::before { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(0,0,0,.92) 0%, rgba(0,0,0,.68) 52%, rgba(198,0,0,.45) 100%), url('<?= base_url('assets/carousel/carousel-3.webp'); ?>') center 43%/cover; }
        .news-masthead .container { position: relative; z-index: 1; }
        .masthead-kicker { display: inline-flex; align-items: center; gap: 9px; color: #ffd04a; font-size: .8rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
        .masthead-title { max-width: 780px; margin: 12px 0 15px; font-size: clamp(2.25rem, 5vw, 4.25rem); line-height: 1.03; }
        .masthead-copy { max-width: 650px; color: rgba(255,255,255,.78); font-size: 1.05rem; line-height: 1.75; }
        .search-panel { position: relative; z-index: 2; margin-top: -32px; background: #fff; border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,.1); padding: 18px; }
        .search-panel .form-control { min-height: 52px; border: 0; background: #f5f5f5; }
        .search-panel .input-group-text { border: 0; background: #f5f5f5; color: #888; }
        .featured-grid { display: grid; grid-template-columns: minmax(0, 1.7fr) minmax(320px, .8fr); gap: 24px; }
        .featured-story { position: relative; min-height: 520px; overflow: hidden; border-radius: 20px; color: #fff; box-shadow: 0 16px 35px rgba(0,0,0,.16); }
        .featured-story img { position: absolute; width: 100%; height: 100%; object-fit: cover; transition: transform .5s ease; }
        .featured-story::after { content: ''; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,.03) 25%, rgba(0,0,0,.88) 100%); }
        .featured-story:hover img { transform: scale(1.035); }
        .featured-content { position: absolute; z-index: 2; left: 0; right: 0; bottom: 0; padding: 36px; }
        .story-label { display: inline-block; padding: 7px 11px; border-radius: 6px; color: #fff; background: var(--brand-primary); font-size: .72rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .featured-content h2 { max-width: 760px; font-size: clamp(1.7rem, 3vw, 2.65rem); line-height: 1.12; margin: 13px 0 11px; }
        .story-meta { display: flex; flex-wrap: wrap; gap: 17px; font-size: .8rem; color: rgba(255,255,255,.78); }
        .latest-panel { background: #fff; border-radius: 20px; padding: 24px; box-shadow: 0 8px 24px rgba(0,0,0,.06); }
        .latest-heading { border-bottom: 2px solid #eee; padding-bottom: 14px; margin-bottom: 4px; }
        .latest-heading span { border-bottom: 2px solid var(--brand-primary); padding-bottom: 14px; }
        .latest-item { display: grid; grid-template-columns: 112px minmax(0, 1fr); gap: 14px; padding: 19px 0; color: #222; text-decoration: none; border-bottom: 1px solid #eee; }
        .latest-item:last-of-type { border-bottom: 0; }
        .latest-item img { width: 112px; height: 90px; object-fit: cover; border-radius: 10px; }
        .latest-item h3 { font-family: 'Oswald', sans-serif; font-size: 1.03rem; line-height: 1.35; margin: 2px 0 7px; text-transform: uppercase; transition: color .2s; }
        .latest-item:hover h3 { color: var(--brand-primary); }
        .section-heading { display: flex; justify-content: space-between; align-items: end; gap: 20px; padding-bottom: 14px; border-bottom: 1px solid #ddd; }
        .section-heading h2 { font-size: 2rem; margin: 0; }
        .section-heading h2::after { content: ''; display: block; width: 54px; height: 4px; margin-top: 10px; background: var(--brand-primary); }
        .article-card { display: flex; flex-direction: column; height: 100%; overflow: hidden; color: #222; text-decoration: none; background: #fff; border-radius: 16px; box-shadow: 0 6px 22px rgba(0,0,0,.055); transition: transform .25s, box-shadow .25s; }
        .article-card:hover { color: #222; transform: translateY(-5px); box-shadow: 0 13px 30px rgba(0,0,0,.1); }
        .article-image { position: relative; aspect-ratio: 16/10; overflow: hidden; background: #e9ecef; }
        .article-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
        .article-card:hover .article-image img { transform: scale(1.04); }
        .article-body { display: flex; flex-direction: column; flex: 1; padding: 21px; }
        .article-date { color: #8b8b8b; font-size: .76rem; }
        .article-body h3 { font-size: 1.25rem; line-height: 1.35; margin: 10px 0; }
        .article-body p { color: #727272; font-size: .88rem; line-height: 1.7; }
        .read-more { margin-top: auto; color: var(--brand-primary); font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; }
        .empty-news { background: #fff; border-radius: 18px; padding: 65px 24px; text-align: center; box-shadow: 0 7px 24px rgba(0,0,0,.05); }
        .news-footer { margin-top: 70px; }
        @media (max-width: 991.98px) { .featured-grid { grid-template-columns: 1fr; } .featured-story { min-height: 470px; } .latest-panel { padding: 22px; } }
        @media (max-width: 767.98px) { body { padding-top: 70px; } .news-masthead { padding: 58px 0 62px; } .search-panel { margin-top: -26px; } .featured-story { min-height: 430px; } .featured-content { padding: 25px; } }
        @media (max-width: 575.98px) { .navbar-brand { font-size: 1rem; } .navbar-brand img { height: 40px; } .featured-story { min-height: 410px; border-radius: 15px; } .featured-content { padding: 21px; } .latest-item { grid-template-columns: 96px minmax(0,1fr); } .latest-item img { width: 96px; height: 78px; } .section-heading { align-items: start; flex-direction: column; gap: 8px; } }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url(); ?>"><img src="<?= base_url('assets/logo/logo.png'); ?>" alt="Logo Digital Pencak Silat">DIGITAL PENCAK SILAT</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNews" aria-controls="navbarNews" aria-expanded="false" aria-label="Buka navigasi"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNews"><ul class="navbar-nav ms-auto"><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>#heroCarousel">Beranda</a></li><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>#about">Tentang Kami</a></li><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>#events">Jadwal Event</a></li><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>#peringkat">Peringkat</a></li><li class="nav-item"><a class="nav-link active" href="<?= base_url('news'); ?>">News</a></li><li class="nav-item"><a class="nav-link" href="#contact">Kontak</a></li></ul></div>
        </div>
    </nav>

    <header class="news-masthead">
        <div class="container"><div class="masthead-kicker"><i class="fas fa-circle-notch"></i> Kanal Informasi Resmi</div><h1 class="masthead-title">DIGITAL SILAT NEWS</h1><p class="masthead-copy mb-0">Kabar terbaru seputar kejuaraan, hasil pertandingan, dan kegiatan Digital Pencak Silat dalam satu portal informasi.</p></div>
    </header>

    <main>
        <div class="container">
            <form class="search-panel" method="get" action="<?= base_url('news'); ?>">
                <div class="row g-2"><div class="col-md"><div class="input-group"><span class="input-group-text"><i class="fas fa-search"></i></span><input class="form-control" name="keyword" value="<?= html_escape($keyword); ?>" placeholder="Cari judul atau isi berita..." aria-label="Cari berita"></div></div><div class="col-md-auto"><button class="btn btn-brand h-100 px-4 w-100"><i class="fas fa-search me-2"></i>Cari Berita</button></div><?php if ($keyword !== ''): ?><div class="col-md-auto"><a class="btn btn-outline-secondary h-100 d-flex align-items-center justify-content-center" href="<?= base_url('news'); ?>"><i class="fas fa-times me-2"></i>Reset</a></div><?php endif; ?></div>
            </form>

            <?php if ($featured): ?>
            <section class="py-5">
                <div class="featured-grid">
                    <a class="featured-story" href="<?= base_url('news/detail/' . rawurlencode($featured['slug'])); ?>"><img src="<?= $image_url($featured); ?>" alt="<?= html_escape($featured['image_alt']); ?>"><div class="featured-content"><span class="story-label"><i class="fas fa-star me-1"></i> Berita Utama</span><h2><?= html_escape($featured['title']); ?></h2><div class="story-meta"><span><i class="far fa-calendar-alt me-1"></i><?= $format_news_date($featured['published_at']); ?></span><span><i class="far fa-user me-1"></i><?= html_escape($featured['author_name']); ?></span></div></div></a>
                    <aside class="latest-panel"><h2 class="h4 latest-heading"><span>Berita Terbaru</span></h2><?php foreach ($latest as $article): ?><a class="latest-item" href="<?= base_url('news/detail/' . rawurlencode($article['slug'])); ?>"><img src="<?= $image_url($article, TRUE); ?>" alt="<?= html_escape($article['image_alt']); ?>"><div><small class="text-danger fw-semibold"><?= $format_news_date($article['published_at']); ?></small><h3><?= html_escape($article['title']); ?></h3><small class="text-muted">Baca selengkapnya <i class="fas fa-arrow-right ms-1"></i></small></div></a><?php endforeach; ?><?php if (!$latest): ?><p class="text-muted small py-4 mb-0">Berita lainnya akan segera hadir.</p><?php endif; ?><a href="#all-news" class="btn btn-outline-danger w-100 mt-3">Jelajahi Semua Berita</a></aside>
                </div>
            </section>
            <?php endif; ?>

            <section id="all-news" class="py-4">
                <div class="section-heading mb-4"><div><small class="text-danger fw-bold text-uppercase">Arsip Informasi</small><h2><?= $keyword !== '' ? 'Hasil Pencarian' : 'Semua Berita'; ?></h2></div><span class="text-muted small"><?= number_format($total); ?> berita ditemukan</span></div>
                <?php if ($articles): ?><div class="row g-4"><?php foreach ($articles as $article): ?><div class="col-md-6 col-lg-4"><a class="article-card" href="<?= base_url('news/detail/' . rawurlencode($article['slug'])); ?>"><div class="article-image"><img src="<?= $image_url($article, TRUE); ?>" alt="<?= html_escape($article['image_alt']); ?>" loading="lazy"></div><div class="article-body"><span class="article-date"><i class="far fa-calendar-alt me-1 text-danger"></i><?= $format_news_date($article['published_at']); ?></span><h3><?= html_escape($article['title']); ?></h3><p><?= html_escape(character_limiter(strip_tags($article['excerpt']), 125, '…')); ?></p><span class="read-more">Baca Berita <i class="fas fa-arrow-right ms-1"></i></span></div></a></div><?php endforeach; ?></div><?php else: ?><div class="empty-news"><i class="far fa-newspaper fa-3x text-danger mb-3"></i><h3 class="h4">Berita tidak ditemukan</h3><p class="text-muted">Belum ada berita yang cocok dengan pencarian Anda.</p><a href="<?= base_url('news'); ?>" class="btn btn-brand">Kembali ke Semua Berita</a></div><?php endif; ?>
                <?php if ($last_page > 1): ?><nav class="mt-5" aria-label="Pagination berita"><ul class="pagination justify-content-center"><?php if ($page > 1): ?><li class="page-item"><a class="page-link" href="?page=<?= $page - 1; ?>&keyword=<?= urlencode($keyword); ?>" aria-label="Sebelumnya">&laquo;</a></li><?php endif; ?><?php for ($i = 1; $i <= $last_page; $i++): ?><li class="page-item <?= $i === $page ? 'active' : ''; ?>"><a class="page-link" href="?page=<?= $i; ?>&keyword=<?= urlencode($keyword); ?>"><?= $i; ?></a></li><?php endfor; ?><?php if ($page < $last_page): ?><li class="page-item"><a class="page-link" href="?page=<?= $page + 1; ?>&keyword=<?= urlencode($keyword); ?>" aria-label="Berikutnya">&raquo;</a></li><?php endif; ?></ul></nav><?php endif; ?>
            </section>
        </div>
    </main>

    <footer id="contact" class="news-footer"><div class="container"><div class="row"><div class="col-md-4 mb-4"><div class="footer-brand"><img src="<?= base_url('assets/logo/logo.png'); ?>" alt="Logo"><span class="brand-text">DIGITAL PENCAK SILAT</span></div><p class="text-muted">Mendukung digitalisasi event pencak silat untuk pelestarian budaya dan prestasi atlet Indonesia.</p></div><div class="col-md-4 mb-4"><h5 class="text-uppercase fw-bold mb-3 text-danger">Navigasi</h5><ul class="list-unstyled"><li class="mb-2"><a href="<?= base_url(); ?>" class="text-dark text-decoration-none">Beranda</a></li><li class="mb-2"><a href="<?= base_url(); ?>#events" class="text-dark text-decoration-none">Jadwal Event</a></li><li class="mb-2"><a href="<?= base_url('peringkat'); ?>" class="text-dark text-decoration-none">Peringkat Atlet</a></li><li class="mb-2"><a href="<?= base_url('news'); ?>" class="text-dark text-decoration-none">Digital Silat News</a></li></ul></div><div class="col-md-4 mb-4"><h5 class="text-uppercase fw-bold mb-3 text-danger">Kontak</h5><ul class="list-unstyled"><li class="mb-2"><i class="fab fa-whatsapp me-2 text-success"></i>+<?= html_escape(isset($s['whatsapp']) ? $s['whatsapp'] : ''); ?></li><li class="mb-2"><i class="far fa-envelope me-2 text-danger"></i><?= html_escape(isset($s['email']) ? $s['email'] : ''); ?></li><li class="mb-2"><a href="https://instagram.com/<?= html_escape(isset($s['instagram']) ? $s['instagram'] : ''); ?>" target="_blank" rel="noopener" class="text-dark text-decoration-none"><i class="fab fa-instagram me-2 text-danger"></i>@<?= html_escape(isset($s['instagram']) ? $s['instagram'] : ''); ?></a></li></ul></div></div><hr><div class="text-center pb-3 pt-2"><small class="text-muted">&copy; <?= date('Y'); ?> Digital Pencak Silat. All rights reserved.</small></div></div></footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
