app.controller('delegationCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.cboYear = '';

    $scope.delegations = [];
    $scope.pager = null;

    $scope.delegation = {
        id: '',
        delegator_id: '',
        authorizer_id: '',
        faction_id: '',
        depart_id: '',
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

    const clearDelegation = function() {
        $scope.delegation = {
            id: '',
            delegator_id: '',
            authorizer_id: '',
            faction_id: '',
            depart_id: '',
            remark: '',
        };
    };

    $scope.getDelegations = function(event) {
        $scope.loading = true;
        $scope.delegations = [];
        $scope.pager = null;

        let faction = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let name = $scope.txtExpenseName === '' ? '' : $scope.txtExpenseName;

        $http.get(`${CONFIG.baseUrl}/delegations/search?faction=${faction}&depart=${depart}&name=${name}`)
        .then(function(res) {
            $scope.setDelegations(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setDelegations = function(res) {
        const { data, ...pager } = res.data.delegations;

        $scope.delegations = data;
        $scope.pager = pager;
    };

    $scope.getDelegationsWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.delegations = [];
        $scope.pager = null;

        let faction = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let name  = $scope.txtExpenseName === '' ? '' : $scope.txtExpenseName;

        $http.get(`${url}&faction=${faction}&depart=${depart}&name=${name}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getById = function(id, cb) {
        $http.get(`${CONFIG.apiUrl}/delegations/${id}`)
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

        $http.post(`${CONFIG.baseUrl}/delegations/store`, $scope.expense)
        .then((res) => {
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");

                window.location.href = `${CONFIG.baseUrl}/system/delegations`;
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
        window.location.href = `${CONFIG.baseUrl}/delegations/edit/${id}`;
    };

    $scope.update = function(event) {
        event.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขรายจ่าย รหัส ${$scope.expense.id} ใช่หรือไม่?`)) {
            $scope.expense.user = $('#user').val();
            $scope.loading = true;

            $http.post(`${CONFIG.baseUrl}/delegations/update/${$scope.expense.id}`, $scope.expense)
            .then((res) => {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/system/delegations`;
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

        if(confirm(`คุณต้องลบรายจ่าย รหัส ${id} ใช่หรือไม่?`)) {
            $scope.loading = false;

            $http.post(`${CONFIG.baseUrl}/delegations/delete/${id}`)
            .then(res => {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลบข้อมูลเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/system/delegations`;
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