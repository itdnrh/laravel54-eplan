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
        id: '',
        year: '2566',
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
        prepaid_person: '',
        prepaid_person_detail: '',
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

        $http.get(`${CONFIG.baseUrl}/orders/search?status=2-3`)
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

        $http.get(`${CONFIG.baseUrl}/orders/search?type=${type}&cate=${cate}&status=2-3`)
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
        console.log(data);

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

    $scope.showPersonList = (_selectedMode) => {
        /** Set default depart of persons list to same user's depart */
        $scope.cboDepart = '2';

        $('#persons-list').modal('show');

        $scope.getPersons();

        $scope.selectedMode = _selectedMode;
    };

    $scope.getPersons = async () => {
        $scope.loading = true;
        $scope.persons = [];
        $scope.persons_pager = null;

        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;
        let keyword = !$scope.searchKey ? '' : $scope.searchKey;

        $http.get(`${CONFIG.baseUrl}/persons/search?depart=${depart}&name=${keyword}`)
        .then(function(res) {
            $scope.setPersons(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPersonsWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.persons = [];
        $scope.persons_pager = null;

        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;
        let keyword = !$scope.searchKey ? '' : $scope.searchKey;

        $http.get(`${url}&depart=${depart}&name=${keyword}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setPersons = function(res) {
        const { data, ...pager } = res.data.persons;

        $scope.persons = data;
        $scope.persons_pager = pager;
    };

    $scope.onSelectedPerson = (mode, person) => {
        if (person) {
            $scope.order.supply_officer_detail = person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname;
            $scope.order.supply_officer = person.person_id;
        }

        $('#persons-list').modal('hide');
        $scope.selectedMode = '';
    };

    $scope.getById = function(id) {
        $scope.loading = true;

        $http.get(`${CONFIG.baseUrl}/withdrawals/get-ajax-byid/${id}`)
        .then(function(res) {
            const { inspection, supplier, ...withdrawal } = res.data.withdrawal;

            $scope.withdrawal.id = withdrawal.id;
            $scope.withdrawal.order = inspection.order;
            $scope.withdrawal.inspection = inspection;
            $scope.withdrawal.withdraw_no = withdrawal.withdraw_no;
            $scope.withdrawal.withdraw_date = withdrawal.withdraw_date;
            $scope.withdrawal.net_total = withdrawal.net_total;
            $scope.withdrawal.year = withdrawal.year;
            $scope.withdrawal.remark = withdrawal.remark;
            $scope.withdrawal.completed = withdrawal.completed;
            $scope.withdrawal.supplier = supplier;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.showWithdrawForm = (e) => {
        $('#withdraw-form').modal('show');
    };

    $scope.errors = {};
    $scope.withdraw = (e) => {
        if ($('#withdraw_no').val() == '') {
            $scope.errors = {
                ...$scope.errors,
                withdraw_no: ['กรุณาระบุเลขที่หนังสือส่งเบิกเงิน']
            }
        } else {
            if ($scope.errors && $scope.errors.hasOwnProperty('withdraw_no')) {
                const { withdraw_no, ...err } = $scope.errors;

                $scope.errors = { ...err }
            }
        }

        if ($('#withdraw_date').val() == '') {
            $scope.errors = {
                ...$scope.errors,
                withdraw_date: ['กรุณาระบุวันที่หนังสือส่งเบิกเงิน']
            }
        } else {
            if ($scope.errors && $scope.errors.hasOwnProperty('withdraw_date')) {
                const { withdraw_date, ...err } = $scope.errors;
    
                $scope.errors = { ...err }
            }
        }

        if (Object.keys($scope.errors).length == 0) {
            console.log($scope.withdrawal);
            let data = { withdraw_no: $('#withdraw_no').val(), withdraw_date: $('#withdraw_date').val() };

            $http.put(`${CONFIG.apiUrl}/withdrawals/${$scope.withdrawal.id}`, data)
            .then(function(res) {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ส่งบันทึกขอสนับสนุนเรียบร้อย !!!");

                    $scope.withdrawal.withdraw_no = res.data.withdrawal.withdraw_no;
                    $scope.withdrawal.withdraw_date = res.data.withdrawal.withdraw_date;

                    sendToDebt($scope.withdrawal);
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถส่งบันทึกขอสนับสนุนได้ !!!");
                }
            }, function(err) {
                console.log(err);
            });

            $('#withdraw-form').modal('hide');
        }
    };

    const sendToDebt = function(withdrawal) {
        const data = {
            withdraw_id: withdrawal.id,
            deliver_no: withdrawal.inspection.deliver_no,
            deliver_date: withdrawal.inspection.deliver_date,
            year: withdrawal.year,
            supplier_id: withdrawal.supplier.supplier_id,
            desc: `${withdrawal.inspection.remark}`,
            po: `ใบสั่งซื้อ/จ้างเลขที่ ${withdrawal.order.po_no} วันที่ ${withdrawal.order.po_date}`,
            items: '',
            amount: withdrawal.order.total,
            vatrate: withdrawal.order.vat_rate,
            vat: withdrawal.order.vat,
            total: withdrawal.order.net_total,
            remark: withdrawal.remark,
        };

        $http.post(`${CONFIG.accApiUrl}/tmp-debts`, data)
        .then(function(res) {
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "ส่งบันทึกขอสนับสนุนเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถส่งบันทึกขอสนับสนุนได้ !!!");
            }
        }, function(err) {
            console.log(err);
        });
    }

    $scope.store = function(event, form) {
        event.preventDefault();

        $http.post(`${CONFIG.baseUrl}/withdrawals/store`, $scope.withdrawal)
        .then(function(res) {
            console.log(res.data);
        }, function(err) {
            console.log(err);
        });

        window.location.href = `${CONFIG.baseUrl}/orders/withdraw`;
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
