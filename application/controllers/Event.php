<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Event extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('pagination');
        $this->load->helper(['url', 'text']);
        $this->load->model('Peringkat_model', 'peringkat');
    }

    public function index()
    {
        // --- 1. TRACKING PENGUNJUNG (BARU) ---
        // Mencatat IP pengunjung untuk statistik di dashboard admin
        $this->_track_visitor();

        // --- 2. AMBIL PENGATURAN KONTAK ---
        // Mengambil data kontak (WA, Email, Sosmed) untuk footer dinamis
        $settings_raw = $this->db->get('site_settings')->result_array();
        $data['s'] = array_column($settings_raw, 'nilai', 'parameter');

        // --- 3. KONFIGURASI PAGINATION & SEARCH ---
        $keyword = $this->input->get('keyword');
        $config['base_url'] = base_url('event/index');
        $config['per_page'] = 6;
        $config['uri_segment'] = 3;

        $this->db->from('events');
        if ($keyword) {
            $this->db->group_start();
            $this->db->like('judul', $keyword);
            $this->db->or_like('tempat', $keyword);
            $this->db->group_end();

            $config['suffix'] = '?keyword=' . urlencode($keyword);
            $config['first_url'] = $config['base_url'] . $config['suffix'];
        }

        $events = $this->db->get()->result_array();
        $events = $this->_prepare_events($events);
        usort($events, [$this, '_sort_events_by_closest_date']);

        // Hitung total baris (dengan filter jika ada)
        $config['total_rows'] = count($events);

        // Styling Pagination menggunakan Bootstrap 5
        $config['full_tag_open']    = '<nav aria-label="Page navigation" class="mt-5"><ul class="pagination justify-content-center">';
        $config['full_tag_close']   = '</ul></nav>';
        $config['first_link']       = 'Awal';
        $config['first_tag_open']   = '<li class="page-item">';
        $config['first_tag_close']  = '</li>';
        $config['last_link']        = 'Akhir';
        $config['last_tag_open']    = '<li class="page-item">';
        $config['last_tag_close']   = '</li>';
        $config['next_link']        = '&raquo;';
        $config['next_tag_open']    = '<li class="page-item">';
        $config['next_tag_close']   = '</li>';
        $config['prev_link']        = '&laquo;';
        $config['prev_tag_open']    = '<li class="page-item">';
        $config['prev_tag_close']   = '</li>';
        $config['cur_tag_open']     = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close']    = '</span></li>';
        $config['num_tag_open']     = '<li class="page-item">';
        $config['num_tag_close']    = '</li>';
        $config['attributes']       = array('class' => 'page-link ajax-pagination');

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? (int) $this->uri->segment(3) : 0;
        $data['events'] = array_slice($events, $page, $config['per_page']);
        $data['pagination'] = $this->pagination->create_links();

        // Logika AJAX: Jika request datang dari AJAX (klik pagination), 
        // hanya kirim data JSON berisi HTML event dan pagination baru.
        if ($this->input->is_ajax_request()) {
            $html_events = $this->_render_event_cards($data['events']);
            echo json_encode([
                'status' => 'success',
                'html_events' => $html_events,
                'html_pagination' => $data['pagination']
            ]);
            exit;
        }

        // --- 5. DATA PERINGKAT ATLET (untuk section leaderboard di landing) ---
        // Tampilkan Top 10 atlet (semua kategori) sebagai default.
        $full_leaderboard = $this->peringkat->build_leaderboard([]);
        $data['leaderboard'] = array_slice($full_leaderboard, 0, 10);
        $data['leaderboard_total'] = count($full_leaderboard);

        // Berita terbaru ditampilkan setelah kalender event di landing page.
        $this->load->model('News_model', 'news');
        $data['featured_news'] = $this->news->featured();
        // One main story plus up to three distinct sidebar stories.
        $data['latest_news'] = $this->news->all('published', '', 4, 0);

        // Load view utama jika bukan request AJAX
        $this->load->view('landing_page', $data);
    }

    // --- FUNGSI PEREKAM JEJAK PENGUNJUNG ---
    private function _track_visitor()
    {
        // ... (tetap ada)
    }

    /**
     * Halaman Detail Event & Daftar Juara
     * /event/detail/{id}
     */
    public function detail($id = NULL)
    {
        if ($id === NULL) redirect('event');

        // 1. Ambil Data Event
        $data['event'] = $this->db->get_where('events', ['id' => $id])->row_array();
        if (!$data['event']) show_404();

        $data['event']['tanggal_pelaksanaan_display'] = $this->_format_date_range($data['event']['tanggal_mulai'], $data['event']['tanggal_selesai']);
        $data['event']['batas_pendaftaran_display'] = $this->_format_display_date($data['event']['batas_pendaftaran']);

        // 2. Ambil Data Hasil Kejuaraan
        // Pisahkan Kategori Tanding
        $this->db->where(['event_id' => $id, 'category_main' => 'tanding']);
        $this->db->order_by('age_category', 'ASC');
        $this->db->order_by('gender', 'ASC');
        $this->db->order_by('category_detail', 'ASC');
        $this->db->order_by('rank_label', 'ASC');
        $data['results_tanding'] = $this->db->get('event_results')->result_array();

        // Pisahkan Kategori Seni
        $this->db->where(['event_id' => $id, 'category_main' => 'seni']);
        $this->db->order_by('age_category', 'ASC');
        $this->db->order_by('gender', 'ASC');
        $this->db->order_by('category_detail', 'ASC');
        $this->db->order_by('rank_label', 'ASC');
        $data['results_seni'] = $this->db->get('event_results')->result_array();

        // 3. Ambil Pengaturan Kontak untuk Footer
        $settings_raw = $this->db->get('site_settings')->result_array();
        $data['s'] = array_column($settings_raw, 'nilai', 'parameter');

        $this->load->view('event_detail', $data);
    }

    private function _prepare_events($events)
    {
        foreach ($events as &$event) {
            $event['_tanggal_timestamp'] = $this->_parse_event_date($event['tanggal_mulai']);
            $event['tanggal_pelaksanaan_display'] = $this->_format_date_range($event['tanggal_mulai'], $event['tanggal_selesai']);
            $event['batas_pendaftaran_display'] = $this->_format_display_date($event['batas_pendaftaran']);
        }
        unset($event);

        return $events;
    }

    public function _sort_events_by_closest_date($event_a, $event_b)
    {
        $today = strtotime(date('Y-m-d'));
        $timestamp_a = !empty($event_a['_tanggal_timestamp']) ? $event_a['_tanggal_timestamp'] : null;
        $timestamp_b = !empty($event_b['_tanggal_timestamp']) ? $event_b['_tanggal_timestamp'] : null;

        $group_a = $this->_resolve_event_sort_group($timestamp_a, $today);
        $group_b = $this->_resolve_event_sort_group($timestamp_b, $today);

        if ($group_a !== $group_b) {
            return $group_a <=> $group_b;
        }

        if ($timestamp_a === $timestamp_b) {
            return strcmp($event_b['created_at'], $event_a['created_at']);
        }

        if ($group_a === 0) {
            return $timestamp_a <=> $timestamp_b;
        }

        if ($group_a === 1) {
            return $timestamp_b <=> $timestamp_a;
        }

        return strcmp($event_b['created_at'], $event_a['created_at']);
    }

    private function _resolve_event_sort_group($timestamp, $today)
    {
        if (empty($timestamp)) {
            return 2;
        }

        return $timestamp >= $today ? 0 : 1;
    }

    private function _parse_event_date($value)
    {
        if (empty($value)) {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return strtotime($value);
        }

        $months = [
            'januari' => '01',
            'februari' => '02',
            'maret' => '03',
            'april' => '04',
            'mei' => '05',
            'juni' => '06',
            'juli' => '07',
            'agustus' => '08',
            'september' => '09',
            'oktober' => '10',
            'november' => '11',
            'desember' => '12'
        ];

        $value = trim($value);
        if (preg_match('/^(\d{1,2})(?:\s*-\s*\d{1,2})?\s+([A-Za-z]+)\s+(\d{4})$/u', $value, $matches)) {
            $month_name = strtolower($matches[2]);
            if (isset($months[$month_name])) {
                return strtotime($matches[3] . '-' . $months[$month_name] . '-' . str_pad($matches[1], 2, '0', STR_PAD_LEFT));
            }
        }

        $timestamp = strtotime($value);
        return $timestamp !== false ? $timestamp : null;
    }

    private function _format_display_date($value)
    {
        $timestamp = $this->_parse_event_date($value);
        if ($timestamp === null) {
            return !empty($value) ? $value : '-';
        }

        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return date('d', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
    }

    private function _format_date_range($tanggal_mulai, $tanggal_selesai)
    {
        if (empty($tanggal_mulai) && empty($tanggal_selesai)) {
            return '-';
        }

        $mulai_display = $this->_format_display_date($tanggal_mulai);
        $selesai_display = $this->_format_display_date($tanggal_selesai);

        if (empty($tanggal_mulai) || $tanggal_mulai === $tanggal_selesai) {
            return $selesai_display;
        }

        if (empty($tanggal_selesai)) {
            return $mulai_display;
        }

        return $mulai_display . ' - ' . $selesai_display;
    }

    // Helper untuk me-render HTML Event Card saat request AJAX
    private function _render_event_cards($events)
    {
        $html = '';
        if (empty($events)) return '<div class="col-12 text-center"><p class="text-muted">Belum ada event yang tersedia saat ini.</p></div>';

        foreach ($events as $event) {
            // Tentukan warna badge status
            $badge = 'bg-secondary text-white';
            if ($event['status'] == 'Open Registration') $badge = 'bg-warning text-dark';
            elseif ($event['status'] == 'Selesai') $badge = 'bg-success text-white';
            elseif ($event['status'] == 'Ditutup') $badge = 'bg-danger text-white';

            // Cek path gambar (apakah URL eksternal atau file lokal)
            $img = $event['poster'];
            if (strpos($img, 'http') !== 0) $img = base_url('assets/uploads/posters/' . $img);

            $link = isset($event['link_pendaftaran']) ? $event['link_pendaftaran'] : '';

            // Generate HTML Card
            $html .= '
            <div class="col-lg-4 col-md-6 fade-in-item">
                <div class="event-card">
                    <div class="event-poster-container">
                        <span class="event-status ' . $badge . '">' . $event['status'] . '</span>
                        <img src="' . $img . '" class="event-poster" onerror="this.src=\'https://via.placeholder.com/800x400?text=No+Image\'">
                    </div>
                    <div class="card-body">
                        <h3 class="event-title">' . $event['judul'] . '</h3>
                        <ul class="info-list">
                            <li><i class="far fa-calendar-alt"></i><div><span class="label-text">Pelaksanaan</span><br><span class="value-text">' . $event['tanggal_pelaksanaan_display'] . '</span></div></li>
                            <li><i class="fas fa-map-marker-alt"></i><div><span class="label-text">Tempat</span><br><span class="value-text">' . $event['tempat'] . '</span></div></li>
                            <li><i class="far fa-clipboard"></i><div><span class="label-text">Batas Pendaftaran</span><br><span class="value-text">' . $event['batas_pendaftaran_display'] . '</span></div></li>
                        </ul>
                        <div class="d-grid gap-2">';
            if ($event['status'] == 'Selesai') {
                $html .= '
                            <a href="' . base_url('event/detail/' . $event['id']) . '" class="btn btn-outline-danger mb-1">
                                <i class="fas fa-trophy me-2"></i> Lihat Hasil Juara
                            </a>';
            }
            $html .= '
                            <button class="btn btn-brand btn-detail"
                                data-title="' . htmlspecialchars($event['judul']) . '"
                                data-date="' . htmlspecialchars($event['tanggal_pelaksanaan_display']) . '"
                                data-place="' . htmlspecialchars($event['tempat']) . '"
                                data-deadline="' . htmlspecialchars($event['batas_pendaftaran_display']) . '"
                                data-poster="' . $img . '"
                                data-status="' . $event['status'] . '"
                                data-link="' . $link . '"
                                data-bs-toggle="modal"
                                data-bs-target="#eventDetailModal">
                                Info Pendaftaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>';
        }
        return $html;
    }
}
