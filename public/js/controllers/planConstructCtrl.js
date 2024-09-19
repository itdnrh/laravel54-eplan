app.controller('planConstructCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.plan = null;

    $scope.isApproved = false;
    $scope.isInPlan = 'I';
    $scope.cboBudget = '';
    $scope.cboPrice = '';
    $scope.txtItemName = '';

    $scope.construct = {
        id: '',
        in_plan: 'I',
        year: '2568', //(moment().year() + 543).toString(),
        plan_no: '',
        faction_id: '',
        depart_id: '',
        division_id: '',
        item_id: '',
        desc: '',
        location: '',
        building_id: '',
        boq_no: '',
        boq_file: '',
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
        building: null,
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

    $('#doc_date')
        .datepicker(dtpDateOptions)
        .datepicker('update', new Date());
        // .on('show', function (e) {
        //     $('.day').click(function(event) {
        //         event.preventDefault();
        //         event.stopPropagation();
        //     });
        // });

    $scope.setUserInfo = function(data) {
        $scope.construct.user = data.user ? data.user.toString() : '';
        $scope.construct.faction_id = data.faction ? data.faction.toString() : '';
        $scope.construct.depart_id = data.depart ? data.depart.toString() : '';

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

    $scope.clearConstruct = function() {
        $scope.construct = {
            id: '',
            in_plan: 'I',
            year: '2568', //(moment().year() + 543).toString(),
            plan_no: '',
            faction_id: '',
            depart_id: '',
            division_id: '',
            item_id: '',
            desc: '',
            location: '',
            building_id: '',
            boq_no: '',
            boq_file: '',
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
            building: null,
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

        $scope.construct.sum_price = price * amount;
        $('#sum_price').val(price * amount);
    };

    $scope.onSelectedItem = function(event, item) {
        if (item) {
            /** Check existed data by depart */
            let depart = $scope.construct.depart_id === '' ? 0 : $scope.construct.depart_id;

            // $http.get(`${CONFIG.apiUrl}/plans/${item.id}/${$scope.construct.year}/${depart}/existed`)
            // .then(function(res) {
            //     if (res.data.isExisted) {
            //         toaster.pop('error', "ผลการตรวจสอบ", "รายการที่คุณเลือกมีอยู่ในแผนแล้ว !!!");
            //     } else {
                    $('#item_id').val(item.id);
                    $scope.construct.item_id = item.id;
                    $scope.construct.desc = item.item_name;
                    $scope.construct.price_per_unit = item.price_per_unit;
                    $scope.construct.unit_id = item.unit_id.toString();
                    $scope.construct.have_subitem = item.have_subitem;
                    $scope.construct.calc_method = item.calc_method;

                    $('#have_subitem').val(item.have_subitem);
                    $('#calc_method').val(item.calc_method);
            //     }
            // }, function(err) {
            //     console.log(err);
            // });
        }

        $('#items-list').modal('hide');
    };

    $scope.getById = function(id, cb) {
        $http.get(`${CONFIG.apiUrl}/constructs/${id}`)
        .then(function(res) {
            cb(res.data.plan);
        }, function(err) {
            console.log(err);
        });
    };

    $scope.setEditControls = function(plan) {
        if (plan) {
            console.log(plan);
            let { plan_item, ...rest } = plan;

            /** Set all plan's props to plan model */
            $scope.plan                         = { ...plan_item, ...rest };

            /** Set global data */
            $scope.planId                       = plan.id;
            $scope.planType                     = 1;

            /** Set ข้อมูลงานก่อสร้าง */
            $scope.construct.id                 = plan.id;
            $scope.construct.in_plan            = plan.in_plan;
            $scope.construct.year               = plan.year.toString();
            // $scope.construct.plan_no            = plan.plan_no;

            $scope.construct.item_id            = plan.plan_item.item ? plan.plan_item.item_id : '';
            $scope.construct.desc               = plan.plan_item.item ? plan.plan_item.item.item_name : '';

            $scope.construct.location           = plan.plan_item.location;
            $scope.construct.building_id        = plan.plan_item.building_id ? plan.plan_item.building_id.toString() : '';
            $scope.construct.boq_no             = plan.plan_item.boq_no;
            $scope.construct.boq_file           = plan.plan_item.boq_file;

            $scope.construct.price_per_unit     = plan.plan_item.price_per_unit;
            $scope.construct.amount             = plan.plan_item.amount;
            $scope.construct.sum_price          = plan.plan_item.sum_price;
            $scope.construct.request_cause      = plan.plan_item.request_cause;
            $scope.construct.start_month        = plan.start_month.toString();
            $scope.construct.reason             = plan.reason;
            $scope.construct.remark             = plan.remark;
            $scope.construct.approved           = plan.approved;
            $scope.construct.status             = plan.status;
            $scope.construct.is_adjust          = plan.is_adjust;

            /** Set value to object props */
            $scope.construct.building           = plan.plan_item.building;
            $scope.construct.item               = plan.plan_item.item;
            $scope.construct.unit               = plan.plan_item.unit;
            $scope.construct.budgetSrc          = plan.budget;
            $scope.construct.faction            = plan.depart.faction;
            $scope.construct.depart             = plan.depart;
            $scope.construct.division           = plan.division && plan.division;
            $scope.construct.strategic          = plan.strategic && plan.strategic;
            $scope.construct.servicePlan        = plan.service_plan && plan.service_plan;

            /** Convert int value to string */
            $scope.construct.plan_type_id       = plan.plan_type_id.toString();
            $scope.construct.unit_id            = plan.plan_item.unit_id.toString();
            $scope.construct.faction_id         = plan.depart.faction_id.toString();
            $scope.construct.depart_id          = plan.depart_id.toString();
            $scope.construct.division_id        = plan.division_id ? plan.division_id.toString() : '';
            $scope.construct.budget_src_id      = plan.budget_src_id.toString();
            $scope.construct.strategic_id       = plan.strategic_id && plan.strategic_id.toString();
            $scope.construct.service_plan_id    = plan.service_plan_id && plan.service_plan_id.toString();
            
            $scope.construct.have_subitem       = plan.plan_item.item ? plan.plan_item.item.have_subitem : '';
            $scope.construct.calc_method        = plan.plan_item.item ? plan.plan_item.item.calc_method : '';

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
    };

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/constructs/edit/${id}`;
    };

    $scope.update = function(event, form) {
        event.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขแผนก่อสร้าง รหัส ${$scope.construct.id} ใช่หรือไม่?`)) {
            $(`#${form}`).submit();
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();
        $scope.loading = true;

        if(confirm(`คุณต้องลบแผนก่อสร้าง รหัส ${id} ใช่หรือไม่?`)) {
            $http.delete(`${CONFIG.baseUrl}/plans/${id}`)
            .then(res => {
                console.log(res);
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลบข้อมูลเรียบร้อย !!!");

                    /** TODO: Reset construct model */
                    $scope.setConstructs(res);
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
        
        if(confirm(`คุณต้องเปลี่ยนสถานะแผนก่อสร้าง รหัส ${id} ใช่หรือไม่?`)) {
            $scope.loading = true;

            $http.put(`${CONFIG.apiUrl}/plans/${id}/status`, { status })
            .then(res => {
                console.log(res);
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "เปลี่ยนสถานะเรียบร้อย !!!");

                    $scope.construct.status = res.data.plan.status;
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

            window.location.href = `${CONFIG.baseUrl}/plans/excel?type=4&year=${year}&cate=${cate}&faction=${faction}&depart=${depart}&division=${division}&status=${status}&approved=${approved}&in_plan=${inPlan}&name=${name}&price=${price}&show_all=1`;
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

            window.location.href = `${CONFIG.baseUrl}/constructs/print?type=4&year=${year}&cate=${cate}&faction=${faction}&depart=${depart}&division=${division}&budget=${budget}&status=${status}&approved=${approved}&in_plan=${inPlan}&name=${name}&price=${price}&show_all=1`;
        }
    };
});