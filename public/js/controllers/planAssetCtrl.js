app.controller('planAssetCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.assets = [];
    $scope.pager = null;

    $scope.asset = {
        asset_id: '',
        year: '',
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
        strategic_id: '',
        service_plan_id: '',
        start_month: '',
        reason: '',
        remark: '',
        owner: '',
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

    $('#doc_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date());
        // .on('show', function (e) {
        //     $('.day').click(function(event) {
        //         event.preventDefault();
        //         event.stopPropagation();
        //     });
        // });

    const clearAsset = function() {
        $scope.asset = {
            asset_id: '',
            year: '',
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
            strategic_id: '',
            service_plan_id: '',
            start_month: '',
            reason: '',
            remark: '',
            owner: '',
        };
    };

    $scope.calculateSumPrice = async function() {
        let price = $(`#price_per_unit`).val() == '' ? 0 : parseFloat($(`#price_per_unit`).val());
        let amount = $(`#amount`).val() == '' ? 0 : parseFloat($(`#amount`).val());

        $scope.asset.sum_price = price * amount;
        $('#sum_price').val(price * amount);
    };

    /** TODO: Duplicated function */
    $scope.getAll = function(event) {
        $scope.loading = true;
        $scope.assets = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let menu    = $scope.cboMenu === '' ? '' : $scope.cboMenu;

        $http.get(`${CONFIG.baseUrl}/plans/search?type=1&year=${year}&cate=${cate}&status=${status}&depart=${depart}&menu=${menu}`)
        .then(function(res) {
            $scope.setAssets(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setAssets = function(res) {
        const { data, ...pager } = res.data.plans;

        $scope.assets = data;
        $scope.pager = pager;
    };

    $scope.getDataWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.assets = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let menu    = $scope.cboMenu === '' ? '' : $scope.cboMenu;

        $http.get(`${url}&type=1&year=${year}&cate=${cate}&status=${status}&depart=${depart}&menu=${menu}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
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
            let depart = $scope.asset.depart_id === '' ? 0 : $scope.asset.depart_id;

            $http.get(`${CONFIG.apiUrl}/plans/${item.id}/${$scope.asset.year}/${depart}/existed`)
            .then(function(res) {
                if (res.data.isExisted) {
                    toaster.pop('error', "ผลการตรวจสอบ", "รายการที่คุณเลือกมีอยู่ในแผนแล้ว !!!");
                } else {
                    $('#item_id').val(item.id);
                    $scope.asset.item_id = item.id;
                    $scope.asset.desc = item.item_name;
                    $scope.asset.price_per_unit = item.price_per_unit;
                    $scope.asset.unit_id = item.unit_id.toString();
                }
            }, function(err) {
                console.log(err);
            });
        }

        $('#items-list').modal('hide');
    };

    $scope.getById = function(id, cb) {
        $http.get(`${CONFIG.apiUrl}/assets/${id}`)
        .then(function(res) {
            cb(res.data.plan);
        }, function(err) {
            console.log(err);
        });
    }

    $scope.setEditControls = function(plan) {
        console.log(plan);
        /** Global data */
        $scope.planId                   = plan.id;
        $scope.planType                 = 1;
        /** ข้อมูลครุภัณฑ์ */
        $scope.asset.asset_id           = plan.id;
        $scope.asset.in_plan            = plan.in_plan;
        $scope.asset.year               = plan.year.toString();
        $scope.asset.plan_no            = plan.plan_no;
        $scope.asset.desc               = plan.plan_item.item.item_name;
        $scope.asset.spec               = plan.plan_item.spec;
        $scope.asset.price_per_unit     = plan.plan_item.price_per_unit;
        $scope.asset.amount             = plan.plan_item.amount;
        $scope.asset.sum_price          = plan.plan_item.sum_price;
        $scope.asset.start_month        = plan.start_month.toString();
        $scope.asset.request_cause      = plan.request_cause;
        $scope.asset.have_amount        = plan.have_amount;
        $scope.asset.reason             = plan.reason;
        $scope.asset.remark             = plan.remark;
        $scope.asset.status             = plan.status;

        /** Convert int value to string */
        $scope.asset.unit_id            = plan.plan_item.unit_id.toString();
        $scope.asset.faction_id         = plan.depart.faction_id.toString();
        $scope.asset.depart_id          = plan.depart_id.toString();
        $scope.asset.division_id        = plan.division_id ? plan.division_id.toString() : '';
        $scope.asset.budget_src_id      = plan.budget_src_id.toString();
        $scope.asset.strategic_id       = plan.strategic_id && plan.strategic_id.toString();
        $scope.asset.service_plan_id    = plan.service_plan_id && plan.service_plan_id.toString();

        /** Convert db date to thai date. */            
        // $scope.leave.leave_date         = StringFormatService.convFromDbDate(data.leave.leave_date);

        /** Generate departs and divisions data from plan */
        $scope.onFactionSelected(plan.depart.faction_id);
        $scope.onDepartSelected(plan.depart_id);
    };

    $scope.getMonthName = function(month) {
        return $scope.monthLists.find(m => m.id == month).name;
    }

    $scope.store = function(event, form) {
        event.preventDefault();

        $(`#${form}`).submit();
    }

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/assets/edit/${id}`;
    };

    $scope.update = function(event) {
        event.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขใบลาเลขที่ ${$scope.asset.asset_id} ใช่หรือไม่?`)) {
            $('#frmEditAsset').submit();
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