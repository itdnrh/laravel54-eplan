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
    Route::get('dashboard/stat1/{year}', 'DashboardController@getStat1');
    Route::get('dashboard/stat2/{year}', 'DashboardController@getStat2');

    /** บุคลากร */
    Route::get('system/persons', 'PersonController@index');
    Route::get('persons/list', 'PersonController@index');
    Route::get('persons/search', 'PersonController@search');
    Route::get('persons/departs', 'PersonController@departs');
    Route::get('persons/departs/head', 'PersonController@getHeadOfDeparts');
    Route::get('persons/detail/{id}', 'PersonController@detail');
    Route::get('persons/edit/{id}', 'PersonController@edit');
    Route::post('persons/update/{id}', 'PersonController@update');
    Route::post('persons/delete/{id}', 'PersonController@delete');

    /** วันหยุดราชการ */
    Route::get('holidays', 'HolidayController@getHolidays');
    Route::get('holidays/list', 'HolidayController@index');

    /** แผนเงินบำรุง */
    Route::get('plans/search', 'PlanController@search');
    Route::get('plans/search-group/{cate}', 'PlanController@searchGroups');
    Route::get('plans/assets', 'PlanAssetController@index');
    Route::get('plans/materials', 'PlanMaterialController@index');
    Route::get('plans/services', 'PlanServiceController@index');
    Route::get('plans/constructs', 'PlanConstructController@index');
    Route::post('plans/create-po/{id}', 'PlanController@createPO');
    Route::post('plans/send-supported/{id}', 'PlanController@sendSupported');
    Route::delete('plans/{id}', 'PlanController@delete');
    Route::get('plans/excel', 'PlanController@excel');

    /** รายการสินค้าและบริการ */
    Route::post('items/validate', 'ItemController@formValidate');
    Route::get('system/items', 'ItemController@index');
    Route::get('items/search', 'ItemController@search');
    Route::get('items/detail/{id}', 'ItemController@detail');
    Route::get('items/add', 'ItemController@add');
    Route::post('items/store', 'ItemController@store');
    Route::get('items/edit/{id}', 'ItemController@edit');
    Route::post('items/update/{id}', 'ItemController@update');
    Route::post('items/delete/{id}', 'ItemController@delete');
    Route::get('items/print/{id}', 'ItemController@printLeaveForm');

    /** แผนครุภัณฑ์ */
    Route::post('assets/validate', 'PlanAssetController@formValidate');
    Route::get('assets/detail/{id}', 'PlanAssetController@detail');
    Route::get('assets/add', 'PlanAssetController@add');
    Route::post('assets/store', 'PlanAssetController@store');
    Route::get('assets/edit/{id}', 'PlanAssetController@edit');
    Route::post('assets/update/{id}', 'PlanAssetController@update');
    Route::post('assets/delete/{id}', 'PlanAssetController@delete');
    Route::get('assets/print/{id}', 'PlanAssetController@printLeaveForm');

    /** แผนวัสดุ */
    Route::post('materials/validate', 'PlanMaterialController@formValidate');
    Route::get('materials/detail/{id}', 'PlanMaterialController@detail');
    Route::get('materials/add', 'PlanMaterialController@add');
    Route::post('materials/store', 'PlanMaterialController@store');
    Route::get('materials/edit/{id}', 'PlanMaterialController@edit');
    Route::post('materials/update/{id}', 'PlanMaterialController@update');
    Route::post('materials/delete/{id}', 'PlanMaterialController@delete');
    Route::get('materials/print/{id}', 'PlanMaterialController@printLeaveForm');

    /** แผนจ้างบริการ */
    Route::post('services/validate', 'PlanServiceController@formValidate');
    Route::get('services/detail/{id}', 'PlanServiceController@detail');
    Route::get('services/add', 'PlanServiceController@add');
    Route::post('services/store', 'PlanServiceController@store');
    Route::get('services/edit/{id}', 'PlanServiceController@edit');
    Route::post('services/update/{id}', 'PlanServiceController@update');
    Route::post('services/delete/{id}', 'PlanServiceController@delete');
    Route::get('services/print/{id}', 'PlanServiceController@printLeaveForm');

    /** แผนก่อสร้าง */
    Route::post('constructs/validate', 'PlanConstructController@formValidate');
    Route::get('constructs/detail/{id}', 'PlanConstructController@detail');
    Route::get('constructs/add', 'PlanConstructController@add');
    Route::post('constructs/store', 'PlanConstructController@store');
    Route::get('constructs/edit/{id}', 'PlanConstructController@edit');
    Route::post('constructs/update/{id}', 'PlanConstructController@update');
    Route::post('constructs/delete/{id}', 'PlanConstructController@delete');
    Route::get('constructs/print/{id}', 'PlanConstructController@printLeaveForm');

    /** แผนโครงการ */
    Route::post('projects/validate', 'ProjectController@formValidate');
    Route::get('plans/projects', 'ProjectController@index');
    Route::get('projects/list', 'ProjectController@index');
    Route::get('projects/search', 'ProjectController@search');
    Route::get('projects/detail/{id}', 'ProjectController@detail');
    Route::get('projects/add', 'ProjectController@add');
    Route::post('projects/store', 'ProjectController@store');
    Route::get('projects/edit/{id}', 'ProjectController@edit');
    Route::post('projects/update/{id}', 'ProjectController@update');
    Route::post('projects/delete/{id}', 'ProjectController@delete');
    Route::post('projects/{id}/payments', 'ProjectController@storePayment');
    Route::post('projects/{id}/timeline', 'ProjectController@storeTimeline');
    Route::post('projects/{id}/{timelineId}/timeline', 'ProjectController@updateTimeline');
    Route::get('projects/print/{id}', 'ProjectController@printForm');
    Route::get('projects/excel', 'ProjectController@excel');

    /** การอนุมัติ */
    Route::get('approvals/assets', 'ApprovalController@assets');
    Route::get('approvals/materials', 'ApprovalController@materials');
    Route::get('approvals/services', 'ApprovalController@services');
    Route::get('approvals/constructs', 'ApprovalController@constructs');
    Route::get('approvals/projects', 'ApprovalController@projects');
    Route::post('approvals', 'ApprovalController@approve');
    Route::post('approvals/{year}/year', 'ApprovalController@approveAll');
    Route::post('approvals/lists', 'ApprovalController@approveByList');

    /** การขอสนับสนุนทั่วไป */
    Route::post('supports/validate', 'SupportController@formValidate');
    Route::get('supports/list', 'SupportController@index');
    Route::get('supports/timeline', 'SupportController@timeline');
    Route::get('supports/search', 'SupportController@search');
    Route::get('supports/detail/{id}', 'SupportController@detail');
    Route::get('supports/add', 'SupportController@create');
    Route::post('supports/store', 'SupportController@store');
    Route::get('supports/edit/{id}', 'SupportController@edit');
    Route::post('supports/update/{id}', 'SupportController@update');
    Route::post('supports/delete/{id}', 'SupportController@delete');
    Route::post('supports/send', 'SupportController@send');
    Route::get('supports/{id}/print', 'SupportController@printForm');
    Route::get('supports/{id}/print/spec-committee', 'SupportController@printSpecCommittee');

    /** การขอสนับสนุนจ้างซ่อม */
    Route::post('repairs/validate', 'RepairController@formValidate');
    Route::get('repairs/list', 'RepairController@index');
    Route::get('repairs/search', 'RepairController@search');
    Route::get('repairs/detail/{id}', 'RepairController@detail');
    Route::get('repairs/add', 'RepairController@create');
    Route::post('repairs/store', 'RepairController@store');
    Route::post('repairs/send', 'RepairController@send');
    Route::get('repairs/{id}/print', 'RepairController@printForm');

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
    Route::post('orders/received/{mode}', 'OrderController@onReceived');
    Route::get('orders/print/{id}', 'OrderController@printSpecCommittee');

    /** ตรวจรับพัสดุ */
    Route::post('inspections/validate', 'InspectionController@formValidate');
    Route::get('orders/inspect', 'InspectionController@index');
    Route::get('inspections/search', 'InspectionController@search');
    Route::get('inspections/{orderId}/order', 'InspectionController@getByOrder');
    Route::get('inspections/{keyword}/deliver-bills', 'InspectionController@getDeliverBills');
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
    Route::get('withdrawals/get-ajax-byid/{id}', 'WithdrawalController@getById');
    Route::get('withdrawals/add', 'WithdrawalController@create');
    Route::post('withdrawals/store', 'WithdrawalController@store');
    Route::get('withdrawals/edit/{id}', 'WithdrawalController@edit');
    Route::get('withdrawals/detail/{id}', 'WithdrawalController@detail');
    Route::post('withdrawals/update', 'WithdrawalController@update');
    Route::post('withdrawals/delete/{id}', 'WithdrawalController@delete');
    Route::get('withdrawals/{id}/print', 'WithdrawalController@printForm');

    /** ควบคุมกำกับติดตาม */
    Route::post('monthly/validate', 'MonthlyController@formValidate');
    Route::get('monthly/list', 'MonthlyController@index');
    Route::get('monthly/summary', 'MonthlyController@summary');
    Route::get('monthly/search', 'MonthlyController@search');
    Route::get('monthly/add', 'MonthlyController@create');
    Route::post('monthly/store', 'MonthlyController@store');
    Route::get('monthly/edit/{id}', 'MonthlyController@edit');
    Route::get('monthly/detail/{id}', 'MonthlyController@detail');
    Route::post('monthly/update/{id}', 'MonthlyController@update');
    Route::post('monthly/delete/{id}', 'MonthlyController@delete');

    /** ประมาณการรายจ่าย */
    Route::post('budgets/validate', 'BudgetController@formValidate');
    Route::get('budgets/list', 'BudgetController@index');
    Route::get('budgets/summary', 'BudgetController@summary');
    Route::get('budgets/search', 'BudgetController@search');
    Route::get('budgets/add', 'BudgetController@create');
    Route::post('budgets/store', 'BudgetController@store');
    Route::get('budgets/edit/{id}', 'BudgetController@edit');
    Route::get('budgets/detail/{id}', 'BudgetController@detail');

    /** ค่าสาธารณูปโภค */
    Route::post('utilities/validate', 'UtilityController@formValidate');
    Route::get('utilities/list', 'UtilityController@index');
    Route::get('utilities/summary', 'UtilityController@summary');
    Route::get('utilities/search', 'UtilityController@search');
    Route::get('utilities/add', 'UtilityController@create');
    Route::post('utilities/store', 'UtilityController@store');
    Route::get('utilities/edit/{id}', 'UtilityController@edit');
    Route::get('utilities/detail/{id}', 'UtilityController@detail');
    Route::post('utilities/update/{id}', 'UtilityController@update');
    Route::post('utilities/delete/{id}', 'UtilityController@delete');

    /** รายจ่าย */
    Route::post('expenses/validate', 'ExpenseController@formValidate');
    Route::get('system/expenses', 'ExpenseController@index');
    Route::get('expenses/add', 'ExpenseController@create');
    Route::post('expenses/store', 'ExpenseController@store');
    Route::get('expenses/edit/{id}', 'ExpenseController@edit');
    Route::get('expenses/detail/{id}', 'ExpenseController@detail');
    Route::post('expenses/update/{id}', 'ExpenseController@update');
    Route::post('expenses/delete/{id}', 'ExpenseController@delete');

    /** เจ้าหนี้ */
    Route::get('suppliers/validate', 'SupplierController@formValidate');
    Route::get('system/suppliers', 'SupplierController@index');
    Route::post('suppliers/store', 'SupplierController@store');
    Route::get('suppliers/edit/{id}', 'SupplierController@edit');
    Route::get('suppliers/detail/{id}', 'SupplierController@detail');
    Route::post('suppliers/update/{id}', 'SupplierController@update');
    Route::post('suppliers/delete/{id}', 'SupplierController@delete');

    /** ตัวชี้วัด */
    Route::post('kpis/validate', 'KpiController@formValidate');
    Route::get('system/kpis', 'KpiController@index');
    Route::get('kpis/list', 'KpiController@index');
    Route::get('kpis/detail/{id}', 'KpiController@detail');
    Route::get('kpis/add', 'KpiController@add');
    Route::post('kpis/store', 'KpiController@store');
    Route::get('kpis/edit/{id}', 'KpiController@edit');
    Route::post('kpis/update/{id}', 'KpiController@update');
    Route::post('kpis/delete/{id}', 'KpiController@delete');
    Route::get('kpis/print/{id}', 'KpiController@printLeaveForm');

    /** รายงาน */
    Route::get('reports/all', 'ReportController@index');
    Route::get('reports/project-faction', 'ReportController@projectByFaction');
    Route::get('reports/project-depart', 'ReportController@projectByDepart');
    Route::get('reports/project-strategic', 'ReportController@projectByStrategic');
    Route::get('reports/project-quarter', 'ReportController@projectByQuarter');
    Route::get('reports/project-summary', 'ReportController@projectSummary');
    Route::get('reports/projects-list', 'ReportController@projectsList');    
    Route::get('reports/plan-faction', 'ReportController@planByFaction');
    Route::get('reports/plan-depart', 'ReportController@planByDepart');
    Route::get('reports/asset-faction', 'ReportController@assetByFaction');
    Route::get('reports/asset-depart', 'ReportController@assetByDepart');
    Route::get('reports/material-depart', 'ReportController@materialByDepart');
    Route::get('reports/plan-item', 'ReportController@planByItem');
    Route::get('reports/plan-type', 'ReportController@planByType');
    Route::get('reports/plan-quarter', 'ReportController@planByQuarter');
    Route::get('reports/plan-process-quarter', 'ReportController@planProcessByQuarter');
});
