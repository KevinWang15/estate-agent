<?php namespace App\Http\Controllers\API;

use App\Agent;
use App\Company;
use App\CompanyType;
use App\DataSource;
use App\EmailSubscription;
use App\Estate;
use App\Form;
use App\FormPreview;
use App\Providers\APIUtilProvider;
use App\Providers\QiniuProvider;
use App\Providers\TestResultProvider;
use App\Submission;
use App\TestResult;
use App\Utils\APIResponseBuilder;
use App\Utils\SendCloudEmail;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Input;

trait ContentAPI
{
    public function postGetRandomRecommendations()
    {
        $estates = Estate::orderBy(\DB::raw("RAND()"))->where(['is_hidden' => 0, 'verified' => 1])->limit(9)->get();
        APIResponseBuilder::respondWithObject(["list" => $estates]);
    }

    public function postGetEstateDetail()
    {
        APIUtilProvider::validateParams(["id" => "required|integer"]);
        $id = intval(Input::get("id"));
        $estate = Estate::where(['is_hidden' => 0, 'verified' => 1, "id" => $id])->first();
        if ($estate == null)
            APIResponseBuilder::err(-4, '');

        $agents = Agent::whereIn("user_id",
            array_pluck(\DB::select("select agent_id from agent_estate where estate_id=?", [$estate->id]),
                'agent_id'
            ))->with("user")->get();

        $seller = $estate->seller()->with('user')->first();
        APIResponseBuilder::respondWithObject(compact("estate", "agents", "seller"));
    }
}
