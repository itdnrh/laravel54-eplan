app.controller('planServiceCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
    /*
    |-----------------------------------------------------------------------------
    | Local variables and constraints initialization
    |-----------------------------------------------------------------------------
    */
    /** Filtering input controls */
    $scope.isApproved = false;
    $scope.isInPlan = 'I';
    $scope.cboBudget = '';
    $scope.cboPrice = '';
    $scope.txtItemName = '';

    $scope.plan = null;

    $scope.service = {
        id: '',
        in_plan: 'I',
        year: '2568', //(moment().year() + 543).toString(),
        plan_no: '',
        faction_id: '',
        depart_id: '',
        division_id: '',
        item_id: '',
        desc: '',
        price_per_unit: '',
        unit_id: '',
        amount: '',
        sum_price: '',
        request_cause: '',
        have_amount: '',
        budget_src_id: '1',
        strategic_id: '',
        service_plan_id: '',
        start_month: '',
        have_subitem: 0,
        calc_method: 1,
        is_addon: false,
        addon_id: '',
        reason: '',
        remark: '',
        item: null,
        unit: null,
        faction: null,
        depart: null,
        division: null,
        budgetSrc: null,
        strategic: null,
        servicePlan: null,
        addon_detail: null,
    };

    /** DatePicker options */
    let dtpDateOptions = {
        autoclose: true,
        language: 'th',
        format: 'dd/mm/yyyy',
        thaiyear: true,
        todayBtn: true,
        todayHighlight: true
    };

    /*
    |-----------------------------------------------------------------------------
    | Form controls initialization
    |-----------------------------------------------------------------------------
    */
    /** ============================ DatePicker initialization ============================ */
    $('#doc_date')
        .datepicker(dtpDateOptions)
        .datepicker('update', new Date());
        // .on('show', function (e) {
        //     $('.day').click(function(event) {
        //         event.preventDefault();
        //         event.stopPropagation();
        //     });
        // });

    /*
    |-----------------------------------------------------------------------------
    | Local methods initialization
    |-----------------------------------------------------------------------------
    */
    $scope.setUserInfo = function(data) {
        $scope.service.user = data.user ? data.user.toString() : '';
        $scope.service.faction_id = data.faction ? data.faction.toString() : '';
        $scope.service.depart_id = data.depart ? data.depart.toString() : '';

        $scope.onFactionSelected(data.faction);
        $scope.onDepartSelected(data.depart);
    };

    $scope.initFiltered = () => {
        if ($('#duty').val() == '1') {
            let faction = $('#faction').val();
    
            $scope.cboFaction = faction;
            $scope.onFactionSelected(faction);
        }
    };

    $scope.clearService = function() {
        $scope.service = {
            id: '',
            in_plan: 'I',
            year: '2568', //(moment().year() + 543).toString(),
            plan_no: '',
            faction_id: '',
            depart_id: '',
            division_id: '',
            item_id: '',
            desc: '',
            price_per_unit: '',
            unit_id: '',
            amount: '',
            sum_price: '',
            request_cause: '',
            have_amount: '',
            budget_src_id: '1',
            strategic_id: '',
            service_plan_id: '',
            start_month: '',
            have_subitem: 0,
            calc_method: 1,
            is_addon: false,
            addon_id: '',
            reason: '',
            remark: '',
            item: null,
            unit: null,
            faction: null,
            depart: null,
            division: null,
            budgetSrc: null,
            strategic: null,
            servicePlan: null,
            addon_detail: null,
        };
    };

    $scope.calculateSumPrice = async function() {
        let price = $(`#price_per_unit`).val() == '' ? 0 : parseFloat($scope.currencyToNumber($(`#price_per_unit`).val()));
        let amount = $(`#amount`).val() == '' ? 0 : parseFloat($scope.currencyToNumber($(`#amount`).val()));

        $scope.service.sum_price = price * amount;
        $('#sum_price').val(price * amount);
    };

    /*
    |-----------------------------------------------------------------------------
    | Plan service CRUD operations
    |-----------------------------------------------------------------------------
    */
    $scope.getById = function(id, cb) {
        $http.get(`${CONFIG.apiUrl}/services/${id}`)
        .then(function(res) {
            cb(res.data.plan);
        }, function(err) {
            console.log(err);
        });
    }

    $scope.setEditControls = function(plan) {
        if (plan) {
            let { plan_item, ...rest } = plan;

            /** Set all plan's props to plan model */
            $scope.plan                     = { ...plan_item, ...rest };

            /** Set global data */
            $scope.planId                   = plan.id;
            $scope.planType                 = 3;

            /** Set ข้อมูลจ้างบริการ */
            $scope.service.id               = plan.id;
            $scope.service.in_plan          = plan.in_plan;
            $scope.service.year             = plan.year.toString();
            // $scope.service.plan_no          = plan.plan_no;

            $scope.service.item_id          = plan.plan_item.item ? plan.plan_item.item_id : '';
            $scope.service.desc             = plan.plan_item.item ? plan.plan_item.item.item_name : '';

            $scope.service.price_per_unit   = plan.plan_item.price_per_unit;
            $scope.service.amount           = plan.plan_item.amount;
            $scope.service.sum_price        = plan.plan_item.sum_price;
            $scope.service.request_cause    = plan.plan_item.request_cause;
            $scope.service.have_amount      = plan.plan_item.have_amount;
            $scope.service.start_month      = plan.start_month.toString();
            $scope.service.reason           = plan.reason;
            $scope.service.remark           = plan.remark;
            $scope.service.approved         = plan.approved;
            $scope.service.status           = plan.status;
            $scope.service.is_adjust        = plan.is_adjust;

            /** Set value to object props */
            $scope.service.item            = plan.plan_item.item;
            $scope.service.unit            = plan.plan_item.unit;
            $scope.service.budgetSrc       = plan.budget;
            $scope.service.faction         = plan.depart.faction;
            $scope.service.depart          = plan.depart;
            $scope.service.division        = plan.division && plan.division;
            $scope.service.strategic       = plan.strategic && plan.strategic;
            $scope.service.servicePlan     = plan.service_plan && plan.service_plan;

            /** Convert int value to string */
            $scope.service.plan_type_id     = plan.plan_type_id.toString();
            $scope.service.unit_id          = plan.plan_item.unit_id.toString();
            $scope.service.faction_id       = plan.depart.faction_id.toString();
            $scope.service.depart_id        = plan.depart_id.toString();
            $scope.service.division_id      = plan.division_id ? plan.division_id.toString() : '';
            $scope.service.budget_src_id    = plan.budget_src_id.toString();
            $scope.service.strategic_id     = plan.strategic_id && plan.strategic_id.toString();
            $scope.service.service_plan_id  = plan.service_plan_id && plan.service_plan_id.toString();

            $scope.service.have_subitem     = plan.plan_item.item ? plan.plan_item.item.have_subitem : '';
            $scope.service.calc_method      = plan.plan_item.item ? plan.plan_item.item.calc_method : '';

            if (plan.plan_item.item) {
                $('#item_id').val(plan.plan_item.item_id);
                $('#have_subitem').val(plan.plan_item.item.have_subitem);
                $('#calc_method').val(plan.plan_item.item.calc_method);
            }

            /** Generate departs and divisions data from plan */
            $scope.onFactionSelected(plan.depart.faction_id);
            $scope.onDepartSelected(plan.depart_id);
        }
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $(`#${form}`).submit();
    }

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/services/edit/${id}`;
    };

    $scope.update = function(event, form) {
        event.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขแผนจ้างบริการ รหัส ${$scope.service.id} ใช่หรือไม่?`)) {
            $(`#${form}`).submit();
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();
        $scope.loading = true;

        if(confirm(`คุณต้องลบแผนจ้างบริการ รหัส ${id} ใช่หรือไม่?`)) {
            $http.delete(`${CONFIG.baseUrl}/plans/${id}`)
            .then(res => {
                console.log(res);
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลบข้อมูลเรียบร้อย !!!");

                    /** TODO: Reset service model */
                    $scope.setServices(res);
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลบข้อมูลได้ !!!");
                }

                $scope.loading = false;
            }, err => {
                console.log(err);

                $scope.loading = false;
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลบข้อมูลได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.setStatus = function(e, id, status) {
        e.preventDefault();
        
        if(confirm(`คุณต้องเปลี่ยนสถานะแผนจ้างบริการ รหัส ${id} ใช่หรือไม่?`)) {
            $scope.loading = true;

            $http.put(`${CONFIG.apiUrl}/plans/${id}/status`, { status })
            .then(res => {
                console.log(res);
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "เปลี่ยนสถานะเรียบร้อย !!!");

                    $scope.service.status = res.data.plan.status;
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถเปลี่ยนสถานะได้ !!!");
                }

                $scope.loading = false;
            }, err => {
                console.log(err);

                $scope.loading = false;
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถเปลี่ยนสถานะได้ !!!");
            });
        }
    };

    /*
    |-----------------------------------------------------------------------------
    | Plan selection processes
    |-----------------------------------------------------------------------------
    */
    $scope.onSelectedPlan = (e, plan) => {
        if (plan) {
            $scope.service.addon_detail = plan;
            $scope.service.addon_id     = plan.id;
        }

        $('#plans-list').modal('hide');
    };

    /*
    |-----------------------------------------------------------------------------
    | Item selection operations
    |-----------------------------------------------------------------------------
    */
    $scope.onSelectedItem = function(event, item) {
        if (item) {
            /** Check existed data by depart */
            let depart = $scope.service.depart_id === '' ? 0 : $scope.service.depart_id;

            // $http.get(`${CONFIG.apiUrl}/plans/${item.id}/${$scope.service.year}/${depart}/existed`)
            // .then(function(res) {
            //     if (res.data.isExisted) {
            //         toaster.pop('error', "ผลการตรวจสอบ", "รายการที่คุณเลือกมีอยู่ในแผนแล้ว !!!");
            //     } else {
                    $('#item_id').val(item.id);
                    $scope.service.item_id = item.id;
                    $scope.service.desc = item.item_name;
                    $scope.service.price_per_unit = item.price_per_unit;
                    $scope.service.unit_id = item.unit_id.toString();
                    $scope.service.have_subitem = item.have_subitem;
                    $scope.service.calc_method = item.calc_method;
                    $scope.service.is_addon = item.is_addon === 1;

                    $('#have_subitem').val(item.have_subitem);
                    $('#calc_method').val(item.calc_method);
            //     }
            // }, function(err) {
            //     console.log(err);
            // });
        }

        $('#items-list').modal('hide');
    };

    /*
    |-----------------------------------------------------------------------------
    | Export data operations
    |-----------------------------------------------------------------------------
    */
    $scope.exportListToExcel = function(e) {
        e.preventDefault();

        if($scope.plans.length == 0) {
            toaster.pop('warning', "", "ไม่พบข้อมูล !!!");
        } else {
            let year        = $scope.cboYear === '' ? '' : $scope.cboYear;
            let cate        = $scope.cboCategory === '' ? '' : $scope.cboCategory;
            let faction     = $scope.cboFaction === '' ? '' : $scope.cboFaction;
            let depart      = !$scope.cboDepart ? '' : $scope.cboDepart;
            let division    = !$scope.cboDivision ? '' : $scope.cboDivision;
            let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
            let price       = $scope.cboPrice === '' ? '' : $scope.cboPrice;
            let name        = $scope.txtItemName === '' ? '' : $scope.txtItemName;
            let approved    = $scope.isApproved ? 'A' : '';
            let inPlan      = $scope.isInPlan === '' ? '' : $scope.isInPlan;

            window.location.href = `${CONFIG.baseUrl}/plans/excel?type=3&year=${year}&cate=${cate}&faction=${faction}&depart=${depart}&division=${division}&status=${status}&approved=${approved}&in_plan=${inPlan}&name=${name}&price=${price}&show_all=1`;
        }
    };

    $scope.exportListToPdf = function(e) {
        e.preventDefault();

        if($scope.plans.length == 0) {
            toaster.pop('warning', "", "ไม่พบข้อมูล !!!");
        } else {
            let year        = $scope.cboYear === '' ? '' : $scope.cboYear;
            let cate        = $scope.cboCategory === '' ? '' : $scope.cboCategory;
            let faction     = $scope.cboFaction === '' ? '' : $scope.cboFaction;
            let depart      = !$scope.cboDepart ? '' : $scope.cboDepart;
            let division    = !$scope.cboDivision ? '' : $scope.cboDivision;
            let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
            let price       = $scope.cboPrice === '' ? '' : $scope.cboPrice;
            let budget      = $scope.cboBudget === '' ? '' : $scope.cboBudget;
            let name        = $scope.txtItemName === '' ? '' : $scope.txtItemName;
            let approved    = $scope.isApproved ? 'A' : '';
            let inPlan      = $scope.isInPlan === '' ? '' : $scope.isInPlan;

            window.location.href = `${CONFIG.baseUrl}/services/print?type=3&year=${year}&cate=${cate}&faction=${faction}&depart=${depart}&division=${division}&budget=${budget}&status=${status}&approved=${approved}&in_plan=${inPlan}&name=${name}&price=${price}&show_all=1`;
        }
    };
});