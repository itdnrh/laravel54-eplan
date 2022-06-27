app.controller('planConstructCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.constructs = [];
    $scope.pager = [];

    $scope.construct = {
        construct_id: '',
        in_plan: 'I',
        year: '',
        plan_no: '',
        faction_id: '',
        depart_id: '',
        division_id: '',
        category_id: '',
        group_id: '',
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
        budget_src_id: '',
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

    $('#start_month')
        .datepicker(dtpMonthOptions)
        .datepicker('update', new Date());

    $('#doc_date')
        .datepicker(dtpDateOptions)
        .datepicker('update', new Date());
        // .on('show', function (e) {
        //     $('.day').click(function(event) {
        //         event.preventDefault();
        //         event.stopPropagation();
        //     });
        // });

    $scope.clearConstruct = function() {
        $scope.construct = {
            construct_id: '',
            in_plan: 'I',
            year: '',
            plan_no: '',
            faction_id: '',
            depart_id: '',
            division_id: '',
            category_id: '',
            group_id: '',
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
            budget_src_id: '',
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

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let menu    = $scope.cboMenu === '' ? '' : $scope.cboMenu;

        $http.get(`${CONFIG.baseUrl}/plans/search?type=4&year=${year}&cate=${cate}&status=${status}&depart=${depart}&menu=${menu}`)
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
            $('#item_id').val(item.id);
            $scope.construct.item_id = item.id;
            $scope.construct.desc = item.item_name;
            $scope.construct.price_per_unit = item.price_per_unit;
            $scope.construct.unit_id = item.unit_id.toString();
            $scope.construct.category_id = item.category_id.toString();
        }

        $('#items-list').modal('hide');
    };

    $scope.getDataWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.constructs = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let menu    = $scope.cboMenu === '' ? '' : $scope.cboMenu;

        $http.get(`${url}&type=4&year=${year}&cate=${cate}&status=${status}&depart=${depart}&menu=${menu}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getById = function(id, cb) {
        $http.get(`${CONFIG.baseUrl}/constructs/get-ajax-byid/${id}`)
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
        $scope.construct.year               = plan.year;
        // $scope.construct.plan_no            = plan.plan_no;
        $scope.construct.desc               = plan.plan_item.item.item_name;
        $scope.construct.item_id            = plan.plan_item.item_id;
        
        $scope.construct.location           = plan.plan_item.location;
        $scope.construct.building_id        = plan.plan_item.building ? plan.plan_item.building : '';
        $scope.construct.boq_no             = plan.plan_item.boq_no;
        $scope.construct.boq_file           = plan.plan_item.boq_file;
        
        $scope.construct.price_per_unit     = plan.plan_item.price_per_unit;
        $scope.construct.amount             = plan.plan_item.amount;
        $scope.construct.sum_price          = plan.plan_item.sum_price;
        $scope.construct.start_month        = $scope.monthLists.find(m => m.id == plan.start_month).name;
        $scope.construct.reason             = plan.reason;
        $scope.construct.remark             = plan.remark;
        $scope.construct.status             = plan.status;

        /** Convert int value to string */
        $scope.construct.category_id        = plan.plan_item.item.category_id.toString();
        $scope.construct.unit_id            = plan.plan_item.unit_id.toString();
        $scope.construct.depart_id          = plan.depart_id.toString();
        $scope.construct.division_id        = plan.division_id ? plan.division_id.toString() : '';
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $(`#${form}`).submit();
    }

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/constructs/edit/${id}`;
    };

    $scope.update = function(event) {
        event.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขใบลาเลขที่ ${$scope.leave.leave_id} ใช่หรือไม่?`)) {
            $('#frmEditLeave').submit();
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