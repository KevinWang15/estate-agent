<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Buyer
 *
 * @mixin \Eloquent
 * @property integer $user_id
 * @method static \Illuminate\Database\Query\Builder|\App\Buyer whereUserId($value)
 */
class Buyer extends Model
{
    protected $primaryKey = "user_id";
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(\App\User::class,"user_id");
    }
}
