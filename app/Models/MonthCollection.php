<?php

namespace App\Models;

/*
 * @OA\Schema(
 *   schema="MonthCollection",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="label", type="string"),
 *   @OA\Property(property="name", type="string"),
 * )
 */

class MonthCollection
{

    public static function all()
    {

        $body = collect([
            [1, "01", "January"],
            [2, "02", "February"],
            [3, "03", "March"],
            [4, "04", "April"],
            [5, "05", "May"],
            [6, "06", "June"],
            [7, "07", "July"],
            [8, "08", "August"],
            [9, "09", "September"],
            [10, "10", "October"],
            [11, "11", "November"],
            [12, "12", "December"],
        ]);

        $rvalue = $body->map(function ($item, $key) {
            return ["id" => $item[0], "label" => $item[1], "name" => $item[2]];
        });
        return $rvalue;
    }
}
