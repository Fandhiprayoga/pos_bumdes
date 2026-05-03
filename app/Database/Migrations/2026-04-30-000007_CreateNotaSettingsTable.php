<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotaSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'paper_size'   => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => '80mm', 'comment' => '58mm, 80mm, custom'],
            'custom_width' => ['type' => 'INT', 'constraint' => 4, 'null' => true, 'comment' => 'Custom width in mm'],
            'font_size'    => ['type' => 'INT', 'constraint' => 2, 'default' => 12],
            'font_family'  => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Courier New'],
            'header_text'  => ['type' => 'TEXT'],
            'header_icon'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'comment' => 'Icon class or emoji'],
            'footer_text'  => ['type' => 'TEXT', 'null' => true],
            'show_logo'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('nota_settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('nota_settings', true);
    }
}
