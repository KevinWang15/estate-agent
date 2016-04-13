<?php
/**
 * Created by IntelliJ IDEA.
 * User: Kevin
 * Date: 4/12/2016
 * Time: 11:00 PM
 */

namespace App\Http\Controllers\API;


use App\Estate;
use App\Order;
use App\Proposal;
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

    public function postAgentProposalsList()
    {
        APIUtilProvider::validateParams([
            'page' => 'required|integer'
        ]);
        $pageSize = 6;

        $user = APIUtilProvider::getUser();

        if ($user->user_type != 2)
            APIResponseBuilder::err(-3, '');

        $query = Proposal::with(['buyer', 'buyer.user', 'estate', 'estate.seller', 'estate.seller.user'])->where(['agent_id' => $user->id]);

        $totalItems = $query->count();
        $query = $query->skip((intval(Input::get('page', '1')) - 1) * $pageSize)->take($pageSize);
        $list = $query->orderBy('id', 'desc')->get();

        APIResponseBuilder::respondWithObject(compact("list", "totalItems"));
    }

    public function postAgentOrdersList()
    {
        APIUtilProvider::validateParams([
            'page' => 'required|integer'
        ]);
        $pageSize = 6;

        $user = APIUtilProvider::getUser();

        if ($user->user_type != 2)
            APIResponseBuilder::err(-3, '');

        $query = Order::leftJoin('proposals', 'proposals.order_id', '=', 'orders.id')->with(['estate', 'seller', 'seller.user', 'buyer', 'buyer.user'])->where(['proposals.agent_id' => $user->id]);

        $totalItems = $query->count();
        $query = $query->skip((intval(Input::get('page', '1')) - 1) * $pageSize)->take($pageSize);
        $list = $query->orderBy('orders.id', 'desc')->get(['*','orders.state as order_state']);

        APIResponseBuilder::respondWithObject(compact("list", "totalItems"));
    }


    private function acceptOrRejectProposal($is_accepted)
    {
        APIUtilProvider::validateParams([
            'id' => 'required|integer'
        ]);

        $user = APIUtilProvider::getUser();

        /** @var Proposal $proposal */
        $proposal = Proposal::find(@intval(@Input::get("id", 0)));
        if ($proposal == null) APIResponseBuilder::err(-3, "找不到预约");
        if (intval($proposal->agent_id) != intval($user->id)) APIResponseBuilder::err(-4, "不是你的预约");
        if ($proposal->state != 0)
            APIResponseBuilder::err(-4, "预约状态无效");

        $proposal->state = $is_accepted ? 1 : -1;
        $proposal->save();
        APIResponseBuilder::respond();
    }

    public function postAgentAcceptProposal()
    {
        self::acceptOrRejectProposal(1);
    }

    public function postAgentRejectProposal()
    {
        self::acceptOrRejectProposal(0);
    }


    public function postAgentSetProposalState()
    {
        APIUtilProvider::validateParams([
            'id' => 'required|integer',
            'state' => 'required|integer'
        ]);

        $user = APIUtilProvider::getUser();

        /** @var Proposal $proposal */
        $proposal = Proposal::find(@intval(@Input::get("id", 0)));
        if ($proposal == null) APIResponseBuilder::err(-3, "找不到预约");
        if (intval($proposal->agent_id) != intval($user->id)) APIResponseBuilder::err(-4, "不是你的预约");
        $state = intval(Input::get("state", 0));

        if ($state >= 0 && $state < 2)
            APIResponseBuilder::err(-4, "预约状态无效");

        $proposal->state = $state;
        $proposal->save();

        if ($proposal->state == 4) {
            $estate = $proposal->estate;
            $order = new \App\Order;
            $order->proposal_id = $proposal->id;
            $order->state = 0;
            $order->estate_id = $proposal->estate_id;
            $order->buyer_id = $proposal->buyer_id;
            $order->seller_id = $estate->seller->user_id;
            $order->save();
            $estate->is_hidden = true;
            $estate->save();
            $proposal->order_id = $order->id;
            $proposal->save();
        }

        APIResponseBuilder::respond();
    }


}