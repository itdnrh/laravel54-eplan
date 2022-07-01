app.controller('budgetCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.budgets = [];
    $scope.pager = null;

    $scope.summary = [];

    $scope.cboYear = (moment().year() + 543).toString();
    $scope.cboExpenseType = '';
    $scope.cboFaction = '';
    $scope.cboDepart = '';

    $scope.expenseBudget = '';
    $scope.expenseRemain = '';

    $scope.budget = {
        monthly_id: '',
        year: '',
        month: '',
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

    const clearBudget = function() {
        $scope.budget = {
            monthly_id: '',
            year: '',
            month: '',
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
        $scope.budgets = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let type = $scope.cboExpenseType === '' ? '' : $scope.cboExpenseType;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;

        $http.get(`${CONFIG.baseUrl}/budgets/search?year=${year}&type=${type}&depart=${depart}&status=${status}`)
        .then(function(res) {
            $scope.setBudgets(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPlanSummaryByExpense = function(e, year, expense) {
        $scope.loading = true;

        $http.get(`${CONFIG.apiUrl}/plan-summary/${year}/${expense}`)
        .then(function(res) {
            $scope.expenseBudget = res.data.plan.budget;
            $scope.expenseRemain = res.data.plan.remain;

            $scope.monthly.remain = res.data.plan.remain;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.calculateRemain = function(total) {
        $scope.monthly.remain = parseFloat($scope.expenseRemain) - parseFloat(total);
    };

    $scope.expenseTypes = [];
    $scope.setBudgets = function(res) {
        // const { data, ...pager } = res.data.budgets;

        $scope.expenseTypes = res.data.expenseTypes.map(type => {
            const budgetsList = res.data.budgets.filter(budget => budget.expense.expense_type_id === type.id);
            type.budgets = budgetsList ? budgetsList : [];

            return type;
        });

        console.log($scope.expenseTypes);
        // $scope.pager = pager;
    };

    $scope.getDataWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.budgets = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;

        $http.get(`${url}&year=${year}&status=${status}&depart=${depart}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
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
        /** Global data */
        $scope.planId                   = plan.id;
        $scope.planType                 = 1;

        /** ข้อมูลครุภัณฑ์ */
        $scope.monthly.monthly_id   = plan.id;
        $scope.monthly.month        = plan.month;
        $scope.monthly.total        = plan.total;
        $scope.monthly.remain       = plan.remain;
        $scope.monthly.depart_id    = plan.depart_id;
        $scope.monthly.reporter_id  = plan.reporter_id;
        $scope.monthly.remark       = plan.remark;
        
        /** Convert int value to string */
        $scope.monthly.year         = plan.year.toString();
        $scope.monthly.expense_id   = plan.expense_id.toString();
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
    
        if(confirm(`คุณต้องแก้ไขข้อมูลควบคุมกำกับติดตามรหัส ${$scope.monthly.monthly_id} ใช่หรือไม่?`)) {
            $scope.monthly.user = $('#user').val();

            $http.post(`${CONFIG.baseUrl}/monthly/update/${$scope.monthly.monthly_id}`, $scope.monthly)
            .then(function(res) {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
                }
            }, function(err) {
                $scope.loading = false;

                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
            });
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();

        if(confirm(`คุณต้องลบแผนครุภัณฑ์รหัส ${id} ใช่หรือไม่?`)) {
            $http.delete(`${CONFIG.baseUrl}/plans/${id}`)
            .then(res => {
                console.log(res);
            }, err => {
                console.log(err);
            });
        }
    };
});