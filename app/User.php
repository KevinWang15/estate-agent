<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\User
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property float $mobile
 * @property string $password
 * @property string $name
 * @property string $email
 * @property string $api_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $user_type
 * @method static \Illuminate\Database\Query\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereApiToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUserType($value)
 */
class User extends Model
{
    public $hidden = ['api_token', 'password', 'created_at', 'updated_at'];
}
