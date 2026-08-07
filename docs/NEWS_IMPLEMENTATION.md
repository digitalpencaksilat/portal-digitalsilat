# Digital Pencak Silat News

Modul news bersifat global tanpa kategori. Artikel dapat ditautkan ke event melalui `related_event_id`, tetapi field tersebut opsional.

## URL

- Publik: `/news`
- Detail: `/news/detail/{slug}`
- Admin: `/admin/news`
- Tulis: `/admin/news/create`
- Edit: `/admin/news/edit/{id}`
- Preview: `/admin/news/preview/{id}`

## Status

- `draft`: hanya terlihat dari admin.
- `published`: tampil di halaman publik.
- `archived`: tersimpan tetapi tidak tampil di halaman publik.

## Gambar

Upload cover melalui admin diproses oleh `Image_optimizer`:

- WebP sebagai format utama.
- JPEG fallback untuk kompatibilitas.
- Cover maksimal 1600px.
- Thumbnail maksimal 480px.
- Orientasi EXIF diperbaiki.
- Metadata tidak ikut disimpan saat hasil gambar dibuat ulang.

PHP GD digunakan untuk resize. Converter `cwebp` digunakan bila tersedia. Jika WebP tidak tersedia, pipeline tetap menghasilkan JPEG agar proses upload tidak gagal total.

Poster event baru juga melewati pipeline yang sama dengan batas 1400px. Poster lama dapat dimigrasikan menggunakan:

```bash
/Applications/XAMPP/bin/php tools/optimize_images.php --dry-run
```

Script migrasi hanya dry-run secara default. Hasil WebP harus ditinjau dan referensi database/view harus disiapkan sebelum menjalankan migrasi nyata.

## Database

Migration tersedia di `application/migrations/20260807140000_create_news_articles.php`. Pada database lokal saat implementasi, tabel `news_articles` dibuat menggunakan schema migration yang sama karena tabel migration CodeIgniter belum tersedia.

Untuk deployment baru, jalankan migration melalui prosedur deployment project setelah membuat tabel migration CodeIgniter. Jangan mengaktifkan auto-migration tanpa backup database.
