app.controller('budgetCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.budgets = [];
    $scope.pager = null;

    $scope.summary = [];

    $scope.cboYear = '2568'; //(moment().year() + 543).toString();
    $scope.cboExpenseType = '';
    $scope.cboFaction = '';
    $scope.cboDepart = '';

    $scope.expenseBudget = '';
    $scope.expenseRemain = '';

    $scope.budget = {
        id: '',
        year: '2568',
        expense_type_id: '',
        expense_id: '',
        plan_id: '',
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
            year: '2568',
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

    /*
    |-----------------------------------------------------------------------------
    | Plan selection processes
    |-----------------------------------------------------------------------------
    */
    $scope.showPlansList = () => {
        if (!$scope.budget.expense_id) {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณาเลือกประเภทแผนและประเภทพัสดุก่อน !!!");
            return;
        }

        $scope.getPlans("", true);
    };

    /** TODO: shold reflactor this method to be global method */
    $scope.getPlans = (status, toggleModal=false) => {
        $scope.loading = true;
        $scope.handleInputChange("plans", []);
        $scope.handleInputChange("plans_pager", null);

        let type = $scope.budget.expense_id === '' ? 1 : $scope.budget.expense_id;
        let cate = '';
        let name = $scope.txtKeyword == '' ? '' : $scope.txtKeyword;
        let depart = ($('#user').val() == '1300200009261' || $('#depart_id').val() == 4 || $('#duty_id').val() == 1) 
                            ? $scope.cboDepart
                            : $('#depart_id').val();

        $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&cate=${cate}&name=${name}&depart=${depart}&status=${status}&approved=A`)
        .then(function(res) {
            if (toggleModal) $('#plans-list').modal('show');

            $scope.setPlans(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    /** TODO: shold reflactor this method to be global method */
    $scope.getPlansWithUrl = function(e, url, status, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.handleInputChange("plans", []);
        $scope.handleInputChange("plans_pager", null);

        let type = $scope.support.plan_type_id === '' ? 1 : $scope.support.plan_type_id;
        let cate = $scope.support.category_id === '' ? '' : $scope.support.category_id;
        let name = $scope.txtKeyword == '' ? '' : $scope.txtKeyword;
        let depart = ($('#user').val() == '1300200009261' || $('#depart_id').val() == 4 || $('#duty_id').val() == 1) 
                            ? $scope.cboDepart
                            : $('#depart_id').val();

        $http.get(`${url}&type=${type}&cate=${cate}&name=${name}&depart=${depart}&status=${status}&approved=A`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.onSelectedPlan = (e, plan) => {
        if (plan) {
            $scope.newItem.plan         = plan;
            $scope.newItem.plan_id      = plan.id;
            $scope.newItem.item_id      = plan.plan_item.item_id;
            $scope.newItem.item         = plan.plan_item.item;
            $scope.newItem.subitem_id   = '';
            $scope.newItem.desc         = '';
            $scope.newItem.price_per_unit = plan.plan_item.calc_method == 1 ? plan.plan_item.price_per_unit : '';
            $scope.newItem.unit_id      = plan.plan_item.calc_method == 1 ? plan.plan_item.unit_id.toString() : '';
            $scope.newItem.unit_name    = plan.plan_item.calc_method == 1 ? plan.plan_item.unit.name : '';
            $scope.newItem.amount       = plan.plan_item.calc_method == 1 ? plan.plan_item.remain_amount : 1;
            $scope.newItem.sum_price    = plan.plan_item.calc_method == 1 ? plan.plan_item.remain_budget : '';
            $scope.newItem.planItem     = plan.plan_item;

            if (plan.plan_item.calc_method == 1) {
                $('#unit_id').val(plan.plan_item.unit_id).trigger("change.select2");
            }
        }

        $('#plans-list').modal('hide');
    };

    /*
    |-----------------------------------------------------------------------------
    | Budget CRUD operations
    |-----------------------------------------------------------------------------
    */
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

    $scope.expenseTypes = [];
    $scope.setBudgets = function(res) {
        // const { data, ...pager } = res.data.budgets;

        $scope.expenseTypes = res.data.expenseTypes.map(type => {
            const budgetsList = res.data.budgets
                                    .filter(budget => budget.expense.expense_type_id === type.id)
                                    .sort((a, b) => a.expense.sort - b.expense.sort);

            type.budgets = budgetsList ? budgetsList : [];

            return type;
        });

        // $scope.pager = pager;
    };

    $scope.getBudgetsWithUrl = function(e, url, cb) {
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