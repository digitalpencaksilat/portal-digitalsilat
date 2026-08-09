<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Publisher_api extends CI_Controller
{
    private $publisher_key;
    private $rate_log_id;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'text']);
        $this->load->model('Publisher_api_model', 'publisher');
        $this->load->model('News_model', 'news');
        $this->load->library(['image_optimizer', 'article_sanitizer']);
    }

    public function events()
    {
        if (!$this->_authorize('search_events', 30, 10)) return;
        if (strtoupper($this->input->method()) !== 'GET') return $this->_fail('Method tidak diizinkan.', 405, 'search_events');

        $keyword = trim((string) $this->input->get('keyword'));
        if (mb_strlen($keyword) < 2 || mb_strlen($keyword) > 100) {
            return $this->_fail('Kata kunci harus berisi 2-100 karakter.', 400, 'search_events');
        }

        $this->db->select('id, judul AS title, slug, tanggal_pelaksanaan AS date, tempat AS location, status');
        $this->db->from('events');
        $this->db->group_start()->like('judul', $keyword)->or_like('slug', url_title($keyword, 'dash', TRUE))->group_end();
        $this->db->order_by('created_at', 'DESC')->limit(10);
        $events = $this->db->get()->result_array();

        $this->publisher->touch($this->publisher_key['id']);
        $this->publisher->complete_log($this->rate_log_id, 'success');
        return $this->_response(['status' => 'success', 'events' => $events]);
    }

    public function create_draft()
    {
        if (!$this->_authorize('create_draft', 10, 60)) return;
        if (strtoupper($this->input->method()) !== 'POST') return $this->_fail('Method tidak diizinkan.', 405, 'create_draft');

        $title = trim((string) $this->input->post('title'));
        $excerpt = trim((string) $this->input->post('excerpt'));
        $raw_content = (string) $this->input->post('content');
        if (strlen($raw_content) > 100000) return $this->_fail('Isi artikel maksimal 100.000 karakter.', 422, 'create_draft');
        $content = $this->article_sanitizer->clean($raw_content);
        $image_alt = trim((string) $this->input->post('image_alt'));
        $author = trim((string) $this->input->post('author_name')) ?: 'Digital Pencak Silat';
        $event_slug = trim((string) $this->input->post('related_event_slug'));

        $validation_error = $this->_validate_article($title, $excerpt, $content, $image_alt, $author);
        if ($validation_error) return $this->_fail($validation_error, 422, 'create_draft', NULL, $event_slug);

        $event_id = NULL;
        if ($event_slug !== '') {
            $event = $this->db->select('id')->get_where('events', ['slug' => $event_slug])->row_array();
            if (!$event) return $this->_fail('Event terkait tidak ditemukan.', 404, 'create_draft', NULL, $event_slug);
            $event_id = (int) $event['id'];
        }

        $upload_error = $this->_validate_cover();
        if ($upload_error) return $this->_fail($upload_error['message'], $upload_error['code'], 'create_draft', NULL, $event_slug);

        $images = $this->image_optimizer->process($_FILES['cover']['tmp_name'], './assets/uploads/news/covers/', [
            'max_dimension' => 1600,
            'quality' => 84,
            'thumbnail' => TRUE,
            'thumbnail_dimension' => 480,
            'crop' => FALSE,
        ]);
        if (!$images['status']) return $this->_fail($images['error'], 422, 'create_draft', NULL, $event_slug);

        $data = [
            'title' => $title,
            'slug' => $this->news->unique_slug($title),
            'excerpt' => $excerpt,
            'content' => $content,
            'cover_image' => $images['webp'],
            'cover_image_fallback' => $images['jpeg'],
            'thumbnail_image' => $images['thumbnail_webp'],
            'thumbnail_image_fallback' => $images['thumbnail_jpeg'],
            'image_alt' => $image_alt,
            'author_name' => $author,
            'status' => 'draft',
            'is_featured' => 0,
            'related_event_id' => $event_id,
            'created_by_api_key_id' => (int) $this->publisher_key['id'],
            'published_at' => NULL,
        ];

        $this->db->trans_start();
        $this->db->insert('news_articles', $data);
        $article_id = $this->db->insert_id();
        $this->db->where('id', (int) $this->publisher_key['id'])->update('publisher_api_keys', ['last_used_at' => date('Y-m-d H:i:s')]);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->_delete_images($images);
            return $this->_fail('Draft gagal disimpan.', 500, 'create_draft', NULL, $event_slug);
        }

        $this->publisher->complete_log($this->rate_log_id, 'success', $article_id, $event_slug);
        return $this->_response([
            'status' => 'success',
            'message' => 'Draft berita berhasil dibuat.',
            'article' => [
                'id' => (int) $article_id,
                'slug' => $data['slug'],
                'status' => 'draft',
                'preview_url' => base_url('admin/news/preview/' . $article_id),
            ],
        ], 201);
    }

    private function _authorize($action, $limit, $minutes)
    {
        if (ENVIRONMENT === 'production' && !$this->_is_https_request()) {
            $this->_response(['status' => 'error', 'message' => 'HTTPS wajib digunakan.'], 403);
            return FALSE;
        }

        $header = trim((string) $this->input->get_request_header('Authorization', TRUE));
        if (!preg_match('/^Bearer\s+([a-f0-9]{64})$/i', $header, $match)) {
            if ($this->publisher->unauthorized_rate_exceeded($action)) {
                $this->_response(['status' => 'error', 'message' => 'Terlalu banyak percobaan autentikasi.'], 429);
                return FALSE;
            }
            $this->publisher->log(NULL, $action, 'failed', NULL, NULL, 'Token tidak tersedia atau format salah.');
            $this->_response(['status' => 'error', 'message' => 'Token tidak valid.'], 401);
            return FALSE;
        }

        $this->publisher_key = $this->publisher->authenticate($match[1]);
        if (!$this->publisher_key) {
            if ($this->publisher->unauthorized_rate_exceeded($action)) {
                $this->_response(['status' => 'error', 'message' => 'Terlalu banyak percobaan autentikasi.'], 429);
                return FALSE;
            }
            $this->publisher->log(NULL, $action, 'failed', NULL, NULL, 'Token tidak dikenal.');
            $this->_response(['status' => 'error', 'message' => 'Token tidak valid.'], 401);
            return FALSE;
        }
        if (!(int) $this->publisher_key['is_active'] || $this->publisher->is_expired($this->publisher_key)) {
            $this->publisher->log($this->publisher_key['id'], $action, 'failed', NULL, NULL, 'Token nonaktif atau kedaluwarsa.');
            $this->_response(['status' => 'error', 'message' => 'Token nonaktif atau kedaluwarsa.'], 403);
            return FALSE;
        }
        $this->rate_log_id = $this->publisher->reserve_rate_slot($this->publisher_key['id'], $action, $limit, $minutes);
        if (!$this->rate_log_id) {
            $this->_response(['status' => 'error', 'message' => 'Terlalu banyak request. Coba kembali nanti.'], 429);
            return FALSE;
        }
        return TRUE;
    }

    private function _is_https_request()
    {
        if (isset($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off' && $_SERVER['HTTPS'] !== '') {
            return TRUE;
        }

        $trusted = array_filter(array_map('trim', explode(',', (string) config_item('proxy_ips'))));
        $remote_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        if (!in_array($remote_ip, $trusted, TRUE)) return FALSE;

        $forwarded_proto = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : '';
        $forwarded_proto = explode(',', $forwarded_proto);
        return strtolower(trim($forwarded_proto[0])) === 'https';
    }

    private function _validate_article($title, $excerpt, $content, $image_alt, $author)
    {
        if ($title === '' || mb_strlen($title) > 255) return 'Judul wajib diisi dan maksimal 255 karakter.';
        if ($excerpt === '' || mb_strlen($excerpt) > 500) return 'Ringkasan wajib diisi dan maksimal 500 karakter.';
        if ($content === '' || strlen($content) > 100000) return 'Isi artikel wajib diisi dan maksimal 100.000 karakter.';
        if ($image_alt === '' || mb_strlen($image_alt) > 255) return 'Alt text wajib diisi dan maksimal 255 karakter.';
        if ($author === '' || mb_strlen($author) > 100) return 'Nama penulis maksimal 100 karakter.';
        return NULL;
    }

    private function _validate_cover()
    {
        if (empty($_FILES['cover']) || $_FILES['cover']['error'] === UPLOAD_ERR_NO_FILE) return ['message' => 'Cover wajib diunggah.', 'code' => 400];
        if ($_FILES['cover']['error'] !== UPLOAD_ERR_OK) return ['message' => 'Upload cover gagal.', 'code' => 400];
        if ($_FILES['cover']['size'] > 8 * 1024 * 1024) return ['message' => 'Ukuran cover maksimal 8 MB.', 'code' => 413];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['cover']['tmp_name']);
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], TRUE)) {
            return ['message' => 'Format cover tidak didukung.', 'code' => 415];
        }
        return NULL;
    }

    private function _fail($message, $code, $action, $article_id = NULL, $event_slug = NULL)
    {
        if ($this->rate_log_id) {
            $this->publisher->complete_log($this->rate_log_id, 'failed', $article_id, $event_slug, $message);
        } else {
            $this->publisher->log($this->publisher_key ? $this->publisher_key['id'] : NULL, $action, 'failed', $article_id, $event_slug, $message);
        }
        return $this->_response(['status' => 'error', 'message' => $message], $code);
    }

    private function _delete_images($images)
    {
        foreach (['webp', 'jpeg', 'thumbnail_webp', 'thumbnail_jpeg'] as $name) {
            if (!empty($images[$name])) @unlink('./assets/uploads/news/covers/' . basename($images[$name]));
        }
    }

    private function _response($data, $status_code = 200)
    {
        return $this->output->set_content_type('application/json')->set_status_header($status_code)->set_output(json_encode($data));
    }
}
