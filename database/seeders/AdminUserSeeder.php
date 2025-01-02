<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crea o actualiza un usuario administrador
        User::updateOrCreate(
            ['email' => 'admin@mail.com'],  // campo único para comparar
            [
                'name' => 'Administrador',
                'password' => Hash::make('Monkey23!'), // encripta la contraseña
                'is_admin' => true,
            ]
        );
    }
}
