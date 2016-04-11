<?php namespace App\Http\Controllers\API;

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
use Illuminate\Support\Facades\Input;

trait ContentAPI
{
    public function postGetRandomRecommendations()
    {
        $estates = Estate::orderBy(\DB::raw("RAND()"))->limit(9)->get();
        APIResponseBuilder::respondWithObject(["list" => $estates]);
    }
}
