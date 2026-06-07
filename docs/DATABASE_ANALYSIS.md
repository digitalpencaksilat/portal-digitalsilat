# Database Analysis - Portal Digital Silat

Dokumen ini memetakan struktur database berdasarkan database riil yang sedang dipakai aplikasi, bukan lagi sekadar inferensi source code.

## Sumber Data Audit

Audit ini diambil langsung dari database yang terkonfigurasi di `application/config/database.php`:

- Host: `localhost`
- User: `root`
- Database: `db_portaldigitalsilat`
- Driver: `mysqli`

Metode audit yang dipakai:

- `SHOW TABLES`
- `SHOW FULL COLUMNS`
- `SHOW INDEX`
- `SHOW CREATE TABLE`
- query agregasi sederhana untuk menghitung volume data

## Ringkasan Database Aktual

Tabel yang benar-benar ada saat audit:

1. `users`
2. `events`
3. `site_settings`
4. `visitors`
5. `event_results`

Tidak ditemukan tabel carousel terpisah pada database ini.

## Volume Data Saat Audit

| Tabel | Jumlah row |
| --- | ---: |
| `users` | 1 |
| `events` | 23 |
| `visitors` | 737 |
| `site_settings` | 4 |
| `event_results` | 7958 |

## Relasi Fungsional

```text
users
  └─ dipakai untuk login admin

events
  ├─ sumber data event publik
  ├─ dikelola dari admin panel
  ├─ menyimpan api_key per event
  └─ memiliki banyak event_results melalui event_id

site_settings
  └─ menyimpan konfigurasi key-value untuk footer/kontak

visitors
  └─ menyimpan log kunjungan harian

event_results
  └─ menyimpan hasil juara per event
```

## Tabel `users`

### Fungsi

Dipakai untuk login admin.

### Struktur Aktual

| Kolom | Tipe | Null | Key | Default | Extra |
| --- | --- | --- | --- | --- | --- |
| `id` | `int(11)` | tidak | `PRI` | `NULL` | `auto_increment` |
| `username` | `varchar(50)` | tidak | - | `NULL` | - |
| `password` | `varchar(255)` | tidak | - | `NULL` | - |
| `nama_lengkap` | `varchar(100)` | ya | - | `NULL` | - |
| `created_at` | `timestamp` | tidak | - | `current_timestamp()` | - |

### Index

- Primary key: `id`

### Contoh Data Aktual

| id | username | nama_lengkap | created_at |
| ---: | --- | --- | --- |
| 1 | `admin` | `Administrator` | `2026-01-21 22:10:40` |

### Hubungan dengan Source Code

Dipakai di `application/controllers/Admin.php:32`:

```php
$user = $this->db->get_where('users', ['username' => $username])->row_array();
```

### Catatan Penting

- Kolom `password` memang ada di database.
- Namun controller login saat ini belum memverifikasi hash dari kolom tersebut; ia masih membandingkan input password dengan string hardcoded `admin123`.
- Secara struktur DB, tabel ini sudah siap untuk auth yang lebih aman.

## Tabel `events`

### Fungsi

Tabel utama untuk menyimpan data event/kejuaraan.

### Struktur Aktual

| Kolom | Tipe | Null | Key | Default | Extra |
| --- | --- | --- | --- | --- | --- |
| `id` | `int(11)` | tidak | `PRI` | `NULL` | `auto_increment` |
| `judul` | `varchar(255)` | tidak | - | `NULL` | - |
| `slug` | `varchar(255)` | tidak | - | `NULL` | - |
| `poster` | `varchar(255)` | ya | - | `default.jpg` | - |
| `status` | `enum('Open Registration','Segera Hadir','Selesai','Ditutup')` | ya | - | `Segera Hadir` | - |
| `tanggal_pelaksanaan` | `varchar(100)` | tidak | - | `NULL` | - |
| `tempat` | `varchar(255)` | tidak | - | `NULL` | - |
| `batas_pendaftaran` | `varchar(100)` | tidak | - | `NULL` | - |
| `technical_meeting` | `varchar(100)` | ya | - | `NULL` | - |
| `link_pendaftaran` | `varchar(255)` | ya | - | `NULL` | - |
| `api_key` | `varchar(100)` | ya | - | `NULL` | - |
| `deskripsi` | `text` | ya | - | `NULL` | - |
| `created_at` | `timestamp` | tidak | - | `current_timestamp()` | - |
| `updated_at` | `timestamp` | tidak | - | `current_timestamp()` | `on update current_timestamp()` |

