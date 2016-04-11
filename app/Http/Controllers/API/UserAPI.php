<?php namespace App\Http\Controllers\API;

use App\Http\Middleware\APIAuth;
use App\User;
use App\Utils\APIResponseBuilder;
use Illuminate\Support\Facades\Redis;
use App\Providers\APIUtilProvider;
use Illuminate\Support\Facades\Input;

trait UserAPI
{

    public function postRegisterSubmit()
    {
        $input = Input::all();

        APIUtilProvider::validateParams([
            'mobile' => 'required|size:11',
            'password' => 'required|min:8',
            'name' => 'required',
            'email' => 'required'
        ]);

        $mobile = $input['mobile'];

        if (User::whereMobile($mobile)->exists())
            APIResponseBuilder::err(-3, "该手机号已经被注册");

        $user = new User();
        $user->mobile = $mobile;
        $user->name = $input["name"];
        $user->email = $input['email'];
        $user->password = bcrypt($input["password"]);
        $user->save();

        APIResponseBuilder::append("API_TOKEN", APIAuth::generateToken($user));
        APIResponseBuilder::respond();
    }

    public function postWhoAmI()
    {
        /** @var \App\User $user */
        $user = APIUtilProvider::getUser();
        if (!$user) {
            APIResponseBuilder::err(-2, "");
        }
        unset($user->API_TOKEN);
        APIResponseBuilder::respondWithObject($user);
    }

    public function postLogin()
    {
        $input = APIUtilProvider::inputWithDefaults([]);
        APIUtilProvider::validateParams([
            'mobile' => 'required',
            'password' => 'required'
        ]);

        $user = User::whereMobile($input["mobile"])->first();

        if ($user == null)
            APIResponseBuilder::err(-3, "用户不存在");

        if (!\Hash::check($input["password"], $user->password))
            APIResponseBuilder::err(-4, "密码错误");

        APIResponseBuilder::append("API_TOKEN", APIAuth::generateToken($user));

        $user->password = bcrypt($input["password"]);
        $user->save();
        APIResponseBuilder::respond();
    }

    public function postEditProfile()
    {
        $user = APIUtilProvider::getUser();
        $input = APIUtilProvider::inputWithDefaults([]);
        $allowed = ['name', 'email'];

        if (isset($input["email"])) {
            $validator = \Validator::make($input, ["email" => "email"]);
            if ($validator->fails()) {
                APIResponseBuilder::err(-4, 'Email无效！');
            }
        }

        foreach ($allowed as $a) {
            if (isset($input[$a]))
                $user[$a] = $input[$a];
        }

        unset ($user->id);
        $user->save();
        APIResponseBuilder::respond();
    }
}
