<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Agent
 *
 * @mixin \Eloquent
 * @property integer $user_id
 * @property float $fee
 * @property string $title
 * @property string $description
 * @method static \Illuminate\Database\Query\Builder|\App\Agent whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Agent whereFee($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Agent whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Agent whereDescription($value)
 */
class Agent extends Model
{
    protected $primaryKey = "user_id";
    public $timestamps = false;
}
