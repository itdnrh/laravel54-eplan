app.controller('orderCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
    /** ################################################################################## */
    $scope.loading = false;
    $scope.vatRates = [0,7];
    $scope.editRow = false;
    $scope.cboYear = '2566'; //(moment().year() + 543).toString();
    $scope.cboSupplier = '';
    $scope.cboStatus = '0';
    $scope.txtSupportNo = '';
    $scope.txtReceivedNo = '';
    $scope.txtPoNo = '';
    $scope.searchKey = '';

    $scope.orders = [];
    $scope.pager = null;

    $scope.plans = [];
    $scope.plans_pager = null;

    $scope.order = {
        po_no: '',
        po_date: '',
        po_req_no: '',
        po_req_prefix: '',
        po_req_date: '',
        po_app_no: '',
        po_app_prefix: '',
        po_app_date: '',
        support_order_id: '',
        year: '2566',
        order_type_id: '',
        plan_type_id: '',
        deliver_amt: 1,
        is_plan_group: false,
        plan_group_desc: '',
        plan_group_amt: 0,
        total: '',
        vat_rate: '7',
        vat: '',
        net_total: '',
        net_total_str: '',
        budget_src_id: '',
        supply_officer: '',
        supply_officer_detail: '',
        remark: '',
        details: [],
        removed: []
    };

    $scope.newItem = {
        plan_no: '',
        plan_detail: '',
        category_name: '',
        plan_depart: '',
        support_id: '',
        support_detail_id: '',
        plan_id: '',
        item_id: '',
        desc: '',
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
            $('#po_app_date')
                .datepicker(dtpOptions)
                .datepicker('update', event.date);

            $('#po_req_date')
                .datepicker(dtpOptions)
                .datepicker('update', event.date)
        });

    $('#po_app_date')
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

    $('#spec_doc_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
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

    $scope.setSupportToOrder = function(support) {
        if (support) {
            $scope.order.plan_type_id = support.plan_type_id.toString();
            $scope.order.order_type_id = [1,2].includes(support.plan_type_id) ? '1' : '';
            // $scope.order.support_order_id = support.plan_type_id;
            $scope.order.supply_officer = support.supply_officer;
            $scope.order.supply_officer_detail = support.officer.prefix.prefix_name+support.officer.person_firstname+ ' ' +support.officer.person_lastname;

            if (support.is_plan_group == 1) {
                $scope.order.is_plan_group = true;
                $scope.order.plan_group_desc = support.plan_group_desc;
                $scope.order.plan_group_amt = support.plan_group_amt;
            }

            support.details.forEach(item => {
                const orderItem = {
                    plan_no: item.plan.plan_no,
                    plan_depart: support.division ? support.division.ward_name : support.depart.depart_name,
                    plan_detail: item.plan.plan_item.item.item_name,
                    category_name: item.plan.plan_item.item.category.name,
                    plan_id: item.plan.id,
                    item_id: item.plan.plan_item.item_id,
                    support_id: support.id,
                    support_detail_id: item.id,
                    desc: item.desc,
                    spec: '',
                    price_per_unit: item.price_per_unit,
                    unit_id: item.unit.id,
                    unit_name: item.unit.name,
                    amount: item.amount,
                    sum_price: item.sum_price
                };

                $scope.order.details.push(orderItem);
            });

            $scope.calculateNetTotal();
        }
    };

    $scope.clearNewItem = () => {
        $scope.newItem = {
            plan_no: '',
            plan_detail: '',
            plan_depart: '',
            support_id: '',
            support_detail_id: '',
            plan_id: '',
            item_id: '',
            spec: '',
            price_per_unit: '',
            unit_id: '',
            unit_name: '',
            amount: '',
            sum_price: ''
        };
    };

    $scope.calculateSumPrice = function(e) {
        let price = e.target.name == 'price_per_unit'
                        ? parseFloat($scope.currencyToNumber($(e.target).val()))
                        : parseFloat($scope.currencyToNumber($(`#price_per_unit`).val()));
        let amount = e.target.name == 'amount'
                        ? parseFloat($scope.currencyToNumber($(e.target).val()))
                        : parseFloat($scope.currencyToNumber($(`#amount`).val()));

        $scope.newItem.sum_price = price * amount;
        $('#sum_price').val(price * amount);
    };

    $scope.calculateVat = function() {
        let net_total = parseFloat($scope.currencyToNumber($(`#net_total`).val()));
        let rate = parseFloat($scope.order.vat_rate);
        let vat = (net_total * rate) / (100 + rate);

        $scope.order.vat = vat.toFixed(2);
        $('#vat').val(vat.toFixed(2));

        $scope.calculateTotal();
    };

    $scope.calculateNetTotal = function() {
        let net_total = 0;

        net_total = $scope.order.details.reduce((sum, curVal) => {
            return sum = sum + curVal.sum_price;
        }, 0);

        $scope.order.net_total = net_total;
        $scope.order.net_total_str = StringFormatService.arabicNumberToText(net_total);
        $('#net_total').val(net_total);

        $scope.calculateVat();
    };

    $scope.calculateTotal = () => {
        let net_total = parseFloat($scope.currencyToNumber($(`#net_total`).val()));
        let vat = parseFloat($scope.currencyToNumber($(`#vat`).val()));
        let total = net_total - vat;

        $scope.order.total = total;
        $('#total').val(total);
    };

    $scope.isSelected = function(planId) {
        if ($scope.order.details.length == 0) return false;

        return $scope.order.details.some(item => item.plan_id === planId && item.plan.calc_method == 1);
    };

    $scope.addOrderItem = () => {
        $scope.order.details.push({ ...$scope.newItem });

        $scope.calculateNetTotal();
        $scope.clearNewItem();
    };

    $scope.removeOrderItem = (selectedIndex) => {
        $scope.order.details = $scope.order.details.filter((d, index) => index !== selectedIndex);

        $scope.calculateNetTotal();
    };

    $scope.removePlanGroup = (e) => {
        $scope.order.is_plan_group = false;
        $scope.order.plan_group_desc = '';
        $scope.order.plan_group_amt = 0.0;

        $scope.order.details = [];

        $scope.calculateNetTotal();
    }

    $scope.editRowIndex = '';
    $scope.toggleEditRow = function(selectedIndex = '') {
        $scope.editRow = !$scope.editRow;
        $scope.editRowIndex = selectedIndex;
    };

    $scope.onEditItem = (selectedIndex) => {
        $scope.toggleEditRow(selectedIndex);

        if (selectedIndex != -1) {
            /** Set select input as select2 */
            $(`#unit_id_${selectedIndex}`).select2({ theme: 'bootstrap' });
    
            let detail = $scope.order.details.find((d, index) => index === selectedIndex);
    
            if (detail) {
                $scope.newItem.price_per_unit   = detail.price_per_unit;
                $scope.newItem.unit_id          = detail.unit_id.toString();
                $scope.newItem.amount           = detail.amount;
                $scope.newItem.sum_price        = detail.sum_price;
    
                $(`#unit_id_${selectedIndex}`).val(detail.unit_id).trigger('change.select2');
            }
        } else {
            $(`#unit_id`).select2({ theme: 'bootstrap' });

            $scope.newItem.price_per_unit   = $scope.order.details[0].price_per_unit;
            $scope.newItem.unit_id          = $scope.order.details[0].unit_id.toString();
            $scope.newItem.amount           = $scope.order.plan_group_amt;
            $scope.newItem.sum_price        = $scope.order.net_total;

            $(`#unit_id`).val($scope.order.details[0].unit_id).trigger('change.select2');
        }
    };

    $scope.confirmEditedItem = (selectedIndex) => {
        let edittedData = $scope.order.details.map((d, index) => {
            if (index === selectedIndex) {
                d.price_per_unit    = $scope.currencyToNumber($scope.newItem.price_per_unit);
                d.unit_id           = $scope.newItem.unit_id;
                d.unit_name         = $(`#unit_id_${selectedIndex} option:selected`).text().replace(/^\s+|\s+$|[\r\n]+/g, "");
                d.amount            = $scope.currencyToNumber($scope.newItem.amount);
                d.sum_price         = $scope.currencyToNumber($scope.newItem.sum_price);
            }

            if (selectedIndex == -1) {
                d.price_per_unit    = $scope.currencyToNumber($scope.newItem.price_per_unit);
                d.unit_id           = $scope.newItem.unit_id;
                d.unit_name         = $(`#unit_id option:selected`).text().replace(/^\s+|\s+$|[\r\n]+/g, "");
                d.sum_price         = d.price_per_unit * d.amount;
            }

            return d;
        });

        $scope.order.details = edittedData;
        $scope.calculateNetTotal();

        /** Clear data */
        $scope.clearNewItem();
        $scope.toggleEditRow();
    };

    $scope.selectedIndex = '';
    $scope.showSpecForm = function(index) {
        $scope.selectedIndex = index;

        $('#spec-form').modal('show');
    };

    $scope.addSpec = function(e) {
        if ($scope.selectedIndex == -1) {
            $scope.order.details.forEach(item => {
                item.spec = $('#planGroup_spec').val();
            });
        }

        $('#spec-form').modal('hide');
    };

    $scope.planGroupItems = [];
    $scope.showPlanGroupItems = function(e, items) {
        e.preventDefault();

        if (items.length > 0) {
            $scope.planGroupItems = items;

            $('#plan-group-items').modal('show');
        }
    };

    $scope.deletePlanGroupItem = function(e, item) {
        e.preventDefault();

        if (item) {
            $scope.planGroupItems = $scope.planGroupItems.filter(it => it.plan_id !== item.plan_id);

            $scope.order.details = $scope.order.details.filter(plan => plan.plan_id !== item.plan_id);

            $scope.calculateNetTotal();
        }
    };

    $scope.showPlanGroupsList = (type) => {
        if (type == '') {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณาเลือกประเภทแผนก่อน !!!");
        } else {
            $scope.loading = true;
            $scope.planGroups = [];
            $scope.planGroups_pager = null;
            $scope.plans = [];

            let year = $scope.order.year === '' ? '' : 2566;

            $http.get(`${CONFIG.apiUrl}/supports/details/group?year=${year}&type=${type}&status=2`)
            .then(function(res) {
                $scope.setPlanGroups(res);

                $scope.loading = false;

                $('#plan-groups-list').modal('show');
            }, function(err) {
                console.log(err);
                $scope.loading = false;
            });
        }
    };

    $scope.getPlanGroupsList = (type) => {
        $scope.loading = true;
        $scope.planGroups = [];
        $scope.planGroups_pager = null;
        $scope.plans = [];

        let year = $scope.order.year === '' ? '' : 2566;

        $http.get(`${CONFIG.apiUrl}/supports/details/group?year=${year}&type=${type}&status=2`)
        .then(function(res) {
            $scope.setPlanGroups(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPlanGroupsListWithUrl = (e, url, type, cb) => {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.planGroups = [];
        $scope.planGroups_pager = null;
        $scope.plans = [];

        let year = $scope.order.year === '' ? '' : 2566;

        $http.get(`${url}&year=${year}&type=${type}&status=2`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;

            $('#plan-groups-list').modal('show');
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setPlanGroups = function(res) {
        const { data, ...pager } = res.data.planGroups;

        $scope.planGroups = data;
        $scope.planGroups_pager = pager;
        $scope.plans = res.data.plans;
    };

    $scope.onSelectedPlanGroup = function(e, planGroup) {
        if (planGroup) {
            $scope.order.is_plan_group = true;
            $scope.order.plan_group_desc = planGroup.item_name;
            $scope.order.plan_group_amt = planGroup.amount;

            const plans = $scope.plans.filter(plan => plan.plan.plan_item.item_id == planGroup.item_id);

            plans.forEach(plan => {
                $scope.newItem = {
                    plan_no: plan.plan.plan_no,
                    plan_depart: plan.support.division ? plan.support.division.ward_name : plan.support.depart.depart_name,
                    plan_detail: plan.plan.plan_item.item.item_name,
                    category_name: plan.plan.plan_item.item.category.name,
                    plan_id: plan.plan.id,
                    item_id: plan.plan.plan_item.item_id,
                    support_id: plan.support.id,
                    support_detail_id: plan.id,
                    desc: plan.desc,
                    spec: '',
                    price_per_unit: plan.price_per_unit,
                    unit_id: plan.unit.id,
                    unit_name: plan.unit.name,
                    amount: plan.amount,
                    sum_price: plan.sum_price
                };

                $scope.addOrderItem();
            });
        }

        $('#plan-groups-list').modal('hide');
    };

    $scope.showPlansList = (type) => {
        if (type == '') {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณาเลือกประเภทแผนก่อน !!!");
        } else {
            $scope.loading = true;
            $scope.plans = [];
            $scope.plans_pager = null;

            let year = $scope.order.year === '' ? '' : 2566;

            $http.get(`${CONFIG.apiUrl}/supports/details/list?year=${year}&type=${type}&status=2`)
            .then(function(res) {
                $scope.setPlans(res);

                $scope.loading = false;

                $('#plans-list').modal('show');
            }, function(err) {
                console.log(err);
                $scope.loading = false;
            });
        }
    };

    $scope.getPlans = (status) => {
        $scope.loading = true;
        $scope.plans = [];
        $scope.plans_pager = null;

        let year = $scope.order.year === '' ? '' : 2566;
        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        let cate = $scope.cboCategory == '' ? '' : $scope.cboCategory;
        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;

        $http.get(`${CONFIG.apiUrl}/supports/details/list?year=${year}type=${type}&cate=${cate}&depart=${depart}&status=${status}`)
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

        let year = $scope.order.year === '' ? '' : 2566;
        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        let cate = $scope.cboCategory == '' ? '' : $scope.cboCategory;
        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;

        $http.get(`${url}&year=${year}type=${type}&cate=${cate}&depart=${depart}&status=${status}`)
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
                plan_no: plan.plan.plan_no,
                plan_depart: plan.support.division ? plan.support.division.ward_name : plan.support.depart.depart_name,
                plan_detail: plan.plan.plan_item.item.item_name,
                category_name: plan.plan.plan_item.item.category.name,
                plan_id: plan.plan.id,
                item_id: plan.plan.plan_item.item_id,
                support_id: plan.support.id,
                support_detail_id: plan.id,
                desc: plan.desc,
                spec: '',
                price_per_unit: plan.price_per_unit,
                unit_id: plan.unit.id,
                unit_name: plan.unit.name,
                amount: plan.amount,
                sum_price: plan.sum_price
            };

            $scope.addOrderItem();
        }

        $('#plans-list').modal('hide');
    };

    $scope.onReceivePlan = function(e, plan) {
        $http.post(`${CONFIG.baseUrl}/orders/received/1`, { id: plan.id })
        .then(function(res) {
            console.log(res);
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "ลงรับเอกสารเรียบร้อย !!!");

                $scope.getReceiveds(2);
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลงรับเอกสารได้ !!!");
            }
        }, function(err) {
            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลงรับเอกสารได้ !!!");
        });
    };

    /** ============================================================================= */
    $scope.receiveds = [];
    $scope.receiveds_pager = null;
    $scope.getReceiveds = (status) => {
        $scope.loading = true;
        $scope.receiveds = [];
        $scope.receiveds_pager = null;

        let year = $scope.cboYear == '' ? '' : $scope.cboYear;
        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        let depart = !$scope.cboDepart ? '' : $scope.cboDepart;
        let doc_no = $scope.txtSupportNo == '' ? '' : $scope.txtSupportNo;
        let received_no = $scope.txtReceivedNo == '' ? '' : $scope.txtReceivedNo;

        $http.get(`${CONFIG.baseUrl}/supports/search?year=${year}&type=${type}&depart=${depart}&doc_no=${doc_no}&received_no=${received_no}&status=${status}`)
        .then(function(res) {
            $scope.setReceiveds(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getReceivedsWithUrl = function(e, url, status, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.receiveds = [];
        $scope.receiveds_pager = null;

        let year = $scope.cboYear == '' ? '' : $scope.cboYear;
        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        let depart = !$scope.cboDepart ? '' : $scope.cboDepart;
        let doc_no = $scope.txtSupportNo == '' ? '' : $scope.txtSupportNo;
        let received_no = $scope.txtReceivedNo == '' ? '' : $scope.txtReceivedNo;

        $http.get(`${url}&year=${year}&type=${type}&depart=${depart}&doc_no=${doc_no}&received_no=${received_no}&status=${status}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setReceiveds = function(res) {
        const { data, ...pager } = res.data.supports;

        $scope.receiveds = data;
        $scope.receiveds_pager = pager;
    };

    $scope.supports = [];
    $scope.supports_pager = null;
    $scope.getSupports = function(res) {
        $scope.loading = true;
        $scope.supports = [];
        $scope.supports_pager = null;

        let year = $scope.cboYear == '' ? '' : $scope.cboYear;
        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        // let cate = !$scope.cboCategory ? '' : $scope.cboCategory;
        let depart = !$scope.cboDepart ? '' : $scope.cboDepart;
        let doc_no = $scope.txtSupportNo == '' ? '' : $scope.txtSupportNo;

        $http.get(`${CONFIG.baseUrl}/supports/search?year=${year}&type=${type}&depart=${depart}&doc_no=${doc_no}&status=1`)
        .then(function(res) {
            $scope.loading = false;

            $scope.setSupports(res);

            $('#supports-receive').modal('show');
        }, function(err) {
            $scope.loading = false;
            console.log(err);
        });
    };

    $scope.getSupportsWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.supports = [];
        $scope.supports_pager = null;

        let year = $scope.cboYear == '' ? '' : $scope.cboYear;
        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        // let cate = !$scope.cboCategory ? '' : $scope.cboCategory;
        let depart = !$scope.cboDepart ? '' : $scope.cboDepart;
        let doc_no = $scope.txtSupportNo == '' ? '' : $scope.txtSupportNo;

        $http.get(`${url}&year=${year}&type=${type}&depart=${depart}&doc_no=${doc_no}&status=1`)
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

    $scope.receive = {
        support_id: '',
        received_no: '',
        received_date: '',
        officer: '',
        remark: ''
    };

    $scope.showReceiveSupportForm = function(e, support) {
        $scope.receive.support_id = support.id;

        $('#received_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            console.log(event.date);
        });

        $('#receive-form').modal('show');
    };

    $scope.onReceiveSupport = function(e, form, support) {
        e.preventDefault();

        if (form.$invalid) {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณากรอกข้อมูลให้ครบ !!!");
            return;
        }

        if (support) {
            $http.post(`${CONFIG.baseUrl}/orders/received/2`, $scope.receive)
            .then(function(res) {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลงรับเอกสารเรียบร้อย !!!");

                    /** Remove support data that has been received */
                    $scope.supports = $scope.supports.filter(el => el.id !== res.data.support.id);

                    $scope.getReceiveds(2);

                    $('#receive-form').modal('hide');
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลงรับเอกสารได้ !!!");
                }
            }, function(err) {
                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลงรับเอกสารได้ !!!");
            });
        }
    };

    $scope.cancel = function(e, id) {
        $scope.loading = true;

        if(confirm(`คุณต้องการยกเลิกรับเอกสารขอสนับสนุน รหัส ${id} ใช่หรือไม่?`)) {
            $http.put(`${CONFIG.apiUrl}/supports/${id}/cancel-received`, { status: 1 })
            .then(function(res) {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ยกเลิกรับบันทึกขอสนับสนุนเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/orders/received`;
                } else {
                    toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถยกเลิกรับบันทึกขอสนับสนุนได้ !!!");
                }

                $scope.loading = false;
            }, function(err) {
                $scope.loading = false;

                console.log(err);
                toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถยกเลิกรับบันทึกขอสนับสนุนได้ !!!");
            });
        }
    };

    $scope.returnData = {
        reason: '',
        support_id: '',
        user: ''
    };
    $scope.showReturnSupportForm = function(e, support) {
        console.log(support);
        if (support) {
            $scope.returnData.support_id = support.id;
    
            $('#return-form').modal('show');
        }
    };

    $scope.onReturnSupport = function(e, form, id) {
        e.preventDefault();

        if (form.$invalid) {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณากรอกข้อมูลให้ครบ !!!");
            return;
        }

        /** Add user's id from laravel auth user */
        $scope.returnData.user = $('#user').val();

        $http.put(`${CONFIG.apiUrl}/supports/${id}/return`, $scope.returnData)
            .then(function(res) {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ตีกลับเอกสารเรียบร้อย !!!");

                    $scope.getSupports();
                    $scope.getReceiveds(2);

                    $('#return-form').modal('hide');
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถตีกลับเอกสารได้ !!!");
                }
            }, function(err) {
                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถตีกลับเอกสารได้ !!!");
            });
    };

    $scope.showSpecCommitteeForm = function(e, id, supportOrders) {
        $('#spec-committee-form').modal('show');

        $scope.specCommittee.support_id = id;

        $('#spec_doc_date')
            .datepicker(dtpOptions)
            .datepicker('update', new Date());

        $('#report_doc_date')
            .datepicker(dtpOptions)
            .datepicker('update', new Date());

        if (supportOrders.length > 0) {
            $scope.specCommittee.purchase_method    = supportOrders[0].purchase_method.toString();
            $scope.specCommittee.source_price       = supportOrders[0].source_price.toString();
            $scope.specCommittee.spec_doc_no        = supportOrders[0].spec_doc_no;
            $scope.specCommittee.spec_doc_date      = supportOrders[0].spec_doc_date;
            $scope.specCommittee.report_doc_no      = supportOrders[0].report_doc_no;
            $scope.specCommittee.report_doc_date    = supportOrders[0].report_doc_date;
            $scope.specCommittee.is_existed         = true;

            $('#spec_doc_date')
                .datepicker(dtpOptions)
                .datepicker('update', moment(supportOrders[0].spec_doc_date).toDate());

            $('#report_doc_date')
                .datepicker(dtpOptions)
                .datepicker('update', moment(supportOrders[0].report_doc_date).toDate());
        }
    };

    $scope.specCommittee = {
        support_id: '',
        purchase_method: '1',
        source_price: '1',
        spec_doc_no: '',
        spec_doc_date: '',
        report_doc_no: '',
        report_doc_date: '',
        is_existed: false
    };

    $scope.clearSpecCommittee = function() {
        $scope.specCommittee = {
            support_id: '',
            purchase_method: '1',
            source_price: '1',
            spec_doc_no: '',
            spec_doc_date: '',
            report_doc_no: '',
            report_doc_date: '',
            is_existed: false
        };
    };

    $scope.onPrintSpecCommittee = function(e, id, isExisted=false) {
        if (isExisted) {
            $scope.clearSpecCommittee();

            $('#spec-committee-form').modal('hide');

            window.location.href = `${CONFIG.baseUrl}/supports/${id}/print/spec-committee`;
        } else {
            $http.post(`${CONFIG.apiUrl}/support-orders`, $scope.specCommittee)
            .then(res => {
                if (res.data.status) {
                    $scope.clearSpecCommittee();

                    $('#spec-committee-form').modal('hide');

                    window.location.href = `${CONFIG.baseUrl}/supports/${id}/print/spec-committee`;
                } else {

                }
            }, err => {
                console.log(err);
            });
        }
    };

    $scope.closeSpecCommitteeForm = function() {
        $scope.clearSpecCommittee();

        $('#spec-committee-form').modal('hide');
    };

    $scope.supportDetails = [];
    $scope.showDetailsList = function(e, details) {
        e.preventDefault();

        if (details.length > 0) {
            $scope.supportDetails = details;

            $('#details-list').modal('show');
        }
    };

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
            $scope.order.po_no = res.data.running+ '/' +$scope.order.year;

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

    $scope.orderDetails = [];
    $scope.showOrderDetails = (items) => {
        if (items) {
            $scope.orderDetails = items;
    
            $('#order-details').modal('show');
        }
    };

    $scope.showInspectForm = (order) => {
        if (order) {    
            $('#inspect-form').modal('show');
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

    $scope.selectedMode = '';
    $scope.onSelectedPerson = (mode, person) => {
        if (person) {
            $scope.order.supply_officer_detail = person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname;
            $scope.order.supply_officer = person.person_id;
        }

        $('#persons-list').modal('hide');
        $scope.selectedMode = '';
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

        if ($scope.order.details.length == 0) {
            toaster.pop('error', "ผลการตรวจสอบ", "คุณยังไม่ได้ระบุรายการที่จะจัดซื้อ/จ้าง !!!");
            return;
        }

        $scope.loading = true;

        $http.post(`${CONFIG.baseUrl}/orders/store`, $scope.order)
        .then(res => {
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกใบสั่งซื้อ/จ้างเรียบร้อย !!!");

                window.location.href = `${CONFIG.baseUrl}/orders/list`;
            } else {
                toaster.pop('error', "ผลการทำงาน", "ไม่สามารถบันทึกใบสั่งซื้อ/จ้างได้ !!!");
            }

            $scope.loading = false;
        }, err => {
            console.log(err);
            toaster.pop('error', "ผลการทำงาน", "ไม่สามารถบันทึกใบสั่งซื้อ/จ้างได้ !!!");

            $scope.loading = false;
        });
    }

    $scope.edit = function(id) {
        $http.get(`${CONFIG.baseUrl}/orders/getOrder/${id}`)
        .then(res => {
            if (res.data.order.po_req_no) {
                const [prefix, req_no] = res.data.order.po_req_no.split("/");
                $scope.order.po_req_prefix = prefix;
                $scope.order.po_req_no = req_no;
            }

            if (res.data.order.po_app_no) {
                const [prefix, app_no] = res.data.order.po_app_no.split("/");
                $scope.order.po_app_prefix = prefix;
                $scope.order.po_app_no = app_no;
            }

            $scope.order.id = res.data.order.id;
            $scope.order.year = res.data.order.year.toString();
            $scope.order.supplier_id = res.data.order.supplier.supplier_id.toString();
            $scope.order.supplier = res.data.order.supplier;
            $scope.order.po_no = res.data.order.po_no;
            $scope.order.po_date = StringFormatService.convFromDbDate(res.data.order.po_date);
            $scope.order.po_req_date = StringFormatService.convFromDbDate(res.data.order.po_req_date);
            $scope.order.po_app_date = StringFormatService.convFromDbDate(res.data.order.po_app_date);
            $scope.order.deliver_amt = res.data.order.deliver_amt;
            $scope.order.plan_type_id = res.data.order.plan_type_id.toString();
            $scope.order.plan_type = res.data.order.plan_type;
            $scope.order.order_type_id = res.data.order.order_type_id.toString();
            $scope.order.order_type = res.data.order.order_type;
            $scope.order.supply_officer = res.data.order.supply_officer;
            $scope.order.supply_officer_detail = res.data.order.officer.prefix.prefix_name+res.data.order.officer.person_firstname+ ' ' +res.data.order.officer.person_lastname;
            $scope.order.officer = res.data.order.officer;
            $scope.order.is_plan_group   = res.data.order.is_plan_group;
            $scope.order.plan_group_desc = res.data.order.plan_group_desc;
            $scope.order.plan_group_amt  = res.data.order.plan_group_amt;
            $scope.order.total = res.data.order.total;
            $scope.order.vat_rate = res.data.order.vat_rate;
            $scope.order.vat = res.data.order.vat;
            $scope.order.net_total = res.data.order.net_total;
            $scope.order.net_total_str = res.data.order.net_total_str;
            $scope.order.remark = res.data.order.remark;
            $scope.order.status = res.data.order.status;
            $scope.order.details = res.data.order.details.map(item => {
                const { plan, unit, ...other } = item;

                return {
                    plan_no: plan.plan_no,
                    plan_detail: plan.plan_item.item.item_name,
                    category_name: plan.plan_item.item.category.name,
                    plan_depart: plan.depart.depart_name,
                    unit_name: unit.name,
                    ...other
                }
            });

            $('#supplier_id').val(res.data.order.supplier.supplier_id).trigger('change.select2');
        }, err => {
            console.log(err);
        });
    };

    $scope.update = function(event, form) {
        event.preventDefault();

        if ($scope.order.details.length == 0) {
            toaster.pop('error', "ผลการตรวจสอบ", "คุณยังไม่ได้ระบุรายการที่จะจัดซื้อ/จ้าง !!!");
            return;
        }

        if(confirm(`คุณต้องแก้ไขรายการใบสั่งซื้อ/จ้าง รหัส ${$scope.order.id} ใช่หรือไม่?`)) {
            $http.post(`${CONFIG.baseUrl}/orders/update/${$scope.order.id}`, $scope.order)
            .then(res => {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "แก้ไขใบสั่งซื้อ/จ้างเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/orders/list`;
                } else {
                    toaster.pop('error', "ผลการทำงาน", "ไม่สามารถแก้ไขใบสั่งซื้อ/จ้างได้ !!!");
                }

                $scope.loading = false;
            }, err => {
                console.log(err);
                toaster.pop('error', "ผลการทำงาน", "ไม่สามารถแก้ไขใบสั่งซื้อ/จ้างได้ !!!");

                $scope.loading = false;
            });
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();

        if (window.confirm(`คุณต้องลบรายการใบสั่งซื้อ/จ้าง รหัส ${id} ใช่หรือไม่?`)) {
            // $http.post(`${CONFIG.baseUrl}/orders/delete/${$scope.order.id}`, {})
            // .then(res => {
            //     if (res.data.status == 1) {
            //         toaster.pop('success', "ผลการทำงาน", "ลบใบสั่งซื้อ/จ้างเรียบร้อย !!!");

            //         window.location.href = `${CONFIG.baseUrl}/orders/list`;
            //     } else {
            //         toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถลบใบสั่งซื้อ/จ้างได้ !!!");
            //     }

            //     $scope.loading = false;
            // }, err => {
            //     console.log(err);
            //     toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถลบใบสั่งซื้อ/จ้างได้ !!!");

            //     $scope.loading = false;
            // });
        }
    };
});
