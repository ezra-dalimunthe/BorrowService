<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookTransaction;
class BookTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $m = BookTransaction::factory()
        ->count(40)
        //->make();
        ->create();
        print($m);

    }
}
