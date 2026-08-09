# Publishing API News

Publishing API hanya dapat mencari event dan membuat artikel berstatus `draft`. Publish, archive, edit, dan delete tetap dilakukan melalui Admin News.

## Konfigurasi Lokal

Salin `.env.publisher.example` menjadi:

```text
.secrets/.env.publisher
```

Isi token yang ditampilkan satu kali oleh halaman Admin > Integrasi API > Publishing API.

```env
NEWS_API_URL=https://portal.digitalsilat.com
NEWS_API_TOKEN=64-character-generated-token
```

File `.secrets/` diabaikan Git dan tidak boleh di-push.

## Format Artikel

```json
{
  "title": "Judul artikel",
  "excerpt": "Ringkasan artikel",
  "content": "<p>Isi artikel lengkap...</p>",
  "image_alt": "Deskripsi gambar",
  "author_name": "Digital Pencak Silat",
  "related_event_slug": "slug-event-production"
}
```

## Mengirim Draft

```bash
/Applications/XAMPP/bin/php tools/publish_news.php article.json foto.jpg
```

Script hanya menerima URL `https://portal.digitalsilat.com`, tidak mengikuti redirect, memverifikasi TLS, dan tidak mencetak token.

## Endpoint

- `GET /api/v1/publisher/events?keyword=...`
- `POST /api/v1/publisher/news/drafts`

Header autentikasi:

```text
Authorization: Bearer {token}
```

## Security

- Token acak 256-bit, server hanya menyimpan SHA-256 hash.
- HTTPS wajib pada production.
- Token hanya dapat membuat draft.
- Maksimal 10 draft per jam per token.
- Maksimal 30 pencarian event per 10 menit per token.
- Cover maksimal 8 MB, 12 megapiksel, dan divalidasi MIME.
- HTML artikel disanitasi dengan whitelist.
- Semua request dicatat tanpa token atau isi artikel.

## Deployment Production

1. Backup database dan file production.
2. Deploy source code modul News dan Publishing API.
3. Jalankan migration `20260807140000_create_news_articles.php` bila tabel News belum ada.
4. Jalankan migration `20260808120000_add_publisher_api.php`.
5. Tetapkan superadmin secara eksplisit dan audit akun yang dipilih: `UPDATE users SET role='superadmin' WHERE username='USERNAME_YANG_DIKENDALIKAN';`.
6. Set `CI_ENV=production` dan `SITE_URL=https://portal.digitalsilat.com` pada environment web server. Request web default ke production jika `CI_ENV` tidak tersedia.
7. Jika memakai TLS-terminating reverse proxy, set `TRUSTED_PROXY_IPS` ke IP proxy yang tepercaya. Jangan memakai wildcard atau IP dari request header.
8. Pastikan `assets/uploads/news/covers/` writable oleh user PHP (`755` atau `775`, bergantung hosting).
9. Pastikan HTTPS aktif, lalu cek URL dan cookie admin menggunakan origin production.
10. Login ke Admin > Integrasi API > Publishing API dan generate token.
11. Simpan token satu kali ke `.secrets/.env.publisher` pada laptop lokal.

CSRF aktif untuk form admin. Endpoint berikut dikecualikan karena memakai autentikasi header, bukan cookie session:

```text
api/push_results
api/v1/publisher/*
```

Login admin hanya menerima password hash dari tabel `users` melalui `password_verify()` pada seluruh environment. Tidak ada fallback password hardcoded.
