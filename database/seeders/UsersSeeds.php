<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\ClientRepository;

class UsersSeeds extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        
        $clientRepository = app(ClientRepository::class);
        $client = $clientRepository->createPersonalAccessClient(
            null, 'Personal Access Client', url('/')
        );

        $users = [
            [
                'name'  => 'admin',
                'email' => 'admin@admin.com',
                'email_verified_at' => now(),
                'password' => bcrypt('admin'),
                'level' => 'admin',
                'active'=> '1',
            ],
            [
                'name'  => 'Bima Satria Utama',
                'email' => 'bima@shindu.com',
                'email_verified_at' => now(),
                'password' => bcrypt('bima'),
                'level' => 'user',
                'active'=> '1',
            ],
            [
                'name'  => 'Toddi Erlangga',
                'email' => 'toddi@galuh.com',
                'email_verified_at' => now(),
                'password' => bcrypt('toddi'),
                'level' => 'user',
                'active'=> '1',
            ]
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
            $user->createToken('sap_token')->accessToken;
        }

        Schema::enableForeignKeyConstraints();
    }
}
