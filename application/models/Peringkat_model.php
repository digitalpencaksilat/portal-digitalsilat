<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Peringkat_model
 *
 * Leaderboard v1 — akumulasi prestasi atlet lintas event berbasis
 * NAMA TERNORMALISASI (tanpa birth_date / athlete_uid).
 *
 * Catatan akurasi: ini pendekatan sementara. Atlet dianggap "sama" bila
 * nama ternormalisasinya identik. Belum menangani:
 *   - variasi ejaan (MUHAMMAD vs MUHAMAD)
 *   - homonim (dua orang beda dgn nama sama persis)
 * Lihat dokumen desain untuk Jalur A (birth_date + athlete_uid) yang akurat.
 */
class Peringkat_model extends CI_Model
{
    // Bobot poin (Emas/Perak/Perunggu)
    private $poin = [
        'emas'     => 3,
        'perak'    => 2,
        'perunggu' => 1,
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Normalisasi nama menjadi kunci pencocokan.
     */
    public function normalize_name($name)
    {
        $key = strtoupper(trim($name));
        // buang titik (gelar/inisial "M.")
        $key = str_replace('.', ' ', $key);
        // hanya sisakan huruf, angka, spasi
        $key = preg_replace('/[^A-Z0-9\s]/u', ' ', $key);
        // collapse spasi ganda
        $key = preg_replace('/\s+/', ' ', $key);
        return trim($key);
    }

    /**
     * Pecah winner_name menjadi daftar atlet individu.
     * Seni ganda/regu dipisah koma, mis "A, , B" -> ["A","B"].
     */
    public function split_names($winner_name)
    {
        $parts = explode(',', (string) $winner_name);
        $names = [];
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p !== '') {
                $names[] = $p;
            }
        }
        // fallback: kalau kosong total, kembalikan string asli
        if (empty($names)) {
            $raw = trim((string) $winner_name);
            if ($raw !== '') {
                $names[] = $raw;
            }
        }
        return $names;
    }

    /**
     * Ambil & agregasi semua hasil menjadi daftar atlet.
     *
     * @param array $filter ['category_main','gender','event_id']
     * @return array daftar atlet terurut (rank tertinggi dulu)
     */
    public function build_leaderboard($filter = [])
    {
        // Include new identifier columns for future enhancement
        $this->db->select('id, event_id, winner_name, contingent, school, rank_label, 
                          category_main, gender, age_category, 
                          athlete_nik, athlete_birthdate');
        $this->db->from('event_results');

        if (!empty($filter['category_main'])) {
            $this->db->where('category_main', $filter['category_main']);
        }
        if (!empty($filter['gender'])) {
            $this->db->where('gender', $filter['gender']);
        }
        if (!empty($filter['event_id'])) {
            $this->db->where('event_id', $filter['event_id']);
        }

        $rows = $this->db->get()->result_array();

        $athletes = []; // name_key => data agregat

        foreach ($rows as $r) {
            $rank = strtolower(trim($r['rank_label']));
            $names = $this->split_names($r['winner_name']);

            foreach ($names as $raw_name) {
                $key = $this->normalize_name($raw_name);
                if ($key === '') {
                    continue;
                }

                if (!isset($athletes[$key])) {
                    $athletes[$key] = [
                        'name_key'        => $key,
                        'display_name'    => $raw_name,
                        'last_contingent' => trim($r['contingent']),
                        '_first_id'       => $r['id'], // Store for clean URL
                        'emas'            => 0,
                        'perak'           => 0,
                        'perunggu'        => 0,
                        'poin'            => 0,
                        'events'          => [],   // set event_id unik
                        'medali_total'    => 0,
                    ];
                }

                $a = &$athletes[$key];

                if ($rank === 'emas') {
                    $a['emas']++;
                    $a['poin'] += $this->poin['emas'];
                } elseif ($rank === 'perak') {
                    $a['perak']++;
                    $a['poin'] += $this->poin['perak'];
                } elseif ($rank === 'perunggu') {
                    $a['perunggu']++;
                    $a['poin'] += $this->poin['perunggu'];
                }

                $a['medali_total']++;
                $a['events'][$r['event_id']] = true;
                // pakai kontingen yang terakhir terlihat
                if (trim($r['contingent']) !== '') {
                    $a['last_contingent'] = trim($r['contingent']);
                }
                unset($a);
            }
        }

        // finalisasi: event_count + urutkan
        $list = [];
        foreach ($athletes as $key => $a) {
            $a['event_count'] = count($a['events']);
            unset($a['events']);
            
            // Get first athlete id for clean URL (use name_key as temporary row_id)
            $first_row_id = isset($a['_first_id']) ? $a['_first_id'] : null;
            unset($a['_first_id']);
            
            $a['row_id'] = $first_row_id ?? 0; // Default to 0 if no ID found
            
            $list[] = $a;
        }

        usort($list, function ($x, $y) {
            if ($x['poin'] !== $y['poin'])         return $y['poin'] - $x['poin'];
            if ($x['emas'] !== $y['emas'])         return $y['emas'] - $x['emas'];
            if ($x['perak'] !== $y['perak'])       return $y['perak'] - $x['perak'];
            if ($x['perunggu'] !== $y['perunggu']) return $y['perunggu'] - $x['perunggu'];
            return strcmp($x['display_name'], $y['display_name']);
        });

        return $list;
    }

    /**
     * Riwayat lengkap satu atlet berdasarkan name_key.
     */
    public function athlete_history($name_key)
    {
        $this->db->select('er.*, e.judul AS event_judul, e.tanggal_pelaksanaan');
        $this->db->from('event_results er');
        $this->db->join('events e', 'e.id = er.event_id', 'left');
        $rows = $this->db->get()->result_array();

        $history = [];
        $summary = [
            'display_name' => '',
            'emas' => 0, 'perak' => 0, 'perunggu' => 0, 'poin' => 0,
            'aliases' => [], 'kontingen' => [],
        ];

        foreach ($rows as $r) {
            foreach ($this->split_names($r['winner_name']) as $raw_name) {
                if ($this->normalize_name($raw_name) !== $name_key) {
                    continue;
                }

                if ($summary['display_name'] === '') {
                    $summary['display_name'] = $raw_name;
                }
                $summary['aliases'][$raw_name] = true;
                if (trim($r['contingent']) !== '') {
                    $summary['kontingen'][trim($r['contingent'])] = true;
                }

                $rank = strtolower(trim($r['rank_label']));
                if (isset($this->poin[$rank])) {
                    $summary[$rank]++;
                    $summary['poin'] += $this->poin[$rank];
                }

                $history[] = [
                    'event_judul'    => $r['event_judul'],
                    'tanggal'        => $r['tanggal_pelaksanaan'],
                    'category_main'  => $r['category_main'],
                    'category_detail' => $r['category_detail'],
                    'age_category'   => $r['age_category'],
                    'gender'         => $r['gender'],
                    'contingent'     => $r['contingent'],
                    'rank_label'     => $r['rank_label'],
                    'raw_name'       => $raw_name,
                ];
            }
        }

        $summary['aliases']   = array_keys($summary['aliases']);
        $summary['kontingen'] = array_keys($summary['kontingen']);

        return ['summary' => $summary, 'history' => $history];
    }

    /**
     * Get athlete by row_id (clean URL support)
     * Uses event_results.id as unique identifier
     */
    public function get_by_row_id($id)
    {
        $this->db->select('winner_name');
        $this->db->from('event_results er');
        $this->db->where('er.id', (int)$id);

        $row = $this->db->get()->row_array();
        if (!$row) {
            return null;
        }

        // The row ID identifies one result; the profile must still show the
        // athlete's history across all events.
        $names = $this->split_names($row['winner_name']);
        if (empty($names)) {
            return null;
        }

        return $this->athlete_history($this->normalize_name($names[0]));
    }
}
