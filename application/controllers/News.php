<?php
defined('BASEPATH') or exit('No direct script access allowed');

class News extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'text']);
        $this->load->model('News_model', 'news');
    }

    public function index()
    {
        $keyword = trim((string) $this->input->get('keyword'));
        $per_page = 9;
        $page = max(1, (int) $this->input->get('page'));
        $total = $this->news->count('published', $keyword);

        $data['keyword'] = $keyword;
        $data['articles'] = $this->news->all('published', $keyword, $per_page, ($page - 1) * $per_page);
        $data['total'] = $total;
        $data['page'] = $page;
        $data['last_page'] = max(1, (int) ceil($total / $per_page));
        $data['s'] = $this->_settings();
        $this->load->view('news/index', $data);
    }

    public function detail($slug = NULL)
    {
        if (!$slug) show_404();
        $article = $this->news->find_by_slug($slug);
        if (!$article) show_404();

        $data['article'] = $article;
        $data['related'] = $this->news->related($article);
        $data['s'] = $this->_settings();
        $this->load->view('news/detail', $data);
    }

    private function _settings()
    {
        return array_column($this->db->get('site_settings')->result_array(), 'nilai', 'parameter');
    }
}
