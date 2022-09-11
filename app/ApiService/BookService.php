<?php
namespace App\ApiService;

use Illuminate\Support\Facades\Http;

class BookService
{
    private static $baseUrl = "http://127.0.0.1:8201/api/v1/";

    public static function getBook($id)
    {

        $response = Http::acceptJson()->withHeaders([
            'api-key' => '34xui34r54q0dfa8',
        ])->get(self::$baseUrl . "entity/book/" . $id);
        return $response->json("book");
    }
    public static function getBooks(array $ids)
    {
        $response = Http::acceptJson()->withHeaders([
            'api-key' => '34xui34r54q0dfa8',
        ])->get(self::$baseUrl . "entity/books", [
            "ids" => implode(",", $ids),
        ]);

        return $response->collect("books");
    }
    public static function bookInhand($book_id, $operation)
    {
        $response = Http::acceptJson()->withHeaders([
            'api-key' => '34xui34r54q0dfa8',
        ])->put(self::$baseUrl . "inter-service/book-inhand/$book_id", [
            "book_id" => $book_id,
            "operation" => $operation,
        ]);
        \Log::info($response->status());
        return $response->status();
    }
}
