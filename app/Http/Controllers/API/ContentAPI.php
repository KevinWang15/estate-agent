<?php namespace App\Http\Controllers\API;

use App\Agent;
use App\Estate;
use App\Proposal;
use App\Providers\APIUtilProvider;
use App\Seller;
use App\Utils\APIResponseBuilder;
use Illuminate\Support\Facades\Input;
use stdClass;

trait ContentAPI
{
    public function postGetRandomRecommendations()
    {
        $estates = Estate::orderBy(\DB::raw("RAND()"))->where(['is_hidden' => 0, 'verified' => 1])->limit(8)->get();
        APIResponseBuilder::respondWithObject(["list" => $estates]);
    }

    public function postGetEstateDetail()
    {
        APIUtilProvider::validateParams(["id" => "required|integer"]);
        $id = intval(Input::get("id"));
        $estate = Estate::where(['verified' => 1, "id" => $id])->first();
        if ($estate == null)
            APIResponseBuilder::err(-4, '');

        if ($estate->is_hidden)
            APIResponseBuilder::err(-5, '现在不能查看本房产的信息，很可能已经被人买走了哦');

//        $agents = Agent::whereIn("user_id",
//            array_pluck(\DB::select("select agent_id from agent_estate where estate_id=?", [$estate->id]),
//                'agent_id'
//            ))->with("user")->get();

        $agents = $estate->agents()->with("user")->get();
        $seller = $estate->seller()->with('user')->first();
        APIResponseBuilder::respondWithObject(compact("estate", "agents", "seller"));
    }

    public function postGetSellerDetail()
    {
        APIUtilProvider::validateParams(["id" => "required|integer"]);
        $id = intval(Input::get("id"));
        $seller = Seller::with('user')->where(['verified' => 1, "user_id" => $id])->first();
        if ($seller == null)
            APIResponseBuilder::err(-4, '');
        $estates = $seller->estates()->where(['is_hidden' => 0, 'verified' => 1])->get();

        APIResponseBuilder::respondWithObject(compact("estates", "seller"));
    }

    public function postGetAgentDetail()
    {
        APIUtilProvider::validateParams(["id" => "required|integer"]);
        $id = intval(Input::get("id"));
        $agent = Agent::with('user')->where(["user_id" => $id])->first();
        if ($agent == null)
            APIResponseBuilder::err(-4, '');
        $estates = $agent->estates()->where(['is_hidden' => 0, 'verified' => 1])->get();

        APIResponseBuilder::respondWithObject(compact("estates", "agent"));
    }

    public function postEstateList()
    {
        APIUtilProvider::validateParams([
            'page' => 'required|integer'
        ]);

        $pageSize = 6;
        $query = Estate::where(['is_hidden' => 0, 'verified' => 1]);

        $filters = Input::get("filters", "{}");

        if (!empty(@$filters['keyword'])) {
            $keyword = '%' . @$filters['keyword'] . '%';
            $query = $query->whereRaw("CONCAT(city,district,zone,neighborhood,room,`condition`,description) like ?", [$keyword]);
        }

        if (!empty(@$filters['priceLow'])) {
            $priceLow = round(@floatval($filters['priceLow']), 2);
            $query = $query->where("price", ">=", $priceLow);
        }

        if (!empty(@$filters['priceHigh'])) {
            $priceHigh = round(@floatval($filters['priceHigh']), 2);
            $query = $query->where("price", "<=", $priceHigh);
        }

        if (!empty(@$filters['type'])) {
            $type = @intval($filters['type']);
            if ($type == 1)
                $query = $query->where("is_for_rent", "=", 1);
            else if ($type == 2)
                $query = $query->where("is_for_rent", "=", 0);
        }

        if (!empty(@$filters['sortByPrice'])) {
            $sortByPrice = @intval($filters['sortByPrice']);
            if ($sortByPrice == 1)
                $query = $query->orderBy("price", "asc");
            else if ($sortByPrice == 2)
                $query = $query->orderBy("price", "desc");
        }

        if (!empty(@$filters['city'])) {
            $query = $query->where("city", @$filters['city']);
        }
        if (!empty(@$filters['district'])) {
            $query = $query->where("district", @$filters['district']);
        }
        if (!empty(@$filters['zone'])) {
            $query = $query->where("zone", @$filters['zone']);
        }
        if (!empty(@$filters['neighborhood'])) {
            $query = $query->where("neighborhood", @$filters['neighborhood']);
        }
        if (!empty(@$filters['condition'])) {
            $query = $query->where("condition", @$filters['condition']);
        }
        $totalItems = $query->count();
        $gets = ['*'];

        $builder = $query->skip((intval(Input::get('page', '1')) - 1) * $pageSize)->take($pageSize);
        $list = $builder->get($gets);

        APIResponseBuilder::respondWithObject(['totalItems' => $totalItems, 'list' => $list]);
    }

    public function postMyProposals()
    {
        $user = APIUtilProvider::getUser();

        $list = Proposal::leftJoin("estates", 'estates.id', '=', 'proposals.estate_id');
        if ($user->user_type == 0) {
            //购买者
            $list = $list->where('proposals.buyer_id', $user->id);
        } else if ($user->user_type == 1) {
            //提供者
            $list = $list->where('estates.user_id', $user->id);
        } else if ($user->user_type == 2) {
            //中介
            $list = $list->where('proposals.agent_id', $user->id);
        }

        APIResponseBuilder::respondWithObject([]);
    }

    public function postMyOrders()
    {
        APIResponseBuilder::respondWithObject([]);
    }
}
