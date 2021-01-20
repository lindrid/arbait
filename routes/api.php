<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::middleware('check.privilege')->group(function ()
{
    /*** действия с заявками ***/
    Route::get('/application/index',                                'Application@index');
    Route::get('/application/index/{date}',                         ['uses' =>'Application@index']);
    Route::get('/application/index/{date}/{type}',                  ['uses' =>'Application@index']);
    Route::get('/application/index/{date}/{type}/{dispatcherId}',   ['uses' =>'Application@index']);
    Route::get('/application/show/{id}',                            ['uses' =>'Application@show']);
    Route::delete('/application/delete/{id}',                       ['uses' =>'Application@destroy']);
    Route::post('application/store',                                'Application@store');
    Route::get('/application/edit/{id}',                            ['uses' => 'Application@edit']);
    Route::post('/application/update/{id}',                         ['uses' =>'Application@update']);

    /*** действия внутри заявки с рабочими и ее статусом ***/
    Route::post('/application/assign/worker',       'Application@assignWorker');
    Route::post('/application/delete/worker/',      'Application@deleteWorker');
    Route::get('/application/end/{id}',             ['uses' =>'Application@end']);
    Route::post('/application/end',                 'Application@end');
    Route::post('/application/save',                'Application@saveData');
    Route::post('/application/ready/to/pay/{id}',   ['uses' =>'Application@readyToPay']);
    Route::post('/application/rollback/{id}',       ['uses' =>'Application@rollback']);
    Route::post('/application/worker/got/money',    'Application@workerGotMoney');
    Route::post('/application/payedbyclient',       'Application@payedByClient');
    Route::get('/worker/card/{phone}',              ['uses' =>'Worker@getDebitCard']);

    /*** отчет(бухгалтерия) по грузчикам ***/
    Route::post('/accountancy/store', 'CAccountancy@store');
    Route::get('/accountancy/payment/index/{date}', ['uses' =>'Accountancy\CPayment@index']);
    Route::get('/accountancy/index', 'CAccountancy@index');
    Route::get('/accountancy/index/{date}', ['uses' =>'CAccountancy@index']);
    Route::delete('/accountancy/app/delete/{id}', ['uses' =>'CAccountancy@destroy']);

    /*** отчет(бухгалтерия) по инстаграму ***/
    Route::get('/accountancy/instagram/stat/{date}', ['uses' =>'Accountancy\Instagram@seeStatistics']);
    Route::get('/accountancy/instagram/index', 'Accountancy\Instagram@index');
    Route::get('/accountancy/instagram/index/{date}', ['uses' =>'Accountancy\Instagram@index']);
    Route::get('/accountancy/instagram/get-publics', 'Accountancy\Instagram@getPublics');
    Route::post('/accountancy/instagram/store', 'Accountancy\Instagram@store');
    Route::delete('/accountancy/instagram/delete/last/{date}', ['uses' =>'Accountancy\Instagram@deleteLast']);
    Route::delete('/accountancy/instagram/pic/delete/{id}', ['uses' =>'Accountancy\Instagram@destroy']);
});

// далее какая-то хуета ненужная

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('upload-img', 'CUploadImage@do');
Route::post('save-progress', 'CRegistrationProgress@save');
Route::post('check-progress', 'CRegistrationProgress@check');

Route::resource('/worker_occs_skills', 'WorkerOccupationsAndSkillsController');
Route::resource('/confirm-worker-xp', 'WorkerOccupationsExperienceController');
//Route::resource('/accountancy', 'CAccountancy');
Route::resource('workers', 'WorkerListController');

Route::get('/rewrite-file', 'BlackList@rewriteFile');
Route::get('/verify_inn_pass', 'InnPassportVer@verify');

//});


//Route::delete('/application/{appId}/delete/worker/{workerId}', ['uses' =>'Application@deleteWorker']);


/*
Route::middleware('auth:api')->get('/confirm-worker', function (Request $request) {

    $occupies = array ('a','b','c');
    return response()->json($occupies);
});
*/
Route::post('auth/register', 'AuthController@register');

Route::get('userindex', 'AuthController@index');

Route::post('/send_ver_request', 'SmscVer@send');
Route::post('/phone_attack', 'SmscVer@attack');
Route::post('/verify_user', 'SmscVer@verify');

Route::post('auth/login', 'AuthController@login');

//Route::group(['middleware' => 'jwt.auth'], function(){
   // Route::post('auth/user', 'AuthController@user');
//});

Route::get('auth/user', function () {
    return redirect('/apps');
});

Route::group(['middleware' => 'jwt.refresh'], function(){
    Route::post('auth/refresh', 'AuthController@refresh');
});

Route::group(['prefix' => '/v1',
    'namespace' => 'Api\V1',
    'as' => 'api.'], function () {
    Route::resource('companies', 'ApplicationsController',
        ['except' => ['create', 'edit', 'indexUser']]);

});

Route::get('indexuser', 'Api\\V1\\ApplicationsController@indexUser');

//Важноe замечание: в этой статье я не буду реализовать аутентификацию, но в реальном проекте
// вы должны защитить роуты через посредника (middleware) или Laravel Passport.

header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');