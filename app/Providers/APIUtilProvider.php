<?php

namespace App\Providers;

use App\Http\Middleware\APIAuth;
use App\Utils\APIResponseBuilder;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\ServiceProvider;

class APIUtilProvider extends ServiceProvider
{
    private static $_user = null;

    public function boot()
    {
    }

    public function register()
    {
    }

    /**
     * @return \App\User
     */
    public static function getUser()
    {
        if (self::$_user !== null)
            return self::$_user;
        $user = APIAuth::validateToken();
        self::$_user = $user;
        return $user;
    }

    public static function validateParams($rules)
    {
        $validator = \Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $errMsg = "";
            foreach ($validator->messages()->all() as $m)
                $errMsg .= $m . "\n";
            APIResponseBuilder::err(-3, $errMsg);
        }
    }

    public static function inputWithDefaults($defaults)
    {
        $input = Input::all();
        foreach ($defaults as $k => $v)
            if (!array_has($input, $k)) $input[$k] = $v;
        return $input;
    }
}
