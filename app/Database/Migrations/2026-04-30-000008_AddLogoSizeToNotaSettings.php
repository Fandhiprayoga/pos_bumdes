<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLogoSizeToNotaSettings extends Migration
{
    public function up()
    {
        $fields = [
            'logo_size' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'medium',
                'after'      => 'show_logo',
                'comment'    => 'small, medium, large',
            ],
        ];

        $this->forge->addColumn('nota_settings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('nota_settings', 'logo_size');
    }
}
