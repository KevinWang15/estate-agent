<?php
/**
 * Created by IntelliJ IDEA.
 * User: Kevin
 * Date: 4/13/2016
 * Time: 12:18 AM
 */

namespace App\Http\Controllers\API;


use App\Estate;
use App\Providers\APIUtilProvider;
use App\Utils\APIResponseBuilder;
use Illuminate\Support\Facades\Input;

trait SellerAPI
{

    public function postGetMyEstatesList()
    {
        APIUtilProvider::validateParams([
            'page' => 'required|integer'
        ]);
        $pageSize = 6;
        $user = APIUtilProvider::getUser();
        if ($user->user_type != 1)
            APIResponseBuilder::err(-3, '');

        $query = Estate::with(['seller', 'seller.user'])->where(['user_id' => $user->id, 'is_hidden' => false]);
        $totalItems = $query->count();
        $query = $query->skip((intval(Input::get('page', '1')) - 1) * $pageSize)->take($pageSize);
        $list = $query->orderBy('id', 'desc')->get();

        APIResponseBuilder::respondWithObject(compact("list", "totalItems"));
    }

    public function postHideMyEstate()
    {
        APIUtilProvider::validateParams([
            'id' => 'required|integer'
        ]);
        $user = APIUtilProvider::getUser();
        $estate = Estate::find(intval(Input::get('id')));
        if (!$estate)
            APIResponseBuilder::err(-5, "房产不存在");
        if ($estate->user_id != $user->id)
            APIResponseBuilder::err(-6, "不是你的房产");
        $estate->is_hidden = true;
        $estate->save();
        APIResponseBuilder::respond();

    }

    public function postEditEstate()
    {

        $user = APIUtilProvider::getUser();
        if ($user->user_type != 1)
            APIResponseBuilder::err(-3, '');
        $id = intval(@Input::get("id", 0));
        if ($id == 0) {
            $estate = new Estate();
        } else {
            $estate = Estate::find($id);
            if (!$estate)
                APIResponseBuilder::err(-5, "房产不存在");
            if ($estate->user_id != $user->id)
                APIResponseBuilder::err(-6, "不是你的房产");
        }
        $estate->user_id = $user->id;
        $estate->city = @Input::get("city", "");
        $estate->district = @Input::get("district", "");
        $estate->zone = @Input::get("zone", "");
        $estate->neighborhood = @Input::get("neighborhood", "");
        $estate->room = @Input::get("room", "");
        $estate->condition = @Input::get("condition", "");
        $estate->description = @Input::get("description", "");
        $estate->price = @Input::get("price", "");
        $estate->is_for_rent = @Input::get("is_for_rent", "");
        $estate->is_hidden = 0;
        $estate->save();

        if ($id == 0) {
            //随机为新添加的房产设置中介
            $agents_array = $agents = \App\User::where('user_type', 2)->get()->toArray();

            shuffle($agents_array);
            $builder = [];
            for ($i = 0; $i < 5; $i++) {
                $builder[] = "({$agents_array[$i]["id"]},$estate->id)";
            }
            \DB::insert("insert into agent_estate (agent_id,estate_id) values " . implode(',', $builder));
        }

        APIResponseBuilder::respond();
    }

}