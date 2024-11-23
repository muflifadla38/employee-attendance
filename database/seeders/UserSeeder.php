<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            'Admin' => 'admin@gmail.com',
            'Rendra' => 'rendragituloh@gmail.com',
            'Khariz' => 'kharizajaah@gmail.com',
            'Joko' => 'jokoterdepan@gmail.com',
            'Maiyamyuk' => 'maiyamyuk@gmail.com',
        ];

        foreach ($users as $name => $email) {
            $user = User::factory()->create([
                'name' => $name,
                'email' => $email,
            ]);

            $user->assignRole($name == 'Admin' ? 'admin' : 'employee');
        }
    }
}
