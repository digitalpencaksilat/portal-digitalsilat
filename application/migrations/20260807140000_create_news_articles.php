<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_news_articles extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('news_articles')) {
            return;
        }

        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'excerpt' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
            'content' => [
                'type' => 'LONGTEXT',
            ],
            'cover_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ],
            'cover_image_fallback' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ],
            'thumbnail_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ],
            'thumbnail_image_fallback' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ],
            'image_alt' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ],
            'author_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'Digital Pencak Silat',
            ],
            'status' => [
                'type' => 'ENUM("draft","published","archived")',
                'default' => 'draft',
            ],
            'is_featured' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'related_event_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ],
            'published_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
                'on_update' => 'CURRENT_TIMESTAMP',
            ],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('slug', TRUE);
        $this->dbforge->add_key(['status', 'published_at']);
        $this->dbforge->add_key(['is_featured', 'status']);
        $this->dbforge->add_key('related_event_id');
        $this->dbforge->create_table('news_articles', TRUE);

        $this->db->query('ALTER TABLE `news_articles` ADD CONSTRAINT `news_articles_event_fk` FOREIGN KEY (`related_event_id`) REFERENCES `events` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->dbforge->drop_table('news_articles', TRUE);
    }
}
