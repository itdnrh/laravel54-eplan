app.controller('planAssetCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.plan = null;
    
    $scope.isApproved = false;
    $scope.isInPlan = 'I';
    $scope.cboPrice = '';
    $scope.cboBudget = '';
    $scope.txtItemName = '';

    $scope.asset = {
        id: '',
        year: '2566', //(moment().year() + 543).toString(),
        in_plan: 'I',
        plan_no: '',
        faction_id: '',
        depart_id: '',
        division_id: '',
        item_id: '',
        desc: '',
        spec: '',
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
        reason: '',
        remark: '',
        owner: '',
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
        $scope.asset.user = data.user ? data.user.toString() : '';
        $scope.asset.faction_id = data.faction ? data.faction.toString() : '';
        $scope.asset.depart_id = data.depart ? data.depart.toString() : '';

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

    const clearAsset = function() {
        $scope.asset = {
            id: '',
            year: '2566', //(moment().year() + 543).toString(),
            in_plan: 'I',
            plan_no: '',
            faction_id: '',
            depart_id: '',
            division_id: '',
            item_id: '',
            desc: '',
            spec: '',
            price_per_unit: '',
            unit_id: '',
            amount: '',
            sum_price: '',
            request_cause: '',
            have_amount: '',
            budget_src_id: '',
            strategic_id: '1',
            service_plan_id: '',
            start_month: '',
            reason: '',
            remark: '',
            owner: '',
        };
    };

    $scope.calculateSumPrice = async function() {
        let price = $(`#price_per_unit`).val() == '' ? 0 : parseFloat($scope.currencyToNumber($(`#price_per_unit`).val()));
        let amount = $(`#amount`).val() == '' ? 0 : parseFloat($scope.currencyToNumber($(`#amount`).val()));

        $scope.asset.sum_price = price * amount;
        $('#sum_price').val(price * amount);
    };

    $scope.onShowItemsList = function() {
        $('#item_id').val('');
        $scope.asset.item_id = '';
        $scope.asset.desc = '';
        $scope.asset.price_per_unit = '';
        $scope.asset.unit_id = '';
    };

    $scope.onSelectedItem = function(event, item) {
        if (item) {
            /** Check existed data by depart */
            // let depart = $scope.asset.depart_id === '' ? 0 : $scope.asset.depart_id;
            // let division = $scope.asset.division_id === '' ? 0 : $scope.asset.division_id;

            // $http.get(`${CONFIG.apiUrl}/plans/${item.id}/${$scope.asset.year}/${depart}/${division}/existed`)
            // .then(function(res) {
            //     if (res.data.isExisted) {
            //         toaster.pop('error', "ผลการตรวจสอบ", "รายการที่คุณเลือกมีอยู่ในแผนแล้ว !!!");
            //     } else {
                    $('#item_id').val(item.id);
                    $scope.asset.item_id = item.id;
                    $scope.asset.desc = item.item_name;
                    $scope.asset.price_per_unit = item.price_per_unit;
                    $scope.asset.unit_id = item.unit_id.toString();
                    $scope.asset.have_subitem = item.have_subitem;
                    $scope.asset.calc_method = item.calc_method;

                    $('#have_subitem').val(item.have_subitem);
                    $('#calc_method').val(item.calc_method);
        //         }
        //     }, function(err) {
        //         console.log(err);
        //     });
        }

        $('#items-list').modal('hide');
    };

    $scope.getById = function(id, cb) {
        $scope.loading = true;

        $http.get(`${CONFIG.apiUrl}/assets/${id}`)
        .then(function(res) {
            cb(res.data.plan);

            $scope.loading = false;
        }, function(err) {
            console.log(err);

            $scope.loading = false;
        });
    }

    $scope.setEditControls = function(plan) {
        let { plan_item, ...rest } = plan;

        /** Set all plan's props to plan model */
        $scope.plan                     = { ...plan_item, ...rest };

        /** Set global data */
        $scope.planId                   = plan.id;
        $scope.planType                 = 1;

        /** Set ข้อมูลครุภัณฑ์ */
        $scope.asset.id                 = plan.id;
        $scope.asset.in_plan            = plan.in_plan;
        $scope.asset.year               = plan.year.toString();
        $scope.asset.plan_no            = plan.plan_no;

        $scope.asset.item_id            = plan.plan_item.item ? plan.plan_item.item_id : '';
        $scope.asset.desc               = plan.plan_item.item ? plan.plan_item.item.item_name : '';

        $scope.asset.spec               = plan.plan_item.spec;
        $scope.asset.price_per_unit     = plan.plan_item.price_per_unit;
        $scope.asset.amount             = plan.plan_item.amount;
        $scope.asset.sum_price          = plan.plan_item.sum_price;
        $scope.asset.request_cause      = plan.plan_item.request_cause;
        $scope.asset.have_amount        = plan.plan_item.have_amount;
        $scope.asset.start_month        = plan.start_month.toString();
        $scope.asset.reason             = plan.reason;
        $scope.asset.remark             = plan.remark;
        $scope.asset.approved           = plan.approved;
        $scope.asset.status             = plan.status;
        $scope.asset.is_adjust          = plan.is_adjust;

        /** Convert int value to string */
        $scope.asset.plan_type_id       = plan.plan_type_id.toString();
        $scope.asset.unit_id            = plan.plan_item.unit_id.toString();
        $scope.asset.unit               = plan.plan_item.unit;
        $scope.asset.faction_id         = plan.depart.faction_id.toString();
        $scope.asset.depart_id          = plan.depart_id.toString();
        $scope.asset.division_id        = plan.division_id ? plan.division_id.toString() : '';
        $scope.asset.budget_src_id      = plan.budget_src_id.toString();
        $scope.asset.strategic_id       = plan.strategic_id && plan.strategic_id.toString();
        $scope.asset.service_plan_id    = plan.service_plan_id && plan.service_plan_id.toString();

        $scope.asset.have_subitem       = plan.plan_item.item ? plan.plan_item.item.have_subitem : '';
        $scope.asset.calc_method        = plan.plan_item.item ? plan.plan_item.item.calc_method : '';

        if (plan.plan_item.item) {
            $('#item_id').val(plan.plan_item.item_id);
            $('#have_subitem').val(plan.plan_item.item.have_subitem);
            $('#calc_method').val(plan.plan_item.item.calc_method);
        }

        /** Generate departs and divisions data from plan */
        $scope.onFactionSelected(plan.depart.faction_id);
        $scope.onDepartSelected(plan.depart_id);
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $(`#${form}`).submit();
    }

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/assets/edit/${id}`;
    };

    $scope.update = function(event, form) {
        event.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขแผนครุภัณฑ์รหัส ${$scope.asset.id} ใช่หรือไม่?`)) {
            $(`#${form}`).submit();
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();
        
        if(confirm(`คุณต้องลบแผนครุภัณฑ์ รหัส ${id} ใช่หรือไม่?`)) {
            $scope.loading = true;

            $http.delete(`${CONFIG.baseUrl}/plans/${id}`)
            .then(res => {
                console.log(res);
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลบข้อมูลเรียบร้อย !!!");

                    /** TODO: Reset asset model */
                    $scope.setAssets(res);
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลบข้อมูลได้ !!!");
                }

                $scope.loading = false;
            }, err => {
                console.log(err);

                $scope.loading = false;
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลบข้อมูลได้ !!!");
            });
        }
    };

    $scope.setStatus = function(e, id, status) {
        e.preventDefault();
        
        if(confirm(`คุณต้องเปลี่ยนสถานะแผนครุภัณฑ์ รหัส ${id} ใช่หรือไม่?`)) {
            $scope.loading = true;

            $http.put(`${CONFIG.apiUrl}/plans/${id}/status`, { status })
            .then(res => {
                console.log(res);
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "เปลี่ยนสถานะเรียบร้อย !!!");

                    $scope.asset.status = res.data.plan.status;
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
            let budget      = $scope.cboBudget === '' ? '' : $scope.cboBudget;
            let name        = $scope.txtItemName === '' ? '' : $scope.txtItemName;
            let approved    = $scope.isApproved ? 'A' : '';
            let inPlan      = $scope.isInPlan === '' ? '' : $scope.isInPlan;

            window.location.href = `${CONFIG.baseUrl}/plans/excel?type=1&year=${year}&cate=${cate}&faction=${faction}&depart=${depart}&division=${division}&budget=${budget}&status=${status}&approved=${approved}&in_plan=${inPlan}&name=${name}&price=${price}&show_all=1`;
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

            window.location.href = `${CONFIG.baseUrl}/assets/print?type=1&year=${year}&cate=${cate}&faction=${faction}&depart=${depart}&division=${division}&budget=${budget}&status=${status}&approved=${approved}&in_plan=${inPlan}&name=${name}&price=${price}&show_all=1`;
        }
    };
});