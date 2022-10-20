app.controller('inspectionCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
    /** ################################################################################## */
    $scope.loading = false;
    $scope.cboYear = '2566'; //(moment().year() + 543).toString();
    $scope.cboSupplier = '';
    $scope.txtDeliverNo = '';

    $scope.inspections = [];
    $scope.pager = [];

    $scope.orders = [];
    $scope.orders_pager = null;

    $scope.inspection = {
        year: '2566',
        order_id: '',
        order: null,
        deliver_seq: '',
        deliver_bill: '',
        deliver_no: '',
        deliver_date: '',
        inspect_sdate: '',
        inspect_edate: '',
        inspect_total: '',
        inspect_result: '',
        remark: '',
    };

    $scope.newItem = {
        plan_no: '',
        plan_detail: '',
        plan_depart: '',
        plan_id: '',
        item_id: '',
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
    $('#inspect_sdate')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            console.log(event.date);

            $scope.inspection.edate = moment(event.date).format('YYYY-MM-DD');
            $('#inspect_edate').datepicker('update', event.date)
        });

    $('#inspect_edate')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $('#deliver_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $scope.showPopup = false;
    $scope.deliverBillsList = [];
    $scope.fetchDeliverBills = function(e) {
        if (e.keyCode === 27) {
            $scope.showPopup = false;
            return;
        }

        let keyword = e.target.value;
        if (keyword != '') {
            $http.get(`${CONFIG.baseUrl}/inspections/${keyword}/deliver-bills`)
            .then((res) => {
                if (res.data.deliver_bills.length > 0) {
                    $scope.deliverBillsList = [ ...new Set(res.data.deliver_bills) ];
                }

                $scope.showPopup = true;
            }, (err) => {
                console.log(err);
            });
        } else {
            $scope.showPopup = false;
        }
    };

    $scope.setDeliverBill = function(bill) {
        $scope.showPopup = false;

        $('#deliver_bill').val(bill);
        $scope.inspection.deliver_bill = bill;
    };

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
            item_id: '',
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

    $scope.showOrdersList = (e) => {
        $scope.loading = true;
        $scope.orders = [];
        $scope.orders_pager = null;

        $http.get(`${CONFIG.baseUrl}/orders/search?status=0-2`)
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

        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let type    = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;

        $http.get(`${CONFIG.baseUrl}/orders/search?type=${type}&cate=${cate}&status=0-2`)
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

    $scope.inspectionsByOrder = [];
    $scope.getInspectionByOrder = function(orderId) {
        $http.get(`${CONFIG.baseUrl}/inspections/${orderId}/order`)
        .then(function(res) {
            $scope.inspectionsByOrder = res.data.inspections;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.onSelectedOrder = (e, order) => {
        if (order) {
            $scope.inspection.order_id      = order.id;
            $scope.inspection.order         = order;
            $scope.inspection.inspect_total = order.net_total;

            $scope.getInspectionByOrder(order.id);
        }

        $('#orders-list').modal('hide');
    };

    $scope.getAll = function() {
        $scope.inspections = [];
        $scope.pager = null;
        $scope.loading = true;
        
        let year = $scope.cboYear === '' ? '' : $scope.cboYear;
        let supplier = $scope.cboSupplier === '' ? '' : $scope.cboSupplier;
        let deliverNo = $scope.txtDeliverNo === '' ? '' : $scope.txtDeliverNo;
        
        $http.get(`${CONFIG.baseUrl}/inspections/search?year=${year}&supplier=${supplier}&deliverNo=${deliverNo}`)
        .then(function(res) {
            $scope.setInspections(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setInspections = function (res) {
        const { data, ...pager } = res.data.inspections;

        $scope.inspections = data;
        $scope.pager = pager;
    };

    $scope.getDataWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.inspections = [];
        $scope.pager = null;
        $scope.loading = true;

        let year = $scope.cboYear === '' ? '' : $scope.cboYear;
        let supplier = $scope.cboSupplier === '' ? '' : $scope.cboSupplier;
        let deliverNo = $scope.txtDeliverNo === '' ? '' : $scope.txtDeliverNo;

        $http.get(`${url}&year=${year}&supplier=${supplier}&deliverNo=${deliverNo}`)
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

    $scope.store = function(event, form) {
        event.preventDefault();
        $scope.loading = true;

        $http.post(`${CONFIG.baseUrl}/inspections/store`, $scope.inspection)
        .then(function(res) {
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกตรวจรับเรียบร้อย !!!");

                window.location.href = `${CONFIG.baseUrl}/orders/inspect`;
            } else {
                toaster.pop('error', "ผลการทำงาน", "ไม่สามารถบันทึกตรวจรับได้ !!!");
            }

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            toaster.pop('error', "ผลการทำงาน", "ไม่สามารถบันทึกตรวจรับได้ !!!");

            $scope.loading = false;
        });
    }

    $scope.edit = function(id) {
        $http.get(`${CONFIG.apiUrl}/inspections/${id}`)
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
