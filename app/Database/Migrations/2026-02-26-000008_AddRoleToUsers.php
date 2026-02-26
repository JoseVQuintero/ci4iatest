<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'user',
                'after'      => 'password_hash',
            ],
        ]);

        $this->db->table('users')->set('role', 'admin')->where('email', 'admin@example.com')->update();
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'role');
    }
}
