<?php
namespace App\ApiService;

use Illuminate\Support\Facades\Http;

class MemberService
{
    

    public static function getMember($id)
    {
        $baseUrl = env("MEMBER_SERVICE_URL");
        $response = Http::acceptJson()->withHeaders([
            'api-key' => '34xui34r54q0dfa8',
        ])->get($baseUrl . "entity/member/" . $id);
        return $response->json("member");
    }
    public static function getMembers(array $ids)
    {
        $baseUrl = env("MEMBER_SERVICE_URL");
        $response = Http::acceptJson()->withHeaders([
            'api-key' => '34xui34r54q0dfa8',
        ])->get($baseUrl . "entity/members", [
            "ids" => implode(",", $ids),
        ]);
        return $response->collect("members");
    }
}
