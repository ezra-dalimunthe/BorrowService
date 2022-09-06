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
}
