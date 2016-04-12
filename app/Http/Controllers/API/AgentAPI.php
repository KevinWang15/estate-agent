<?php
/**
 * Created by IntelliJ IDEA.
 * User: Kevin
 * Date: 4/12/2016
 * Time: 11:00 PM
 */

namespace App\Http\Controllers\API;


use App\Estate;
use App\Providers\APIUtilProvider;
use App\Seller;
use App\User;
use App\Utils\APIResponseBuilder;
use Illuminate\Support\Facades\Input;

trait AgentAPI
{
    public function postGetVerifySellerList()
    {
        APIUtilProvider::validateParams([
            'page' => 'required|integer'
        ]);
        $pageSize = 6;
        $user = APIUtilProvider::getUser();
        if ($user->user_type != 2)
            APIResponseBuilder::err(-3, '');

        $query = User::leftJoin('sellers', 'sellers.user_id', '=', 'users.id')
            ->where(['user_type' => 1, 'verified' => 0]);
        $totalItems = $query->count();
        $query = $query->orderBy('id', 'desc')->skip((intval(Input::get('page', '1')) - 1) * $pageSize)->take($pageSize);
        $list = $query->get();

        APIResponseBuilder::respondWithObject(compact("list", "totalItems"));
    }

    public function postVerifySeller()
    {
        APIUtilProvider::validateParams([
            'id' => 'required|integer'
        ]);
        $user = APIUtilProvider::getUser();
        if ($user->user_type != 2)
            APIResponseBuilder::err(-3, '');
        /** @var Seller $seller */
        $seller = Seller::find(intval(Input::get('id')));
        if ($seller->verified)
            APIResponseBuilder::err(-4, "");

        $seller->verified = true;
        $seller->verified_by_agent_id = $user->id;
        $seller->save();

        APIResponseBuilder::respond();
    }

    public function postGetVerifyEstateList()
    {
        APIUtilProvider::validateParams([
            'page' => 'required|integer'
        ]);
        $pageSize = 6;
        $user = APIUtilProvider::getUser();
        if ($user->user_type != 2)
            APIResponseBuilder::err(-3, '');

        $query = Estate::with(['seller', 'seller.user'])->where(['verified' => 0, 'is_hidden' => 0]);
        $totalItems = $query->count();
        $query = $query->skip((intval(Input::get('page', '1')) - 1) * $pageSize)->take($pageSize);

        $list = $query->orderBy('id', 'desc')->get();

        APIResponseBuilder::respondWithObject(compact("list", "totalItems"));
    }

    public function postVerifyEstate()
    {
        APIUtilProvider::validateParams([
            'id' => 'required|integer'
        ]);
        $user = APIUtilProvider::getUser();
        if ($user->user_type != 2)
            APIResponseBuilder::err(-3, '');
        /** @var Estate $estate */
        $estate = Estate::find(intval(Input::get('id')));
        if ($estate->verified)
            APIResponseBuilder::err(-4, "");

        $estate->verified = true;
        $estate->verified_by_agent_id = $user->id;
        $estate->save();

        APIResponseBuilder::respond();
    }
}