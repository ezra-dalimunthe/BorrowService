<?php

namespace Database\Seeders;

use App\Models\BookBorrow;
use App\Models\BookReturn;
use Illuminate\Database\Seeder;

class BookTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "truncate table", "\n";
        for ($i = 0; $i < 100; $i++) {
            echo ".";
            $faker = \Faker\Factory::create();
            $book_id = $faker->numberBetween(1, 30);
            $member_id = $faker->numberBetween(1, 30);
            $loan_date = $faker->dateTimeBetween('-12 months', '-2 days');
            $due_back_date = clone $loan_date->add(new \DateInterval("P14D"));
            $entity = new BookBorrow;
            $entity->book_id = $book_id;
            $entity->member_id = $member_id;
            $entity->loan_date = $loan_date;
            $entity->due_back_date = $due_back_date;
            $entity->save();

            $rndReturned = (bool) random_int(0, 1);
            if ($rndReturned) {
                $borrowedBook = BookBorrow::inRandomOrder()->first();
                if ($borrowedBook==null) continue;
                echo "\n", $borrowedBook->id, "\n";
                if ($borrowedBook) {
                    $entityReturn = BookReturn::find($borrowedBook->id);
                    $isLate = (bool) random_int(0, 1);
                    if ($isLate) {
                        $returnDate = clone $due_back_date;
                        $returnDate->add(new \DateInterval("P" .
                            $faker->numberBetween(1, 7) . "D"));
                    } else {
                        $returnDate = clone $due_back_date;
                        $returnDate->sub(new \DateInterval("P" .
                            $faker->numberBetween(1, 4) . "D"));
                    }
                    $returnDate = $returnDate->format("Y-m-d");
                    $entityReturn->return_date = $returnDate;
                    $entityReturn->save();
                }

            }
        }

    }
}
