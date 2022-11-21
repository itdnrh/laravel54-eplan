app.controller('monthlyCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.plans = [];
    $scope.pager = null;

    $scope.summary = [];

    $scope.cboYear = '2566'; //(moment().year() + 543).toString();
    $scope.cboExpenseType = '';
    $scope.cboFaction = '';
    $scope.cboDepart = '';

    $scope.expenseBudget = '';
    $scope.expenseRemain = '';

    $scope.monthly = {
        monthly_id: '',
        year: '2566',
        month: '',
        expense_type_id: '',
        expense_id: '',
        total: '',
        remain: '',
        depart_id: '',
        reporter_id: '',
        remark: '',
        user: '',
    };

    /** ============================== Init Form elements ============================== */
    let dtpOptions = {
        autoclose: true,
        language: 'th',
        format: 'dd/mm/yyyy',
        thaiyear: true,
        todayBtn: true,
        todayHighlight: true
    };

    // $('#doc_date')
    //     .datepicker(dtpOptions)
        // .datepicker('update', new Date());
        // .on('show', function (e) {
        //     $('.day').click(function(event) {
        //         event.preventDefault();
        //         event.stopPropagation();
        //     });
        // });

    $('#remain').prop('disabled', true);

    const clearMonthly = function() {
        $scope.monthly = {
            monthly_id: '',
            year: '2566',
            month: '',
            expense_type_id: '',
            expense_id: '',
            total: '',
            remain: '',
            depart_id: '',
            reporter_id: '',
            remark: '',
            user: ''
        };
    };

    $scope.getAll = function(event) {
        $scope.loading = true;
        $scope.plans = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let type    = $scope.cboExpenseType === '' ? '' : $scope.cboExpenseType;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;

        $http.get(`${CONFIG.baseUrl}/monthly/search?year=${year}&type=${type}&status=${status}&depart=${depart}`)
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
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let menu    = $scope.cboMenu === '' ? '' : $scope.cboMenu;

        $http.get(`${url}&year=${year}&cate=${cate}&status=${status}&depart=${depart}&menu=${menu}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.totalSummary = {
        budget: 0,
        oct: 0,
        nov: 0,
        dec: 0,
        jan: 0,
        fab: 0,
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
    $scope.getSummary = function(event) {
        $scope.loading = true;
        $scope.summary = [];

        $scope.totalSummary = {
            budget: 0,
            oct: 0,
            nov: 0,
            dec: 0,
            jan: 0,
            fab: 0,
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

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let type    = $scope.cboExpenseType === '' ? '' : $scope.cboExpenseType;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;

        $http.get(`${CONFIG.apiUrl}/monthly/${year}/summary?type=${type}&depart=${depart}&status=${status}`)
        .then(function(res) {
            const { monthly, budget } = res.data;

            $scope.summary = monthly.map(mon => {
                const summary = budget.find(b => b.expense_id === mon.expense_id);
                mon.budget = summary.budget;

                return mon;
            });

            if ($scope.summary) {
                $scope.summary.forEach(sum => {
                    $scope.totalSummary.budget += sum.budget,
                    $scope.totalSummary.oct += sum.oct_total,
                    $scope.totalSummary.nov += sum.nov_total;
                    $scope.totalSummary.dec += sum.dec_total;
                    $scope.totalSummary.jan += sum.jan_total;
                    $scope.totalSummary.fab += sum.fab_total;
                    $scope.totalSummary.mar += sum.mar_total;
                    $scope.totalSummary.apr += sum.apr_total;
                    $scope.totalSummary.may += sum.may_total;
                    $scope.totalSummary.jun += sum.jun_total;
                    $scope.totalSummary.jul += sum.jul_total;
                    $scope.totalSummary.aug += sum.aug_total;
                    $scope.totalSummary.sep += sum.sep_total;
                    $scope.totalSummary.total += sum.total;
                    $scope.totalSummary.remain += sum.budget - sum.total;
                });
            } else {
                $scope.totalSummary = {
                    budget: 0,
                    oct: 0,
                    nov: 0,
                    dec: 0,
                    jan: 0,
                    fab: 0,
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
            }

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPlanSummaryByExpense = function(e, year, expense) {
        $scope.loading = true;

        $http.get(`${CONFIG.apiUrl}/budgets/${year}/${expense}`)
        .then(function(res) {
            if (res.data.plan) {
                $scope.expenseBudget = res.data.plan.budget;
                $scope.expenseRemain = res.data.plan.remain;

                $scope.monthly.remain = res.data.plan.remain;
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

        /** Convert int value to string */
        $scope.monthly.year         = plan.year.toString();
        $scope.monthly.expense_id   = plan.expense_id.toString();
        $scope.monthly.expense_type_id   = plan.expense.expense_type_id.toString();
        $scope.monthly.faction_id   = plan.depart.faction_id.toString();
        $scope.monthly.depart_id    = plan.depart_id.toString();

        $scope.getPlanSummaryByExpense(null, plan.year, plan.expense_id);
        $scope.onFilterExpenses(plan.expense.expense_type_id);
        $scope.onFactionSelected(plan.depart.faction_id);

        $('#expense_type_id').val(plan.expense.expense_type_id).trigger('change.select2');
        $('#faction_id').val(plan.depart.faction_id).trigger('change.select2');
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

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/leaves/edit/${id}`;
    };

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