app.controller('budgetCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.budgets = [];
    $scope.pager = null;

    $scope.summary = [];

    $scope.cboYear = '2566'; //(moment().year() + 543).toString();
    $scope.cboExpenseType = '';
    $scope.cboFaction = '';
    $scope.cboDepart = '';

    $scope.expenseBudget = '';
    $scope.expenseRemain = '';

    $scope.budget = {
        id: '',
        year: '2566',
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
    let dtpDateOptions = {
        autoclose: true,
        language: 'th',
        format: 'dd/mm/yyyy',
        thaiyear: true,
        todayBtn: true,
        todayHighlight: true
    };

    $('#remain').prop('disabled', true);

    const clearBudget = function() {
        $scope.budget = {
            id: '',
            year: '2566',
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
        let type    = $scope.cboExpenseType === '' ? '' : $scope.cboExpenseType;
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

        $http.get(`${CONFIG.apiUrl}/budgets/${year}/${expense}`)
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
        if (budget) {
            $scope.budget.id                = budget.id;
            $scope.budget.budget            = budget.budget;
            $scope.budget.remain            = budget.remain;
            $scope.budget.remark            = budget.remark;

            /** Convert int value to string */
            $scope.budget.year              = budget.year.toString();
            $scope.budget.expense_id        = budget.expense_id.toString();
            $scope.budget.expense_type_id   = budget.expense.expense_type_id.toString();
            $scope.budget.faction_id        = budget.depart.faction_id.toString();
            $scope.budget.owner_depart      = budget.owner_depart.toString();

            /** Initial model values in mainCtrl */
            $scope.onFilterExpenses(budget.expense.expense_type_id)
            $scope.onFactionSelected(budget.depart.faction_id)

            $('#expense_type_id').val(budget.expense.expense_type_id).trigger('change.select2')
            $('#faction_id').val(budget.depart.faction_id).trigger('change.select2')
        }
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

                window.location.href = `${CONFIG.baseUrl}/budgets/list`;
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อพิดผลาด ไม่สามารถบันทึกข้อมูลได้ !!!");
            }
        }, function(err) {
            $scope.loading = false;

            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "พบข้อพิดผลาด ไม่สามารถบันทึกข้อมูลได้ !!!");
        });
    }

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/budgets/edit/${id}`;
    };

    $scope.update = function(event, form) {
        event.preventDefault();
        console.log($scope.budget);

        $scope.loading = true;
        $scope.budget.user = $('#user').val();

        if(confirm(`คุณต้องแก้ไขประมาณการรายจ่ายรหัส ${$scope.budget.id} ใช่หรือไม่?`)) {
            $scope.budget.user = $('#user').val();

            $http.put(`${CONFIG.apiUrl}/budgets/${$scope.budget.id}`, $scope.budget)
            .then(function(res) {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/budgets/list`;
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อพิดผลาด ไม่สามารถแก้ไขข้อมูลได้ !!!");
                }
            }, function(err) {
                $scope.loading = false;

                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อพิดผลาด ไม่สามารถแก้ไขข้อมูลได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();

        $scope.loading = true;

        if(confirm(`คุณต้องประมาณการรายจ่ายรหัส ${id} ใช่หรือไม่?`)) {
            $http.delete(`${CONFIG.apiUrl}/budgets/${id}`)
            .then(res => {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลบข้อมูลเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/budgets/list`;
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อพิดผลาด ไม่สามารถลบข้อมูลได้ !!!");
                }
            }, function(err) {
                $scope.loading = false;
                
                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อพิดผลาด ไม่สามารถลบข้อมูลได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };
});