### Index

- Primary key: `id`

### Komentar Kolom dari Schema

Schema tabel menyimpan komentar yang berguna:

- `slug`: untuk URL ramah SEO
- `poster`: menyimpan nama file poster
- `tanggal_pelaksanaan`: bisa berupa range tanggal teks, misalnya `20-22 Okt 2024`

### Distribusi Status Saat Audit

| Status | Jumlah |
| --- | ---: |
| `Open Registration` | 7 |
| `Selesai` | 16 |

Tidak ada row aktif dengan status `Segera Hadir` atau `Ditutup` saat audit ini, walaupun keduanya valid di enum.

### Contoh Data Aktual

| id | judul | status | poster | api_key | created_at |
| ---: | --- | --- | --- | --- | --- |
| 19 | `PANGDAM JAYA OPEN CHAMPIONSHIP` | `Selesai` | `0dae425a0956f4b7c852f77701678b4e.png` | `06938c4cd21ca3a30a2b351b9a9b692f` | `2026-01-21 22:25:37` |
| 20 | `Serang Silat Championship 2026` | `Selesai` | `2097c09b4734619ab211a8a420f7b666.jpeg` | `680a155a5f5ae6ec19106eb5bf4dc662` | `2026-01-21 22:28:33` |
| 26 | `Kejuaraan Antar Kolat Merpati Putih Jakarta Pusat VI` | `Selesai` | `c88b97e04f3f5b52ba1a22ee1391b3d3.jpeg` | `NULL` | `2026-01-21 22:44:57` |
| 28 | `Walikota Jakarta Pusat Championship` | `Selesai` | `85327e44f94d34d88c93f4fe1bea89d4.jpeg` | `1aebcf08830bd2ee8d860a946e338d39` | `2026-02-16 03:19:30` |

### Hubungan dengan Source Code

Dipakai oleh:

- `application/controllers/Event.php`
- `application/controllers/Admin.php`
- `application/controllers/Api.php`

### Catatan Penting

- Source code CRUD admin saat ini belum memproses kolom `technical_meeting` dan `deskripsi`, walaupun keduanya sudah ada di database.
- Artinya ada gap antara schema aktual dan form admin yang aktif sekarang.
- `api_key` sudah ada dan dipakai untuk otorisasi sinkronisasi hasil.

## Tabel `site_settings`

### Fungsi

Menyimpan konfigurasi key-value untuk kontak/footer website.

### Struktur Aktual

| Kolom | Tipe | Null | Key | Default | Extra |
| --- | --- | --- | --- | --- | --- |
| `id` | `int(11)` | tidak | `PRI` | `NULL` | `auto_increment` |
| `parameter` | `varchar(50)` | tidak | `UNI` | `NULL` | - |
| `nilai` | `text` | ya | - | `NULL` | - |
| `updated_at` | `timestamp` | tidak | - | `current_timestamp()` | `on update current_timestamp()` |

### Index

- Primary key: `id`
- Unique key: `parameter`

### Data Aktual

| id | parameter | nilai | updated_at |
| ---: | --- | --- | --- |
| 1 | `whatsapp` | `6281234567890` | `2026-01-21 23:57:16` |
| 2 | `email` | `digitalpencaksilat@gmail.com` | `2026-01-22 00:06:57` |
| 3 | `instagram` | `digitalpencaksilat` | `2026-01-21 23:57:16` |
| 4 | `youtube` | `https://youtube.com/@digitalpencaksilat` | `2026-01-21 23:57:16` |

### Hubungan dengan Source Code

Controller public dan admin mengambil semua row lalu mengubahnya menjadi array:

```php
$data['s'] = array_column($settings, 'nilai', 'parameter');
```

Karena itu key `parameter` menjadi nama property yang dipakai di view seperti:

- `$s['whatsapp']`
- `$s['email']`
- `$s['instagram']`
- `$s['youtube']`

## Tabel `visitors`

### Fungsi

