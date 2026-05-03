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
        $this->db->order_by('created_at', 'DESC');
        $data['events'] = $this->db->get('events')->result_array();

        $this->load->view('admin/dashboard', $data);
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

        $data = [
            'judul' => $this->input->post('judul'),
            'slug' => url_title($this->input->post('judul'), 'dash', TRUE),
            'tanggal_pelaksanaan' => $this->input->post('tanggal_pelaksanaan'),
            'tempat' => $this->input->post('tempat'),
            'batas_pendaftaran' => $this->input->post('batas_pendaftaran'),
            'status' => $this->input->post('status'),
            'link_pendaftaran' => $this->input->post('link_pendaftaran'),
            'poster' => $poster_name
        ];

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
        $this->load->view('admin/event_form', $data);
    }

    public function update($id)
    {
        $this->_check_login();
        $old_event = $this->db->get_where('events', ['id' => $id])->row_array();

        $data = [
            'judul' => $this->input->post('judul'),
            'slug' => url_title($this->input->post('judul'), 'dash', TRUE),
            'tanggal_pelaksanaan' => $this->input->post('tanggal_pelaksanaan'),
            'tempat' => $this->input->post('tempat'),
            'batas_pendaftaran' => $this->input->post('batas_pendaftaran'),
            'status' => $this->input->post('status'),
            'link_pendaftaran' => $this->input->post('link_pendaftaran'),
        ];

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
        
        // Generate key acak: DPS-RANDOM
        $new_key = 'DPS-' . strtoupper(bin2hex(random_bytes(8)));
        
        $this->db->where('id', $id);
        $this->db->update('events', ['api_key' => $new_key]);
        
        $this->session->set_flashdata('success', 'API Key berhasil diperbarui!');
        redirect('admin/api_management');
    }

    private function _check_login()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('admin');
        }
    }
}
