<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Seller
 *
 * @mixin \Eloquent
 * @property integer $user_id
 * @property boolean $verified
 * @property integer $verified_by_agent_id
 * @property string $id_card_num
 * @method static \Illuminate\Database\Query\Builder|\App\Seller whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Seller whereVerified($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Seller whereVerifiedByAgentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Seller whereIdCardNum($value)
 */
class Seller extends Model
{
    protected $primaryKey = "user_id";
    public $timestamps = false;

    public $hidden = ['verified', 'verified_by_agent_id'];

    public function user()
    {
        return $this->belongsTo(\App\User::class, "user_id");
    }

    public function estates()
    {
        return $this->hasMany(Estate::class, "user_id", "user_id");
    }
}
