<?php
defined('BASEPATH') or exit('No direct script access allowed');

class News_model extends CI_Model
{
    private $table = 'news_articles';

    public function all($status = NULL, $keyword = '', $limit = NULL, $offset = 0)
    {
        $this->db->select('n.*, e.judul AS event_title');
        $this->db->from($this->table . ' n');
        $this->db->join('events e', 'e.id = n.related_event_id', 'left');

        if ($status !== NULL && $status !== '') {
            $this->db->where('n.status', $status);
        }
        if ($keyword !== '') {
            $this->db->group_start();
            $this->db->like('n.title', $keyword);
            $this->db->or_like('n.excerpt', $keyword);
            $this->db->or_like('n.content', $keyword);
            $this->db->group_end();
        }

        $this->db->order_by('n.published_at', 'DESC');
        $this->db->order_by('n.created_at', 'DESC');
        if ($limit !== NULL) {
            $this->db->limit((int) $limit, (int) $offset);
        }
        return $this->db->get()->result_array();
    }

    public function count($status = NULL, $keyword = '')
    {
        $this->db->from($this->table . ' n');
        if ($status !== NULL && $status !== '') {
            $this->db->where('n.status', $status);
        }
        if ($keyword !== '') {
            $this->db->group_start();
            $this->db->like('n.title', $keyword);
            $this->db->or_like('n.excerpt', $keyword);
            $this->db->or_like('n.content', $keyword);
            $this->db->group_end();
        }
        return $this->db->count_all_results();
    }

    public function find($id)
    {
        return $this->db->select('n.*, e.judul AS event_title, e.poster AS event_poster, e.tanggal_mulai, e.tanggal_selesai, e.tanggal_pelaksanaan, e.tempat')
            ->from($this->table . ' n')
            ->join('events e', 'e.id = n.related_event_id', 'left')
            ->where('n.id', (int) $id)
            ->get()->row_array();
    }

    public function find_by_slug($slug, $published_only = TRUE)
    {
        $this->db->select('n.*, e.judul AS event_title, e.poster AS event_poster, e.tanggal_mulai, e.tanggal_selesai, e.tanggal_pelaksanaan, e.tempat');
        $this->db->from($this->table . ' n');
        $this->db->join('events e', 'e.id = n.related_event_id', 'left');
        $this->db->where('n.slug', $slug);
        if ($published_only) {
            $this->db->where('n.status', 'published');
        }
        return $this->db->get()->row_array();
    }

    public function related($article, $limit = 3)
    {
        $this->db->where('status', 'published');
        $this->db->where('id !=', (int) $article['id']);
        if (!empty($article['related_event_id'])) {
            $this->db->group_start();
            $this->db->where('related_event_id', (int) $article['related_event_id']);
            $this->db->or_where('related_event_id IS NULL', NULL, FALSE);
            $this->db->group_end();
        }
        return $this->db->order_by('published_at', 'DESC')->limit($limit)->get($this->table)->result_array();
    }

    public function featured()
    {
        return $this->db->where(['status' => 'published', 'is_featured' => 1])
            ->order_by('published_at', 'DESC')->limit(1)->get($this->table)->row_array();
    }

    public function unique_slug($title, $ignore_id = NULL)
    {
        $base = url_title($title, 'dash', TRUE);
        $base = $base !== '' ? $base : 'berita';
        $slug = $base;
        $counter = 2;
        while (TRUE) {
            $this->db->where('slug', $slug);
            if ($ignore_id !== NULL) {
                $this->db->where('id !=', (int) $ignore_id);
            }
            if (!$this->db->count_all_results($this->table)) {
                return $slug;
            }
            $slug = $base . '-' . $counter++;
        }
    }
}
