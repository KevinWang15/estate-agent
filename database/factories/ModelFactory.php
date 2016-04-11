<?php

use App\Helpers\Util;

class fakerData
{
    public static $faker_cities = ["上海", "北京", "广州"];
    public static $faker_districts = ["上海" => ["闵行区", "宝山区", "浦东新区"], "北京" => ["东城区", "西城区", "海淀区"], "广州" => ["海珠区", "天河区"]];
    public static $faker_conditions = ["全新房", "毛坯房", "2年以下房龄", "5年以下房龄", "5-10年房龄", "10年以上房龄"];
    public static $faker_sellers_list;
    public static $faker_agents_list;
}

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'mobile' => strval($faker->numberBetween(100, 139)) . strval($faker->numberBetween(10000000, 99999999)),
        'password' => bcrypt('123'),
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'api_token' => str_random(32),
        'user_type' => random_int(0, 2)
    ];
});

$factory->define(App\Estate::class, function (Faker\Generator $faker) {

    fakerData::$faker_sellers_list = \App\Seller::where('verified', 1)->get();
    fakerData::$faker_agents_list = \App\Agent::all();

    $city = Util::randomArrayMember(fakerData::$faker_cities);
    $district = Util::randomArrayMember(fakerData::$faker_districts[$city]);
    $zone = "Zone " . $faker->numberBetween(1, 3);
    $neighbourhood = "Neighborhood " . $faker->numberBetween(1, 3);
    $is_verified = 1 - intval($faker->numberBetween(0, 7) / 5);
    $is_for_rent = $faker->numberBetween(0, 1);
    return [
        'user_id' => Util::randomArrayMember(fakerData::$faker_sellers_list)->user_id,
        'city' => $city,
        'district' => $district,
        'zone' => $zone,
        'neighborhood' => $neighbourhood,
        'room' => "#" . $faker->numberBetween(1, 8) . "0" . $faker->numberBetween(0, 9),
        'condition' => Util::randomArrayMember(fakerData::$faker_conditions),
        'description' => $faker->paragraph,
        'verified' => $is_verified,
        'verified_by_agent_id' => $is_verified ? App\Helpers\Util::randomArrayMember(fakerData::$faker_agents_list)->user_id : null,
        'is_for_rent' => $is_for_rent,
        'price' => ($is_for_rent ? 1 : 200) * $faker->numberBetween(1000, 8000),
        'is_hidden' => 0,
    ];
});
