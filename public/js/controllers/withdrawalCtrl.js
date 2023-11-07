app.controller('withdrawalCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
    /** ################################################################################## */
    $scope.loading = false;
    $scope.cboYear = '2567';
    $scope.cboSupplier = '';
    $scope.cboIsCompleted = '1';
    $scope.txtWithdrawNo = '';
    $scope.dtpSdate = '';
    $scope.dtpEdate = '';

    /** Iterating models */
    $scope.sumWithdrawals = 0;
    $scope.withdrawals = [];
    $scope.pager = null;

    $scope.inspections = [];
    $scope.inspections_pager = null;

    $scope.orders = [];
    $scope.orders_pager = null;

    $scope.withdrawal = {
        id: '',
        year: '2567',
        order: null,
        order_id: '',
        doc_prefix: '',
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

    $('#dtpSdate')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            $('#dtpSdate')
                .datepicker(dtpOptions)
                .datepicker('update', event.date);

            $('#dtpEdate')
                .datepicker(dtpOptions)
                .datepicker('update', moment(event.date).endOf('month').toDate());

            $scope.getAll(event);
        });

    $('#dtpEdate')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            $('#dtpEdate')
                .datepicker(dtpOptions)
                .datepicker('update', event.date);

            $scope.getAll(event);
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
            $scope.setOrders(res);

            $scope.loading = false;

            $('#orders-list').modal('show');
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getOrders = () => {
        $scope.loading = true;
        $scope.orders = [];
        $scope.orders_pager = null;

        let type    = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let po_no   = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;

        $http.get(`${CONFIG.baseUrl}/orders/search?type=${type}&cate=${cate}&po_no=${po_no}&status=2-3`)
        .then(function(res) {
            $scope.setOrders(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getOrdersWithUrl = (e, url, cb) => {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.orders = [];
        $scope.orders_pager = null;

        let type    = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let po_no   = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;

        $http.get(`${url}&type=${type}&cate=${cate}&po_no=${po_no}&status=2-3`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setOrders = function(res) {
        const { data, ...pager } = res.data.orders;

        $scope.orders = data;
        $scope.orders_pager = pager;
    };

    $scope.onSelectedOrder = (e, order) => {
        if (order) {
            $scope.withdrawal.order          = order;
            $scope.withdrawal.order_id       = order.id;
            $scope.withdrawal.inspections    = order.inspections;
            $scope.withdrawal.supplier_id    = order.supplier.supplier_id;
            $scope.withdrawal.supplier       = order.supplier;
        }

        $('#orders-list').modal('hide');
    };

    $scope.onDeliverSeqSelected = function(seq) {
        const inspection = $scope.withdrawal.inspections.find(insp => insp.deliver_seq === parseInt(seq));

        $scope.withdrawal.inspection_id = inspection.id;
        $scope.withdrawal.deliver_no    = inspection.deliver_no;
        $scope.withdrawal.deliver_date  = inspection.deliver_date;
        $scope.withdrawal.net_total     = inspection.inspect_total;
    };

    $scope.getAll = function() {
        $scope.loading = true;

        $scope.withdrawals = [];
        $scope.pager = null;

        let year        = $scope.cboYear === '' ? 0 : $scope.cboYear;
        let supplier    = $scope.cboSupplier === '' ? 0 : $scope.cboSupplier;
        let doc_no      = $scope.txtWithdrawNo === '' ? 0 : $scope.txtWithdrawNo;
        let sdate       = $scope.dtpSdate === '' ? '' : $scope.dtpSdate;
        let edate       = $scope.dtpEdate === '' ? '' : $scope.dtpEdate;
        let completed   = $scope.cboIsCompleted === '' ? '' : $scope.cboIsCompleted;
        
        $http.get(`${CONFIG.baseUrl}/withdrawals/search?year=${year}&doc_no=${doc_no}&supplier=${supplier}&date=${sdate}-${edate}&completed=${completed}`)
        .then(function(res) {
            $scope.setWithdrawals(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getWithdrawalsWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.withdrawals = [];
        $scope.pager = null;

        let year        = $scope.cboYear === '' ? 0 : $scope.cboYear;
        let supplier    = $scope.cboSupplier === '' ? 0 : $scope.cboSupplier;
        let doc_no      = $scope.txtWithdrawNo === '' ? 0 : $scope.txtWithdrawNo;
        let sdate       = $scope.dtpSdate === '' ? '' : $scope.dtpSdate;
        let edate       = $scope.dtpEdate === '' ? '' : $scope.dtpEdate;
        let completed   = $scope.cboIsCompleted === '' ? '' : $scope.cboIsCompleted;

        $http.get(`${url}&year=${year}&doc_no=${doc_no}&supplier=${supplier}&date=${sdate}-${edate}&completed=${completed}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setWithdrawals = function (res) {
        console.log(res);
        const { data, ...pager } = res.data.withdrawals;

        $scope.withdrawals = data;
        $scope.pager = pager;

        $scope.sumWithdrawals = res.data.sumWithdrawals;
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
            $scope.withdrawal.prepaid_person_detail = person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname;
            $scope.withdrawal.prepaid_person = person.person_id;
        }

        $('#persons-list').modal('hide');
        $scope.selectedMode = '';
    };

    $scope.getById = function(id, cb) {
        $scope.loading = true;

        $http.get(`${CONFIG.apiUrl}/withdrawals/${id}`)
        .then(function(res) {
            cb(res)

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setEditControls = function(res) {
        const { inspection, supplier, prepaid, ...withdrawal } = res.data.withdrawal;

        if (withdrawal.withdraw_no) {
            const [prefix, doc_no] = withdrawal.withdraw_no.split("/");
            $scope.withdrawal.doc_prefix = prefix;
            $scope.withdrawal.withdraw_no = doc_no;
        }

        $scope.withdrawal.id = withdrawal.id;
        $scope.withdrawal.year = withdrawal.year;
        $scope.withdrawal.withdraw_date = withdrawal.withdraw_date;
        $scope.withdrawal.order = inspection.order;
        $scope.withdrawal.inspection = inspection;
        $scope.withdrawal.supplier = supplier;
        $scope.withdrawal.net_total = withdrawal.net_total;
        $scope.withdrawal.prepaid_person_detail = prepaid ? prepaid.prefix.prefix_name+prepaid.person_firstname+ ' ' +prepaid.person_lastname : '';
        $scope.withdrawal.prepaid_person = withdrawal.prepaid_person;
        $scope.withdrawal.completed = withdrawal.completed;
        $scope.withdrawal.remark = withdrawal.remark;

        $('#withdraw_date')
            .datepicker(dtpOptions)
            .datepicker('update', moment(withdrawal.withdraw_date).toDate());
    };

    $scope.showWithdrawForm = (e) => {
        $scope.loading = false;

        $('#withdraw-form').modal('show');
    };

    $scope.withdraw = (e, frm) => {
        e.preventDefault();

        if (frm.$invalid) {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณากรอกข้อมูลให้ครบ !!!");
            return;
        }

        if(confirm(`คุณต้องการส่งเบิกเงิน รหัส ${$scope.withdrawal.id} ใช่หรือไม่?`)) {
            $scope.loading = true;

            let data = { withdraw_no: $('#withdraw_no').val(), withdraw_date: $('#withdraw_date').val() };

            $http.put(`${CONFIG.apiUrl}/withdrawals/${$scope.withdrawal.id}`, data)
            .then(function(res) {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ส่งเบิกเงินเรียบร้อย !!!");

                    $scope.withdrawal.completed = res.data.withdrawal.completed;
                    $scope.withdrawal.withdraw_no = res.data.withdrawal.withdraw_no;
                    $scope.withdrawal.withdraw_date = res.data.withdrawal.withdraw_date;

                    sendToDebt($scope.withdrawal);

                    $('#withdraw-form').modal('hide');
                } else {
                    toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถส่งเบิกเงินได้ !!!");
                }
            }, function(err) {
                console.log(err);

                $scope.loading = false;

                toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถส่งเบิกเงินได้ !!!");
            });
        }
    };

    const sendToDebt = function(withdrawal) {
        const data = {
            withdraw_id: withdrawal.id,
            withdraw_no: withdrawal.withdraw_no,
            withdraw_date: withdrawal.withdraw_date,
            deliver_no: withdrawal.inspection.deliver_no,
            deliver_date: withdrawal.inspection.deliver_date,
            year: withdrawal.year,
            supplier_id: withdrawal.supplier.supplier_id,
            supplier_name: withdrawal.supplier.supplier_name,
            desc: '',
            po_no: withdrawal.order.po_no,
            po_date: withdrawal.order.po_date,
            items: '',
            amount: withdrawal.order.total,
            vatrate: withdrawal.order.vat_rate,
            vat: withdrawal.order.vat,
            total: withdrawal.order.net_total,
            remark: withdrawal.remark,
            user: $('#user').val()
        };

        $http.post(`${CONFIG.accApiUrl}/tmp-debts`, data)
        .then(function(res) {
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "ส่งเบิกเงินเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการทำงาน", "ไม่สามารถส่งเบิกเงินได้ !!!");
            }
        }, function(err) {
            console.log(err);

            toaster.pop('error', "ผลการทำงาน", "ไม่สามารถส่งเบิกเงินได้ !!!");
        });
    }

    $scope.store = function(event, form) {
        event.preventDefault();
        $scope.loading = true;

        $scope.withdrawal.user = $('#user').val();

        $http.post(`${CONFIG.baseUrl}/withdrawals/store`, $scope.withdrawal)
        .then(function(res) {
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");

                window.location.href = `${CONFIG.baseUrl}/orders/withdraw`;
            } else {
                toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ !!!");
            }
        }, function(err) {
            console.log(err);
            toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ !!!");

            $scope.loading = false;
        });
    }

    $scope.edit = function(id) {
        $http.get(`${CONFIG.apiUrl}/withdrawals/${id}`)
        .then(res => {
            const { inspection, supplier, prepaid, ...withdrawal } = res.data.withdrawal;

            if (withdrawal.withdraw_no) {
                const [prefix, doc_no] = withdrawal.withdraw_no.split("/");
                $scope.withdrawal.doc_prefix = prefix;
                $scope.withdrawal.withdraw_no = doc_no;
            }

            $scope.withdrawal.id = withdrawal.id;
            $scope.withdrawal.year = withdrawal.year.toString();
            $scope.withdrawal.order_id = inspection.order_id;
            $scope.withdrawal.order = inspection.order;
            $scope.withdrawal.inspection_id = withdrawal.inspection_id;
            $scope.withdrawal.inspection = inspection;
            $scope.withdrawal.supplier_id = supplier.supplier_id;
            $scope.withdrawal.supplier = supplier;
            $scope.withdrawal.deliver_seq = inspection.deliver_seq.toString();
            $scope.withdrawal.deliver_no = inspection.deliver_no;
            $scope.withdrawal.deliver_date = inspection.deliver_date;
            $scope.withdrawal.net_total = withdrawal.net_total;
            $scope.withdrawal.prepaid_person_detail = prepaid ? prepaid.prefix.prefix_name+prepaid.person_firstname+ ' ' +prepaid.person_lastname : '';
            $scope.withdrawal.prepaid_person = withdrawal.prepaid_person;
            $scope.withdrawal.remark = withdrawal.remark;

            $scope.withdrawal.inspections    = res.data.inspections;

            $('#po_date')
                .datepicker(dtpOptions)
                .datepicker('update', moment(inspection.order.po_date).toDate());

            $('#withdraw_date')
                .datepicker(dtpOptions)
                .datepicker('update', moment(withdrawal.withdraw_date).toDate());
        }, err => {
            console.log(err);
        });
    };

    $scope.update = function(event, form) {
        event.preventDefault();

        if(confirm(`คุณต้องการแก้ไขรายการส่งเบิกเงิน รหัส ${$scope.withdrawal.id} ใช่หรือไม่?`)) {
            $scope.loading = true;
            $scope.withdrawal.user = $('#user').val();

            $http.post(`${CONFIG.baseUrl}/withdrawals/update/${$scope.withdrawal.id}`, $scope.withdrawal)
            .then(function(res) {
                console.log(res);
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/orders/withdraw`;
                } else {
                    toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถแก้ไขข้อมูลได้ !!!");
                }
            }, function(err) {
                console.log(err);
                $scope.loading = false;

                toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถแก้ไขข้อมูลได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.delete = function(e, id, withdraw) {
        e.preventDefault();

        if (window.confirm(`คุณต้องการลบรายการส่งเบิกเงิน รหัส ${id} ใช่หรือไม่?`)) {
            $http.post(`${CONFIG.baseUrl}/withdrawals/delete/${id}`, { order_id: withdraw.inspection.order_id })
            .then(function(res) {
                console.log(res);
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลบข้อมูลเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/orders/withdraw`;
                } else {
                    toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถลบข้อมูลได้ !!!");
                }
            }, function(err) {
                console.log(err);
                $scope.loading = false;

                toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถลบข้อมูลได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.cancel = function(e, id, withdraw) {
        e.preventDefault();

        if (window.confirm(`คุณต้องการยกเลิกส่งเบิกเงิน รหัส ${id} ใช่หรือไม่?`)) {
            $http.put(`${CONFIG.apiUrl}/withdrawals/${id}/cancel`, { order_id: withdraw.inspection.order_id })
            .then(function(res) {
                console.log(res);
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ยกเลิกข้อมูลเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/orders/withdraw`;
                } else {
                    toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถยกเลิกข้อมูลได้ !!!");
                }
            }, function(err) {
                console.log(err);
                $scope.loading = false;

                toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถยกเลิกข้อมูลได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };

    const cancelSendToDebt = function(withdrawal) {
        $http.put(`${CONFIG.accApiUrl}/tmp-debts/${withdrawal.id}/pending`)
        .then(function(res) {
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "ยกเลิกส่งเบิกเงินเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการทำงาน", "ไม่สามารถยกเลิกส่งเบิกเงินได้ !!!");
            }
        }, function(err) {
            console.log(err);

            toaster.pop('error', "ผลการทำงาน", "ไม่สามารถยกเลิกส่งเบิกเงินได้ !!!");
        });
    }

    const reSendToDebt = function(withdrawal) {
        $http.put(`${CONFIG.accApiUrl}/tmp-debts/${withdrawal.id}/resend`)
        .then(function(res) {
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "ส่งเบิกเงินเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการทำงาน", "ไม่สามารถส่งเบิกเงินได้ !!!");
            }
        }, function(err) {
            console.log(err);

            toaster.pop('error', "ผลการทำงาน", "ไม่สามารถส่งเบิกเงินได้ !!!");
        });
    }
});
