<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Proposal
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $estate_id
 * @property integer $agent_id
 * @property integer $buyer_id
 * @property integer $order_id
 * @property integer $state
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Proposal whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Proposal whereEstateId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Proposal whereAgentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Proposal whereOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Proposal whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Proposal whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Proposal whereUpdatedAt($value)
 */
class Proposal extends Model
{
    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }
}
