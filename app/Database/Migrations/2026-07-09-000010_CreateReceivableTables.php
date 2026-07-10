<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReceivableTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'address' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'credit_limit' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'default_credit_term' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 30],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->addKey('is_active');
        $this->forge->createTable('customers', true);

        $this->db->table('customers')->insert([
            'name' => 'Pelanggan Umum',
            'credit_limit' => 0,
            'default_credit_term' => 30,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->forge->addColumn('sales', [
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'customer_name',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'completed',
                'after' => 'payment_method',
            ],
            'credit_term' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
                'after' => 'status',
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'credit_term',
            ],
        ]);

        $this->db->query('CREATE INDEX idx_sales_status_due_date ON sales(status, due_date)');
        $this->db->query('CREATE INDEX idx_sales_customer_id ON sales(customer_id)');

        $this->forge->addColumn('pending_pos_transactions', [
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'customer_name',
            ],
            'credit_term' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
                'after' => 'payment_method',
            ],
        ]);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'sale_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'customer_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'credit_term' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'due_date' => ['type' => 'DATE', 'null' => true],
            'grand_total' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'amount_paid' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'outstanding' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'open'],
            'notes' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('sale_id');
        $this->forge->addKey(['customer_id', 'due_date']);
        $this->forge->addKey(['status', 'due_date']);
        $this->forge->createTable('receivables', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'receivable_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'payment_date' => ['type' => 'DATETIME'],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'cash'],
            'reference_no' => ['type' => 'VARCHAR', 'constraint' => 50],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'recorded'],
            'notes' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('reference_no');
        $this->forge->addKey(['receivable_id', 'payment_date']);
        $this->forge->addKey(['status', 'payment_date']);
        $this->forge->createTable('receivable_payments', true);
    }

    public function down()
    {
        $this->forge->dropTable('receivable_payments', true);
        $this->forge->dropTable('receivables', true);

        $this->forge->dropColumn('pending_pos_transactions', ['customer_id', 'credit_term']);

        $this->db->query('DROP INDEX idx_sales_status_due_date ON sales');
        $this->db->query('DROP INDEX idx_sales_customer_id ON sales');
        $this->forge->dropColumn('sales', ['customer_id', 'status', 'credit_term', 'due_date']);

        $this->forge->dropTable('customers', true);
    }
}