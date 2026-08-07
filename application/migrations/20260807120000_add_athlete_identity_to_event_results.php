<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_athlete_identity_to_event_results extends CI_Migration
{
    public function up()
    {
        $fields = [
            'athlete_nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'null'       => TRUE,
                'after'      => 'rank_label',
            ],
            'athlete_birthdate' => [
                'type'   => 'DATE',
                'null'   => TRUE,
                'after'  => 'athlete_nik',
            ],
        ];

        foreach ($fields as $column => $definition) {
            if (!$this->db->field_exists($column, 'event_results')) {
                $this->dbforge->add_column('event_results', [$column => $definition]);
            }
        }
    }

    public function down()
    {
        $this->dbforge->drop_column('event_results', 'athlete_birthdate');
        $this->dbforge->drop_column('event_results', 'athlete_nik');
    }
}
