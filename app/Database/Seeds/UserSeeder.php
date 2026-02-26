<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name'          => 'Admin User',
            'email'         => 'admin@example.com',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'role'          => 'admin',
        ];

        // Using query() for simplicity
        $this->db->table('users')->insert($data);
    }
}
