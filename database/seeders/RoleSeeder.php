<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super',
                'label' => 'Super Admin',
                'redirect' => 'dashboard'
            ],
            [
                'name' => 'operator',
                'label' => 'Operator',
                'redirect' => 'dashboard'
            ],
            [
                'name' => 'user',
                'label' => 'Pengguna',
                'redirect' => 'home'
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
