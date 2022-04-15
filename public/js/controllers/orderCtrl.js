app.controller('orderCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
    /** ################################################################################## */
    $scope.loading = false;
    $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? (moment().year() + 544).toString()
                        : (moment().year() + 543).toString();
    $scope.cboMonth = moment().format('MM');
    $scope.cboCategory = "";
    $scope.cboStatus = "";
    $scope.cboPlanType = "";
    $scope.cboMenu = "";
    $scope.searchKeyword = "";
    $scope.cboQuery = "";
    $scope.budgetYearRange = [2560,2561,2562,2563,2564,2565,2566,2567];
    $scope.vatRates = [1,2,3,4,5,6,7,8,9,10];
    $scope.editRow = false;

    $scope.orders = [];
    $scope.pager = [];

    $scope.plans = [];
    $scope.plans_pager = [];

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

    $scope.showPlansList = () => {
        $scope.assets = [];
        $scope.loading = true;

        let year    = $scope.cboYear === '' ? 0 : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? 0 : $scope.cboCategory;
        let status  = $scope.cboStatus === '' ? '-' : $scope.cboStatus;
        let type    = $scope.cboPlanType === '' ? 2 : $scope.cboPlanType;
        let query   = $scope.cboQuery === '' ? '' : `?${$scope.cboQuery}`;

        $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}`)
        .then(function(res) {
            $scope.setPlans(res, type);

            $scope.loading = false;

            $('#plans-list').modal('show');
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.onSelectedPlan = (e, plan) => {
        if (plan) {
            $scope.newItem = {
                plan_no: plan.plan_no,
                plan_detail: `${plan.plan_item.item.item_name} (${plan.plan_item.item.category.name})`,
                plan_depart: plan.division ? plan.division.ward_name : plan.depart.depart_name,
                plan_id: plan.id,
                price_per_unit: plan.price_per_unit,
                unit_id: `${plan.unit_id}`,
                amount: plan.amount,
                sum_price: plan.sum_price
            };
        }

        $('#plans-list').modal('hide');
    };

    $scope.setPlans = function(res, type) {
        const { data, ...pager } = res.data.plans;

        $scope.plans = data;
        $scope.plans_pager = pager;
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

        $scope.orders = data.map(order => {
            let newDetails = order.details.map(item => {
                let plan = res.data.plans.find(pl => pl.id === item.plan_id);

                return {
                    ...item,
                    ...plan
                };
            });

            order.details = newDetails;

            return order;
        });

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
        $http.get(`${CONFIG.baseUrl}/orders/getOrder/${id}`)
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
