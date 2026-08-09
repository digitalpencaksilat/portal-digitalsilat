<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_publisher_api extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('role', 'users')) {
            $this->dbforge->add_column('users', [
                'role' => [
                    'type' => 'ENUM("superadmin","admin")',
                    'default' => 'admin',
                    'after' => 'nama_lengkap',
                ],
            ]);
        }
        if (!$this->db->table_exists('publisher_api_keys')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
                'key_name' => ['type' => 'VARCHAR', 'constraint' => 100],
                'token_hash' => ['type' => 'CHAR', 'constraint' => 64],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'expires_at' => ['type' => 'DATETIME', 'null' => TRUE],
                'last_used_at' => ['type' => 'DATETIME', 'null' => TRUE],
                'created_by' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('token_hash', TRUE);
            $this->dbforge->add_key(['is_active', 'expires_at']);
            $this->dbforge->create_table('publisher_api_keys', TRUE);
        }

        if (!$this->db->table_exists('publisher_api_logs')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'BIGINT', 'constraint' => 20, 'auto_increment' => TRUE],
                'api_key_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'action' => ['type' => 'VARCHAR', 'constraint' => 50],
                'article_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'event_slug' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45],
                'status' => ['type' => 'ENUM("success","failed")'],
                'error_message' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key(['api_key_id', 'action', 'created_at']);
            $this->dbforge->add_key(['ip_address', 'created_at']);
            $this->dbforge->create_table('publisher_api_logs', TRUE);
        }

        if (!$this->db->field_exists('created_by_api_key_id', 'news_articles')) {
            $this->dbforge->add_column('news_articles', [
                'created_by_api_key_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE,
                    'after' => 'related_event_id',
                ],
            ]);
            $this->db->query('ALTER TABLE `news_articles` ADD INDEX `idx_news_publisher_key` (`created_by_api_key_id`)');
        }
    }

    public function down()
    {
        if ($this->db->field_exists('created_by_api_key_id', 'news_articles')) {
            $this->dbforge->drop_column('news_articles', 'created_by_api_key_id');
        }
        $this->dbforge->drop_table('publisher_api_logs', TRUE);
        $this->dbforge->drop_table('publisher_api_keys', TRUE);
        if ($this->db->field_exists('role', 'users')) {
            $this->dbforge->drop_column('users', 'role');
        }
    }
}
