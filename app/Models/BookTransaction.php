<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * @OA\Schema(
 *   schema="BookBorrow",
 *   @OA\Property(property="book_id", type="integer"),
 *   @OA\Property(property="member_id", type="integer"),
 * )
 */

class BookTransaction extends Model
{
    use HasFactory;
    public static function BorrowBook($book_id, $member_id)
    {
        $entity = new BookBorrow;
        $entity->book_id = $book_id;
        $entity->member_id = $member_id;
        $entity->loan_date = date("Y-m-d");
        $entity->due_back_date = date("Y-m-d", strtotime('+14 days'));
        $entity->save();
        return $entity;
    }

    public static function showBorrowBook($id)
    {
        $entity = BookBorrow::find($id);
        if ($entity) {

        }
        return null;
    }
    public static function ReturnBook($id)
    {
       
        $entity = BookReturn::findOrFail($id);
       
        $entity->return_date = date("Y-m-d");
        $entity->save();
        return $entity;
    }

}
