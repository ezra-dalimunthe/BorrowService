<?php

namespace App\Models;

use App\ApiService\BookService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="BookBorrow",
 *   @OA\Property(property="book_id", type="integer"),
 *   @OA\Property(property="member_id", type="integer"),
 *   @OA\Property(property="loan_date", type="string"),
 *   @OA\Property(property="due_back_date", type="string"),
 * )
 */
class BookBorrow extends Model
{

    protected $table = 'book_transactions';
    protected $primaryKey = 'id';
    protected $fillable = ["book_id", "member_id", "loan_date", "due_back_date"];
    protected $hidden = ["return_date"];
    protected $appends = ["is_late"];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        "loan_date", "due_back_date",
    ];
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('exclude_return_date', function (Builder $builder) {
            $builder->whereNull('return_date');
        });

        static::creating(function (BookBorrow $model) {
            
            $status = BookService::bookInhand($model->book_id, "decrement");
            \Log::info(["service book"=> $status]);
            if ($status < 200 || $status >= 300) {
                //update failed, roll back.
            
                return false;
            }
        });
    }
    public static function getDefaultValidator()
    {
        return [
            "book_ids" => "required|array|min:1|max:3",
            "book_ids.*" => "required|integer|distinct|min:1",
            "member_id" => "required|integer",
        ];
    }
    public function getIsLateAttribute()
    {
        return $this->return_date == null &&
        $this->due_back_date->lessThanOrEqualTo(Carbon::now());
    }
}
