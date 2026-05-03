<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Event extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('pagination');
        $this->load->helper('url');
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

        // --- 3. KONFIGURASI PAGINATION ---
        $config['base_url'] = base_url('event/index');
        $config['total_rows'] = $this->db->count_all('events');
        $config['per_page'] = 6; // Menampilkan 6 event per halaman
        $config['uri_segment'] = 3;

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

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        // Ambil data event diurutkan dari yang terbaru
        $this->db->order_by('created_at', 'DESC');
        $data['events'] = $this->db->get('events', $config['per_page'], $page)->result_array();
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

        // Load view utama jika bukan request AJAX
        $this->load->view('landing_page', $data);
    }

    // --- FUNGSI PEREKAM JEJAK PENGUNJUNG ---
    private function _track_visitor()
    {
        // Cek IP Address User saat ini
        $ip = $this->input->ip_address();
        $date = date('Y-m-d');

        // Cek apakah IP ini sudah terekam di database pada tanggal hari ini?
        $this->db->where('ip_address', $ip);
        $this->db->where('access_date', $date);
        $check = $this->db->get('visitors');

        // Jika belum ada (berarti pengunjung unik baru hari ini), maka simpan datanya
        if ($check->num_rows() == 0) {
            $data = [
                'ip_address' => $ip,
                'user_agent' => $this->input->user_agent(), // Informasi Browser/Device
                'access_date' => $date
            ];
            $this->db->insert('visitors', $data);
        }
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
                            <li><i class="far fa-calendar-alt"></i><div><span class="label-text">Pelaksanaan</span><br><span class="value-text">' . $event['tanggal_pelaksanaan'] . '</span></div></li>
                            <li><i class="fas fa-map-marker-alt"></i><div><span class="label-text">Tempat</span><br><span class="value-text">' . $event['tempat'] . '</span></div></li>
                            <li><i class="far fa-clipboard"></i><div><span class="label-text">Batas Pendaftaran</span><br><span class="value-text">' . $event['batas_pendaftaran'] . '</span></div></li>
                        </ul>
                        <div class="d-grid gap-2">
                            <button class="btn btn-brand btn-detail"
                                data-title="' . htmlspecialchars($event['judul']) . '"
                                data-date="' . htmlspecialchars($event['tanggal_pelaksanaan']) . '"
                                data-place="' . htmlspecialchars($event['tempat']) . '"
                                data-deadline="' . htmlspecialchars($event['batas_pendaftaran']) . '"
                                data-poster="' . $img . '"
                                data-status="' . $event['status'] . '"
                                data-link="' . $link . '"
                                data-bs-toggle="modal"
                                data-bs-target="#eventDetailModal">
                                Detail & Pendaftaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>';
        }
        return $html;
    }
}
