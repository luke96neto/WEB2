<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as FakerFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    FakerFactory::create()->unique(true);

    // Usuário Admin
    User::firstOrCreate(
        ['email' => 'admin@biblioteca.com'],
        [
            'name' => 'Admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]
    );

    // Usuário Bibliotecário
    User::firstOrCreate(
        ['email' => 'bibliotecario@biblioteca.com'],
        [
            'name' => 'Bibliotecário',
            'password' => Hash::make('bib123'),
            'role' => 'bibliotecario',
        ]
    );

    // Usuário Cliente
    User::firstOrCreate(
        ['email' => 'cliente@biblioteca.com'],
        [
            'name' => 'Cliente',
            'password' => Hash::make('cliente123'),
            'role' => 'cliente',
        ]
    );

    // Chamando outros seeders
    $this->call([
        CategorySeeder::class,
        AuthorPublisherBookSeeder::class,
        UserBorrowingSeeder::class,
    ]);
}

}
