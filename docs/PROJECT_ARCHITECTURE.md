# Project Architecture - Portal Digital Silat

Dokumen ini merangkum arsitektur, modul, dan flow aplikasi berdasarkan source code saat ini.

## Ringkasan

Portal Digital Silat adalah aplikasi web untuk menampilkan informasi event Pencak Silat, mengelola event dari admin panel, dan menerima sinkronisasi hasil kejuaraan dari sistem scoring eksternal.

- Framework backend: CodeIgniter 3
- Frontend: PHP view, Bootstrap 5, jQuery, DataTables, SweetAlert2
- Database: MySQL/MariaDB via driver `mysqli`
- Default controller: `Event`
- Base URL lokal: `http://localhost/portal-digitalsilat/`
- Database aktif: `db_portaldigitalsilat`

## Struktur Penting

```text
application/
  config/
    autoload.php       # session dan database autoload, helper url/file
    config.php         # base_url dan konfigurasi umum CI
    database.php       # koneksi database MySQL
    routes.php         # routing default ke Event
  controllers/
    Event.php          # halaman publik, listing event, detail hasil
    Admin.php          # login, dashboard, CRUD event, setting, API key
    Api.php            # endpoint push hasil kejuaraan
  views/
    landing_page.php   # halaman publik utama
    event_detail.php   # halaman detail hasil juara
    admin/
      login.php
      dashboard.php
      event_form.php
      settings.php
      api_settings.php
      carousel.php     # view tersedia, controller belum ada di Admin.php saat ini
assets/
  carousel/            # gambar carousel statis public
  css/                 # styling public/admin
  logo/                # logo/favicon
  uploads/posters/     # upload poster event
```

## Konfigurasi Utama

### Routing

File: `application/config/routes.php`

```php
$route['default_controller'] = 'event';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
```

Artinya request root website akan masuk ke `Event::index()`.

### Autoload

File: `application/config/autoload.php`

- Library autoload: `session`, `database`
- Helper autoload: `url`, `file`
- Model autoload: kosong

Project ini tidak memakai model khusus; query database dilakukan langsung dari controller memakai Query Builder CodeIgniter.

### Database

File: `application/config/database.php`

```php
'hostname' => 'localhost',
'username' => 'root',
'password' => '',
'database' => 'db_portaldigitalsilat',
'dbdriver' => 'mysqli',
```

Catatan: README menyebut nama database `digitalsilat_website`, tetapi konfigurasi aktual memakai `db_portaldigitalsilat`.

## Modul Public Portal

### Controller

File: `application/controllers/Event.php`

#### `index()`

Flow:

1. Memanggil `_track_visitor()` untuk statistik pengunjung.
2. Mengambil `site_settings` dan mengubahnya menjadi array `$s[parameter] = nilai`.
3. Mengambil query string `keyword` untuk filter pencarian.
4. Query tabel `events` dengan filter `judul` atau `tempat` jika keyword ada.
5. Menghitung total data untuk pagination.
6. Mengambil 6 event per halaman, urut `created_at DESC`.
7. Jika request AJAX, response berupa JSON:
   - `status`
   - `html_events`
   - `html_pagination`
8. Jika bukan AJAX, load view `landing_page`.

URL umum:

- `/`
- `/event`
- `/event/index`
- `/event/index/{offset}`
- `/event/index?keyword=...`

#### `_track_visitor()`

Method sudah ada, tetapi isi saat ini masih placeholder:

```php
private function _track_visitor()
{
    // ... (tetap ada)
}
```

Dashboard admin sudah membaca tabel `visitors`, jadi jika statistik visitor ingin akurat, method ini perlu diimplementasikan.

#### `detail($id)`

Flow:

1. Jika `id` kosong, redirect ke `event`.
2. Ambil event dari tabel `events` berdasarkan `id`.
3. Jika event tidak ditemukan, tampilkan 404.
4. Ambil hasil `event_results` kategori `tanding`.
5. Ambil hasil `event_results` kategori `seni`.
6. Ambil `site_settings` untuk footer.
7. Load view `event_detail`.

URL umum:

- `/event/detail/{id}`

### View Landing Page

File: `application/views/landing_page.php`

Bagian utama:

- Navbar
- Hero carousel statis dari `assets/carousel/carousel-1.jpg` sampai `carousel-3.jpg`
- About section
- Event list section
- Search event
- Pagination AJAX
- Footer kontak dinamis dari `site_settings`
- Modal detail event
- Tombol WhatsApp atau link pendaftaran

Status event yang dikenali di UI:

- `Segera Hadir`
- `Open Registration`
- `Ditutup`
- `Selesai`

Jika event status `Selesai`, card event menampilkan tombol `Lihat Hasil Juara` ke `/event/detail/{id}`.

### View Detail Hasil

File: `application/views/event_detail.php`

Fitur:

- Header detail event
- Poster event
- Info tempat dan tanggal
- Tab hasil kategori `tanding` dan `seni`
- DataTables untuk pagination table
- Search custom untuk nama atlet/kontingen
- Footer kontak dinamis

## Modul Admin

### Controller

File: `application/controllers/Admin.php`

#### Auth

- `index()` menampilkan login jika belum login; redirect dashboard jika sudah login.
- `auth()` mengambil `username` dan `password` dari POST.
- User dicek dari tabel `users` berdasarkan `username`.
- Password admin diverifikasi terhadap hash pada kolom `users.password` menggunakan `password_verify()`.
- Jika sukses, session berisi:
  - `id`
  - `nama`
  - `logged_in = TRUE`
- `logout()` menghapus session login.

URL:

- `/admin`
- `/admin/auth`
- `/admin/logout`

