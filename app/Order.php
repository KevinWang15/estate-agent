<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Order
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $state
 * @property integer $proposal_id
 * @property integer $estate_id
 * @property integer $buyer_id
 * @property integer $seller_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereProposalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereEstateId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereBuyerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereSellerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Order whereUpdatedAt($value)
 */
class Order extends Model
{
    //
}
