<?php

namespace Database\Factories;

use App\Models\BookTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $d = $this->faker->dateTimeBetween("-1 week", "-1 day");

        $loanDate = clone $d;
        $rndReturned = (bool) random_int(0, 1);
        $dueDate = clone $d->add(new \DateInterval("P14D"));

        $returnDate = null;
        if ($rndReturned == 1) {
            $isLate = (bool) random_int(0, 1);
            if ($isLate) {
                $returnDate = clone $dueDate;
                $returnDate->add(new \DateInterval("P" .
                    $this->faker->numberBetween(1, 4) . "D"));
            } else {
                $returnDate = clone $dueDate;
                $returnDate->sub(new \DateInterval("P" .
                    $this->faker->numberBetween(1, 4) . "D"));
            }
            $returnDate = $returnDate->format("Y-m-d");

        }
        return [
            'book_id' => $this->faker->numberBetween(1, 30),
            'member_id' => $this->faker->numberBetween(1, 30),
            'loan_date' => $loanDate->format("Y-m-d"),
            'due_back_date' => $dueDate->format("Y-m-d"),
            "return_date" => $returnDate,

        ];
    }
}
