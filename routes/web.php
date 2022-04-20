<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'Auth\LoginController@showLogin');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'web'], function() {
    /** ============= Authentication ============= */
    Route::get('/auth/login', 'Auth\LoginController@showLogin');
    Route::post('/auth/signin', 'Auth\LoginController@doLogin');
    Route::get('/auth/logout', 'Auth\LoginController@doLogout');
    Route::get('/auth/register', 'Auth\RegisterController@register');
    Route::post('/auth/signup', 'Auth\RegisterController@create');
    Route::get('/auth/checking', 'Auth\LoginController@getChecking');
    Route::post('/auth/checking', 'Auth\LoginController@checking');
});

Route::group(['middleware' => ['web','auth']], function () {
    /** Dashboard */
    Route::get('dashboard/head/{date}', 'DashboardController@getHeadData');
    Route::get('dashboard/depart/{date}', 'DashboardController@getDepartData');
    Route::get('dashboard/stat1/{year}', 'DashboardController@getStat1');
    Route::get('dashboard/stat2/{year}', 'DashboardController@getStat2');

    /** บุคลากร */
    Route::get('persons/list', 'PersonController@index');
    Route::get('persons/search', 'PersonController@search');
    Route::get('persons/departs', 'PersonController@departs');
    Route::get('persons/departs/head', 'PersonController@getHeadOfDeparts');
    Route::get('persons/detail/{id}', 'PersonController@detail');

    /** วันหยุดราชการ */
    Route::get('holidays', 'HolidayController@getHolidays');
    Route::get('holidays/list', 'HolidayController@index');

    /** แผนจัดซื้อจัดจ้าง */
    Route::get('plans/search', 'PlanController@search');
    Route::get('plans/assets', 'PlanAssetController@index');
    Route::get('plans/materials', 'PlanMaterialController@index');
    Route::get('plans/services', 'PlanServiceController@index');
    Route::get('plans/constructs', 'PlanConstructController@index');
    Route::post('plans/create-po/{id}', 'PlanController@createPO');
    Route::post('plans/send-supported/{id}', 'PlanController@sendSupported');
    Route::delete('plans/{id}', 'PlanController@delete');

    /** รายการสินค้า */
    Route::post('items/validate', 'ItemController@formValidate');
    Route::get('items/search', 'ItemController@search');
    Route::get('items/get-ajax-all', 'ItemController@getAll');
    Route::get('items/get-ajax-byid/{id}', 'ItemController@getById');
    Route::get('items/detail/{id}', 'ItemController@detail');
    Route::get('items/add', 'ItemController@add');
    Route::post('items/store', 'ItemController@store');
    Route::get('items/edit/{id}', 'ItemController@edit');
    Route::post('items/update', 'ItemController@update');
    Route::post('items/delete/{id}', 'ItemController@delete');
    Route::get('items/print/{id}', 'ItemController@printLeaveForm');

    /** แผนครุภัณฑ์ */
    Route::post('assets/validate', 'PlanAssetController@formValidate');
    Route::get('assets/get-ajax-all', 'PlanController@getAll');
    Route::get('assets/get-ajax-byid/{id}', 'PlanController@getById');
    Route::get('assets/detail/{id}', 'PlanAssetController@detail');
    Route::get('assets/add', 'PlanAssetController@add');
    Route::post('assets/store', 'PlanAssetController@store');
    Route::get('assets/edit/{id}', 'PlanAssetController@edit');
    Route::post('assets/update', 'PlanAssetController@update');
    Route::post('assets/delete/{id}', 'PlanAssetController@delete');
    Route::get('assets/print/{id}', 'PlanAssetController@printLeaveForm');

    /** แผนวัสดุ */
    Route::post('materials/validate', 'PlanMaterialController@formValidate');
    Route::get('materials/get-ajax-all', 'PlanController@getAll');
    Route::get('materials/get-ajax-byid/{id}', 'PlanController@getById');
    Route::get('materials/detail/{id}', 'PlanMaterialController@detail');
    Route::get('materials/add', 'PlanMaterialController@add');
    Route::post('materials/store', 'PlanMaterialController@store');
    Route::get('materials/edit/{id}', 'PlanMaterialController@edit');
    Route::post('materials/update', 'PlanMaterialController@update');
    Route::post('materials/delete/{id}', 'PlanMaterialController@delete');
    Route::get('materials/print/{id}', 'PlanMaterialController@printLeaveForm');

    /** แผนจ้างบริการ */
    Route::post('services/validate', 'PlanServiceController@formValidate');
    Route::get('services/get-ajax-all', 'PlanController@getAll');
    Route::get('services/get-ajax-byid/{id}', 'PlanController@getById');
    Route::get('services/detail/{id}', 'PlanServiceController@detail');
    Route::get('services/add', 'PlanServiceController@add');
    Route::post('services/store', 'PlanServiceController@store');
    Route::get('services/edit/{id}', 'PlanServiceController@edit');
    Route::post('services/update', 'PlanServiceController@update');
    Route::post('services/delete/{id}', 'PlanServiceController@delete');
    Route::get('services/print/{id}', 'PlanServiceController@printLeaveForm');

    /** แผนก่อสร้าง */
    Route::post('constructs/validate', 'PlanConstructController@formValidate');
    Route::get('constructs/get-ajax-all', 'PlanController@getAll');
    Route::get('constructs/get-ajax-byid/{id}', 'PlanController@getById');
    Route::get('constructs/detail/{id}', 'PlanConstructController@detail');
    Route::get('constructs/add', 'PlanConstructController@add');
    Route::post('constructs/store', 'PlanConstructController@store');
    Route::get('constructs/edit/{id}', 'PlanConstructController@edit');
    Route::post('constructs/update', 'PlanConstructController@update');
    Route::post('constructs/delete/{id}', 'PlanConstructController@delete');
    Route::get('constructs/print/{id}', 'PlanConstructController@printLeaveForm');

    /** การอนุมัติ */
    Route::get('approvals/comment', 'ApprovalController@getComment');
    Route::post('approvals/comment', 'ApprovalController@doComment');
    Route::get('approvals/receive', 'ApprovalController@getReceive');
    Route::post('approvals/receive', 'ApprovalController@doReceive');
    Route::get('approvals/approve', 'ApprovalController@getApprove');
    Route::post('approvals/approve', 'ApprovalController@doApprove');
    Route::post('approvals/status', 'ApprovalController@setStatus');
    
    /** การขอสนับสนุน */
    Route::post('supports/validate', 'SupportController@formValidate');
    Route::get('supports/list', 'SupportController@index');
    Route::get('supports/search', 'SupportController@search');
    Route::get('supports/add', 'SupportController@create');
    Route::get('supports/store', 'SupportController@store');

    /** จัดซื้อจัดจ้าง */
    Route::post('orders/validate', 'OrderController@formValidate');
    Route::get('orders/list', 'OrderController@index');
    Route::get('orders/search', 'OrderController@search');
    Route::get('orders/add', 'OrderController@create');
    Route::post('orders/store', 'OrderController@store');
    Route::get('orders/edit/{id}', 'OrderController@edit');
    Route::get('orders/detail/{id}', 'OrderController@detail');
    Route::get('orders/getOrder/{id}', 'OrderController@getOrder');
    Route::post('orders/update', 'OrderController@update');
    Route::post('orders/delete/{id}', 'OrderController@delete');
    Route::get('orders/received', 'OrderController@received');
    Route::post('orders/received', 'OrderController@doReceived');
    Route::get('orders/print/{id}', 'OrderController@printCancelForm');

    /** ตรวจรับพัสดุ */
    Route::post('inspections/validate', 'InspectionController@formValidate');
    Route::get('orders/inspect', 'InspectionController@index');
    Route::get('inspections/search', 'InspectionController@search');
    Route::get('inspections/{orderId}/order', 'InspectionController@getByOrder');
    Route::get('inspections/add', 'InspectionController@create');
    Route::post('inspections/store', 'InspectionController@store');
    Route::get('inspections/edit/{id}', 'InspectionController@edit');
    Route::get('inspections/detail/{id}', 'InspectionController@detail');
    Route::post('inspections/update', 'InspectionController@update');
    Route::post('inspections/delete/{id}', 'InspectionController@delete');

    /** การส่งเบิกเงิน */
    Route::post('withdrawals/validate', 'WithdrawalController@formValidate');
    Route::get('orders/withdraw', 'WithdrawalController@index');
    Route::get('withdrawals/search', 'WithdrawalController@search');
    Route::get('withdrawals/add', 'WithdrawalController@create');
    Route::post('withdrawals/store', 'WithdrawalController@store');
    Route::get('withdrawals/edit/{id}', 'WithdrawalController@edit');
    Route::get('withdrawals/detail/{id}', 'WithdrawalController@detail');
    Route::post('withdrawals/update', 'WithdrawalController@update');
    Route::post('withdrawals/delete/{id}', 'WithdrawalController@delete');

    /** เจ้าหนี้ */
    Route::get('suppliers', 'SupplierController@getAll');

    /** รายงาน */
    Route::get('reports/daily', 'ReportController@daily');
    Route::get('reports/daily-data', 'ReportController@getDailyData');
    Route::get('reports/summary', 'ReportController@summary');
    Route::get('reports/summary-data', 'ReportController@getSummaryData');
    Route::get('reports/remain', 'ReportController@remain');
    // Route::get('reports/remain-data', 'ReportController@getRemainData');
    // Route::get('reports/debt-creditor/rpt/{creditor}/{sdate}/{edate}/{showall}', 'ReportController@debtCreditorRpt');
    // Route::get('reports/debt-creditor-excel/{creditor}/{sdate}/{edate}/{showall}', 'ReportController@debtCreditorExcel');     
    // Route::get('reports/debt-debttype/list', 'ReportController@debtDebttype');    
    // Route::get('reports/debt-debttype/rpt/{debtType}/{sdate}/{edate}/{showall}', 'ReportController@debtDebttypeRpt');
    // Route::get('reports/debt-debttype-excel/{debttype}/{sdate}/{edate}/{showall}', 'ReportController@debtDebttypeExcel');   
});
