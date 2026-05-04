# Changelog

Semua perubahan signifikan pada proyek ini akan didokumentasikan di file ini.

## [Unreleased]

### Added
- Fitur **Live Search** pada kalender event (pencarian otomatis saat mengetik dengan *debounce* 500ms).
- Tombol **Clear Search** (X) pada input pencarian untuk memudahkan reset pencarian.
- Indikator **Loading** yang lebih intuitif saat proses pencarian atau perpindahan halaman (AJAX).
- Penambahan field **Gender** (Putra/Putri) pada data hasil kejuaraan di API dan halaman detail event.
- Integrasi **DataTables** pada halaman Manajemen API untuk manajemen data yang lebih efisien.
- Fitur **Copy Event ID** pada halaman Manajemen API untuk memudahkan integrasi eksternal.
- File CSS eksternal baru (`variables.css`, `admin-style.css`, `main-style.css`) untuk standarisasi desain.

### Changed
- **Refaktor CSS**: Memindahkan seluruh *inline style* ke file CSS eksternal untuk meningkatkan performa dan kemudahan pemeliharaan.
- **Peningkatan UI/UX**: Pembaruan tampilan pada halaman Login Admin, Dashboard, Manajemen API, dan Detail Event menggunakan standar visual baru.
- Update logika pengurutan (*ordering*) hasil kejuaraan agar lebih teratur berdasarkan kategori usia dan jenis kelamin.
- Pembaruan regenerasi API Key menjadi format 32-character hex string.
- Formatting otomatis nama kontingen menjadi uppercase pada tabel hasil kejuaraan.

### Fixed
- Optimasi query database pada controller `Event.php` untuk menghindari redundansi filter.
- Perbaikan logika penampilan tombol "Lihat Hasil Juara" yang hanya muncul jika status event adalah "Selesai".
- Perbaikan perataan teks (*alignment*) dan responsivitas pada tabel hasil kejuaraan di halaman detail event.
- Perbaikan format nama pemenang (kapitalisasi otomatis) pada tabel hasil kejuaraan.

### Removed
- Menghapus file perencanaan `CHAMPIONSHIP_RESULTS_PLAN.md` karena fitur telah selesai diimplementasikan.
