<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePosMvpTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'sku'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'category'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'unit'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pcs'],
            'cost_price' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'sell_price' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'stock'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'min_stock'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('sku');
        $this->forge->createTable('products', true);

        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'opened_at'           => ['type' => 'DATETIME'],
            'opening_cash'        => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'closed_at'           => ['type' => 'DATETIME', 'null' => true],
            'closing_cash_system' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'closing_cash_actual' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'variance'            => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'notes'               => ['type' => 'TEXT', 'null' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'closed_at']);
        $this->forge->createTable('cash_shifts', true);

        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'invoice_no'     => ['type' => 'VARCHAR', 'constraint' => 30],
            'shift_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'cashier_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'customer_name'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'payment_method'  => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'cash'],
            'subtotal'        => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'discount_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'grand_total'     => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'amount_paid'     => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'change_amount'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'sold_at'         => ['type' => 'DATETIME'],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('invoice_no');
        $this->forge->addKey(['sold_at', 'cashier_id']);
        $this->forge->createTable('sales', true);

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'sale_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'qty'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'unit_price'   => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'line_total'   => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('sale_id');
        $this->forge->addKey('product_id');
        $this->forge->createTable('sale_items', true);

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'product_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'movement_type' => ['type' => 'VARCHAR', 'constraint' => 20],
            'qty'           => ['type' => 'INT', 'constraint' => 11],
            'reference_no'  => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'notes'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['product_id', 'movement_type']);
        $this->forge->createTable('stock_movements', true);
    }

    public function down()
    {
        $this->forge->dropTable('stock_movements', true);
        $this->forge->dropTable('sale_items', true);
        $this->forge->dropTable('sales', true);
        $this->forge->dropTable('cash_shifts', true);
        $this->forge->dropTable('products', true);
    }
}
