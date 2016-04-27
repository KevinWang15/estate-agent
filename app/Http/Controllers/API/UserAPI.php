<?php namespace App\Http\Controllers\API;

use App\Agent;
use App\Buyer;
use App\Http\Middleware\APIAuth;
use App\Seller;
use App\User;
use App\Utils\APIResponseBuilder;
use Illuminate\Support\Facades\Redis;
use App\Providers\APIUtilProvider;
use Illuminate\Support\Facades\Input;

trait UserAPI
{
    public function postRegisterSubmit(\Faker\Generator $faker)
    {
        $input = Input::all();

        APIUtilProvider::validateParams([
            'mobile' => 'required|size:11',
            'password' => 'required|min:8',
            'name' => 'required',
            'user_type' => 'required|integer',
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
        $user->user_type = intval($input["user_type"]);
        $user->save();

        if ($user->user_type == 1) {
            \DB::insert(
                "INSERT into sellers (user_id,verified,verified_by_agent_id,id_card_num) VALUES (?,0,null,?)",
                [$user->id, strval($faker->randomNumber(8)) . strval($faker->randomNumber(8))]
            );
        } else if ($user->user_type == 0) {
            \DB::insert(
                "INSERT into buyers (user_id) VALUES (?)",
                [$user->id]
            );
        }

        APIResponseBuilder::append("API_TOKEN", APIAuth::generateToken($user));
        APIResponseBuilder::respond();
    }

    public function postWhoAmI()
    {
        /** @var \App\User $user */
        $user = APIUtilProvider::getUser();
        switch ($user->user_type) {
            case 0:
                $user->buyer = Buyer::find($user->id);
                break;
            case 1:
                $user->seller = Seller::find($user->id);
                break;
            case 2:
                $user->agent = Agent::find($user->id);
                break;
        }
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

}
