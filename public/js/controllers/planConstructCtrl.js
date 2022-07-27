app.controller('planConstructCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.constructs = [];
    $scope.pager = [];

    $scope.isApproved = false;
    $scope.txtPrice = '';

    $scope.construct = {
        construct_id: '',
        in_plan: 'I',
        year: (moment().year() + 543).toString(),
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

    $scope.setIsApproved = function(e) {
        $scope.isApproved = e.target.checked;

        $scope.getAll(e);
    };

    $scope.clearConstruct = function() {
        $scope.construct = {
            construct_id: '',
            in_plan: 'I',
            year: (moment().year() + 543).toString(),
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
            start_month: '',
            request_cause: '',
            have_amount: '',
            budget_src_id: '1',
            strategic_id: '',
            service_plan_id: '',
            reason: '',
            remark: ''
        };
    };

    $scope.calculateSumPrice = async function() {
        let price = $(`#price_per_unit`).val() == '' ? 0 : parseFloat($(`#price_per_unit`).val());
        let amount = $(`#amount`).val() == '' ? 0 : parseFloat($(`#amount`).val());

        $scope.construct.sum_price = price * amount;
        $('#sum_price').val(price * amount);
    };

    $scope.getAll = function(event) {
        $scope.constructs = [];
        $scope.loading = true;

        let year        = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate        = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let depart      = !$scope.cboDepart ? '' : $scope.cboDepart;
        let division    = !$scope.cboDivision ? '' : $scope.cboDivision;
        let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let price       = $scope.txtPrice === '' ? '' : $scope.txtPrice;
        let approved    = $scope.isApproved ? 'A' : '';

        $http.get(`${CONFIG.baseUrl}/plans/search?type=4&year=${year}&cate=${cate}&depart=${depart}&division=${division}&status=${status}&approved=${approved}&price=${price}&show_all=1`)
        .then(function(res) {
            $scope.setConstructs(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setConstructs = function(res) {
        const { data, ...pager } = res.data.plans;

        $scope.constructs = data;
        $scope.pager = pager;
    };

    $scope.onSelectedItem = function(event, item) {
        if (item) {
            /** Check existed data by depart */
            let depart = $scope.construct.depart_id === '' ? 0 : $scope.construct.depart_id;

            $http.get(`${CONFIG.apiUrl}/plans/${item.id}/${$scope.construct.year}/${depart}/existed`)
            .then(function(res) {
                if (res.data.isExisted) {
                    toaster.pop('error', "ผลการตรวจสอบ", "รายการที่คุณเลือกมีอยู่ในแผนแล้ว !!!");
                } else {
                    $('#item_id').val(item.id);
                    $scope.construct.item_id = item.id;
                    $scope.construct.desc = item.item_name;
                    $scope.construct.price_per_unit = item.price_per_unit;
                    $scope.construct.unit_id = item.unit_id.toString();
                }
            }, function(err) {
                console.log(err);
            });
        }

        $('#items-list').modal('hide');
    };

    $scope.getDataWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.constructs = [];
        $scope.pager = null;

        let year        = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate        = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let depart      = !$scope.cboDepart ? '' : $scope.cboDepart;
        let division    = !$scope.cboDivision ? '' : $scope.cboDivision;
        let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let price       = $scope.txtPrice === '' ? '' : $scope.txtPrice;
        let approved    = $scope.isApproved ? 'A' : '';

        $http.get(`${url}&type=4&year=${year}&cate=${cate}&depart=${depart}&division=${division}&status=${status}&approved=${approved}&price=${price}&show_all=1`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getById = function(id, cb) {
        $http.get(`${CONFIG.apiUrl}/constructs/${id}`)
        .then(function(res) {
            cb(res.data.plan);
        }, function(err) {
            console.log(err);
        });
    }

    $scope.setEditControls = function(plan) {
        /** Global data */
        $scope.planId                       = plan.id;
        $scope.planType                     = 1;

        /** ข้อมูลงานก่อสร้าง */
        $scope.construct.construct_id       = plan.id;
        $scope.construct.in_plan            = plan.in_plan;
        $scope.construct.year               = plan.year.toString();
        // $scope.construct.plan_no            = plan.plan_no;
        $scope.construct.desc               = plan.plan_item.item.item_name;
        $scope.construct.item_id            = plan.plan_item.item_id;
        $('#item_id').val(plan.plan_item.item_id);

        $scope.construct.location           = plan.plan_item.location;
        $scope.construct.building_id        = plan.plan_item.building_id ? plan.plan_item.building_id.toString() : '';
        $scope.construct.boq_no             = plan.plan_item.boq_no;
        $scope.construct.boq_file           = plan.plan_item.boq_file;

        $scope.construct.price_per_unit     = plan.plan_item.price_per_unit;
        $scope.construct.amount             = plan.plan_item.amount;
        $scope.construct.sum_price          = plan.plan_item.sum_price;
        $scope.construct.start_month        = plan.start_month.toString();
        $scope.construct.request_cause      = plan.request_cause;
        $scope.construct.reason             = plan.reason;
        $scope.construct.remark             = plan.remark;
        $scope.construct.approved           = plan.approved;
        $scope.construct.status             = plan.status;

        /** Convert int value to string */
        $scope.construct.unit_id            = plan.plan_item.unit_id.toString();
        $scope.construct.faction_id         = plan.depart.faction_id.toString();
        $scope.construct.depart_id          = plan.depart_id.toString();
        $scope.construct.division_id        = plan.division_id ? plan.division_id.toString() : '';
        $scope.construct.budget_src_id      = plan.budget_src_id.toString();
        $scope.construct.strategic_id       = plan.strategic_id && plan.strategic_id.toString();
        $scope.construct.service_plan_id    = plan.service_plan_id && plan.service_plan_id.toString();

        /** Generate departs and divisions data from plan */
        $scope.onFactionSelected(plan.depart.faction_id);
        $scope.onDepartSelected(plan.depart_id);
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $(`#${form}`).submit();
    }

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/constructs/edit/${id}`;
    };

    $scope.update = function(event, form) {
        event.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขแผนก่อสร้างรหัส ${$scope.construct.construct_id} ใช่หรือไม่?`)) {
            $(`#${form}`).submit();
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();
        $scope.loading = true;

        if(confirm(`คุณต้องลบใบลาเลขที่ ${id} ใช่หรือไม่?`)) {
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
        }
    };

    $scope.exportListToExcel = function(e) {
        e.preventDefault();

        if($scope.constructs.length == 0) {
            toaster.pop('warning', "", "ไม่พบข้อมูล !!!");
        } else {
            let year        = $scope.cboYear === '' ? '' : $scope.cboYear;
            let cate        = $scope.cboCategory === '' ? '' : $scope.cboCategory;
            let depart      = !$scope.cboDepart ? '' : $scope.cboDepart;
            let division    = !$scope.cboDivision ? '' : $scope.cboDivision;
            let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
            let price       = $scope.txtPrice === '' ? '' : $scope.txtPrice;
            let approved    = $scope.isApproved ? 'A' : '';
            
            window.location.href = `${CONFIG.baseUrl}/plans/excel?type=4&year=${year}&cate=${cate}&depart=${depart}&division=${division}&status=${status}&approved=${approved}&price=${price}&show_all=1`;
        }
    };
});