app.controller('monthlyCtrl', function(CONFIG, $scope, $http, toaster, DatetimeService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.plans = [];
    $scope.pager = null;

    $scope.expenseTypes = [];

    $scope.cboYear = '2567'; //(moment().year() + 543).toString();
    $scope.cboExpenseType = '';
    $scope.cboFaction = '';
    $scope.cboDepart = '';
    $scope.cboPrice = '';
    $scope.cboInPlan = 'I';

    $scope.expenseBudget = '';
    $scope.expenseRemain = '';

    $scope.monthly = {
        monthly_id: '',
        year: '2567',
        month: '',
        expense_type_id: '',
        expense_id: '',
        plan_id: '',
        total: '',
        remain: '',
        depart_id: '',
        reporter_id: '',
        remark: '',
        user: '',
    };

    /** ============================== Init Form elements ============================== */
    let dtpDateOptions = {
        autoclose: true,
        language: 'th',
        format: 'dd/mm/yyyy',
        thaiyear: true,
        todayBtn: true,
        todayHighlight: true
    };

    let dtpMonthOptions = {
        autoclose: true,
        format: 'mm/yyyy',
        viewMode: "months", 
        minViewMode: "months",
        language: 'th',
        thaiyear: true
    };

    // $('#doc_date')
    //     .datepicker(dtpDateOptions)
        // .datepicker('update', new Date());
        // .on('show', function (e) {
        //     $('.day').click(function(event) {
        //         event.preventDefault();
        //         event.stopPropagation();
        //     });
        // });

    $('#dtpMonth')
        .datepicker(dtpMonthOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            console.log(event.date);

            $('#dtpMonth')
                .datepicker(dtpMonthOptions)
                .datepicker('update', event.date);

            $scope.getMultipleData();
        });

    $('#remain').prop('disabled', true);

    const clearMonthly = function() {
        $scope.monthly.monthly_id = '';
        $scope.monthly.year = '2567';
        $scope.monthly.month = '';
        $scope.monthly.expense_type_id = '';
        $scope.monthly.expense_id = '';
        $scope.monthly.plan_id = '';
        $scope.monthly.total = '';
        $scope.monthly.remain = '';
        $scope.monthly.depart_id = '';
        $scope.monthly.reporter_id = '';
        $scope.monthly.remark = '';
        $scope.monthly.user = '';
    };

    $scope.getAll = function(event) {
        $scope.loading = true;
        $scope.plans = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let month   = $scope.cboMonth === '' ? '' : $scope.cboMonth;
        let type    = $scope.cboExpenseType === '' ? '' : $scope.cboExpenseType;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;

        $http.get(`${CONFIG.baseUrl}/monthly/search?year=${year}&month=${month}&type=${type}&status=${status}&depart=${depart}`)
        .then(function(res) {
            $scope.setMonthlys(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setMonthlys = function(res) {
        const { data, ...pager } = res.data.plans;

        /** Merge budget property of budgets data to plans data  */
        $scope.plans = data.map(plan => {
            let budget = res.data.budgets.find(bg => bg.expense_id === plan.expense_id);

            if (budget.budget) {
                plan.budget = budget.budget;
            }

            return plan;
        });

        $scope.pager = pager;
    };

    $scope.getDataWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.plans = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let month   = $scope.cboMonth === '' ? '' : $scope.cboMonth;
        let type    = $scope.cboExpenseType === '' ? '' : $scope.cboExpenseType;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;

        $http.get(`${url}&year=${year}&month=${month}&type=${type}&status=${status}&depart=${depart}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.totalSummary = [];
    $scope.getSummary = function(event) {
        $scope.loading = true;
        $scope.expenseTypes = [];
        $scope.totalSummary = [];

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let type    = $scope.cboExpenseType === '' ? '' : $scope.cboExpenseType;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;

        $http.get(`${CONFIG.apiUrl}/monthly/${year}/summary?type=${type}&depart=${depart}&status=${status}`)
        .then(function(res) {
            const { monthly, budgets, expenseTypes } = res.data;

            let summaries = monthly.map(mon => {
                const summary = budgets.find(b => b.expense_id === mon.expense_id);

                if (summary) mon.budget = summary.budget;

                return mon;
            });

            $scope.expenseTypes = expenseTypes.map(et => {
                et.summaries = summaries.filter(sum => et.id == sum.expense_type_id);

                let total = {
                    expense_type_id: '',
                    budget: 0,
                    oct: 0,
                    nov: 0,
                    dec: 0,
                    jan: 0,
                    feb: 0,
                    mar: 0,
                    apr: 0,
                    may: 0,
                    jun: 0,
                    jul: 0,
                    aug: 0,
                    sep: 0,
                    total: 0,
                    remain: 0,
                };
                if (et.summaries.length > 0) {
                    et.summaries.forEach(sum => {
                        total.expense_type_id = et.id;
                        total.budget += sum.budget;
                        total.oct += sum.oct_total;
                        total.nov += sum.nov_total;
                        total.dec += sum.dec_total;
                        total.jan += sum.jan_total;
                        total.feb += sum.feb_total;
                        total.mar += sum.mar_total;
                        total.apr += sum.apr_total;
                        total.may += sum.may_total;
                        total.jun += sum.jun_total;
                        total.jul += sum.jul_total;
                        total.aug += sum.aug_total;
                        total.sep += sum.sep_total;
                        total.total += sum.total;
                        total.remain += sum.budget - sum.total;
                    });

                    $scope.totalSummary.push(total);
                }

                return et;
            });

            // if (summaries) {
            //     summaries.forEach(sum => {
            //         $scope.totalSummary.budget += sum.budget,
            //         $scope.totalSummary.oct += sum.oct_total,
            //         $scope.totalSummary.nov += sum.nov_total;
            //         $scope.totalSummary.dec += sum.dec_total;
            //         $scope.totalSummary.jan += sum.jan_total;
            //         $scope.totalSummary.fab += sum.fab_total;
            //         $scope.totalSummary.mar += sum.mar_total;
            //         $scope.totalSummary.apr += sum.apr_total;
            //         $scope.totalSummary.may += sum.may_total;
            //         $scope.totalSummary.jun += sum.jun_total;
            //         $scope.totalSummary.jul += sum.jul_total;
            //         $scope.totalSummary.aug += sum.aug_total;
            //         $scope.totalSummary.sep += sum.sep_total;
            //         $scope.totalSummary.total += sum.total;
            //         $scope.totalSummary.remain += sum.budget - sum.total;
            //     });
            // } else {
            //     $scope.totalSummary = {
            //         budget: 0,
            //         oct: 0,
            //         nov: 0,
            //         dec: 0,
            //         jan: 0,
            //         fab: 0,
            //         mar: 0,
            //         apr: 0,
            //         may: 0,
            //         jun: 0,
            //         jul: 0,
            //         aug: 0,
            //         sep: 0,
            //         total: 0,
            //         remain: 0,
            //     };
            // }

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getExpenseTypeTotal = function(id) {
        return $scope.totalSummary.find(t => t.expense_type_id == id);
    };

    $scope.getPlanSummaryByExpense = function(e, year, expense) {
        $scope.loading = true;

        $http.get(`${CONFIG.apiUrl}/budgets/${year}/${expense}`)
        .then(function(res) {
            if (res.data.budget) {
                $scope.expenseBudget = res.data.budget.budget;
                $scope.expenseRemain = res.data.budget.remain;

                $scope.monthly.remain = res.data.budget.remain;
                $scope.monthly.plan_id = res.data.budget.plan_id || '';
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่พบข้อมูลในรายการประมาณการรายจ่าย !!!");

                $scope.monthly.expense_id = '';
            }

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.calculateRemain = function(total) {
        $scope.monthly.remain = parseFloat($scope.currencyToNumber($scope.expenseRemain)) - parseFloat($scope.currencyToNumber(total));
    };

    $scope.getById = function(id, cb) {
        $http.get(`${CONFIG.apiUrl}/monthly/${id}`)
        .then(function(res) {
            cb(res.data.plan);
        }, function(err) {
            console.log(err);
        });
    }

    $scope.setEditControls = function(plan) {
        $scope.monthly.id           = plan.id;
        $scope.monthly.month        = plan.month;
        $scope.monthly.total        = plan.total;
        $scope.monthly.remain       = plan.remain;
        $scope.monthly.reporter_id  = plan.reporter_id;
        $scope.monthly.remark       = plan.remark;
        $scope.monthly.expense      = plan.expense;
        $scope.monthly.depart       = plan.depart;

        /** Convert int value to string */
        $scope.monthly.year         = plan.year.toString();
        $scope.monthly.expense_id   = plan.expense_id.toString();
        $scope.monthly.expense_type_id = plan.expense.expense_type_id.toString();
        $scope.monthly.faction_id   = plan.depart.faction_id.toString();
        $scope.monthly.depart_id    = plan.depart_id.toString();

        $scope.getPlanSummaryByExpense(null, plan.year, plan.expense_id);
        $scope.onFilterExpenses(plan.expense.expense_type_id);
        $scope.onFactionSelected(plan.depart.faction_id);

        $('#expense_type_id').val(plan.expense.expense_type_id).trigger('change.select2');
        $('#faction_id').val(plan.depart.faction_id).trigger('change.select2');
    };

    $scope.setCategoryName = function(name, type) {
        if (type != '1' || $scope.cboPrice == '') return name;

        return ($scope.cboPrice == '1') ? name : name.replace('ครุภัณฑ์', 'ครุภัณฑ์ต่ำกว่าเกณฑ์ ');
    };

    $scope.showMultipleForm = function(e) {
        $scope.getMultipleData();

        $('#multiple-form').modal('show');
    };

    $scope.multipleData = {
        year: '2567',
        month: DatetimeService.fotmatYearMonthBE(moment().format('YYYY-MM')),
        plan_type_id: '1',
        expenses: [],
        user: '',
        isExisted: false
    };
    $scope.getMultipleData = function() {
        $scope.loading = true;
        $scope.multipleData.expenses = [];

        let year    = $scope.multipleData.year;
        let month   = $scope.multipleData.month == '' ? '' : DatetimeService.fotmatYearMonth($scope.multipleData.month);
        let type    = $scope.multipleData.plan_type_id == '' ? '1' : $scope.multipleData.plan_type_id;
        let price   = $scope.cboPrice == '' ? '' : $scope.cboPrice;
        let in_plan = $scope.cboInPlan == '' ? '' : $scope.cboInPlan;

        $http.get(`${CONFIG.baseUrl}/monthly/multiple-data?year=${year}&type=${type}&month=${month}&price=${price}&in_plan=${in_plan}`)
        .then(function(res) {
            if ($scope.multipleData.plan_type_id == '3' || $scope.multipleData.plan_type_id == '4') {
                let exp = {};

                exp.net_total = res.data.expenses.reduce((acc, curVal) => acc = acc + parseFloat(curVal.net_total), 0.0);

                if ($scope.multipleData.plan_type_id == '3') {
                    exp.category_id = 69;
                    exp.category_name = 'จ้างเหมา';
                    exp.expense_id = 69;
                    exp.plan_type_id = '3';
                } else {
                    exp.category_id = 67;
                    exp.category_name = 'สิ่งก่อสร้างและระบบต่างๆ';
                    exp.expense_id = 67;
                    exp.plan_type_id = '4';
                }

                $scope.multipleData.expenses.push(exp);
            } else {
                $scope.multipleData.expenses = res.data.expenses.map(ex => {
                    let category = res.data.categories.find(cat => cat.id == ex.category_id);

                    if (category) {
                        ex.expense_id   = ($scope.multipleData.plan_type_id == '1' && $scope.cboPrice == '2')
                                                ? category.expense_less10k 
                                                : category.expense_id;
                        ex.plan_type_id = category.plan_type_id;
                    }

                    return ex;
                });
            }

            $scope.multipleData.expenses.map(ex => {
                let budget = res.data.budgets.find(bg => bg.expense_id == ex.expense_id);

                if (budget) {
                    ex.budget = budget.budget;
                    ex.remain = parseFloat(budget.remain) - parseFloat(ex.net_total);
                }

                return ex;
            });

            console.log($scope.multipleData);
            checkDataExistance(moment(month).format('MM'));

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    const checkDataExistance = function(month) {
        $scope.loading = true;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let type    = $scope.multipleData.plan_type_id === '' ? '' : $scope.multipleData.plan_type_id;

        let price = '';
        if ($scope.multipleData.plan_type_id == '1') {
            price = $scope.cboPrice == '' ? '0' : $scope.cboPrice;
        } else {
            price = '0';
        }

        $http.get(`${CONFIG.apiUrl}/monthly/check-multiple/${year}/${month}/${type}/${price}`)
        .then(function(res) {
            if (res.data.monthly > 0) {
                $scope.multipleData.isExisted = true;
            } else {
                $scope.multipleData.isExisted = false;
            }

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.multipleStore = function(event, form) {
        event.preventDefault();

        if (form.$invalid) {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณากรอกข้อมูลให้ครบ !!!");
            return;
        }

        if (!$scope.multipleData.isExisted) {
            $scope.loading = true;
            $scope.multipleData.user = $('#user').val();
    
            $http.post(`${CONFIG.baseUrl}/monthly/multiple-store`, $scope.multipleData)
            .then(function(res) {
                $scope.loading = false;
    
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");
    
                    $scope.getAll();
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
                }
            }, function(err) {
                $scope.loading = false;
    
                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
            });
        } else {
            $scope.multipleUpdate();
        }
    };

    $scope.multipleUpdate = function() {
        if (confirm('คุณต้องการปรับปรุงข้อมูลควบคุมกำกับติดตามใช่หรือไม่?')) {
            $scope.loading = true;
            $scope.multipleData.user = $('#user').val();

            $http.post(`${CONFIG.baseUrl}/monthly/multiple-update`, $scope.multipleData)
            .then(function(res) {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ปรับปรุงข้อมูลเรียบร้อย !!!");

                    $scope.getAll();
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถปรับปรุงข้อมูลได้ !!!");
                }
            }, function(err) {
                $scope.loading = false;

                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถปรับปรุงข้อมูลได้ !!!");
            });
        }
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $scope.loading = true;
        $scope.monthly.user = $('#user').val();

        $http.post(`${CONFIG.baseUrl}/monthly/store`, $scope.monthly)
        .then(function(res) {
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");

                window.location.href = `${CONFIG.baseUrl}/monthly/list`;
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
            }
        }, function(err) {
            $scope.loading = false;

            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
        });
    }

    $scope.update = function(event) {
        event.preventDefault();

        if(confirm(`คุณต้องแก้ไขข้อมูลควบคุมกำกับติดตาม รหัส ${$scope.monthly.id} ใช่หรือไม่?`)) {
            $scope.monthly.user = $('#user').val();
            $scope.loading = true;

            $http.post(`${CONFIG.baseUrl}/monthly/update/${$scope.monthly.id}`, $scope.monthly)
            .then(function(res) {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/monthly/list`;
                } else {
                    toaster.pop('error', "ผลการทำงาน", "ไม่สามารถแก้ไขข้อมูลได้ !!!");
                }
            }, function(err) {
                $scope.loading = false;

                console.log(err);
                toaster.pop('error', "ผลการทำงาน", "ไม่สามารถแก้ไขข้อมูลได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();

        if(confirm(`คุณต้องลบข้อมูลควบคุมกำกับติดตาม รหัส ${id} ใช่หรือไม่?`)) {
            $scope.loading = true;

            $http.post(`${CONFIG.baseUrl}/monthly/delete/${id}`)
            .then(res => {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลบข้อมูลเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/monthly/list`;
                } else {
                    toaster.pop('error', "ผลการทำงาน", "ไม่สามารถลบข้อมูลได้ !!!");
                }
            }, err => {
                $scope.loading = false;
                console.log(err);
                toaster.pop('error', "ผลการทำงาน", "ไม่สามารถลบข้อมูลได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };
});