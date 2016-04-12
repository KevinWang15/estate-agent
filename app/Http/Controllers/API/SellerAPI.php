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
            APIResponseBuilder::err(-5, "");
        $estate->is_hidden = true;
        $estate->save();
        APIResponseBuilder::respond();

    }

    public function postNewEstate()
    {
        $user = APIUtilProvider::getUser();
        if ($user->user_type != 1)
            APIResponseBuilder::err(-3, '');
        $estate = new Estate();
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
        APIResponseBuilder::respond();
    }

}