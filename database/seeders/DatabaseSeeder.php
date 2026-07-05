<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MemberBalance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'username' => 'admin',
                'name'     => 'BFC Administrator',
                'email'    => 'admin@beijingfc.co.ke',
                'phone'    => '0700000001',
                'position' => 'MF',
                'role'     => 'admin',
                'password' => Hash::make('Admin@1234'),
                'is_active'=> true,
            ],
            [
                'username' => 'treasurer',
                'name'     => 'BFC Treasurer',
                'email'    => 'treasurer@beijingfc.co.ke',
                'phone'    => '0700000002',
                'position' => 'DF',
                'role'     => 'treasurer',
                'password' => Hash::make('Treasurer@1234'),
                'is_active'=> true,
            ],
            [
                'username' => 'coach',
                'name'     => 'BFC Coach',
                'email'    => 'coach@beijingfc.co.ke',
                'phone'    => '0700000003',
                'position' => 'GK',
                'role'     => 'coach',
                'password' => Hash::make('Coach@1234'),
                'is_active'=> true,
            ],
            [
                'username' => 'member1',
                'name'     => 'Sample Member',
                'email'    => null,
                'phone'    => '0700000004',
                'position' => 'FW',
                'role'     => 'member',
                'password' => Hash::make('Member@1234'),
                'is_active'=> true,
            ],
        ];

        foreach ($accounts as $data) {
            $user = User::firstOrCreate(
                ['username' => $data['username']],
                $data
            );

            // Create balance record if it doesn't exist
            MemberBalance::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        }

        $this->command->info('✅ Seeded 4 accounts: admin / treasurer / coach / member1');
        $this->command->info('   Passwords: Admin@1234 / Treasurer@1234 / Coach@1234 / Member@1234');
    }
}
