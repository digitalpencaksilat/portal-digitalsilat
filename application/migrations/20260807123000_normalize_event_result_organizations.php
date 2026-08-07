<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Normalize_event_result_organizations extends CI_Migration
{
    public function up()
    {
        $this->db->set('contingent', "COALESCE(NULLIF(TRIM(contingent), ''), '-')", FALSE);
        $this->db->set('school', "COALESCE(NULLIF(TRIM(school), ''), '-')", FALSE);
        $this->db->set('contingent', 'UPPER(contingent)', FALSE);
        $this->db->set('school', 'UPPER(school)', FALSE);
        $this->db->update('event_results');
    }

    public function down()
    {
        // Uppercase normalization is intentionally not reversible.
    }
}
