<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil role
        $roles = Role::whereIn('name', ['super', 'operator'])->get()->keyBy('name');

        $usersData = [
            [
                'phone' => '111',
                'name' => 'Super User',
                'password' => 'super',
                'role' => 'super',
                'profile' => [
                    'username_1' => 'super'
                ]
            ],
            [
                'phone' => '666',
                'name' => 'Operator User',
                'password' => 'operator',
                'role' => 'operator',
                'profile' => [
                    'username_1' => 'operator'
                ]
            ],
        ];

        foreach ($usersData as $data) {
            $user = User::firstOrCreate(
                ['phone' => $data['phone']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'verified_at' => now(),
                    'is_active' => true,
                    'role_id' => $roles[$data['role']]->id
                ]
            );

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $data['profile']
            );
        }
    }
}
