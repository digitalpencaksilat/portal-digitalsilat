# Changelog

Semua perubahan signifikan pada proyek ini akan didokumentasikan di file ini.

## [Unreleased]

### Added
- Fitur **Live Search** pada kalender event (pencarian otomatis saat mengetik dengan *debounce* 500ms).
- Tombol **Clear Search** (X) pada input pencarian untuk memudahkan reset pencarian.
- Indikator **Loading** yang lebih intuitif saat proses pencarian atau perpindahan halaman (AJAX).

### Fixed
- Optimasi query database pada controller `Event.php` untuk menghindari redundansi filter.
- Perbaikan logika penampilan tombol "Lihat Hasil Juara" yang hanya muncul jika status event adalah "Selesai".
- Perbaikan perataan teks (*alignment*) pada tabel hasil kejuaraan di halaman detail event.
- Perbaikan format nama pemenang (kapitalisasi otomatis) pada tabel hasil kejuaraan.

### Changed
- Refaktor mekanisme pagination AJAX agar lebih stabil dan sinkron dengan filter pencarian.
- Peningkatan UX pada kolom pencarian: halaman hanya akan *scroll* otomatis jika pengguna menekan Enter atau tombol Cari.
