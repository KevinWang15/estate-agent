<?php
namespace App\Http\Controllers\API;

use App\Estate;
use App\Order;
use App\Proposal;
use App\Providers\APIUtilProvider;
use App\Seller;
use App\User;
use App\Utils\APIResponseBuilder;
use Illuminate\Support\Facades\Input;

trait BuyerAPI
{
    public function postMakeProposal()
    {
        APIUtilProvider::validateParams(
            [
                "estate_id" => "integer|required",
                "agent_id" => "integer|required",
            ]
        );
        $user = APIUtilProvider::getUser();
        if ($user->user_type != 0)
            APIResponseBuilder::err(-3, '只有普通用户才能发起预约');

        $estate_id = intval(Input::get('estate_id', 0));
        $agent_id = intval(Input::get('agent_id', 0));
        if (Proposal::where(['buyer_id' => $user->id, 'estate_id' => $estate_id])->exists()) {
            APIResponseBuilder::err(-4, '您已经委托过中介看此房，<br/>请先取消之前的委托。');
        }

        $proposal = new Proposal();
        $proposal->buyer_id = $user->id;
        $proposal->estate_id = $estate_id;
        $proposal->agent_id = $agent_id;
        $proposal->state = 0;

        $proposal->save();

        APIResponseBuilder::respond();
    }

    public function postBuyerSetOrderState()
    {
        APIUtilProvider::validateParams([
            'id' => 'required|integer',
            'state' => 'required|integer'
        ]);

        $user = APIUtilProvider::getUser();

        /** @var Order $order */
        $order = Order::find(@intval(@Input::get("id", 0)));
        if ($order == null) APIResponseBuilder::err(-3, "找不到交易单");
        if (intval($order->buyer_id) != intval($user->id)) APIResponseBuilder::err(-4, "不是你的交易单");
        $state = intval(Input::get("state", 0));

        if ($state != 3)
            APIResponseBuilder::err(-4, "状态无效");

        $order->state = $state;
        $order->save();
        APIResponseBuilder::respond();
    }

    public function postBuyerProposalsList()
    {
        APIUtilProvider::validateParams([
            'page' => 'required|integer'
        ]);
        $pageSize = 6;

        $user = APIUtilProvider::getUser();

        if ($user->user_type != 0)
            APIResponseBuilder::err(-3, '');

        $query = Proposal::with(['agent', 'agent.user', 'estate', 'estate.seller', 'estate.seller.user'])->where(['buyer_id' => $user->id]);

        $totalItems = $query->count();
        $query = $query->skip((intval(Input::get('page', '1')) - 1) * $pageSize)->take($pageSize);
        $list = $query->orderBy('id', 'desc')->get();

        APIResponseBuilder::respondWithObject(compact("list", "totalItems"));
    }

    public function postBuyerOrdersList()
    {
        APIUtilProvider::validateParams([
            'page' => 'required|integer'
        ]);
        $pageSize = 6;

        $user = APIUtilProvider::getUser();

        if ($user->user_type != 0)
            APIResponseBuilder::err(-3, '');

        $query = Order::with(['estate', 'seller', 'seller.user'])->where(['buyer_id' => $user->id]);

        $totalItems = $query->count();
        $query = $query->skip((intval(Input::get('page', '1')) - 1) * $pageSize)->take($pageSize);
        $list = $query->orderBy('id', 'desc')->get();

        APIResponseBuilder::respondWithObject(compact("list", "totalItems"));
    }

    public function postBuyerCancelProposal()
    {
        APIUtilProvider::validateParams([
            'id' => 'required|integer'
        ]);

        $user = APIUtilProvider::getUser();

        /** @var Proposal $proposal */
        $proposal = Proposal::find(@intval(@Input::get("id", 0)));
        if ($proposal == null) APIResponseBuilder::err(-3, "找不到预约");
        if (intval($proposal->buyer_id) != intval($user->id)) APIResponseBuilder::err(-4, "不是你的预约");
        if ($proposal->state != 0) APIResponseBuilder::err(-4, "预约状态无效");
        $proposal->state = -9;
        $proposal->save();
        APIResponseBuilder::respond();
    }
}