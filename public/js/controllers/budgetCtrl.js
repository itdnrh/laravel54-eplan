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
        id: '',
        year: '',
        expense_type_id: '',
        expense_id: '',
        budget: '',
        remain: '',
        faction_id: '',
        owner_depart: '',
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
            id: '',
            year: '',
            expense_type_id: '',
            expense_id: '',
            budget: '',
            remain: '',
            faction_id: '',
            owner_depart: '',
            remark: '',
            user: '',
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

            $scope.budget.remain = res.data.plan.remain;

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
        $http.get(`${CONFIG.apiUrl}/budgets/${id}`)
        .then(function(res) {
            cb(res.data.budget);
        }, function(err) {
            console.log(err);
        });
    }

    $scope.setEditControls = function(budget) {
        $scope.budget.id                = budget.id;
        $scope.budget.budget            = budget.budget;
        $scope.budget.remain            = budget.remain;
        $scope.budget.remark            = budget.remark;
        
        /** Convert int value to string */
        $scope.budget.year              = budget.year.toString();
        $scope.budget.expense_id        = budget.expense_id.toString();
        $scope.budget.expense_type_id   = budget.expense_type_id.toString();
        $scope.budget.faction_id        = budget.faction_id;
        $scope.budget.owner_depart      = budget.owner_depart;
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $scope.loading = true;
        $scope.budget.user = $('#user').val();

        $http.post(`${CONFIG.baseUrl}/budgets/store`, $scope.budget)
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
        window.location.href = `${CONFIG.baseUrl}/budgets/edit/${id}`;
    };

    $scope.update = function(event) {
        event.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขข้อมูลควบคุมกำกับติดตามรหัส ${$scope.budget.id} ใช่หรือไม่?`)) {
            $scope.budget.user = $('#user').val();

            $http.post(`${CONFIG.baseUrl}/budget/update/${$scope.budget.budget_id}`, $scope.budget)
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