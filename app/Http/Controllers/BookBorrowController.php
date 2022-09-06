<?php

namespace App\Http\Controllers;

use App\ApiService\BookService;
use App\ApiService\MemberService;
use App\Models\BookBorrow;
use App\Models\BookTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BookBorrowController extends Controller
{

    /**
     * @OA\Get(
     *   tags={"BookBorrow"},
     *   path="/api/v1/book-borrows",
     *   summary="BookBorrow index",
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
     *                        @OA\Schema(ref="#/components/schemas/BookBorrow"),
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
        $dataPage = BookBorrow::orderBy($sortBy, $sortDir)
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
     * @OA\Post(
     *   tags={"BookBorrow"},
     *   path="/api/v1/book-borrow",
     *   summary="BookBorrow store",
     *    @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *       allOf={
     *          @OA\Schema(ref="#/components/schemas/BookBorrow")
     *       },
     *      )
     *    ),
     *    @OA\Response(
     *      response=201,
     *      description="OK",
     *      @OA\JsonContent(
     *       allOf={
     *          @OA\Schema(ref="#/components/schemas/data_manipulation_response"),
     *       },
     *       @OA\Property(property="model", type="object",
     *          allOf={
     *            @OA\Schema(ref="#/components/schemas/AutoIncrement"),
     *             @OA\Schema(ref="#/components/schemas/BookBorrow"),
     *          }
     *       )
     *      )
     *    ),
     *    @OA\Response(response=403, description="Forbidden",
     *       @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
     *    )
     * )
     */
    public function store(Request $request)
    {
        $this->validate($request, BookBorrow::getDefaultValidator());
        $book_ids = $request->input("book_ids");
        $member_id = $request->input("member_id");

        $rvalues = [];
        foreach ($book_ids as $book_id) {
            $bookBorrow = BookTransaction::BorrowBook($book_id, $member_id);
            $rvalues[] = $bookBorrow;

        }

        return response()->json(["models" => $rvalues]);
    }

    /**
     * @OA\Get(
     *   tags={"BookBorrow"},
     *   path="/api/v1/book-borrow/{id}",
     *   summary="BookBorrow show",
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(type="integer")
     *    ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(ref="#/components/schemas/BookBorrow")
     *     ),
     *   ),
     *   @OA\Response(response=404, description="Not Found",
     *       @OA\JsonContent(ref="#/components/schemas/ResourceNotFoundResponse")
     *   ),
     *   @OA\Response(response=403, description="User'rights is insufficient",
     *       @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
     *    ),
     * )
     */
    public function show(Request $request, $id)
    {
        $model = BookBorrow::findOrFail($id);
        $bookModel = BookService::getBook($model->book_id);
        $memberModel = MemberService::getMember($model->member_id);

        $model->setHidden(["created_at", "deleted_at", "updated_at"]);
        $model->book = $bookModel;
        $model->member = $memberModel;
        return response()->json(["model" => $model]);
    }

    /**
     * @OA\Get(
     *   tags={"BookBorrow"},
     *   path="/api/v1/book-borrow/by-member/{member_id}",
     *   summary="Summary",
     *   @OA\Parameter(
     *      name="member_id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(type="string")
     *    ),
     *   @OA\Response(response=200, description="OK"),
     * )
     */
    public function byMember(Request $request, $member_id)
    {
        $dataPage = BookBorrow::where("member_id", $member_id)
            ->orderBy("loan_date", "asc")
            ->paginate(5);

        $models = $dataPage->getCollection();
        $book_ids = $models->pluck("book_id")->unique()->toArray();
        $bookModels = BookService::getBooks($book_ids);

        $results = $models->map(function ($item, $key) use ($bookModels) {
            $book = $bookModels->firstWhere("id", $item["book_id"]);
            Arr::forget($item, "created_at");
            Arr::forget($item, "updated_at");
            Arr::forget($item, "deleted_at");
            return Arr::add($item, "book", $book);
        });
        $dataPage->setCollection($results);
        return response()->json($dataPage);
    }

}
