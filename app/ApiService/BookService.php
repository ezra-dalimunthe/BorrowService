<?php
namespace App\ApiService;

use Illuminate\Support\Facades\Http;

class BookService
{

    public static function getBook($id)
    {
        $baseUrl = env("BOOK_SERVICE_URL");
        $response = Http::acceptJson()->withHeaders([
            'api-key' => '34xui34r54q0dfa8',
        ])->get($baseUrl . "/api/v1/entity/book/" . $id);
        return $response->json("book");
    }
    public static function getBooks(array $ids)
    {
        $baseUrl = env("BOOK_SERVICE_URL");
        $response = Http::acceptJson()->withHeaders([
            'api-key' => '34xui34r54q0dfa8',
        ])->get($baseUrl . "/api/v1/entity/books", [
            "ids" => implode(",", $ids),
        ]);

        return $response->collect("books");
    }
    public static function bookInhand($book_id, $operation)
    {
        $baseUrl = env("BOOK_SERVICE_URL") . "/api/v1/inter-service/book-inhand/$book_id";
        \Log::info($baseUrl);
        $response = Http::acceptJson()->withHeaders([
            'api-key' => '34xui34r54q0dfa8',
        ])->put($baseUrl, [
            "book_id" => $book_id,
            "operation" => $operation,
        ]);

        return $response->status();
    }
}
