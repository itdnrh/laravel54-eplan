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
    Route::get('dashboard/summary-services', 'DashboardController@getSummaryServices');
    Route::get('dashboard/summary-constructs', 'DashboardController@getSummaryConstructs');
    Route::get('dashboard/project-type', 'DashboardController@getProjectByType');

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
    Route::get('projects/{id}/modifications', 'ProjectController@getProjectModifications');
    Route::put('projects/{id}/approve', 'ProjectController@approve');
    Route::put('projects/{id}/cancel', 'ProjectController@cancel');

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
    Route::get('plans/{itemId}/{year}/{depart}/{division}/existed', 'PlanController@isExisted');
    Route::get('plans/{id}/balance', 'PlanController@getBalance');
    Route::put('plans/{id}/change', 'PlanController@change');
    Route::put('plans/{id}/status', 'PlanController@setStatus');
    Route::put('plans/{id}/adjust', 'PlanController@storeAdjust');
    Route::put('plans/{id}/inplan', 'PlanController@inPlan');

    /** ควบคุมกำกับติดตาม */
    Route::get('monthly', 'MonthlyController@getAll');
    Route::get('monthly/{id}', 'MonthlyController@getById');
    Route::get('monthly/{year}/summary', 'MonthlyController@getSummary');
    Route::get('monthly/check-multiple/{year}/{month}/{type}/{price}', 'MonthlyController@checkMultiple');

    /** ค่าสาธารณูปโภค */
    Route::get('utilities/{year}/summary', 'UtilityController@getSummary');
    Route::get('utilities', 'UtilityController@getAll');
    Route::get('utilities/{id}', 'UtilityController@getById');

    /** ประมาณการรายจ่าย */
    Route::get('budgets', 'BudgetController@getAll');
    Route::get('budgets/{id}', 'BudgetController@getById');
    Route::get('budgets/{year}/{expense}', 'BudgetController@getByExpense');
    Route::put('budgets/{id}', 'BudgetController@update');
    Route::delete('budgets/{id}', 'BudgetController@delete');

    /** ตรวจรับพัสดุ */
    Route::get('inspections/search', 'InspectionController@search');
    Route::get('inspections/{id}', 'InspectionController@getById');

    /** การส่งเบิกเงิน */
    Route::get('withdrawals/{id}', 'WithdrawalController@getById');
    Route::put('withdrawals/{id}', 'WithdrawalController@withdraw');
    Route::put('withdrawals/{id}/cancel', 'WithdrawalController@cancel');
    Route::put('withdrawals/{id}/set-debt', 'WithdrawalController@setDebt');

    /** รายจ่าย */
    Route::get('expenses', 'ExpenseController@search');
    Route::get('expenses/{id}', 'ExpenseController@getById');

    /** เลขที่เอกสาร */
    Route::get('runnings/{docType}/doc-type', 'RunningController@getByDocType');

    /** การขอสนับสนุน */
    Route::get('supports', 'SupportController@search');
    Route::get('supports/{id}', 'SupportController@getById');
    Route::get('supports/details/list', 'SupportController@getSupportDetails');
    Route::get('supports/details/group', 'SupportController@getSupportDetailGroups');
    Route::put('supports/{id}/return', 'SupportController@onReturn');
    Route::put('supports/{id}/cancel-sent', 'SupportController@cancelSent');
    Route::put('supports/{id}/cancel-sent-plan', 'SupportController@cancelSentPlan');
    Route::put('supports/{id}/cancel-received', 'SupportController@cancelReceived');
    // Route::put('supports/{id}/sendDocPlan', 'SupportController@sendDocPlan');
    /** แผนอนุมตัิ*/
    Route::put('supports/{id}/plan_return', 'SupportController@planOnReturn');
    Route::put('supports/{id}/plan-cancel-received', 'SupportController@planCancelReceived');

    Route::post('support-orders', 'SupportOrderController@store');
    Route::put('support-orders/{id}', 'SupportOrderController@update');

    /** จ้างซ่อมแซม/บำรุงรักษา */
    Route::get('repairs', 'RepairController@getAll');
    Route::get('repairs/{id}', 'RepairController@getById');

    /** การอนุมัติ */
    Route::put('approvals/{id}/cancel', 'ApprovalController@cancel');

    /** เจ้าหนี้ */    
    Route::get('suppliers', 'SupplierController@getAll');
    Route::get('suppliers/{id}', 'SupplierController@getById');

     /** บุคลากร */
    Route::get('persons', 'PersonController@getAll');
    Route::get('persons/{id}', 'PersonController@getById');
    Route::put('persons/{id}/move', 'PersonController@move');
    Route::put('persons/{id}/transfer', 'PersonController@transfer');
    Route::put('persons/{id}/leave', 'PersonController@leave');
    Route::put('persons/{id}/status', 'PersonController@status');
    Route::put('persons/{id}/rename', 'PersonController@rename');
    Route::get('persons/{id}/movings', 'PersonController@getMoving');

    /** การปฏิบัติงานแทน */
    Route::get('delegations', 'DelegationController@getAll');
    Route::get('delegations/{id}', 'DelegationController@getById');

    /** คำสั่งจังหวัด */
    Route::get('provinces', 'ProvinceOrderController@getAll');
    Route::get('provinces/{id}', 'ProvinceOrderController@getById');
    Route::put('provinces/{id}/activate', 'ProvinceOrderController@activate');
    Route::put('provinces/{id}/deactivate', 'ProvinceOrderController@deactivate');

    /** ข้อมูลหน่วยงาน */
    Route::get('factions', 'FactionController@getAll');
    Route::get('factions/{id}', 'FactionController@getById');
    Route::post('factions', 'FactionController@store');
    Route::put('factions/{id}', 'FactionController@update');
    Route::delete('factions/{id}', 'FactionController@delete');
    Route::put('factions/{id}/active', 'FactionController@active');

    /** กลุ่มงาน */
    Route::get('departs', 'DepartController@search');
    Route::get('departs/{id}', 'DepartController@getById');
    Route::post('departs', 'DepartController@store');
    Route::put('departs/{id}', 'DepartController@update');
    Route::delete('departs/{id}', 'DepartController@delete');
    Route::put('departs/{id}/active', 'DepartController@active');

    /** งาน */
    Route::get('divisions', 'DivisionController@search');
    Route::get('divisions/{id}', 'DivisionController@getById');
    Route::post('divisions', 'DivisionController@store');
    Route::put('divisions/{id}', 'DivisionController@update');
    Route::delete('divisions/{id}', 'DivisionController@delete');
    Route::put('divisions/{id}/active', 'DivisionController@active');

    /** ปรับเปลี่ยนแผน */
    Route::put('adjustments/{id}/update', 'PlanAdjustmentController@update');
    Route::delete('adjustments/{id}/delete', 'PlanAdjustmentController@delete');

    /** รายงาน */
    Route::get('reports/project-faction', 'ReportController@getProjectByFaction');
    Route::get('reports/project-depart', 'ReportController@getProjectByDepart');
    Route::get('reports/project-strategic', 'ReportController@getProjectByStrategic');
    Route::get('reports/project-quarter', 'ReportController@getProjectByQuarter');
    Route::get('reports/project-summary', 'ReportController@getProjectSummary');
    Route::get('reports/project-process-quarter', 'ReportController@getProjectProcessByQuarter');
    Route::get('reports/project-strategy-quarter/{strategy}', 'ReportController@getProjectStrategyByQuarter');

    Route::get('reports/plan-faction', 'ReportController@getPlanByFaction');
    Route::get('reports/plan-depart', 'ReportController@getPlanByDepart');
    Route::get('reports/asset-faction', 'ReportController@getAssetByFaction');
    Route::get('reports/asset-depart', 'ReportController@getAssetByDepart');
    Route::get('reports/material-depart', 'ReportController@getMaterialByDepart');
    Route::get('reports/plan-item', 'ReportController@getPlanByItem');
    Route::get('reports/plan-type', 'ReportController@getPlanByType');
    Route::get('reports/plan-quarter', 'ReportController@getPlanByQuarter');
    Route::get('reports/plan-process-quarter', 'ReportController@getPlanProcessByQuarter');
    Route::get('reports/plan-process-details/{type}', 'ReportController@getPlanProcessByDetails');
    Route::get('reports/plan-process-requests/{type}', 'ReportController@getPlanProcessByRequests');

    Route::get('reports/order-compare-support', 'ReportController@getOrderCompareSupport');
    Route::get('reports/order-backward-month', 'ReportController@getOrderBackwardMonth');

    //รายการบิลภาครัฐ 
    //Route::post('invoice_head/delete', 'InvoiceController@destroy');
    //Route::delete('/invoice_head/{id}', 'ItemController@destroy')->name('items.destroy');
    Route::get('invoice/{id}', 'InvoiceController@getById');
    
    Route::get('invoicedetail/{id}', 'InvoiceDetailController@getById');
    Route::get('invoicedetail/getInvoiceDetailDataById/{id}', 'InvoiceDetailController@getInvoiceDetailDataById');
    Route::put('invoicedetail/{id}/cancel-sent-plan', 'InvoiceDetailController@cancelSentPlan');

    Route::get('invoicedetails/get-invoice', 'InvoiceDetailController@getInvoiceReportData');
    Route::get('invoicedetail/get_personnel/{cid}', 'InvoiceDetailController@getPersonnel');


    Route::get('invoice-details', 'InvoiceDetailController@index');
    Route::post('invoice-details', 'InvoiceDetailController@store');
    Route::put('invoice-details/{id}', 'InvoiceDetailController@update');
    Route::delete('invoice-details/{id}', 'InvoiceDetailController@destroy');

    // Route for fetching invoice items (this will be used to populate the dropdown)
    //Route::get('invoice-items', 'InvoiceItemController@index');
    Route::get('invoiceitem/getById/{id}', 'InvoiceItemController@getById');

    

    //Route::get('getInvoiceReport', function () {return 'Hello, World!';});

    //Route::get('invoicedetails/getInvoiceReport', function () {return 'Hello, World!';});

});
