<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{
    private $api_key = 'DPS_SECRET_2024'; // Ganti dengan key yang lebih aman di produksi

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Endpoint untuk menerima data hasil kejuaraan
     * POST /api/push_results
     */
    public function push_results()
    {
        // 1. Ambil Input JSON Terlebih Dahulu
        $input = json_decode($this->input->raw_input_stream, true);
        if (empty($input) || !isset($input['event_id']) || !isset($input['results'])) {
            return $this->_response(['status' => 'error', 'message' => 'Invalid Data Format'], 400);
        }

        $event_id = $input['event_id'];
        $results = $input['results'];

        // 2. Ambil API Key dari Database untuk Event Ini
        $event = $this->db->get_where('events', ['id' => $event_id])->row_array();
        if (!$event) {
            return $this->_response(['status' => 'error', 'message' => 'Event not found'], 404);
        }

        // 3. Verifikasi API Key dari Header vs Database
        $received_key = $this->input->get_request_header('X-API-KEY');
        if (empty($event['api_key']) || $received_key !== $event['api_key']) {
            return $this->_response(['status' => 'error', 'message' => 'Unauthorized Access: Invalid API Key for this Event'], 401);
        }

        // Proses Simpan Data (Gunakan Transaksi)
        $this->db->trans_start();

        // Hapus data lama untuk event ini (Full Sync)
        $this->db->delete('event_results', ['event_id' => $event_id]);

        $batch_data = [];
        foreach ($results as $row) {
            $batch_data[] = [
                'event_id'        => $event_id,
                'category_main'   => $row['category_main'],   // 'tanding' atau 'seni'
                'category_detail' => $row['category_detail'], // misal: 'Kelas A Putra'
                'age_category'    => $row['age_category'],    // misal: 'Usia Dini'
                'gender'          => $row['gender'],          // 'Putra' atau 'Putri'
                'winner_name'     => $row['winner_name'],
                'contingent'      => $row['contingent'],
                'rank_label'      => $row['rank_label']       // 'Emas', 'Perak', 'Perunggu'
            ];
        }

        if (!empty($batch_data)) {
            $this->db->insert_batch('event_results', $batch_data);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return $this->_response(['status' => 'error', 'message' => 'Failed to save results'], 500);
        }

        return $this->_response(['status' => 'success', 'message' => count($batch_data) . ' results processed']);
    }

    private function _response($data, $status_code = 200)
    {
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($status_code)
            ->set_output(json_encode($data));
    }
}
