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
    
    /** ตัวชี้วัด */
    Route::get('kpis', 'KpiController@getAll');

    /** โครงการ */
    Route::get('projects', 'ProjectController@getAll');
    Route::get('projects/{id}', 'ProjectController@getById');
    Route::get('projects/{id}/payments', 'ProjectController@getProjectPayments');
    Route::get('projects/{id}/timeline', 'ProjectController@getProjectTimeline');

    /** แผนครุภัณฑ์ */
    Route::get('assets', 'PlanController@getAll');
    Route::get('assets/{id}', 'PlanController@getById');

    /** แผนวัสดุ */
    Route::get('materials', 'PlanController@getAll');
    Route::get('materials/{id}', 'PlanController@getById');

    /** แผนจ้างบริการ */
    Route::get('services', 'PlanController@getAll');
    Route::get('services/{id}', 'PlanController@getById');

    /** แผนก่อสร้าง */
    Route::get('constructs', 'PlanController@getAll');
    Route::get('constructs/{id}', 'PlanController@getById');

    /** แผนเงินบำรุง */
    Route::get('plans/{itemId}/{year}/{depart}/existed', 'PlanController@isExisted');

    /** ควบคุมกำกับติดตาม */
    Route::get('monthly', 'MonthlyController@getAll');
    Route::get('monthly/{id}', 'MonthlyController@getById');
    Route::get('monthly/{year}/summary', 'MonthlyController@getSummary');

    /** ค่าสาธารณูปโภค */
    Route::get('utilities/{year}/summary', 'UtilityController@getSummary');
    Route::get('utilities', 'UtilityController@getAll');
    Route::get('utilities/{id}', 'UtilityController@getById');

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

    /** รายงาน */
    Route::get('reports/summary-depart', 'ReportController@getSummaryByDepart');
    Route::get('reports/asset-depart', 'ReportController@getAssetByDepart');
    Route::get('reports/material-depart', 'ReportController@getMaterialByDepart');
});
