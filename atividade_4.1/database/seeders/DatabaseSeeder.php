<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
class DatabaseSeeder extends Seeder
{
    
    public function run()
{
    $this->call([
        CategorySeeder::class,
        AuthorPublisherBookSeeder::class,
        UserBorrowingSeeder::class, // Novo seeder adicionado aqui
    ]);
}
}