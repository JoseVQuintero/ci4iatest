<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'email', 'password_hash', 'google_id', 'github_id', 'avatar'];

    protected $validationRules = [
        'name'  => 'required|min_length[3]|max_length[100]',
        'email' => 'required|valid_email|is_unique[users.email]',
        // password validation handled in controller before hashing
    ];
    protected $validationMessages = [];
}
