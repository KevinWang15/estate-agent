<?php namespace App\Http\Controllers;

use App\Utils\EduMailAddressParser;
use Illuminate\Routing;

class APIController extends Controller
{
    use API\UserAPI;
    use API\ContentAPI;
    use API\AgentAPI;
    use API\BuyerAPI;
    use API\SellerAPI;

    public function __construct()
    {
    }

    public function getIndex()
    {
        return "API";
    }

}
