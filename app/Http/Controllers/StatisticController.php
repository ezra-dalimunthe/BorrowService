<?php

namespace App\Http\Controllers;

use App\Models\BookTransaction;
use App\Models\MonthCollection;
use Illuminate\Http\Request;

class StatisticController extends Controller
{

    /**
     * @OA\Get(
     *   tags={"Statistic"},
     *   path="/api/v1/statistic/book-loan-transaction-by-year/{year}",
     *   summary="Summary",
     *   @OA\Parameter(
     *      name="year",
     *      in="path",
     *      required=false,
     *      allowEmptyValue=true,
     *      @OA\Schema(type="integer")
     *    ),
     *   @OA\Response(response=200, description="OK"),
     * )
     */
    public function bookLoanTransactionByYear(Request $request, $year = null)
    {
        $months = MonthCollection::all();

        //dd($months->where("label",'01')->first());
        if ($year == null || !is_numeric($year)) {
            $year = date("Y");
        }
        $rvalue = BookTransaction::selectRaw("EXTRACT( YEAR_MONTH FROM loan_date) ym ,"
            . " count(id) as total")
            ->where(\DB::raw("Year(loan_date)"), $year)
            ->groupBy("ym")
            ->get();

        $data = $months->map(function ($item, $key) use ($rvalue, $year) {
            $d = $rvalue->where("ym", $year . $item["label"])->first();
            if ($d) {
                return $d->total;
            }
            return null;

        });
        $labels = $months->pluck("name");
        return response()->json(["labels" => $labels, "datasets" => [
            ["data" => $data, "label" => $year],
        ]]);

    }
    /**
     * @OA\Get(
     *   tags={"Statistic"},
     *   path="/api/v1/statistic/book-return-transaction-by-year/{year}",
     *   summary="Summary",
     *   @OA\Parameter(
     *      name="year",
     *      in="path",
     *      required=false,
     *      allowEmptyValue=true,
     *      @OA\Schema(type="string")
     *    ),
     *   @OA\Response(response=200, description="OK"),
     * )
     */
    public function bookReturnTransactionByYear(Request $request, $year = null)
    {
        $months = MonthCollection::all();

        //dd($months->where("label",'01')->first());
        if ($year == null || !is_numeric($year)) {
            $year = date("Y");
        }
        $rvalue = BookTransaction::selectRaw("EXTRACT( YEAR_MONTH FROM loan_date) ym ,"
            . " count(id) as total")
            ->where(\DB::raw("Year(loan_date)"), $year)
            ->whereNotNull("return_date")
            ->groupBy("ym")
            ->get();

        $data = $months->map(function ($item, $key) use ($rvalue, $year) {
            $d = $rvalue->where("ym", $year . $item["label"])->first();
            if ($d) {
                return $d->total;
            }
            return null;

        });
        $labels = $months->pluck("name");
        return response()->json(["labels" => $labels, "datasets" => [
            ["data" => $data, "label" => $year],
        ]]);

    }

}