Menyimpan log pengunjung untuk statistik dashboard.

### Struktur Aktual

| Kolom | Tipe | Null | Key | Default | Extra |
| --- | --- | --- | --- | --- | --- |
| `id` | `int(11)` | tidak | `PRI` | `NULL` | `auto_increment` |
| `ip_address` | `varchar(50)` | tidak | - | `NULL` | - |
| `user_agent` | `text` | ya | - | `NULL` | - |
| `access_date` | `date` | tidak | - | `NULL` | - |
| `created_at` | `timestamp` | tidak | - | `current_timestamp()` | - |

### Index

- Primary key: `id`

Tidak ada unique index pada kombinasi `ip_address + access_date`.

### Statistik Aktual

| Metric | Nilai |
| --- | ---: |
| total row | 737 |
| distinct IP | 664 |
| distinct hari | 102 |

### Contoh Data Aktual Terbaru

| id | ip_address | access_date | created_at |
| ---: | --- | --- | --- |
| 737 | `::1` | `2026-05-03` | `2026-05-03 19:19:11` |
| 736 | `180.252.161.212` | `2026-05-03` | `2026-05-03 18:56:58` |
| 735 | `17.241.75.203` | `2026-05-03` | `2026-05-03 18:44:05` |
| 734 | `69.171.234.114` | `2026-05-03` | `2026-05-03 18:09:46` |
| 733 | `173.252.127.25` | `2026-05-03` | `2026-05-03 18:09:07` |

### Hubungan dengan Source Code

- `application/controllers/Admin.php` memakai tabel ini untuk `visitor_today` dan `visitor_total`.
- `application/controllers/Event.php` memanggil `_track_visitor()`, tetapi implementasi method tersebut belum terlihat di file yang aktif sekarang.

### Catatan Penting

- Database nyata membuktikan bahwa kolom `ip_address`, `user_agent`, `access_date`, dan `created_at` memang ada.
- Karena tidak ada unique constraint, potensi duplikasi visitor harian per IP tetap terbuka jika logic aplikasinya tidak melakukan pengecekan manual sebelum insert.

## Tabel `event_results`

### Fungsi

Menyimpan hasil juara/pemenang yang disinkronkan dari sistem scoring eksternal.

### Struktur Aktual

| Kolom | Tipe | Null | Key | Default | Extra |
| --- | --- | --- | --- | --- | --- |
| `id` | `int(11)` | tidak | `PRI` | `NULL` | `auto_increment` |
| `event_id` | `int(11)` | tidak | `MUL` | `NULL` | - |
| `category_main` | `enum('tanding','seni')` | tidak | - | `NULL` | - |
| `category_detail` | `varchar(255)` | tidak | - | `NULL` | - |
| `age_category` | `varchar(100)` | tidak | - | `NULL` | - |
| `gender` | `varchar(20)` | ya | - | `NULL` | - |
| `winner_name` | `varchar(255)` | tidak | - | `NULL` | - |
| `contingent` | `varchar(255)` | tidak | - | `NULL` | - |
| `school` | `varchar(255)` | ya | - | `NULL` | - |
| `rank_label` | `varchar(50)` | tidak | - | `NULL` | - |
| `created_at` | `timestamp` | tidak | - | `current_timestamp()` | - |

### Index dan Constraint

- Primary key: `id`
- Index: `event_id`
- Foreign key: `event_results_ibfk_1`
  - `event_id` references `events(id)`
  - `ON DELETE CASCADE`

### Statistik Aktual

| Metric | Nilai |
| --- | ---: |
| total row | 7958 |
| kategori `tanding` | 6727 |
| kategori `seni` | 1231 |

### Distribusi Rank Label

| Rank | Jumlah |
| --- | ---: |
| `Emas` | 2747 |
| `Perak` | 2697 |
| `Perunggu` | 2514 |

### Event dengan Hasil Terbanyak

| event_id | jumlah hasil |
| ---: | ---: |
| 24 | 1512 |
| 30 | 1379 |
| 37 | 1011 |
| 22 | 698 |
| 32 | 621 |
| 23 | 587 |
| 41 | 579 |
| 28 | 403 |
| 29 | 321 |
| 27 | 311 |

### Contoh Data Aktual Terbaru