Catatan keamanan: password admin disimpan sebagai hash pada tabel `users`; tidak ada password fallback hardcoded.

#### Dashboard

Method: `dashboard()`

Flow:

1. Cek session login.
2. Hitung statistik event:
   - total event
   - status `Open Registration`
   - status `Segera Hadir`
   - status `Selesai`
3. Hitung visitor:
   - `visitor_today` berdasarkan `access_date = date('Y-m-d')`
   - `visitor_total` dari count semua `visitors`
4. Ambil semua event urut `created_at DESC`.
5. Load view `admin/dashboard`.

URL:

- `/admin/dashboard`

#### CRUD Event

Methods:

- `tambah()` menampilkan form tambah event.
- `simpan()` menyimpan event baru.
- `edit($id)` menampilkan form edit.
- `update($id)` memperbarui event.
- `hapus($id)` menghapus event dan poster lokal jika bukan `default.jpg` atau URL eksternal.

Upload poster:

- Path: `./assets/uploads/posters/`
- Allowed types: `gif|jpg|png|jpeg`
- Max size: `2048` KB
- Nama file dienkripsi (`encrypt_name = TRUE`)

Kolom form event:

- `judul`
- `tanggal_pelaksanaan`
- `tempat`
- `batas_pendaftaran`
- `status`
- `link_pendaftaran`
- `poster`

Slug dibuat otomatis dari `judul` memakai `url_title()`.

URL:

- `/admin/tambah`
- `/admin/simpan`
- `/admin/edit/{id}`
- `/admin/update/{id}`
- `/admin/hapus/{id}`

#### Pengaturan Kontak

Methods:

- `pengaturan()` mengambil semua data `site_settings`.
- `update_settings()` update parameter:
  - `whatsapp`
  - `email`
  - `instagram`
  - `youtube`

URL:

- `/admin/pengaturan`
- `/admin/update_settings`

#### Manajemen API Key

Methods:

- `api_management()` menampilkan daftar event dan API key.
- `generate_api_key($id)` membuat 32 karakter hex memakai `random_bytes(16)` lalu menyimpan ke kolom `events.api_key`.

URL:

- `/admin/api_management`
- `/admin/generate_api_key/{id}`

### View Admin

- `admin/login.php`: form login admin.
- `admin/dashboard.php`: statistik visitor/event dan tabel CRUD event.
- `admin/event_form.php`: form tambah/edit event.
- `admin/settings.php`: form kontak footer.
- `admin/api_settings.php`: daftar event, API key, copy key, regenerate key.
- `admin/carousel.php`: view manajemen carousel, tetapi method controller `carousel`, `simpan_carousel`, dan `hapus_carousel` belum ada di `Admin.php` saat audit ini.

## Modul API

### Controller

File: `application/controllers/Api.php`

Endpoint:

- `POST /api/push_results`

Header wajib:

```text
X-API-KEY: {api_key_event}
```

Payload JSON minimal:

```json
{
  "event_id": 1,
  "results": [
    {
      "category_main": "tanding",
      "category_detail": "Kelas A Putra",
      "age_category": "Usia Dini",
      "gender": "Putra",
      "winner_name": "Nama Atlet",
      "contingent": "Nama Kontingen",
      "school": "Nama Sekolah",
      "rank_label": "Emas"
    }
  ]
}
```

Flow:

1. Decode JSON dari raw input.
2. Validasi wajib ada `event_id` dan `results`.
3. Ambil event berdasarkan `event_id`.
4. Validasi `X-API-KEY` terhadap `events.api_key`.
5. Mulai transaksi database.
6. Hapus semua hasil lama untuk event tersebut dari `event_results`.
7. Insert semua hasil baru memakai `insert_batch`.
8. Commit transaksi.
9. Return JSON success atau error.

Response sukses:

```json
{
  "status": "success",
  "message": "10 results processed"
}
```

Response error yang mungkin:

- `400 Invalid Data Format`
- `401 Unauthorized Access: Invalid API Key for this Event`
- `404 Event not found`
- `500 Failed to save results`

Catatan: property private `$api_key = 'DPS_SECRET_2024'` ada di controller, tetapi tidak dipakai pada flow saat ini karena validasi API key sudah mengambil dari database per event.

## Asset dan Dependency Frontend

Dependency CDN yang dipakai di view:

- Bootstrap 5 CSS/JS
- Font Awesome 6
- Google Fonts `Oswald` dan `Poppins`
- jQuery
- DataTables
- SweetAlert2

CSS lokal:

- `assets/css/variables.css`
- `assets/css/main-style.css`
- `assets/css/admin-style.css`

Asset lokal:

- Logo: `assets/logo/logo.png`, `assets/logo/logo.ico`
- Carousel: `assets/carousel/carousel-1.jpg`, `carousel-2.jpg`, `carousel-3.jpg`, `carousel-4.jpg`
- Poster upload: `assets/uploads/posters/`

## Catatan Pengembangan

- Project tidak memakai model layer; jika fitur makin besar, pertimbangkan menambah model agar query tidak menumpuk di controller.
- Belum ditemukan file migration/schema SQL di repo, jadi perubahan database harus didokumentasikan manual atau dibuat file SQL/migration baru.
- `_track_visitor()` masih placeholder, sedangkan dashboard sudah bergantung pada tabel `visitors`.
- Admin auth sebaiknya diubah ke `password_hash()` dan `password_verify()`.
- Beberapa output view memakai echo langsung dari database; untuk data user-generated, sebaiknya konsisten memakai `htmlspecialchars()` untuk mengurangi risiko XSS.
- View `admin/carousel.php` tampak belum terhubung ke controller saat ini.
