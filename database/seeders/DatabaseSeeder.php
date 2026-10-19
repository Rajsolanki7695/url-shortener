<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement("
            INSERT INTO companies (id, name, created_at, updated_at)
            VALUES (1, 'Primary Tenant', NOW(), NOW())
            ON DUPLICATE KEY UPDATE name = VALUES(name), updated_at = NOW()
        ");

        DB::statement("
            INSERT INTO roles (id, name, created_at, updated_at) VALUES
                (1, 'SuperAdmin', NOW(), NOW()),
                (2, 'Admin', NOW(), NOW()),
                (3, 'Member', NOW(), NOW())
            ON DUPLICATE KEY UPDATE name = VALUES(name), updated_at = NOW()
        ");

        $password = Hash::make('12345678');

        DB::statement("
            INSERT INTO users (name, email, password, role_id, company_id, created_at, updated_at)
            VALUES ('System SuperAdmin', 'superadmin@gmail.com', '$password', 1, 1, NOW(), NOW())
            ON DUPLICATE KEY UPDATE password = '$password', role_id = 1, company_id = 1, updated_at = NOW()
        ");
    }
}