| id | event_id | category_main | category_detail | age_category | gender | winner_name | contingent | school | rank_label | created_at |
| ---: | ---: | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 12974 | 41 | `seni` | `Ganda - IPSI - Pool 1` | `DEWASA` | `Putra` | `DAFFA ARYA MAHENDRA, , GAVIN BERYL PRATAMA` | `KENCANA JIWA TANGSEL` | `NULL` | `Perak` | `2026-05-18 12:05:44` |
| 12973 | 41 | `seni` | `Ganda - IPSI - Pool 1` | `DEWASA` | `Putra` | `MUHAMAD RIDHO TUMANGGER, , MUHAMAD RESTU TUMANGGER` | `IPSI TANGSEL` | `NULL` | `Emas` | `2026-05-18 12:05:44` |
| 12972 | 41 | `seni` | `Tunggal - IPSI - Pool 1` | `DEWASA` | `Putra` | `ARKA BIMA SATRIA` | `KENCANA JIWA TANGSEL` | `NULL` | `Perak` | `2026-05-18 12:05:44` |

### Hubungan dengan Source Code

- `application/controllers/Api.php` melakukan full sync per event:
  1. hapus semua `event_results` dengan `event_id` terkait
  2. `insert_batch()` semua hasil baru
- `application/controllers/Event.php` membaca tabel ini untuk detail hasil kategori `tanding` dan `seni`

### Catatan Penting

- Struktur foreign key sudah benar dan mendukung `ON DELETE CASCADE`.
- `school` memang nullable, sesuai logika API yang mengizinkan field ini kosong.
- `rank_label` di database berupa `varchar`, bukan `enum`.

## Kesesuaian Database vs Source Code

### Sudah Sesuai

- Tabel inti yang dipakai source code semuanya benar-benar ada: `users`, `events`, `site_settings`, `visitors`, `event_results`.
- Kolom `api_key` pada `events` tersedia dan dipakai endpoint API.
- Kolom `school` pada `event_results` tersedia dan nullable, sesuai payload API.
- Tabel `visitors` benar-benar menyimpan `ip_address`, `user_agent`, dan `access_date`.

### Gap yang Ditemukan

- `events.technical_meeting` dan `events.deskripsi` ada di database, tetapi belum dikelola oleh controller/form admin yang aktif.
- Tabel `users.password` ada, tetapi logic login belum memanfaatkannya.
- Tabel `visitors` tidak punya unique constraint untuk visitor harian per IP.
- View `admin/carousel.php` ada, tetapi database ini tidak memiliki tabel carousel dan controller terkait juga belum terlihat pada `Admin.php`.

## Draft ERD Sederhana

```text
users
- id (PK)
- username
- password
- nama_lengkap
- created_at

events
- id (PK)
- judul
- slug
- poster
- status
- tanggal_pelaksanaan
- tempat
- batas_pendaftaran
- technical_meeting
- link_pendaftaran
- api_key
- deskripsi
- created_at
- updated_at

site_settings
- id (PK)
- parameter (UNIQUE)
- nilai
- updated_at

visitors
- id (PK)
- ip_address
- user_agent
- access_date
- created_at

event_results
- id (PK)
- event_id (FK -> events.id)
- category_main
- category_detail
- age_category
- gender
- winner_name
- contingent
- school
- rank_label
- created_at
```

## Rekomendasi Lanjutan

- Ubah login admin agar memakai `password_hash()` dan `password_verify()` terhadap `users.password`.
- Jika visitor ingin dihitung unik per hari per IP, tambahkan constraint unik atau validasi insert di level aplikasi.
- Sinkronkan form admin event dengan kolom database yang belum termanfaatkan, khususnya `technical_meeting` dan `deskripsi`.
- Jika fitur carousel memang akan dipakai, tentukan apakah akan memakai tabel baru atau cukup asset statis seperti implementasi public saat ini.

## Kesimpulan

Database riil project ini cukup rapi dan sudah mendukung flow utama aplikasi:

- manajemen event
- kontak dinamis
- statistik pengunjung
- sinkronisasi hasil juara per event

Bagian yang paling perlu diselaraskan ke depan bukan struktur database inti, tetapi logic aplikasi yang belum memanfaatkan seluruh kolom yang sudah tersedia di schema.