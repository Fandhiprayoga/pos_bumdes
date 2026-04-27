<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Dapatkan user provider dari Shield
        $users = auth()->getProvider();

        /**
         * Daftar user default yang akan dibuat
         */
        $defaultUsers = [
            [
                'username' => 'superadmin',
                'email'    => 'superadmin@example.com',
                'password' => 'password123',
                'group'    => 'superadmin',
            ],
            [
                'username' => 'admin',
                'email'    => 'admin@example.com',
                'password' => 'password123',
                'group'    => 'admin',
            ],
            [
                'username' => 'manager',
                'email'    => 'manager@example.com',
                'password' => 'password123',
                'group'    => 'manager',
            ],
            [
                'username' => 'cashier',
                'email'    => 'cashier@example.com',
                'password' => 'password123',
                'group'    => 'cashier',
            ],
            [
                'username' => 'user',
                'email'    => 'user@example.com',
                'password' => 'password123',
                'group'    => 'user',
            ],
        ];

        foreach ($defaultUsers as $userData) {
            $existingUser = $users->findByCredentials(['email' => $userData['email']]);

            if ($existingUser !== null) {
                if (! $existingUser->inGroup($userData['group'])) {
                    $existingUser->addGroup($userData['group']);
                }

                echo "User '{$userData['username']}' sudah ada, dilewati.\n";
                continue;
            }

            // Buat user entity
            $user = new User([
                'username' => $userData['username'],
                'email'    => $userData['email'],
                'password' => $userData['password'],
                'active'   => 1,
            ]);

            // Simpan user
            $users->save($user);

            // Ambil user yang baru dibuat
            $user = $users->findById($users->getInsertID());

            // Assign group/role
            $user->addGroup($userData['group']);

            echo "User '{$userData['username']}' created with role '{$userData['group']}'\n";
        }

        echo "\n=== Default Login Credentials ===\n";
        echo "Super Admin : superadmin@example.com / password123\n";
        echo "Admin       : admin@example.com / password123\n";
        echo "Manager     : manager@example.com / password123\n";
        echo "Cashier     : cashier@example.com / password123\n";
        echo "User        : user@example.com / password123\n";
        echo "=================================\n";
    }
}
