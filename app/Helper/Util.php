<?php

namespace App\Helper;

use App;
use Faker\Generator;

/**
 * Created by IntelliJ IDEA.
 * User: Kevin
 * Date: 4/11/2016
 * Time: 5:35 PM
 */
class Util
{
    public static function randomArrayMember($array)
    {
        /** @var Generator $faker */
        $faker = App::make(Generator::class);
        if (count($array) == 0)
            return null;

        return $array[$faker->numberBetween(0, count($array) - 1)];
    }

}