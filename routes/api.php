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

Route::group(['middleware' => 'api'], function () {
    /** รายการสินค้าและบริการ */
    Route::get('items', 'ItemController@getAll');
    Route::post('items', 'ItemController@store');

    /** โครงการ */
    Route::get('projects', 'ProjectController@getAll');
    Route::get('projects/{id}', 'ProjectController@getById');

    /** ควบคุมกำกับติดตาม */
    Route::get('monthly', 'MonthlyController@getAll');
    Route::get('monthly/{id}', 'MonthlyController@getById');
    Route::get('monthly/{year}/summary', 'MonthlyController@getSummary');

    Route::get('utilities/{year}/summary', 'UtilityController@getSummary');

    /** หมวดค่าใช้จ่าย */
    Route::get('plan-summary', 'PlanSummaryController@getAll');
    Route::get('plan-summary/{id}', 'PlanSummaryController@getById');
    Route::get('plan-summary/{year}/{expense}', 'PlanSummaryController@getByExpense');

    /** การส่งเบิกเงิน */
    Route::put('withdrawals/{id}', 'WithdrawalController@withdraw');
    
    /** เลขที่เอกสาร */
    Route::get('runnings/{docType}/doc-type', 'RunningController@getByDocType');

    /** จ้างซ่อมแซม/บำรุงรักษา */
    Route::get('repairs', 'RepairController@getAll');
    Route::get('repairs/{id}', 'RepairController@getById');
});
