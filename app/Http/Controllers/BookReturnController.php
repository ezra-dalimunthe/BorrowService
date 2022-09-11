<?php

namespace App\Http\Controllers;

use App\ApiService\BookService;
use App\ApiService\MemberService;
use App\Models\BookReturn;
use App\Models\BookTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BookReturnController extends Controller
{

    /**
     * @OA\Get(
     *   tags={"BookReturn"},
     *   path="/api/v1/book-returns",
     *   summary="List Of returned book",
     *    @OA\Parameter( name="page", in="query", required=false,
     *        description="expected page number", @OA\Schema(type="integer")
     *    ),
     *    @OA\Parameter( name="per-page", in="query", required=false,
     *        description="number of items on page", @OA\Schema(type="integer")
     *    ),
     *    @OA\Parameter( name="search", in="query", required=false,
     *        description="search by keyword", @OA\Schema(type="string")
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="OK",
     *        @OA\JsonContent(
     *            allOf={ @OA\Schema(ref="#/components/schemas/data-pagination") },
     *            @OA\Property(
     *                property="models",
     *                type="array",
     *                @OA\Items(
     *                    allOf={
     *                        @OA\Schema(ref="#/components/schemas/AutoIncrement"),
     *                        @OA\Schema(ref="#/components/schemas/BookReturn"),
     *                    }
     *                ),
     *            )
     *        ),
     *    ),
     *    @OA\Response(response=403, description="Forbidden",
     *        @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
     *    ),
     *    @OA\Response(response=404, description="Not Found",
     *        @OA\JsonContent(ref="#/components/schemas/ResourceNotFoundResponse")
     *    )
     * )
     */
    public function index(Request $request)
    {
        $sortBy = $request->input("sort-by", "loan_date");
        $sortDir = $request->input("sort-dir", "asc");
        $perPage = $request->input("per-page", 20);
        \DB::connection()->enableQueryLog();
        $dataPage = BookReturn::orderBy($sortBy, $sortDir)
            ->paginate($perPage);
        $queries = \DB::getQueryLog();
        //return response()->json($queries);
        $models = $dataPage->getCollection();
        $member_ids = $models->pluck("member_id")->unique()->toArray();
        $memberModels = MemberService::getMembers($member_ids);

        $book_ids = $models->pluck("book_id")->unique()->toArray();
        $bookModels = BookService::getBooks($book_ids);

        $results = $models->map(function ($item, $key) use ($memberModels, $bookModels) {
            $member = $memberModels->firstWhere("id", $item["member_id"]);
            $book = $bookModels->firstWhere("id", $item["book_id"]);
            Arr::forget($item, "created_at");
            Arr::forget($item, "updated_at");
            Arr::forget($item, "deleted_at");
            Arr::add($item, "book", $book);
            return Arr::add($item, "member", $member);
        });

        $dataPage->setCollection($results);
        return response()->json($dataPage);
    }

    /**
     * @OA\Put(
     *   tags={"BookReturn"},
     *   path="/api/v1/book-return/{id}",
     *   summary="Set borrowed book returned.",
     *    @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(type="integer")
     *    ),
     *    @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *       allOf={
     *          @OA\Schema(ref="#/components/schemas/BookReturn")
     *       },
     *      )
     *    ),
     *    @OA\Response(
     *      response=200,
     *      description="OK",
     *      @OA\JsonContent(
     *       allOf={
     *          @OA\Schema(ref="#/components/schemas/data_manipulation_response"),
     *       },
     *       @OA\Property(property="model", type="object",
     *          allOf={
     *            @OA\Schema(ref="#/components/schemas/AutoIncrement"),
     *             @OA\Schema(ref="#/components/schemas/BookReturn"),
     *          }
     *       )
     *      )
     *    ),
     *    @OA\Response(response=403, description="Forbidden",
     *       @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
     *    )
     * )
     */
    public function update(Request $request, $id)
    {

        
        $this->validate($request, BookReturn::getDefaultValidator());
        $bookReturn = BookTransaction::ReturnBook($request->input("id"));
        
        return response()->json(["model" => $bookReturn]);
    }

}
