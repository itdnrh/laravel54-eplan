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

    /** โครงการ */
    Route::get('projects', 'ProjectController@getAll');
    Route::get('projects/{id}', 'ProjectController@getById');

    /** ควบคุมกำกับติดตาม */
    Route::get('monthly', 'MonthlyController@getAll');
    Route::get('monthly/{id}', 'MonthlyController@getById');

    /** การส่งเบิกเงิน */
    Route::put('withdrawals/{id}', 'WithdrawalController@withdraw');
    
    /** เลขที่เอกสาร */
    Route::get('runnings/{docType}/doc-type', 'RunningController@getByDocType');
});
