<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFavoriteFlagToCustomers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('customers', [
            'is_favorite' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'is_active',
            ],
        ]);

        $this->db->query('CREATE INDEX idx_customers_is_favorite ON customers(is_favorite)');
    }

    public function down()
    {
        $this->db->query('DROP INDEX idx_customers_is_favorite ON customers');
        $this->forge->dropColumn('customers', 'is_favorite');
    }
}