app.controller('planServiceCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.services = [];
    $scope.pager = [];

    $scope.isApproved = false;
    $scope.txtPrice = '';

    $scope.service = {
        service_id: '',
        in_plan: 'I',
        year: '2566', //(moment().year() + 543).toString(),
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
        reason: '',
        remark: ''
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

    $scope.setIsApproved = function(e) {
        $scope.isApproved = e.target.checked;

        $scope.getAll(e);
    };

    $scope.clearService = function() {
        $scope.service = {
            service_id: '',
            in_plan: 'I',
            year: '2566', //(moment().year() + 543).toString(),
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
            reason: '',
            remark: ''
        };
    };

    $scope.calculateSumPrice = async function() {
        let price = $(`#price_per_unit`).val() == '' ? 0 : parseFloat($(`#price_per_unit`).val());
        let amount = $(`#amount`).val() == '' ? 0 : parseFloat($(`#amount`).val());

        $scope.service.sum_price = price * amount;
        $('#sum_price').val(price * amount);
    };

    $scope.getAll = function(event) {
        $scope.loading = true;
        $scope.services = [];
        $scope.pager = null;

        let year        = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate        = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let faction     = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart      = !$scope.cboDepart ? '' : $scope.cboDepart;
        let division    = !$scope.cboDivision ? '' : $scope.cboDivision;
        let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let price       = $scope.txtPrice === '' ? '' : $scope.txtPrice;
        let approved    = $scope.isApproved ? 'A' : '';

        $http.get(`${CONFIG.baseUrl}/plans/search?type=3&year=${year}&cate=${cate}&faction=${faction}&depart=${depart}&division=${division}&status=${status}&approved=${approved}&price=${price}&show_all=1`)
        .then(function(res) {
            $scope.setServices(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setServices = function(res) {
        const { data, ...pager } = res.data.plans;

        $scope.services = data;
        $scope.pager = pager;
    };

    $scope.getDataWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.services = [];
        $scope.pager = null;

        let year        = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate        = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let faction     = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart      = !$scope.cboDepart ? '' : $scope.cboDepart;
        let division    = !$scope.cboDivision ? '' : $scope.cboDivision;
        let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let price       = $scope.txtPrice === '' ? '' : $scope.txtPrice;
        let approved    = $scope.isApproved ? 'A' : '';

        $http.get(`${url}&type=3&year=${year}&cate=${cate}&faction=${faction}&depart=${depart}&division=${division}&status=${status}&approved=${approved}&price=${price}&show_all=1`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

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
        $http.get(`${CONFIG.apiUrl}/services/${id}`)
        .then(function(res) {
            cb(res.data.plan);
        }, function(err) {
            console.log(err);
        });
    }

    $scope.setEditControls = function(plan) {
        /** Global data */
        $scope.planId                   = plan.id;
        $scope.planType                 = 3;

        /** ข้อมูลจ้างบริการ */
        $scope.service.service_id       = plan.id;
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

        /** Convert int value to string */
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
    
        if(confirm(`คุณต้องแก้ไขแผนจ้างบริการรหัส ${$scope.service.service_id} ใช่หรือไม่?`)) {
            $(`#${form}`).submit();
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();
        $scope.loading = true;

        if(confirm(`คุณต้องลบแผนครุภัณฑ์รหัส ${id} ใช่หรือไม่?`)) {
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

    $scope.exportListToExcel = function(e) {
        e.preventDefault();

        if($scope.services.length == 0) {
            toaster.pop('warning', "", "ไม่พบข้อมูล !!!");
        } else {
            let year        = $scope.cboYear === '' ? '' : $scope.cboYear;
            let cate        = $scope.cboCategory === '' ? '' : $scope.cboCategory;
            let depart      = !$scope.cboDepart ? '' : $scope.cboDepart;
            let division    = !$scope.cboDivision ? '' : $scope.cboDivision;
            let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
            let price       = $scope.txtPrice === '' ? '' : $scope.txtPrice;
            let approved    = $scope.isApproved ? 'A' : '';
            
            window.location.href = `${CONFIG.baseUrl}/plans/excel?type=3&year=${year}&cate=${cate}&depart=${depart}&division=${division}&status=${status}&approved=${approved}&price=${price}&show_all=1`;
        }
    };
});