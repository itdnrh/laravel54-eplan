app.controller('planMaterialCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.materials = [];
    $scope.pager = [];

    $scope.material = {
        material_id: '',
        in_plan: 'I',
        year: '',
        plan_no: '',
        faction_id: '',
        depart_id: '',
        division_id: '',
        category_id: '',
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
        in_stock: '0',
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

    $scope.clearMaterial = function() {
        $scope.material = {
            asset_id: '',
            in_plan: 'I',
            plan_no: '',
            faction_id: '',
            depart_id: '',
            division_id: '',
            category_id: '',
            item_id: '',
            desc: '',
            spec: '',
            price_per_unit: '',
            unit_id: '',
            amount: '',
            sum_price: '',
            start_month: '',
            request_cause: '',
            have_amount: '',
            budget_src_id: '',
            strategic_id: '',
            service_plan_id: '',
            reason: '',
            remark: '',
            in_stock: '0',
        };
    };

    $scope.calculateSumPrice = async function() {
        let price = $(`#price_per_unit`).val() == '' ? 0 : parseFloat($(`#price_per_unit`).val());
        let amount = $(`#amount`).val() == '' ? 0 : parseFloat($(`#amount`).val());

        $scope.material.sum_price = price * amount;
        $('#sum_price').val(price * amount);
    };

    /** TODO: Duplicated function */
    $scope.getAll = function(event) {
        $scope.loading = true;
        $scope.materials = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let menu    = $scope.cboMenu === '' ? '' : $scope.cboMenu;

        $http.get(`${CONFIG.baseUrl}/plans/search?type=2&year${year}&cate=${cate}&status=${status}&depart=${depart}`)
        .then(function(res) {
            $scope.setMaterials(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setMaterials = function(res) {
        const { data, ...pager } = res.data.plans;

        $scope.materials = data;
        $scope.pager = pager;
    };

    $scope.getDataWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.materials = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let menu    = $scope.cboMenu === '' ? '' : $scope.cboMenu;

        $http.get(`${url}&type=2&year=${year}&cate=${cate}&status=${status}&depart=${depart}&menu=${menu}`)
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
            $('#item_id').val(item.id);
            $scope.material.item_id = item.id;
            $scope.material.desc = item.item_name;
            $scope.material.price_per_unit = item.price_per_unit;
            $scope.material.unit_id = item.unit_id.toString();
        }

        $('#items-list').modal('hide');
    };

    $scope.getById = function(id, cb) {
        $http.get(`${CONFIG.baseUrl}/materials/get-ajax-byid/${id}`)
        .then(function(res) {
            cb(res.data.plan);
        }, function(err) {
            console.log(err);
        });
    }

    $scope.setEditControls = function(plan) {
        /** Global data */
        $scope.planId                   = plan.id;
        $scope.planType                 = 2;

        /** ข้อมูลวัสดุ */
        $scope.material.material_id     = plan.id;
        $scope.material.in_plan         = plan.in_plan;
        $scope.material.year            = plan.year;
        // $scope.material.plan_no         = plan.plan_no;
        $scope.material.item_id         = plan.plan_item.item_id;
        $scope.material.desc            = plan.plan_item.item.item_name;
        $scope.material.spec            = plan.plan_item.spec;
        $scope.material.price_per_unit  = plan.plan_item.price_per_unit;
        $scope.material.amount          = plan.plan_item.amount;
        $scope.material.sum_price       = plan.plan_item.sum_price;
        $scope.material.start_month     = $scope.monthLists.find(m => m.id == plan.start_month).name;
        $scope.material.reason          = plan.reason;
        $scope.material.remark          = plan.remark;
        $scope.material.status          = plan.status;

        /** Convert int value to string */
        $scope.material.category_id     = plan.plan_item.item.category_id.toString();
        $scope.material.unit_id         = plan.plan_item.unit_id.toString();
        $scope.material.depart_id       = plan.depart_id.toString();
        $scope.material.division_id     = plan.division_id ? plan.division_id.toString() : '';
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $(`#${form}`).submit();
    }

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/materials/edit/${id}`;
    };

    $scope.update = function(event, form) {
        event.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขใบลาเลขที่ ${$scope.material.material_id} ใช่หรือไม่?`)) {
            $(`#${form}`).submit();
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();

        const actionUrl = $('#frmDelete').attr('action');
        $('#frmDelete').attr('action', `${actionUrl}/${id}`);

        if(confirm(`คุณต้องลบใบลาเลขที่ ${id} ใช่หรือไม่?`)) {
            $('#frmDelete').submit();
        }
    };
});