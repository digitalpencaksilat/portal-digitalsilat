<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Peringkat — Halaman leaderboard atlet (v1, berbasis nama ternormalisasi).
 */
class Peringkat extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('Peringkat_model', 'peringkat');
    }

    /**
     * Leaderboard utama: /peringkat
     * Query string opsional: ?kategori=tanding|seni &gender=Putra|Putri &event_id=NN
     */
    public function index()
    {
        $filter = [
            'category_main' => $this->input->get('kategori'),
            'gender'        => $this->input->get('gender'),
            'event_id'      => $this->input->get('event_id'),
            'keyword'       => trim((string) $this->input->get('cari')),
        ];

        $list = $this->peringkat->build_leaderboard($filter);

        // Filter pencarian nama (atas hasil agregasi)
        if ($filter['keyword'] !== '') {
            $kw = strtolower($filter['keyword']);
            $list = array_values(array_filter($list, function ($a) use ($kw) {
                return strpos(strtolower($a['display_name']), $kw) !== false
                    || strpos(strtolower($a['last_contingent']), $kw) !== false;
            }));
        }

        // pagination sederhana (server-side slice)
        $per_page = 10;
        $page = max(1, (int) $this->input->get('page'));
        $total = count($list);
        $offset = ($page - 1) * $per_page;

        $data['total']     = $total;
        $data['page']      = $page;
        $data['per_page']  = $per_page;
        $data['last_page'] = max(1, (int) ceil($total / $per_page));
        $data['list']      = array_slice($list, $offset, $per_page);
        $data['offset']    = $offset;
        $data['filter']    = $filter;

        // daftar event untuk dropdown filter
        $data['events'] = $this->db->select('id, judul')->order_by('judul', 'ASC')
            ->get('events')->result_array();

        // settings footer (konsisten dgn halaman lain)
        $settings_raw = $this->db->get('site_settings')->result_array();
        $data['s'] = array_column($settings_raw, 'nilai', 'parameter');

        $this->load->view('peringkat/index', $data);
    }

    /**
     * Endpoint AJAX untuk section leaderboard di landing page.
     * GET /peringkat/data?kategori=tanding|seni  (limit Top 20)
     * Mengembalikan HTML partial (_leaderboard_inner).
     */
    public function data()
    {
        $filter = [
            'category_main' => $this->input->get('kategori'),
            'gender'        => $this->input->get('gender'),
        ];

        $full = $this->peringkat->build_leaderboard($filter);
        $data['list'] = array_slice($full, 0, 10);

        $this->load->view('peringkat/_leaderboard_inner', $data);
    }

    /**
     * Detail atlet: /peringkat/atlet?key=NAMA_TERNORMALISASI
     */
    public function atlet()
    {
        $name_key = $this->input->get('key');
        if (empty($name_key)) {
            redirect('peringkat');
        }

        $result = $this->peringkat->athlete_history($name_key);
        if (empty($result['history'])) {
            show_404();
        }

        $data['summary'] = $result['summary'];
        $data['history'] = $result['history'];
        $data['name_key'] = $name_key;

        $settings_raw = $this->db->get('site_settings')->result_array();
        $data['s'] = array_column($settings_raw, 'nilai', 'parameter');

        $this->load->view('peringkat/detail', $data);
    }
}
