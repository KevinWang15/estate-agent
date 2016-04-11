<?php


try {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, If-Modified-Since, Cache-Control, Pragma, token, Admin-Token, scope");
} catch (\Exception $ex) {
}


\App\Helpers\LogHelper::logSql();

Route::get('/', function () {
    return view('welcome');
});


Route::controllers([
    'api' => "APIController",
]);

