<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Estate
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $user_id
 * @property string $city
 * @property string $district
 * @property string $zone
 * @property string $room
 * @property string $neighborhood
 * @property string $condition
 * @property string $description
 * @property boolean $verified
 * @property integer $verified_by_agent_id
 * @property float $price
 * @property boolean $is_for_rent
 * @property boolean $is_hidden
 * @method static \Illuminate\Database\Query\Builder|\App\Estate whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Estate whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Estate whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Estate whereDistrict($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Estate whereZone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Estate whereNeighborhood($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Estate whereCondition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Estate whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Estate whereVerified($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Estate whereVerifiedByAgentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Estate wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Estate whereIsForRent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Estate whereIsHidden($value)
 */
class Estate extends Model
{
    protected $primaryKey = "id";

    public $hidden = ['verified_by_agent_id'];

    public function seller()
    {
        return $this->belongsTo(Seller::class, "user_id", "user_id");
    }

    public function agents()
    {
        return $this->belongsToMany(Agent::class, 'agent_estate', "estate_id", "agent_id");
    }
}
