<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImageBlobToProducts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products', [
            'image_data' => [
                'type' => 'LONGBLOB',
                'null' => true,
                'after' => 'image',
            ],
            'image_mime' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'image_data',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products', ['image_data', 'image_mime']);
    }
}

