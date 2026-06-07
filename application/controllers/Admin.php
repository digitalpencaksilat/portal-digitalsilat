<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Load library dan helper yang dibutuhkan
        $this->load->database();
        $this->load->library(['session', 'form_validation', 'upload']);
        $this->load->helper(['url', 'form', 'file']);
    }

    // --- 1. HALAMAN LOGIN & AUTH ---
    public function index()
    {
        // Jika sudah login, langsung lempar ke dashboard
        if ($this->session->userdata('logged_in')) {
            redirect('admin/dashboard');
        }
        $this->load->view('admin/login');
    }

    public function auth()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // Cek user di database
        $user = $this->db->get_where('users', ['username' => $username])->row_array();

        // Validasi Password (Demo: Hardcoded 'admin123')
        // Di produksi, disarankan menggunakan password_verify($password, $user['password'])
        if ($user && $password == 'admin123') {
            $sess_data = [
                'id' => $user['id'],
                'nama' => $user['nama_lengkap'],
                'logged_in' => TRUE
            ];
            $this->session->set_userdata($sess_data);
            $this->session->set_flashdata('success', 'Selamat Datang, Administrator!');
            redirect('admin/dashboard');
        } else {
            $this->session->set_flashdata('error', 'Username atau Password salah!');
            redirect('admin');
        }
    }

    public function logout()
    {
        $this->session->unset_userdata(['id', 'nama', 'logged_in']);
        $this->session->set_flashdata('success_logout', 'Anda berhasil keluar dari sistem.');
        redirect('admin');
    }

    // --- 2. DASHBOARD (STATISTIK LENGKAP) ---
    public function dashboard()
    {
        $this->_check_login();

        // --- A. Statistik Event ---
        $data['total_events'] = $this->db->count_all('events');

        $this->db->where('status', 'Open Registration');
        $data['active_events'] = $this->db->count_all_results('events');

        $this->db->where('status', 'Segera Hadir');
        $data['coming_soon'] = $this->db->count_all_results('events');

        $this->db->where('status', 'Selesai');
        $data['finished_events'] = $this->db->count_all_results('events');

        // --- B. Statistik Pengunjung (BARU) ---
        // Hitung Pengunjung Hari Ini
        $this->db->where('access_date', date('Y-m-d'));
        $data['visitor_today'] = $this->db->count_all_results('visitors');

        // Hitung Total Pengunjung Selamanya
        $data['visitor_total'] = $this->db->count_all('visitors');

        // --- C. Ambil Data Event untuk Tabel ---
        $events = $this->db->get('events')->result_array();
        foreach ($events as &$event) {
            $event['_tanggal_mulai_timestamp'] = !empty($event['tanggal_mulai']) ? strtotime($event['tanggal_mulai']) : null;
            $event['tanggal_pelaksanaan_display'] = $this->_format_date_range($event['tanggal_mulai'], $event['tanggal_selesai']);
            $event['batas_pendaftaran_display'] = $this->_format_display_date($event['batas_pendaftaran']);
        }
        unset($event);
        usort($events, [$this, '_sort_events_by_closest_start']);
        $data['events'] = $events;

        $this->load->view('admin/dashboard', $data);
    }

    private function _format_display_date($value)
    {
        if (empty($value)) {
            return '-';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            $normalized = $this->_normalize_date_for_input($value);
            $timestamp = !empty($normalized) ? strtotime($normalized) : false;
        }

        if ($timestamp === false) {
            return $value;
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

    private function _normalize_date_for_input($value)
    {
        if (empty($value)) {
            return '';
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
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
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month_name = strtolower($matches[2]);
            $year = $matches[3];

            if (isset($months[$month_name])) {
                return $year . '-' . $months[$month_name] . '-' . $day;
            }
        }

        $timestamp = strtotime($value);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return '';
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

    private function _has_valid_date_range($tanggal_mulai, $tanggal_selesai)
    {
        if (empty($tanggal_mulai) || empty($tanggal_selesai)) {
            return false;
        }

        return strtotime($tanggal_mulai) <= strtotime($tanggal_selesai);
    }

    private function _sort_events_by_closest_start($event_a, $event_b)
    {
        $today = strtotime(date('Y-m-d'));
        $timestamp_a = !empty($event_a['_tanggal_mulai_timestamp']) ? $event_a['_tanggal_mulai_timestamp'] : null;
        $timestamp_b = !empty($event_b['_tanggal_mulai_timestamp']) ? $event_b['_tanggal_mulai_timestamp'] : null;

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

    private function _collect_event_payload()
    {
        $tanggal_mulai = $this->input->post('tanggal_mulai');
        $tanggal_selesai = $this->input->post('tanggal_selesai');

        return [
            'judul' => $this->input->post('judul'),
            'slug' => url_title($this->input->post('judul'), 'dash', TRUE),
            'tanggal_pelaksanaan' => $tanggal_mulai,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'tempat' => $this->input->post('tempat'),
            'batas_pendaftaran' => $this->input->post('batas_pendaftaran'),
            'status' => $this->input->post('status'),
            'link_pendaftaran' => $this->input->post('link_pendaftaran')
        ];
    }

    // --- 3. CREATE (TAMBAH DATA) ---
    public function tambah()
    {
        $this->_check_login();
        $this->load->view('admin/event_form'); // Load form kosong
    }

    public function simpan()
    {
        $this->_check_login();

        // Config Upload Poster
        $poster_name = 'default.jpg';
        if (!empty($_FILES['poster']['name'])) {
            $upload = $this->_do_upload();
            if ($upload['status']) {
                $poster_name = $upload['file_name'];
            } else {
                $this->session->set_flashdata('error', $upload['error']);
                redirect('admin/tambah');
            }
        }

        $data = $this->_collect_event_payload();
        if (!$this->_has_valid_date_range($data['tanggal_mulai'], $data['tanggal_selesai'])) {
            $this->session->set_flashdata('error', 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai!');
            redirect('admin/tambah');
        }
        $data['poster'] = $poster_name;

        $this->db->insert('events', $data);
        $this->session->set_flashdata('success', 'Event berhasil ditambahkan!');
        redirect('admin/dashboard');
    }

    // --- 4. UPDATE (EDIT DATA) ---
    public function edit($id)
    {
        $this->_check_login();
        $data['event'] = $this->db->get_where('events', ['id' => $id])->row_array();
        if (!$data['event']) show_404();

        $data['event']['tanggal_mulai_input'] = $this->_normalize_date_for_input($data['event']['tanggal_mulai']);
        $data['event']['tanggal_selesai_input'] = $this->_normalize_date_for_input($data['event']['tanggal_selesai']);
        $data['event']['batas_pendaftaran_input'] = $this->_normalize_date_for_input($data['event']['batas_pendaftaran']);

        $this->load->view('admin/event_form', $data);
    }

    public function update($id)
    {
        $this->_check_login();
        $old_event = $this->db->get_where('events', ['id' => $id])->row_array();

        $data = $this->_collect_event_payload();
        if (!$this->_has_valid_date_range($data['tanggal_mulai'], $data['tanggal_selesai'])) {
            $this->session->set_flashdata('error', 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai!');
            redirect('admin/edit/' . $id);
        }

        // Cek jika ada upload gambar baru
        if (!empty($_FILES['poster']['name'])) {
            $upload = $this->_do_upload();
            if ($upload['status']) {
                // Hapus gambar lama jika bukan default & bukan URL
                if ($old_event['poster'] != 'default.jpg' && strpos($old_event['poster'], 'http') === false) {
                    @unlink('./assets/uploads/posters/' . $old_event['poster']);
                }
                $data['poster'] = $upload['file_name'];
            } else {
                $this->session->set_flashdata('error', $upload['error']);
                redirect('admin/edit/' . $id);
            }
        }

        $this->db->where('id', $id);
        $this->db->update('events', $data);
        $this->session->set_flashdata('success', 'Data event berhasil diperbarui!');
        redirect('admin/dashboard');
    }

    // --- 5. DELETE (HAPUS DATA) ---
    public function hapus($id)
    {
        $this->_check_login();
        $event = $this->db->get_where('events', ['id' => $id])->row_array();

        if ($event) {
            // Hapus file fisik gambar
            if ($event['poster'] != 'default.jpg' && strpos($event['poster'], 'http') === false) {
                @unlink('./assets/uploads/posters/' . $event['poster']);
            }
            $this->db->delete('events', ['id' => $id]);
            $this->session->set_flashdata('success', 'Event berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Data tidak ditemukan!');
        }
        redirect('admin/dashboard');
    }

    // --- HELPER FUNCTIONS ---

    private function _check_login()
    {
        if (!$this->session->userdata('logged_in')) redirect('admin');
    }

    private function _do_upload()
    {
        $config['upload_path'] = './assets/uploads/posters/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 2048; // 2MB
        $config['encrypt_name'] = TRUE;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('poster')) {
            return ['status' => false, 'error' => $this->upload->display_errors()];
        } else {
            return ['status' => true, 'file_name' => $this->upload->data('file_name')];
        }
    }

    // --- 6. PENGATURAN KONTAK ---
    public function pengaturan()
    {
        $this->_check_login();
        // Ambil semua settingan
        $settings = $this->db->get('site_settings')->result_array();
        // Format ulang array agar mudah dipanggil di view ($s['whatsapp'], dll)
        $data['s'] = array_column($settings, 'nilai', 'parameter');

        $this->load->view('admin/settings', $data);
    }

    public function update_settings()
    {
        $this->_check_login();
        $params = ['whatsapp', 'email', 'instagram', 'youtube'];

        foreach ($params as $p) {
            $this->db->where('parameter', $p);
            $this->db->update('site_settings', ['nilai' => $this->input->post($p)]);
        }

        $this->session->set_flashdata('success', 'Kontak berhasil diperbarui!');
        redirect('admin/pengaturan');
    }

    // --- 7. MANAJEMEN API KEY (BARU) ---
    public function api_management()
    {
        $this->_check_login();
        $this->db->order_by('created_at', 'DESC');
        $data['events'] = $this->db->get('events')->result_array();
        $this->load->view('admin/api_settings', $data);
    }

    public function generate_api_key($id)
    {
        $this->_check_login();
        
        // Generate 32-character hex string from 16 random bytes
        $new_key = bin2hex(random_bytes(16));
        
        $this->db->where('id', $id);
        $this->db->update('events', ['api_key' => $new_key]);
        
        $this->session->set_flashdata('success', 'API Key berhasil diperbarui!');
        redirect('admin/api_management');
    }
}
