<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMwaAuditColumnsToStockMovements extends Migration
{
    public function up()
    {
        $this->forge->addColumn('stock_movements', [
            'unit_cost_in' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'after' => 'qty',
            ],
            'avg_cost_before' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'after' => 'unit_cost_in',
            ],
            'avg_cost_after' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'after' => 'avg_cost_before',
            ],
            'stock_before' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'avg_cost_after',
            ],
            'stock_after' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'stock_before',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('stock_movements', [
            'unit_cost_in',
            'avg_cost_before',
            'avg_cost_after',
            'stock_before',
            'stock_after',
        ]);
    }
}
