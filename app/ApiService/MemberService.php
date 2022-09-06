<?php
namespace App\ApiService;

use Illuminate\Support\Facades\Http;

class MemberService
{
    private static $baseUrl = "http://127.0.0.1:8202/api/v1/";

    public static function getMember($id)
    {

        $response = Http::acceptJson()->withHeaders([
            'api-key' => '34xui34r54q0dfa8',
        ])->get(self::$baseUrl . "entity/member/" . $id);
        return $response->json("member");
    }
    public static function getMembers(array $ids)
    {
        $response = Http::acceptJson()->withHeaders([
            'api-key' => '34xui34r54q0dfa8',
        ])->get(self::$baseUrl . "entity/members", [
            "ids" => implode(",", $ids),
        ]);
        return $response->collect("members");
    }
}
