app.controller('orderCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
    /** ################################################################################## */
    $scope.loading = false;
    $scope.vatRates = [0,7];
    $scope.editRow = false;
    $scope.cboYear = (moment().year() + 543).toString();
    $scope.cboSupplier = '';
    $scope.txtPoNo = '';

    $scope.orders = [];
    $scope.pager = null;

    $scope.plans = [];
    $scope.plans_pager = null;

    $scope.order = {
        po_no: '',
        po_date: '',
        po_req_no: '',
        po_req_date: '',
        po_app_no: '',
        po_app_date: '',
        year: '',
        supplier_id: '',
        order_type_id: '',
        plan_type_id: '',
        deliver_amt: 1,
        total: '',
        vat_rate: '',
        vat: '',
        net_total: '',
        net_total_str: '',
        budget_src_id: '',
        remark: '',
        details: [],
    };

    $scope.newItem = {
        plan_no: '',
        plan_detail: '',
        plan_depart: '',
        plan_id: '',
        item_id: '',
        spec: '',
        price_per_unit: '',
        unit: null,
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

    $('#inspect_sdate')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $('#inspect_edate')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $('#withdraw_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $('#po_req_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $('#po_app_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $scope.clearNewItem = () => {
        $scope.newItem = {
            plan_no: '',
            plan_detail: '',
            plan_depart: '',
            plan_id: '',
            item_id: '',
            spec: '',
            price_per_unit: '',
            unit_id: '',
            amount: '',
            sum_price: ''
        };
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
        $scope.order.net_total_str = StringFormatService.arabicNumberToText(net_total);
        $('#net_total').val(net_total);
    };

    $scope.calculateTotal = () => {
        let total = 0;

        total = $scope.order.details.reduce((sum, curVal) => {
            return sum = sum + curVal.sum_price;
        }, 0);

        $scope.order.total = total;
        $('#total').val(total);
    };

    $scope.addOrderItem = () => {
        $scope.order.details.push({ ...$scope.newItem });

        $scope.calculateTotal();
        $scope.clearNewItem();
    };

    $scope.removeOrderItem = (index) => {
        console.log(index);
        // $scope.order.details.push({ ...$scope.newItem });

        // $scope.calculateTotal();
    };

    $scope.showSpecForm = function(detail) {
        $scope.newItem.plan_id = detail.plan_id;
        $scope.newItem.item_id = detail.item_id;

        $('#spec-form').modal('show');
    };

    $scope.addSpec = function() {
        $scope.order.details.map(detail => {
            detail.spec = $('#spec').val();
        });

        $('#spec-form').modal('hide');
    };

    $scope.toggleEditRow = function() {
        $scope.editRow = !$scope.editRow;
    };

    $scope.showPlansList = () => {
        $scope.loading = true;
        $scope.plans = [];
        $scope.plans_pager = null;

        let type = $scope.order.plan_type_id === '' ? 1 : $scope.order.plan_type_id;

        $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&status=2`)
        .then(function(res) {
            $scope.setPlans(res);

            $scope.loading = false;

            $('#plans-list').modal('show');
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPlans = (status) => {
        $scope.loading = true;
        $scope.plans = [];
        $scope.plans_pager = null;

        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        let cate = $scope.cboCategory == '' ? '' : $scope.cboCategory;
        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;

        $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&cate=${cate}&depart=${depart}&status=${status}`)
        .then(function(res) {
            $scope.setPlans(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPlansWithUrl = function(e, url, status, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.orders = [];
        $scope.pager = null;

        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        let cate = $scope.cboCategory == '' ? '' : $scope.cboCategory;
        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;

        $http.get(`${url}&type=${type}&cate=${cate}&depart=${depart}&status=${status}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setPlans = function(res) {
        const { data, ...pager } = res.data.plans;

        $scope.plans = data;
        $scope.plans_pager = pager;
    };

    $scope.onSelectedPlan = (e, plan) => {
        if (plan) {
            $scope.newItem = {
                plan_no: plan.plan_no,
                plan_detail: `${plan.plan_item.item.item_name} (${plan.plan_item.item.category.name})`,
                plan_depart: plan.division ? plan.division.ward_name : plan.depart.depart_name,
                plan_id: plan.id,
                item_id: plan.plan_item.item_id,
                spec: '',
                price_per_unit: plan.price_per_unit,
                unit_id: `${plan.plan_item.unit_id}`,
                unit: plan.plan_item.unit,
                amount: plan.amount,
                sum_price: plan.sum_price
            };

            $scope.addOrderItem();
        }

        $('#plans-list').modal('hide');
    };

    $scope.planToReceives = [];
    $scope.planToReceives_pager = null;
    $scope.showPlansToReceive = () => {
        $scope.getPlansToReceive();
    };

    $scope.getPlansToReceive = function(res) {
        $scope.loading = true;
        $scope.planToReceives = [];
        $scope.planToReceives_pager = null;

        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        let cate = $scope.cboCategory == '' ? '' : $scope.cboCategory;
        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;

        $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&cate=${cate}&depart=${depart}&status=1`)
        .then(function(res) {
            $scope.loading = false;

            $scope.setPlansToReceive(res);

            $('#receive-list').modal('show');
        }, function(err) {
            $scope.loading = false;
            console.log(err);
        });
    };

    $scope.getPlansToReceiveWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.planToReceives = [];
        $scope.planToReceives_pager = null;

        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        let cate = $scope.cboCategory == '' ? '' : $scope.cboCategory;
        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;

        $http.get(`${url}&type=${type}&cate=${cate}&depart=${depart}&status=1`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setPlansToReceive = function(res) {
        const { data, ...pager } = res.data.plans;

        $scope.planToReceives = data;
        $scope.planToReceives_pager = pager;
    };

    $scope.onReceivePlan = function(e, plan) {
        $http.post(`${CONFIG.baseUrl}/orders/received/1`, { id: plan.id })
        .then(function(res) {
            console.log(res);
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "ลงรับเอกสารเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลงรับเอกสารได้ !!!");
            }
        }, function(err) {
            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลงรับเอกสารได้ !!!");
        });

        $('#receive-list').modal('hide');
    };

    /** ============================================================================= */
    $scope.supports = [];
    $scope.supports_pager = null;
    $scope.getSupports = (status) => {
        $scope.loading = true;
        $scope.supports = [];
        $scope.supports_pager = null;

        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;

        $http.get(`${CONFIG.baseUrl}/supports/search?type=${type}&depart=${depart}&status=${status}`)
        .then(function(res) {
            $scope.setSupports(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getSupportWithUrl = function(e, url, status, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.supports = [];
        $scope.supports_pager = null;

        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;

        $http.get(`${url}&type=${type}&depart=${depart}&status=${status}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setSupports = function(res) {
        const { data, ...pager } = res.data.supports;

        $scope.supports = data;
        $scope.supports_pager = pager;
    };

    $scope.supportsToReceives = [];
    $scope.supportsToReceives_pager = null;
    $scope.getSupportsToReceive = function(res) {
        $scope.loading = true;
        $scope.supportsToReceives = [];
        $scope.supportsToReceives_pager = null;

        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        let cate = $scope.cboCategory == '' ? '' : $scope.cboCategory;
        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;

        $http.get(`${CONFIG.baseUrl}/supports/search?type=${type}&depart=${depart}&status=1`)
        .then(function(res) {
            $scope.loading = false;

            $scope.setSupportsToReceive(res);

            $('#supports-receive').modal('show');
        }, function(err) {
            $scope.loading = false;
            console.log(err);
        });
    };

    $scope.getSupportsToReceiveWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.supportsToReceives = [];
        $scope.supportsToReceives_pager = null;

        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        let cate = $scope.cboCategory == '' ? '' : $scope.cboCategory;
        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;

        $http.get(`${url}&type=${type}&depart=${depart}&status=1`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setSupportsToReceive = function(res) {
        const { data, ...pager } = res.data.supports;

        $scope.supportsToReceives = data;
        $scope.supportsToReceives_pager = pager;
    };

    $scope.onReceiveSupport = function(e, support) {
        console.log(support);
        $http.post(`${CONFIG.baseUrl}/orders/received/2`, support)
        .then(function(res) {
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "ลงรับเอกสารเรียบร้อย !!!");

                /** Remove support data that has been received */
                $scope.supportsToReceives = $scope.supportsToReceives.filter(el => el.id !== res.data.support.id);
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลงรับเอกสารได้ !!!");
            }
        }, function(err) {
            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลงรับเอกสารได้ !!!");
        });
    };
    /** ============================================================================= */

    $scope.getRunningNo = function(orderType) {
        $scope.loading = true;

        let docType = '';
        if (orderType == '1') {
            docType = '7';
        } else if (orderType == '2') {
            docType = '8';
        } else if (orderType == '3') {
            docType = '9';
        }

        $http.get(`${CONFIG.apiUrl}/runnings/${docType}/doc-type`)
        .then(function(res) {
            $scope.order.po_no = res.data.running+ '/' +$scope.order.year;showPlansList

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getAll = function() {
        $scope.loading = true;
        $scope.orders = [];
        $scope.pager = null;

        let year = $scope.cboYear === '' ? '' : $scope.cboYear;
        let supplier = $scope.cboSupplier === '' ? '' : $scope.cboSupplier;
        let po_no = $scope.txtPoNo === '' ? '' : $scope.txtPoNo;
        let status = $scope.cboStatus === '' ? '' : $scope.cboStatus;

        $http.get(`${CONFIG.baseUrl}/orders/search?year=${year}&supplier=${supplier}&po_no=${po_no}&status=${status}`)
        .then(function(res) {
            $scope.setOrders(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getAllWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.orders = [];
        $scope.pager = null;

        let year = $scope.cboYear === '' ? '' : $scope.cboYear;
        let supplier = $scope.cboSupplier === '' ? '' : $scope.cboSupplier;

        $http.get(`${url}&year=${year}&supplier=${supplier}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setOrders = function (res) {
        const { data, ...pager } = res.data.orders;

        $scope.orders = data.map(order => {
            /** ถ้าเป็นรายการตามแผนพัสดุ ให้อัพเดต details property */
            if (res.data.plans) {
                let newDetails = order.details.map(item => {
                    let plan = res.data.plans.find(pl => pl.id === item.plan_id);

                    return {
                        ...item,
                        ...plan
                    };
                });

                order.details = newDetails;
            }

            return order;
        });

        $scope.pager = pager;
    };

    $scope.showOrderDetails = (items) => {
        if (items) {
            $scope.assets = items;
    
            $('#order-details').modal('show');
        }
    };

    $scope.showInspectForm = (order) => {
        if (order) {    
            $('#inspect-form').modal('show');
        }
    };

    $scope.onInspect = (e) => {
        e.preventDefault();

        let data = {
            po_id: $('#po_id').val(),
            deliver_seq: $('#deliver_seq').val(),
            deliver_no: $('#deliver_no').val(),
            inspect_sdate: $('#inspect_sdate').val(),
            inspect_edate: $('#inspect_edate').val(),
            inspect_total: $('#inspect_total').val().replace(',', ''),
            inspect_result: $('#inspect_result').val(),
            inspect_user: $('#inspect_user').val(),
            remark: $('#remark').val(),
        };

        $http.post(`${CONFIG.baseUrl}/inspections/store`, data)
        .then(function(res) {
            console.log(res.data);
        }, function(err) {
            console.log(err);
        });

        $('#inspect-form').modal('hide');
    };

    $scope.inspections = [];
    $scope.withdrawal = {
        withdraw_no: '',
        withdraw_date: '',
        inspection_id: '',
        order_id: '',
        deliver_seq: '',
        deliver_no: '',
        net_total: '',
        remark: ''
    };
    $scope.showWithdrawForm = (order) => {
        if (order) {
            $http.get(`${CONFIG.baseUrl}/inspections/${order.id}/order`)
            .then(function(res) {
                $scope.inspections = res.data.inspections;

                $('#withdraw-form').modal('show');
            }, function(err) {
                console.log(err);
            });
        }
    };

    $scope.onDeliverSeqSelected = (seq) => {
        const inspection = $scope.inspections.find(insp => insp.deliver_seq === parseInt(seq));

        $scope.withdrawal.inspection_id = inspection.id;
        $scope.withdrawal.order_id = inspection.order_id;
        $scope.withdrawal.deliver_no = inspection.deliver_no;
        $scope.withdrawal.net_total = inspection.inspect_total;
    };

    $scope.onWithdraw = (e) => {
        e.preventDefault();

        console.log($scope.withdrawal);

        $http.post(`${CONFIG.baseUrl}/withdrawals/store`, $scope.withdrawal)
        .then(function(res) {
            console.log(res.data);
        }, function(err) {
            console.log(err);
        });

        $('#withdraw-form').modal('hide');

        /** Clear withdrawal data */
        $scope.withdrawal = {
            withdraw_no: '',
            withdraw_date: '',
            inspection_id: '',
            order_id: '',
            deliver_seq: '',
            deliver_no: '',
            net_total: '',
            remark: ''
        };
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $http.post(`${CONFIG.baseUrl}/orders/store`, $scope.order)
        .then(res => {
            console.log(res);
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกใบสั่งซื้อเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกใบสั่งซื้อได้ !!!");
            }
        }, err => {
            console.log(err);
        });

        // window.location.href = `${CONFIG.baseUrl}/orders/list`;
    }

    $scope.edit = function(id) {
        $http.get(`${CONFIG.baseUrl}/orders/getOrder/${id}`)
        .then(res => {
            $scope.order.id = res.data.order.id;
            $scope.order.year = res.data.order.year.toString();
            $scope.order.supplier_id = res.data.order.supplier.supplier_name;
            $scope.order.po_no = res.data.order.po_no;
            $scope.order.po_date = StringFormatService.convFromDbDate(res.data.order.po_date);
            $scope.order.deliver_amt = res.data.order.deliver_amt;
            $scope.order.plan_type_id = res.data.order.plan_type_id;
            $scope.order.remark = res.data.order.remark;
            $scope.order.total = res.data.order.total;
            $scope.order.vat_rate = res.data.order.vat_rate+'%';
            $scope.order.vat = res.data.order.vat;
            $scope.order.net_total = res.data.order.net_total;
            $scope.order.status = res.data.order.status;
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
