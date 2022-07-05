app.controller('expenseCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.cboYear = '';
    $scope.cboExpenseType = '';
    $scope.txtExpenseName = '';

    $scope.expenses = [];
    $scope.pager = null;

    $scope.expense = {
        id: '',
        name: '',
        expense_type_id: '',
        faction_id: '',
        owner_depart: '',
        sort: '',
        remark: '',
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
    //     .datepicker('update', new Date());
        // .on('show', function (e) {
        //     $('.day').click(function(event) {
        //         event.preventDefault();
        //         event.stopPropagation();
        //     });
        // });

    const clearExpense = function() {
        $scope.expense = {
            id: '',
            name: '',
            expense_type_id: '',
            faction_id: '',
            owner_depart: '',
            sort: '',
            remark: '',
        };
    };

    $scope.getAll = function(event) {
        $scope.loading = true;
        $scope.expenses = [];
        $scope.pager = null;

        let type = $scope.cboExpenseType === '' ? '' : $scope.cboExpenseType;
        let name = $scope.txtExpenseName === '' ? '' : $scope.txtExpenseName;

        $http.get(`${CONFIG.apiUrl}/expenses?type=${type}&name=${name}`)
        .then(function(res) {
            $scope.setExpenses(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setExpenses = function(res) {
        const { data, ...pager } = res.data.expenses;

        $scope.expenses = data;
        $scope.pager = pager;
    };

    $scope.getDataWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.expenses = [];
        $scope.pager = null;

        let type = $scope.cboExpenseType === '' ? '' : $scope.cboExpenseType;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let name  = $scope.txtExpenseName === '' ? '' : $scope.txtExpenseName;

        $http.get(`${url}&type=${type}&status=${status}&name=${name}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getById = function(id, cb) {
        $http.get(`${CONFIG.apiUrl}/expenses/${id}`)
        .then(function(res) {
            cb(res.data.expense);
        }, function(err) {
            console.log(err);
        });
    }

    $scope.setEditControls = function(expense) {
        $scope.expense.id               = expense.id;
        $scope.expense.name             = expense.name;
        $scope.expense.remark           = expense.remark;
        $scope.expense.status           = expense.status;
        $scope.expense.depart           = expense.depart;
        $scope.expense.expense_type     = expense.expense_type;

        /** Convert int value to string */
        $scope.expense.expense_type_id  = expense.expense_type_id.toString();
        $scope.expense.owner_depart     = expense.owner_depart && expense.owner_depart.toString();
        $scope.expense.faction_id       = expense.depart && expense.depart.faction_id.toString();

        /** Generate departs and divisions data from plan */
        if (expense.owner_depart) {
            $scope.onFactionSelected(plan.depart.faction_id);
        }
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $scope.loading = true;
        $scope.expense.user = $('#user').val();

        $http.post(`${CONFIG.apiUrl}/expenses`, $scope.expense)
        .then((res) => {
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");

                window.location.href = `${CONFIG.baseUrl}/system/expenses`;
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
        window.location.href = `${CONFIG.baseUrl}/expenses/edit/${id}`;
    };

    $scope.update = function(event) {
        event.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขรายจ่าย รหัส ${$scope.expense.id} ใช่หรือไม่?`)) {
            $scope.expense.user = $('#user').val();

            $http.post(`${CONFIG.baseUrl}/expenses/update/${$scope.expense.id}`, $scope.expense)
            .then((res) => {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/system/expenses`;
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถแก้ไขข้อมูลได้ !!!");
                }
            }, function(err) {
                $scope.loading = false;

                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถแก้ไขข้อมูลได้ !!!");
            });
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();

        if(confirm(`คุณต้องลบรายจ่าย รหัส ${id} ใช่หรือไม่?`)) {
            $http.delete(`${CONFIG.baseUrl}/expenses/${id}`)
            .then(res => {
                console.log(res);
            }, err => {
                console.log(err);
            });
        }
    };
});