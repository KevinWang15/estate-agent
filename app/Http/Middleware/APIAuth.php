<?php namespace App\Http\Middleware;

use App\User;
use App\Utils\APIResponseBuilder;
use Closure;
use Illuminate\Support\Facades\Input;
use Request;

class APIAuth
{
    public static function generateToken(User $user)
    {
        if (empty($user->api_token)) {
            $user->api_token = str_random(64);
            $user->save();
        }
        return $user->api_token;
    }

    /**
     * @return \App\User
     */
    public static function validateToken()
    {
        if (strlen(Request::header('admin_token')) == 64) {
            $user = User::where('ADMIN_API_TOKEN', '=', Input::header('admin_token'))->first();
            if ($user != null) {
                return $user;
            }
        }

        $token = Request::header('token');
        if ($token == 'guest')
            return null;
        if (empty($token) || strlen($token) != 64) {
            APIResponseBuilder::err(-1, "请先登入");
            return null;
        }

        $user = User::where("API_TOKEN", "=", $token)->first();
        if ($user == null) {
            APIResponseBuilder::err(-2, "登入信息无效或已过期");
        }
        return $user;
    }

    public function handle($request, Closure $next)
    {
        $this::validateToken();
        return $next($request);
    }
}
