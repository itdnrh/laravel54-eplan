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
    Route::get('dashboard/stat/{year}', 'DashboardController@getStatYear');

    /** บุคลากร */
    Route::get('persons/list', 'PersonController@index');
    Route::get('persons/search/{depart}/{searchKey}', 'PersonController@search');
    Route::get('persons/departs', 'PersonController@departs');
    Route::get('persons/departs/head', 'PersonController@getHeadOfDeparts');
    Route::get('persons/detail/{id}', 'PersonController@detail');

    /** วันหยุดราชการ */
    Route::get('holidays', 'HolidayController@getHolidays');
    Route::get('holidays/list', 'HolidayController@index');

    /** แผนจัดซื้อจัดจ้าง */
    Route::post('plans/send-supported/{id}', 'PlanController@sendSupported');
    Route::post('plans/create-op/{id}', 'PlanController@createPO');

    /** แผนครุภัณฑ์ */
    Route::post('assets/validate', 'PlanAssetController@formValidate');
    Route::get('assets/list', 'PlanAssetController@index');
    Route::get('assets/search/{year}/{cate}/{status}/{menu}', 'PlanAssetController@search');
    Route::get('assets/get-ajax-all', 'PlanAssetController@getAll');
    Route::get('assets/get-ajax-byid/{id}', 'PlanAssetController@getById');
    Route::get('assets/detail/{id}', 'PlanAssetController@detail');
    Route::get('assets/add', 'PlanAssetController@add');
    Route::post('assets/store', 'PlanAssetController@store');
    Route::get('assets/edit/{id}', 'PlanAssetController@edit');
    Route::post('assets/update', 'PlanAssetController@update');
    Route::post('assets/delete/{id}', 'PlanAssetController@delete');
    Route::get('assets/print/{id}', 'PlanAssetController@printLeaveForm');

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
    Route::post('orders/update', 'OrderController@update');
    Route::post('orders/delete/{id}', 'OrderController@delete');
    Route::post('orders/approve', 'OrderController@doApprove');
    Route::post('orders/receive', 'OrderController@doReceive');
    Route::get('orders/print/{id}', 'OrderController@printCancelForm');

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
