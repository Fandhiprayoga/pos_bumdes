<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfitSnapshotToSaleItems extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sale_items', [
            'cost_price_snapshot' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'after' => 'unit_price',
            ],
            'cogs_total' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'after' => 'cost_price_snapshot',
            ],
            'discount_allocated' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'after' => 'cogs_total',
            ],
            'net_line_total' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'after' => 'line_total',
            ],
            'gross_profit' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'after' => 'net_line_total',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sale_items', [
            'cost_price_snapshot',
            'cogs_total',
            'discount_allocated',
            'net_line_total',
            'gross_profit',
        ]);
    }
}