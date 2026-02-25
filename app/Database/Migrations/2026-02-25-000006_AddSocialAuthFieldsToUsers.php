<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSocialAuthFieldsToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'google_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 191,
                'null'       => true,
                'after'      => 'email',
            ],
            'github_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 191,
                'null'       => true,
                'after'      => 'google_id',
            ],
            'avatar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'github_id',
            ],
        ]);

        $this->forge->modifyColumn('users', [
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('users', [
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
        ]);

        $this->forge->dropColumn('users', ['google_id', 'github_id', 'avatar']);
    }
}

