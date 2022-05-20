app.controller('withdrawalCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
    /** ################################################################################## */
    $scope.loading = false;
    $scope.withdrawals = [];
    $scope.pager = null;

    $scope.inspections = [];
    $scope.inspections_pager = null;

    $scope.orders = [];
    $scope.orders_pager = null;

    $scope.withdrawal = {
        order: null,
        order_id: '',
        withdraw_no: '',
        withdraw_date: '',
        inspection_id: '',
        inspection: null,
        deliver_seq: '',
        supplier_id: '',
        supplier: null,
        net_total: '',
        remark: '',
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
    $('#withdraw_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
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

    $scope.calculateTotal = () => {
        let total = 0;

        total = $scope.order.details.reduce((sum, curVal) => {
            return sum = sum + curVal.sum_price;
        }, 0);

        $scope.order.total = total;
        $('#total').val(total);
    };

    $scope.showOrdersList = (e) => {
        $scope.loading = true;
        $scope.orders = [];
        $scope.orders_pager = null;

        $http.get(`${CONFIG.baseUrl}/orders/search?status=1`)
        .then(function(res) {
            $scope.setOrder(res);

            $scope.loading = false;

            $('#orders-list').modal('show');
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getOrder = () => {
        $scope.loading = true;
        $scope.orders = [];
        $scope.orders_pager = null;

        let cate    = $scope.cboCategory === '' ? 0 : $scope.cboCategory;
        let type    = $scope.cboPlanType === '' ? 1 : $scope.cboPlanType;

        $http.get(`${CONFIG.baseUrl}/orders/search?type=${type}&cate=${cate}&status=1`)
        .then(function(res) {
            $scope.setOrder(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setOrder = function(res) {
        const { data, ...pager } = res.data.orders;

        $scope.orders = data;
        $scope.orders_pager = pager;
    };

    $scope.onSelectedOrder = (e, order) => {
        if (order) {
            $scope.withdrawal = {
                order: order,
                order_id: order.id,
                inspections: order.inspections,
                supplier_id: order.supplier.supplier_id,
                supplier: order.supplier
            };
        }

        $('#orders-list').modal('hide');
    };

    $scope.onDeliverSeqSelected = function(seq) {
        const inspection = $scope.withdrawal.inspections.find(insp => insp.deliver_seq === parseInt(seq));

        $scope.withdrawal.inspection_id = inspection.id;
        $scope.withdrawal.deliver_no = inspection.deliver_no;
        $scope.withdrawal.net_total = inspection.inspect_total;
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
        
        $http.get(`${CONFIG.baseUrl}/withdrawals/search`)
        .then(function(res) {
            $scope.setWithdrawals(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setWithdrawals = function (res) {
        const { data, ...pager } = res.data.withdrawals;

        $scope.withdrawals = data;
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
            $scope.assets = items;
    
            $('#order-details').modal('show');
        }
    };

    $scope.getById = function(id) {
        $scope.loading = true;

        $http.get(`${CONFIG.baseUrl}/withdrawals/get-ajax-byid/${id}`)
        .then(function(res) {
            console.log(res);
            const { inspection, supplier, ...withdrawal } = res.data.withdrawal;

            $scope.withdrawal.order = inspection.order;
            $scope.withdrawal.inspection = inspection;
            $scope.withdrawal.withdraw_no = withdrawal.withdraw_no;
            $scope.withdrawal.withdraw_date = withdrawal.withdraw_date;
            $scope.withdrawal.net_total = withdrawal.net_total;
            $scope.withdrawal.remark = withdrawal.remark;
            $scope.withdrawal.supplier = supplier;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $http.post(`${CONFIG.baseUrl}/withdrawals/store`, $scope.withdrawal)
        .then(function(res) {
            console.log(res.data);
        }, function(err) {
            console.log(err);
        });

        window.location.href = `${CONFIG.baseUrl}/withdrawals/list`;
    }

    $scope.edit = function(id) {
        $http.get(`${CONFIG.baseUrl}/withdrawals/getOrder/${id}`)
        .then(res => {
            $scope.order.id = res.data.order.id;
            $scope.order.year = res.data.order.year.toString();
            $scope.order.supplier_id = res.data.order.supplier.supplier_name;
            $scope.order.po_no = res.data.order.po_no;
            $scope.order.po_date = StringFormatService.convFromDbDate(res.data.order.po_date);
            $scope.order.remark = res.data.order.remark;
            $scope.order.total = res.data.order.total;
            $scope.order.vat_rate = res.data.order.vat_rate+'%';
            $scope.order.vat = res.data.order.vat;
            $scope.order.net_total = res.data.order.net_total;
            $scope.order.details = res.data.order.details;

            $('#po_date')
                .datepicker(dtpOptions)
                .datepicker('update', moment(res.data.order.po_date).toDate());
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
