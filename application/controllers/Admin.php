<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Load library dan helper yang dibutuhkan
        $this->load->database();
        $this->load->library(['session', 'form_validation', 'upload', 'image_optimizer']);
        $this->load->helper(['url', 'form', 'file', 'text']);
        $this->load->model('News_model', 'news');
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

        $valid_password = $user && password_verify($password, $user['password']);

        if ($valid_password) {
            $this->session->sess_regenerate(TRUE);
            $sess_data = [
                'id' => $user['id'],
                'nama' => $user['nama_lengkap'],
                'role' => isset($user['role']) ? $user['role'] : 'admin',
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
        $this->session->unset_userdata(['id', 'nama', 'role', 'logged_in']);
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
        if (strtolower($this->input->method()) !== 'post') show_404();
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
        $config['upload_path'] = './assets/uploads/posters/tmp/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|webp';
        $config['max_size'] = 5120; // 5MB before optimization
        $config['encrypt_name'] = TRUE;

        if (!is_dir($config['upload_path'])) @mkdir($config['upload_path'], 0755, TRUE);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('poster')) {
            return ['status' => false, 'error' => $this->upload->display_errors()];
        } else {
            $temporary = $this->upload->data('full_path');
            $optimized = $this->image_optimizer->process($temporary, './assets/uploads/posters/', [
                'max_dimension' => 1400,
                'quality' => 84,
                'thumbnail' => false,
                'crop' => false,
            ]);
            @unlink($temporary);
            if (!$optimized['status']) return $optimized;

            // The events schema has one poster column, so keep one optimized asset.
            if (!empty($optimized['jpeg']) && !empty($optimized['webp'])) {
                @unlink('./assets/uploads/posters/' . $optimized['jpeg']);
            }
            return ['status' => true, 'file_name' => $optimized['webp'] ?: $optimized['jpeg']];
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
        $this->load->view('admin/api_settings', $this->_api_management_data());
    }

    public function generate_publisher_token()
    {
        $this->_check_superadmin();
        if (strtolower($this->input->method()) !== 'post') show_404();

        $name = trim((string) $this->input->post('key_name'));
        $valid_days = max(30, min(365, (int) $this->input->post('valid_days')));
        $current_password = (string) $this->input->post('current_password');
        $current_user = $this->db->get_where('users', ['id' => (int) $this->session->userdata('id')])->row_array();
        if (!$current_user || !password_verify($current_password, $current_user['password'])) {
            $this->session->set_flashdata('error', 'Password admin tidak valid. Token tidak dibuat.');
            redirect('admin/api_management#publishing-api');
        }
        if ($name === '' || mb_strlen($name) > 100) {
            $this->session->set_flashdata('error', 'Nama token wajib diisi dan maksimal 100 karakter.');
            redirect('admin/api_management#publishing-api');
        }

        $plain_token = bin2hex(random_bytes(32));
        $inserted = $this->db->insert('publisher_api_keys', [
            'key_name' => $name,
            'token_hash' => hash('sha256', $plain_token),
            'is_active' => 1,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+' . $valid_days . ' days')),
            'created_by' => (int) $this->session->userdata('id'),
        ]);
        if (!$inserted) {
            $this->session->set_flashdata('error', 'Token gagal disimpan ke database.');
            redirect('admin/api_management#publishing-api');
        }

        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Referrer-Policy: no-referrer');
        $this->output->set_header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; script-src 'unsafe-inline'; img-src 'self' data:; base-uri 'none'; form-action 'self'; frame-ancestors 'none'");
        $this->load->view('admin/publisher_token_created', [
            'publisher_plain_token' => $plain_token,
            'publisher_token_name' => $name,
        ]);
    }

    public function revoke_publisher_token($id)
    {
        $this->_check_superadmin();
        if (strtolower($this->input->method()) !== 'post') show_404();
        $this->db->where('id', (int) $id)->update('publisher_api_keys', ['is_active' => 0]);
        $this->session->set_flashdata('success', 'Publishing token berhasil dinonaktifkan.');
        redirect('admin/api_management#publishing-api');
    }

    private function _api_management_data()
    {
        $this->db->where('created_at <', date('Y-m-d H:i:s', strtotime('-180 days')))->delete('publisher_api_logs');
        $this->db->order_by('created_at', 'DESC');
        $data['events'] = $this->db->get('events')->result_array();
        $user = $this->db->select('role')->get_where('users', ['id' => (int) $this->session->userdata('id')])->row_array();
        $data['can_manage_publisher'] = $user && $user['role'] === 'superadmin';
        $data['publisher_keys'] = [];
        $data['publisher_logs'] = [];
        if ($data['can_manage_publisher']) {
            $data['publisher_keys'] = $this->db->select('k.*, u.nama_lengkap AS creator_name')
                ->from('publisher_api_keys k')->join('users u', 'u.id = k.created_by', 'left')
                ->order_by('k.created_at', 'DESC')->get()->result_array();
            $data['publisher_logs'] = $this->db->select('l.*, k.key_name')
                ->from('publisher_api_logs l')->join('publisher_api_keys k', 'k.id = l.api_key_id', 'left')
                ->order_by('l.created_at', 'DESC')->limit(30)->get()->result_array();
        }
        return $data;
    }

    private function _check_superadmin()
    {
        $this->_check_login();
        $user = $this->db->select('role')->get_where('users', ['id' => (int) $this->session->userdata('id')])->row_array();
        if (!$user || $user['role'] !== 'superadmin') show_error('Akses khusus superadmin.', 403);
    }

    public function generate_api_key($id)
    {
        $this->_check_login();
        if (strtolower($this->input->method()) !== 'post') show_404();
        
        // Generate 32-character hex string from 16 random bytes
        $new_key = bin2hex(random_bytes(16));
        
        $this->db->where('id', $id);
        $this->db->update('events', ['api_key' => $new_key]);
        
        $this->session->set_flashdata('success', 'API Key berhasil diperbarui!');
        redirect('admin/api_management');
    }

    // --- 8. MANAJEMEN NEWS ---
    public function news()
    {
        $this->_check_login();
        $keyword = trim((string) $this->input->get('keyword'));
        $status = trim((string) $this->input->get('status'));
        $per_page = 10;
        $page = max(1, (int) $this->input->get('page'));
        $total = $this->news->count($status, $keyword);

        $data['articles'] = $this->news->all($status, $keyword, $per_page, ($page - 1) * $per_page);
        $data['keyword'] = $keyword;
        $data['status_filter'] = $status;
        $data['page'] = $page;
        $data['last_page'] = max(1, (int) ceil($total / $per_page));
        $data['news_counts'] = [
            'all' => $this->news->count(),
            'published' => $this->news->count('published'),
            'draft' => $this->news->count('draft'),
            'archived' => $this->news->count('archived'),
        ];
        $this->load->view('admin/news/index', $data);
    }

    public function news_create()
    {
        $this->_check_login();
        $data['events'] = $this->_news_events();
        $this->load->view('admin/news/form', $data);
    }

    public function news_edit($id)
    {
        $this->_check_login();
        $data['article'] = $this->news->find($id);
        if (!$data['article']) show_404();
        $data['events'] = $this->_news_events();
        $this->load->view('admin/news/form', $data);
    }

    public function news_save()
    {
        $this->_check_login();
        $id = (int) $this->input->post('id');
        $existing = $id ? $this->news->find($id) : NULL;
        if ($id && !$existing) show_404();

        $title = trim((string) $this->input->post('title'));
        $content = trim((string) $this->input->post('content'));
        $status = $this->input->post('status') === 'published' ? 'published' : ($this->input->post('status') === 'archived' ? 'archived' : 'draft');
        if ($title === '' || ($status === 'published' && $content === '')) {
            $this->session->set_flashdata('error', 'Judul wajib diisi dan berita published harus memiliki isi.');
            redirect($id ? 'admin/news/edit/' . $id : 'admin/news/create');
        }

        $data = [
            'title' => $title,
            'slug' => $this->news->unique_slug($title, $id ?: NULL),
            'excerpt' => trim((string) $this->input->post('excerpt')),
            'content' => $this->_sanitize_article($content),
            'image_alt' => trim((string) $this->input->post('image_alt')) ?: $title,
            'author_name' => trim((string) $this->input->post('author_name')) ?: 'Digital Pencak Silat',
            'status' => $status,
            'is_featured' => $this->input->post('is_featured') ? 1 : 0,
            'related_event_id' => $this->input->post('related_event_id') ? (int) $this->input->post('related_event_id') : NULL,
            'published_at' => $status === 'published' ? ($this->input->post('published_at') ?: date('Y-m-d H:i:s')) : NULL,
        ];

        if (!empty($_FILES['cover']['name'])) {
            $upload = $this->_optimize_news_image();
            if (!$upload['status']) {
                $this->session->set_flashdata('error', $upload['error']);
                redirect($id ? 'admin/news/edit/' . $id : 'admin/news/create');
            }
            $data['cover_image'] = $upload['webp'];
            $data['cover_image_fallback'] = $upload['jpeg'];
            $data['thumbnail_image'] = $upload['thumbnail_webp'];
            $data['thumbnail_image_fallback'] = $upload['thumbnail_jpeg'];
            if ($existing) $this->_delete_news_images($existing);
        } elseif (!$existing && $status === 'published') {
            $this->session->set_flashdata('error', 'Cover berita wajib diunggah sebelum publikasi.');
            redirect('admin/news/create');
        }

        if ($data['is_featured']) {
            $this->db->set('is_featured', 0)->where('id !=', $id ?: 0)->update('news_articles');
        }

        if ($id) {
            $this->db->where('id', $id)->update('news_articles', $data);
        } else {
            $this->db->insert('news_articles', $data);
            $id = $this->db->insert_id();
        }
        $this->session->set_flashdata('success', 'Berita berhasil disimpan.');
        redirect('admin/news/edit/' . $id);
    }

    public function news_delete($id)
    {
        $this->_check_login();
        if (strtolower($this->input->method()) !== 'post') show_404();
        $article = $this->news->find($id);
        if ($article) {
            $this->_delete_news_images($article);
            $this->db->delete('news_articles', ['id' => $id]);
            $this->session->set_flashdata('success', 'Berita berhasil dihapus.');
        }
        redirect('admin/news');
    }

    public function news_preview($id)
    {
        $this->_check_login();
        $article = $this->news->find($id);
        if (!$article) show_404();
        $data['article'] = $article;
        $data['related'] = $this->news->related($article);
        $data['s'] = array_column($this->db->get('site_settings')->result_array(), 'nilai', 'parameter');
        $this->load->view('news/detail', $data);
    }

    private function _news_events()
    {
        return $this->db->select('id, judul, tanggal_pelaksanaan, tempat')->order_by('created_at', 'DESC')->get('events')->result_array();
    }

    private function _optimize_news_image()
    {
        $tmp = $_FILES['cover']['tmp_name'];
        $directory = './assets/uploads/news/covers/';
        return $this->image_optimizer->process($tmp, $directory, [
            'max_dimension' => 1600,
            'quality' => 84,
            'thumbnail' => TRUE,
            'thumbnail_dimension' => 480,
            'crop' => FALSE,
        ]);
    }

    private function _delete_news_images($article)
    {
        $names = ['cover_image', 'cover_image_fallback', 'thumbnail_image', 'thumbnail_image_fallback'];
        foreach ($names as $name) {
            if (!empty($article[$name])) @unlink('./assets/uploads/news/covers/' . basename($article[$name]));
        }
    }

    private function _sanitize_article($content)
    {
        $this->load->library('article_sanitizer');
        return $this->article_sanitizer->clean($content);
    }
}
