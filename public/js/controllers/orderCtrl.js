app.controller('orderCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
    /** ################################################################################## */
    $scope.loading = false;
    $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? (moment().year() + 544).toString()
                        : (moment().year() + 543).toString();
    $scope.cboMonth = moment().format('MM');
    $scope.cboCategory = "";
    $scope.cboStatus = "";
    $scope.cboMenu = "";
    $scope.searchKeyword = "";
    $scope.cboQuery = "";
    $scope.budgetYearRange = [2560,2561,2562,2563,2564,2565,2566,2567];
    $scope.vatRates = [1,2,3,4,5,6,7,8,9,10];
    $scope.editRow = false;

    $scope.orders = [];
    $scope.pager = [];

    $scope.assets = [];
    $scope.assets_pager = [];

    $scope.order = {
        year: '',
        supplier_id: '',
        po_no: '',
        po_date: '',
        remark: '',
        total: '',
        vat_rate: '',
        vat: '',
        net_total: '',
        details: [],
    };

    $scope.newItem = {
        plan_no: '',
        plan_detail: '',
        plan_depart: '',
        plan_id: '',
        price_per_unit: '',
        unit_id: '',
        amount: '',
        sum_price: ''
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

    /** ==================== Add form ==================== */
    $('#po_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('show', function (e) {
            console.log(e);
        })
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $scope.calculateSumPrice = function() {
        let price = parseFloat($(`#price_per_unit`).val());
        let amount = parseFloat($(`#amount`).val());

        $scope.newItem.sum_price = price * amount;
        $('#sum_price').val(price * amount);
    };

    $scope.calculateVat = function() {
        let total = parseFloat($(`#total`).val());
        let rate = parseFloat($(`#vat_rate`).val());
        let vat = (total * rate) / 100;

        $scope.order.vat = vat;
        $('#vat').val(vat);

        $scope.calculateNetTotal();
    };

    $scope.calculateNetTotal = function() {
        let total = parseFloat($(`#total`).val());
        let vat = parseFloat($(`#vat`).val());

        let net_total = total + vat;

        $scope.order.net_total = net_total;
        $('#net_total').val(net_total);
    };

    $scope.addOrderItem = () => {
        $scope.order.details.push({ ...$scope.newItem });

        $scope.calculateTotal();
        $scope.clearNewItem();
    };

    $scope.removeOrderItem = (index) => {
        console.log(index);
        // $scope.order.details.push({ ...$scope.newItem });

        $scope.calculateTotal();
    };

    $scope.clearNewItem = () => {
        $scope.newItem = {
            plan_no: '',
            plan_detail: '',
            plan_depart: '',
            plan_id: '',
            price_per_unit: '',
            unit_id: '',
            amount: '',
            sum_price: ''
        };
    }

    $scope.calculateTotal = () => {
        let total = 0;

        total = $scope.order.details.reduce((sum, curVal) => {
            return sum = sum + curVal.sum_price;
        }, 0);

        $scope.order.total = total;
        $('#total').val(total);
    };

    $scope.showAssetsList = () => {
        $scope.assets = [];
        $scope.loading = true;

        let year    = $scope.cboYear === '' ? 0 : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? 0 : $scope.cboCategory;
        let status  = $scope.cboStatus === '' ? '1' : $scope.cboStatus;
        let menu    = $scope.cboMenu === '' ? 0 : $scope.cboMenu;
        let query   = $scope.cboQuery === '' ? '' : `?${$scope.cboQuery}`;

        $http.get(`${CONFIG.baseUrl}/assets/search/${year}/${cate}/${status}/${menu}${query}`)
        .then(function(res) {
            $scope.setAssets(res);

            $scope.loading = false;

            $('#assets-list').modal('show');
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.onSelectedPlan = (e, plan) => {
        if (plan) {
            $scope.newItem = {
                plan_no: plan.plan_no,
                plan_detail: `${plan.desc} (${plan.category.category_name})`,
                plan_depart: plan.division.ward_name,
                plan_id: plan.id,
                price_per_unit: plan.price_per_unit,
                unit_id: `${plan.unit_id}`,
                amount: plan.amount,
                sum_price: plan.sum_price
            };
        }

        $('#assets-list').modal('hide');
    };

    $scope.setAssets = function(res) {
        const { data, ...pager } = res.data.assets;

        $scope.assets = data;
        $scope.assets_pager = pager;
    };

    $scope.getAll = function() {
        $scope.orders = [];
        $scope.pager = null;
        
        $scope.loading = true;
        
        // let year    = $scope.cboYear === '' ? 0 : $scope.cboYear;
        // let type    = $scope.cboLeaveType === '' ? 0 : $scope.cboLeaveType;
        // let status  = $scope.cboLeaveStatus === '' ? '-' : $scope.cboLeaveStatus;
        // let menu    = $scope.cboMenu === '' ? 0 : $scope.cboMenu;
        // let query   = $scope.cboQuery === '' ? '' : `?${$scope.cboQuery}`;
        
        $http.get(`${CONFIG.baseUrl}/orders/search`)
        .then(function(res) {
            $scope.setOrders(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setOrders = function (res) {
        const { data, ...pager } = res.data.orders;

        $scope.orders = data;
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

    $scope.showOrderDetails = (items) => {
        if (items) {
            console.log(items);
            $scope.assets = items;
    
            $('#order-details').modal('show');
        }
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $http.post(`${CONFIG.baseUrl}/orders/store`, $scope.order)
        .then(res => {
            console.log(res);
        }, err => {
            console.log(err);
        });
    }

    $scope.edit = function(id) {
        console.log(id);

        $http.get(`${CONFIG.baseUrl}/orders`)
        .then(res => {
            console.log(res);

            // $scope.cancellation.leave_id = leave.id;
            // $scope.cancellation.reason = leave.cancellation[0].reason;
            // $scope.cancellation.start_date = StringFormatService.convFromDbDate(leave.cancellation[0].start_date);
            // $scope.cancellation.end_date = StringFormatService.convFromDbDate(leave.cancellation[0].end_date);
            // $scope.cancellation.start_period = leave.cancellation[0].start_period.toString();
            // $scope.cancellation.end_period = leave.cancellation[0].end_period.toString();
            // $scope.cancellation.days = leave.cancellation[0].days;
            // $scope.cancellation.working_days = leave.cancellation[0].working_days;

            // $('#po_date')
            //     .datepicker(dtpOptions)
            //     .datepicker('update', moment(leave.cancellation[0].start_date).toDate());
        }, err => {
            console.log(err);
        });
    };

    $scope.update = function(event, form) {
        event.preventDefault();

        if(confirm(`คุณต้องแก้ไขรายการขอยกเลิกวันลาเลขที่ ${$scope.cancellation.leave_id} ใช่หรือไม่?`)) {
            $(`#${form}`).submit();
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();

        const actionUrl = $('#frmDelete').attr('action');
        $('#frmDelete').attr('action', `${actionUrl}/${id}`);

        if (window.confirm(`คุณต้องลบรายการขอยกเลิกวันลาเลขที่ ${id} ใช่หรือไม่?`)) {
            $('#frmDelete').submit();
        }
    };
});
