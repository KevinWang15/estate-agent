<?php namespace App\Http\Controllers;

use App\Utils\EduMailAddressParser;
use Illuminate\Routing;

class APIController extends Controller
{
    use API\UserAPI;
    use API\ContentAPI;

    public function __construct()
    {
    }

    /**
     * @return \App\User
     */

    public function getIndex()
    {
        return "API";
    }

}
