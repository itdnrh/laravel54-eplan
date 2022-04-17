app.controller('planConstructCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.constructs = [];
    $scope.pager = [];

    $scope.construct = {
        construct_id: '',
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
        reason: '',
        remark: ''
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

    $scope.clearConstructObj = function() {
        $scope.construct = {
            construct_id: '',
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

        $http.get(`${CONFIG.baseUrl}/plans/search?type=1&year=${year}&cate=${cate}&status=${status}&depart=${depart}&menu=${menu}`)
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

    $scope.getDataWithURL = function(e, URL, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;

        $http.get(URL)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getById = function(id, cb) {
        $http.get(`${CONFIG.baseUrl}/services/get-ajax-byid/${id}`)
        .then(function(res) {
            cb(res.data);
        }, function(err) {
            console.log(err);
        });
    }

    $scope.setEditControls = function(data) {
        $scope.service.service_id       = data.plan.id;
        $scope.service.year             = data.plan.year;
        $scope.service.plan_no          = data.plan.plan_no;
        $scope.service.desc             = data.plan.service.desc;
        $scope.service.price_per_unit   = data.plan.service.price_per_unit;
        $scope.service.amount           = data.plan.service.amount;
        $scope.service.sum_price        = data.plan.service.sum_price;
        $scope.service.start_month      = $scope.monthLists.find(m => m.id == data.plan.start_month).name;
        $scope.service.reason           = data.plan.reason;
        $scope.service.remark           = data.plan.remark;
        $scope.service.status           = data.plan.status;

        /** Convert int value to string */
        $scope.service.category_id      = data.plan.service.category_id.toString();
        $scope.service.unit_id          = data.plan.service.unit_id.toString();
        $scope.service.depart_id        = data.plan.depart_id.toString();
        $scope.service.division_id      = data.plan.division_id ? data.plan.division_id.toString() : '';

        /** Convert db date to thai date. */            
        // $scope.service.service_date     = StringFormatService.convFromDbDate(data.plan.service.service_date);
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $(`#${form}`).submit();
    }

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/services/edit/${id}`;
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