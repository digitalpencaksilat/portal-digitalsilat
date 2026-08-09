<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Publisher_api_model extends CI_Model
{
    public function authenticate($plain_token)
    {
        if (!is_string($plain_token) || strlen($plain_token) !== 64 || !ctype_xdigit($plain_token)) {
            return NULL;
        }

        $hash = hash('sha256', $plain_token);
        $key = $this->db->get_where('publisher_api_keys', ['token_hash' => $hash])->row_array();
        if (!$key || !hash_equals($key['token_hash'], $hash)) {
            return NULL;
        }
        return $key;
    }

    public function is_expired($key)
    {
        return !empty($key['expires_at']) && strtotime($key['expires_at']) <= time();
    }

    public function reserve_rate_slot($key_id, $action, $limit, $minutes)
    {
        $lock_name = 'publisher_rate_' . (int) $key_id . '_' . preg_replace('/[^a-z0-9_]/i', '', $action);
        $lock = $this->db->query('SELECT GET_LOCK(?, 5) AS acquired', [$lock_name])->row_array();
        if (!$lock || (int) $lock['acquired'] !== 1) return FALSE;

        $this->db->where('api_key_id', (int) $key_id);
        $this->db->where('action', $action);
        $this->db->where('created_at >=', date('Y-m-d H:i:s', time() - ($minutes * 60)));
        $exceeded = $this->db->count_all_results('publisher_api_logs') >= $limit;
        $log_id = FALSE;
        if (!$exceeded) {
            $this->db->insert('publisher_api_logs', [
                'api_key_id' => (int) $key_id,
                'action' => $action,
                'ip_address' => $this->input->ip_address(),
                'status' => 'success',
            ]);
            $log_id = $this->db->insert_id();
        }
        $this->db->query('SELECT RELEASE_LOCK(?)', [$lock_name]);
        return $log_id;
    }

    public function unauthorized_rate_exceeded($action, $limit = 30, $minutes = 10)
    {
        $this->db->where('api_key_id IS NULL', NULL, FALSE);
        $this->db->where('action', $action);
        $this->db->where('ip_address', $this->input->ip_address());
        $this->db->where('created_at >=', date('Y-m-d H:i:s', time() - ($minutes * 60)));
        return $this->db->count_all_results('publisher_api_logs') >= $limit;
    }

    public function log($key_id, $action, $status, $article_id = NULL, $event_slug = NULL, $error = NULL)
    {
        $this->db->insert('publisher_api_logs', [
            'api_key_id' => $key_id ? (int) $key_id : NULL,
            'action' => $action,
            'article_id' => $article_id ? (int) $article_id : NULL,
            'event_slug' => $event_slug ?: NULL,
            'ip_address' => $this->input->ip_address(),
            'status' => $status,
            'error_message' => $error ? substr($error, 0, 255) : NULL,
        ]);
    }

    public function complete_log($log_id, $status, $article_id = NULL, $event_slug = NULL, $error = NULL)
    {
        if (!$log_id) return;
        $this->db->where('id', (int) $log_id)->update('publisher_api_logs', [
            'article_id' => $article_id ? (int) $article_id : NULL,
            'event_slug' => $event_slug ?: NULL,
            'status' => $status,
            'error_message' => $error ? substr($error, 0, 255) : NULL,
        ]);
    }

    public function touch($key_id)
    {
        $this->db->where('id', (int) $key_id)->update('publisher_api_keys', ['last_used_at' => date('Y-m-d H:i:s')]);
    }
}
