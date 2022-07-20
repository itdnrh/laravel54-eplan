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
    /** Dashboard */
    Route::get('dashboard/summary-assets', 'DashboardController@getSummaryAssets');
    Route::get('dashboard/summary-materials', 'DashboardController@getSummaryMaterials');

    /** รายการสินค้าและบริการ */
    Route::get('items', 'ItemController@getAll');
    Route::get('items/{id}', 'ItemController@getById');
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

    /** รายจ่าย */
    Route::get('expenses', 'ExpenseController@search');
    Route::get('expenses/{id}', 'ExpenseController@getById');

    /** เลขที่เอกสาร */
    Route::get('runnings/{docType}/doc-type', 'RunningController@getByDocType');

    /** การขอสนับสนุน */
    Route::get('supports', 'SupportController@search');
    Route::get('supports/{id}', 'SupportController@getById');
    Route::get('supports/details/list', 'SupportController@getSupportDetails');

    /** จ้างซ่อมแซม/บำรุงรักษา */
    Route::get('repairs', 'RepairController@getAll');
    Route::get('repairs/{id}', 'RepairController@getById');

    /** เจ้าหนี้ */    
    Route::get('suppliers', 'SupplierController@getAll');
    Route::get('suppliers/{id}', 'SupplierController@getById');

     /** บุคลากร */
    Route::get('persons', 'PersonController@getAll');
    Route::get('persons/{id}', 'PersonController@getById');
    Route::put('persons/{id}/move', 'PersonController@move');
    Route::put('persons/{id}/transfer', 'PersonController@transfer');
    Route::put('persons/{id}/leave', 'PersonController@leave');
    Route::put('persons/{id}/unknown', 'PersonController@unknown');
    Route::get('persons/{id}/movings', 'PersonController@getMoving');
    /** รายงาน */
    Route::get('reports/summary-depart', 'ReportController@getSummaryByDepart');
    Route::get('reports/asset-depart', 'ReportController@getAssetByDepart');
    Route::get('reports/material-depart', 'ReportController@getMaterialByDepart');
});
