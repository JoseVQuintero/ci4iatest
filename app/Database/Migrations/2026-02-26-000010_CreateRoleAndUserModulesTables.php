<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoleAndUserModulesTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 9,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'role_slug' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'module_key' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'is_visible' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['role_slug', 'module_key']);
        $this->forge->createTable('role_modules');

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 9,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 9,
                'unsigned' => true,
            ],
            'module_key' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'is_visible' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'module_key']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_modules');

        $this->seedDefaultPermissions();
    }

    public function down()
    {
        $this->forge->dropTable('user_modules');
        $this->forge->dropTable('role_modules');
    }

    private function seedDefaultPermissions(): void
    {
        $now = date('Y-m-d H:i:s');
        $permissions = [
            ['role_slug' => 'admin', 'module_key' => 'dashboard', 'is_visible' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['role_slug' => 'admin', 'module_key' => 'products', 'is_visible' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['role_slug' => 'admin', 'module_key' => 'users', 'is_visible' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['role_slug' => 'admin', 'module_key' => 'roles', 'is_visible' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['role_slug' => 'user', 'module_key' => 'dashboard', 'is_visible' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['role_slug' => 'user', 'module_key' => 'products', 'is_visible' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['role_slug' => 'user', 'module_key' => 'users', 'is_visible' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['role_slug' => 'user', 'module_key' => 'roles', 'is_visible' => 0, 'created_at' => $now, 'updated_at' => $now],
        ];

        $this->db->table('role_modules')->insertBatch($permissions);
    }
}
