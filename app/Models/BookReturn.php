<?php

namespace App\Models;

use App\ApiService\BookService;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="BookReturn",
 *   @OA\Property(property="book_borrow_id", type="integer")
 * )
 *
 */
class BookReturn extends Model
{

    protected $fillable = ["id"];
    protected $table = 'book_transactions';
    protected $primaryKey = 'id';
    // protected $appends = ["is_late"];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        "loan_date", "due_back_date", "return_date",
    ];
    protected static function boot()
    {
        parent::boot();

        static::updating(function (BookReturn $model) {
            
            $status = BookService::bookInhand($model->book_id, "increment");
            if ($status < 200 || $status >= 300) {
                //update failed, roll back.
                $model->refresh();
            
                return false;
            }
        });
    }
    public static function getDefaultValidator()
    {
        return [
            "id" => "required|integer",
        ];
    }

}
