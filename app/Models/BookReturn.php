<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="BookReturn",
 *   @OA\Property(property="book_borrow_id", type="integer"),
 *   @OA\Property(property="return_date", type="string"),
 * )
 *
 */
class BookReturn extends Model
{

    protected $fillable = ["book_borrow_id", "return_date"];
    protected $table = 'book_transactions';
    protected $primaryKey = 'id';
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

        static::addGlobalScope('include_return_date', function (Builder $builder) {
            $builder->whereNotNull('return_date');
        });
    }
    public static function getDefaultValidator()
    {
        return [
            "book_borrow_id" => "required|integer",
            "return_date" => "required|date",

        ];
    }
    public function getIsLateAttribute()
    {
        return $this->return_date != null &&
        $this->due_back_date->lessThanOrEqualTo($this->return_date);
    }
}
