app.controller('supportCtrl', function(CONFIG, $rootScope, $scope, $http, toaster, StringFormatService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? (moment().year() + 544).toString()
                        : (moment().year() + 543).toString();
    $scope.cboPlanType = '';
    $scope.cboFaction = '';
    $scope.cboDepart = '';
    $scope.cboDivision = '';
    $scope.cboCategory = '';
    $scope.cboInPlan = '';
    $scope.cboStatus = '';
    $scope.txtKeyword = '';
    $scope.txtDesc = '';
    $scope.searchKey = '';

    $scope.sumSupports = 0;
    $scope.supports = [];
    $scope.pager = [];

    $scope.plans = [];
    $scope.plans_pager = null;

    $scope.persons = [];
    $scope.persons_pager = null;

    $scope.timelinePlan = null;
    $scope.showTimeline = false;

    $scope.support = {
        doc_prefix: '',
        doc_no: '',
        doc_date: '',
        topic: '',
        depart_id: '',
        division_id: '',
        year: '2566', //(moment().year() + 543).toString(),
        plan_type_id: '',
        category_id: '',
        is_plan_group: false,
        planGroups: [],
        total: '',
        contact_detail: '',
        contact_person: '',
        head_of_depart_detail: '',
        head_of_depart: '',
        head_of_faction_detail: '',
        head_of_faction: '',
        reason: '',
        remark: '',
        details: [],
        removed: [],
        spec_committee: [],
        env_committee: [],
        insp_committee: [],
        user: null
    };

    $scope.newItem = {
        plan: null,
        plan_id: '',
        item_id: '',
        item: null,
        subitem_id: '',
        desc: '',
        price_per_unit: '',
        unit_id: '',
        unit_name: '',
        amount: '',
        sum_price: '',
        error: null,
        planItem: null,
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
    $('#po_date')
        .datepicker(dtpDateOptions)
        .datepicker('update', new Date())
        .on('show', function (e) {
            console.log(e);
        })
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $scope.onShowTimeline = function(plan) {
        $scope.showTimeline = false;

        if (plan) {
            $scope.timelinePlan = plan;
            $scope.showTimeline = true;
        } else {
            $scope.timelinePlan = null;
        }
    };

    $scope.initFiltered = () => {
        console.log($('#depart').val());
        if ($('#duty').val() == '1' || $('#depart').val() == '65') {
            let faction = $('#faction').val();
    
            $scope.cboFaction = faction;
            $scope.onFactionSelected(faction);
        }
    };

    $scope.clearNewItem = () => {
        $scope.newItem = {
            plan: null,
            plan_id: '',
            item_id: '',
            item: null,
            subitem_id: '',
            desc: '',
            price_per_unit: '',
            unit_id: '',
            unit_name: '',
            amount: '',
            sum_price: '',
            error: null,
            planItem: null,
        };
    };

    $scope.getAll = function() {
        $scope.loading = true;
        $scope.supports = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let type    = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let faction = $('#depart').val() == '4' ? $scope.cboFaction : $('#faction').val();
        let depart  = ($('#duty').val() == '1' || ['4','65'].includes($('#depart').val()))
                        ? !$scope.cboDepart ? '' : $scope.cboDepart
                        : $('#depart').val();
        let division = $scope.cboDivision != '' ? $scope.cboDivision : '';
        let doc_no  = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;
        let desc    = $scope.txtDesc === '' ? '' : $scope.txtDesc;
        let cate    = !$scope.cboCategory ? '' : $scope.cboCategory;
        let in_plan = $scope.cboInPlan === '' ? '' : $scope.cboInPlan;
        let status  = $scope.cboStatus === '' ? '0-9' : $scope.cboStatus;

        $http.get(`${CONFIG.baseUrl}/supports/search?year=${year}&stype=1&type=${type}&cate=${cate}&faction=${faction}&depart=${depart}&division=${division}&doc_no=${doc_no}&desc=${desc}&in_plan=${in_plan}&status=${status}`)
        .then(function(res) {
            $scope.setSupports(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.getSupportsWithUrl = function(e, url, cb) {
		/** Check whether parent of clicked a tag is .disabled just do nothing */
		if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.supports = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let type    = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let faction = $('#depart').val() == '4' ? $scope.cboFaction : $('#faction').val();
        let depart  = ($('#duty').val() == '1' || ['4','65'].includes($('#depart').val()))
                        ? !$scope.cboDepart ? '' : $scope.cboDepart
                        : $('#depart').val();
        let division = $scope.cboDivision != '' ? $scope.cboDivision : '';
        let doc_no  = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;
        let desc    = $scope.txtDesc === '' ? '' : $scope.txtDesc;
        let cate    = !$scope.cboCategory ? '' : $scope.cboCategory;
        let in_plan = $scope.cboInPlan === '' ? '' : $scope.cboInPlan;
        let status  = $scope.cboStatus === '' ? '0-9' : $scope.cboStatus;

        $http.get(`${url}&year=${year}&stype=1&type=${type}&cate=${cate}&faction=${faction}&depart=${depart}&division=${division}&doc_no=${doc_no}&desc=${desc}&in_plan=${in_plan}&status=${status}`)
        .then(function(res) {
            $scope.setSupports(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setSupports = function(res) {
        const { data, ...pager } = res.data.supports;

        $scope.supports = data;
        $scope.pager = pager;

        $scope.sumSupports = res.data.sumSupports;
    };

    $scope.supportDetails = [];
    $scope.showDetailsList = function(e, details) {
        e.preventDefault();

        if (details.length > 0) {
            $scope.supportDetails = details;

            $('#details-list').modal('show');
        }
    };

    $scope.planGroups = [];
    $scope.plansGroups_pager = null;
    $scope.showPlanGroupsList = function() {
        if (!$scope.support.category_id) {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณาเลือกประเภทแผนและประเภทพัสดุก่อน !!!");
        } else {
            $scope.loading = true;
            $scope.support.is_plan_group = true;

            $scope.plans = [];
            $scope.planGroups = [];
            $scope.plansGroups_pager = null;

            let year = $scope.cboYear === '' ? '' : $scope.cboYear;
            let cate = $scope.support.category_id === '' ? 1 : $scope.support.category_id;
            let depart = $('#user').val() == '1300200009261' ? '' : $('#depart_id').val();

            $http.get(`${CONFIG.baseUrl}/plans/search-group/${cate}?year=${year}&depart=${depart}&status=0&approved=A`)
            .then(function(res) {
                $scope.setPlanGroupsList(res);

                $scope.loading = false;

                $('#plan-groups-list').modal('show');
            }, function(err) {
                console.log(err);

                $scope.loading = false;
            });
        }
    };

    $scope.getPlanGroupsList = function() {
        $scope.loading = true;
        $scope.plans = [];
        $scope.planGroups = [];
        $scope.plansGroups_pager = null;

        let year = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate = $scope.support.category_id === '' ? 1 : $scope.support.category_id;
        let depart = $('#user').val() == '1300200009261' ? '' : $('#depart_id').val();
        let name = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;

        $http.get(`${CONFIG.baseUrl}/plans/search-group/${cate}?year=${year}&depart=${depart}&name=${name}&status=0&approved=A`)
        .then(function(res) {
            $scope.setPlanGroupsList(res);

            $scope.loading = false;

            $('#plan-groups-list').modal('show');
        }, function(err) {
            console.log(err);

            $scope.loading = false;
        });
    };

    $scope.getPlanGroupsListWithUrl = function(e, url, cb) {
		/** Check whether parent of clicked a tag is .disabled just do nothing */
		if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.plans = [];
        $scope.planGroups = [];
        $scope.plansGroups_pager = null;

        let year = $scope.cboYear === '' ? '' : $scope.cboYear;
        let depart = $('#user').val() == '1300200009261' ? '' : $('#depart_id').val();
        let name = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;

        $http.get(`${url}&year=${year}&depart=${depart}&name=${name}&status=0&approved=A`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);

            $scope.loading = false;
        });
    };

    $scope.setPlanGroupsList = function(res) {
        const { data, ...pager } = res.data.planGroups;

        $scope.planGroups = data;
        $scope.plansGroups_pager = pager;
        $scope.plans = res.data.plans;
    };

    $scope.onSelectedPlanGroup = function(e, planGroup) {
        if (planGroup) {
            const plans = $scope.plans.filter(plan => plan.plan_item.item_id == planGroup.item_id);

            plans.forEach(plan => {
                $scope.newItem.plan         = plan;
                $scope.newItem.plan_id      = plan.id;
                $scope.newItem.item_id      = plan.plan_item.item_id;
                $scope.newItem.item         = plan.plan_item.item;
                $scope.newItem.price_per_unit = plan.calc_method == 1 ? plan.price_per_unit : '';
                $scope.newItem.unit_id      = plan.calc_method == 1 ? `${plan.plan_item.unit_id}` : '';
                $scope.newItem.unit_name    = plan.calc_method == 1 ? plan.plan_item.unit.name : '';
                $scope.newItem.amount       = plan.calc_method == 1 ? plan.remain_amount : '';
                $scope.newItem.sum_price    = plan.calc_method == 1 ? plan.remain_budget : '';
                $scope.support.details.push({ ...$scope.newItem });
            });

            $scope.calculateTotal();
            $scope.clearNewItem();
            $scope.support.planGroups.push(planGroup);
        }

        $('#plan-groups-list').modal('hide');
    };

    $scope.showPlansList = () => {
        if (!$scope.support.plan_type_id || !$scope.support.category_id) {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณาเลือกประเภทแผนและประเภทพัสดุก่อน !!!");
        } else {
            $scope.loading = true;
            $scope.plans = [];
            $scope.plans_pager = null;
    
            let type = $scope.support.plan_type_id === '' ? 1 : $scope.support.plan_type_id;
            let cate = $scope.support.category_id === '' ? 1 : $scope.support.category_id;
            let depart = ($('#user').val() == '1300200009261' || $('#depart_id').val() == 4 || $('#duty_id').val() == 1) 
                            ? $scope.cboDepart
                            : $('#depart_id').val();
    
            $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&cate=${cate}&depart=${depart}&status=0-1&approved=A`)
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

        let type = $scope.support.plan_type_id === '' ? 1 : $scope.support.plan_type_id;
        let cate = $scope.support.category_id === '' ? '' : $scope.support.category_id;
        let name = $scope.txtKeyword == '' ? '' : $scope.txtKeyword;
        let depart = ($('#user').val() == '1300200009261' || $('#depart_id').val() == 4 || $('#duty_id').val() == 1) 
                            ? $scope.cboDepart
                            : $('#depart_id').val();

        $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&cate=${cate}&name=${name}&depart=${depart}&status=${status}&approved=A`)
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
        $scope.plans = [];
        $scope.plans_pager = null;

        let type = $scope.support.plan_type_id === '' ? 1 : $scope.support.plan_type_id;
        let cate = $scope.support.category_id === '' ? '' : $scope.support.category_id;
        let name = $scope.txtKeyword == '' ? '' : $scope.txtKeyword;
        let depart = ($('#user').val() == '1300200009261' || $('#depart_id').val() == 4 || $('#duty_id').val() == 1) 
                            ? $scope.cboDepart
                            : $('#depart_id').val();

        $http.get(`${url}&type=${type}&cate=${cate}&name=${name}&depart=${depart}&status=${status}&approved=A`)
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
            $scope.newItem.plan         = plan;
            $scope.newItem.plan_id      = plan.id;
            $scope.newItem.item_id      = plan.plan_item.item_id;
            $scope.newItem.item         = plan.plan_item.item;
            $scope.newItem.subitem_id   = '';
            $scope.newItem.desc         = '';
            $scope.newItem.price_per_unit = plan.plan_item.calc_method == 1 ? plan.plan_item.price_per_unit : '';
            $scope.newItem.unit_id      = plan.plan_item.calc_method == 1 ? plan.plan_item.unit_id.toString() : '';
            $scope.newItem.unit_name    = plan.plan_item.calc_method == 1 ? plan.plan_item.unit.name : '';
            $scope.newItem.amount       = plan.plan_item.calc_method == 1 ? plan.plan_item.remain_amount : '';
            $scope.newItem.sum_price    = plan.plan_item.calc_method == 1 ? plan.plan_item.remain_budget : '';
            $scope.newItem.planItem     = plan.plan_item;

            if (plan.plan_item.calc_method == 1) {
                $('#unit_id').val(plan.plan_item.unit_id).trigger("change.select2");
            }
        }

        $('#plans-list').modal('hide');
    };

    $scope.calculateSumPrice = function(price, amount) {
        let sumPrice = parseFloat($scope.currencyToNumber(price)) * parseFloat($scope.currencyToNumber(amount));

        /** ตรวจสอบว่ารายการที่ขอยอดเงินเกินงบประมาณที่ขอหรือไม่ */
        if ($scope.newItem.planItem.sum_price < sumPrice) {
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถระบุยอดรวมเป็นเงินเกินงบประมาณที่ขอได้ !!!");

            $scope.newItem.price_per_unit = $scope.newItem.planItem.price_per_unit;
            $scope.newItem.amount = $scope.newItem.planItem.amount;
            $scope.newItem.sum_price = $scope.newItem.planItem.sum_price;

            return;
        }

        $scope.newItem.sum_price = sumPrice;
    };

    $scope.calculateTotal = () => {
        let total = 0;

        total = $scope.support.details.reduce((sum, curVal) => {
            return sum = sum + parseFloat($scope.currencyToNumber(curVal.sum_price));
        }, 0);

        $scope.support.total = total;
        $('#total').val(total);
    };

    $scope.showSubitemsList = function() {
        if ($scope.newItem.plan_id) {
            $scope.getItems('#subitems-list', 0);
        } else {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณาเลือกรายการแผนก่อน !!!");
        }
    };
    
    $scope.handleSubitemSelected = function(e, item) {
        if (item) {
            $('#subitem_id').val(item.id);
            $scope.newItem.subitem_id       = item.id;
            $scope.newItem.desc             = item.item_name;
            $scope.newItem.price_per_unit   = item.price_per_unit;
            $scope.newItem.unit_id          = item.unit_id ? item.unit_id.toString() : '';
        }

        $('#subitems-list').modal('hide');
    };

    $scope.showSpecForm = function(planId) {
        if (planId) {
            $('#spec-form').modal('show');
        } else {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณาเลือกรายการแผนก่อน !!!");
        }
    };

    $scope.addSpec = function() {
        $('#spec-form').modal('hide');
    };

    const validateNewItem = () => {
        if ($scope.newItem.item.have_subitem == 1 && $scope.newItem.desc == '') {
            $scope.newItem.error = { ...$scope.newItem.error, desc: 'กรุณาระบุรายละเอียด/รายการย่อย' }
        } else {
            if ($scope.newItem.error && $scope.newItem.error.hasOwnProperty('desc')) {
                const { desc, ...rest } = $scope.newItem.error;
                $scope.newItem.error = { ...rest }
            }
        }

        if ($scope.newItem.price_per_unit == '') {
            $scope.newItem.error = { ...$scope.newItem.error, price_per_unit: 'กรุณาระบุราคาต่อหน่วย' }
        } else {
            if ($scope.newItem.error && $scope.newItem.error.hasOwnProperty('price_per_unit')) {
                const { price_per_unit, ...rest } = $scope.newItem.error;
                $scope.newItem.error = { ...rest }
            }
        }

        if ($scope.newItem.unit_id == '') {
            $scope.newItem.error = { ...$scope.newItem.error, unit_id: 'กรุณาเลือกหน่วยนับ' }
        } else {
            if ($scope.newItem.error && $scope.newItem.error.hasOwnProperty('unit_id')) {
                const { unit_id, ...rest } = $scope.newItem.error;
                $scope.newItem.error = { ...rest }
            }
        }

        if ($scope.newItem.amount == '') {
            $scope.newItem.error = { ...$scope.newItem.error, amount: 'กรุณาเลือกหน่วยนับ' }
        } else {
            if ($scope.newItem.error && $scope.newItem.error.hasOwnProperty('amount')) {
                const { amount, ...rest } = $scope.newItem.error;
                $scope.newItem.error = { ...rest }
            }
        }

        return $scope.newItem.error ? Object.keys($scope.newItem.error).length === 0 : true;
    };

    $scope.addItem = () => {
        if ($scope.newItem.plan_id !== '') {
            if (!validateNewItem($scope.newItem)) {
                toaster.pop('error', "ผลการตรวจสอบ", "กรุณารายละเอียดรายการให้ครบก่อน !!!");
            } else {
                /** เซตชื่อหน่วยนับเพื่อแสดงผลในรายการ */
                $scope.newItem.unit_name = $('#unit_id option:selected').text().trim();

                $scope.support.details.push({ ...$scope.newItem });

                $("#unit_id").val(null).trigger('change.select2');
                $scope.calculateTotal();
                $scope.clearNewItem();
            }
        } else {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณาเลือกรายการแผนก่อน !!!");
        }
    };

    $scope.removeAddedItem = (selectedIndex) => {
        const rm = $scope.support.details.find((d, index) => index === selectedIndex);

        if (rm) {
            $scope.support.removed = [...new Set([...$scope.support.removed, rm.id])];
        }

        $scope.support.details = $scope.support.details.filter((d, index) => index !== selectedIndex);
        $scope.calculateTotal();
    };

    $scope.isSelected = function(planId) {
        if ($scope.support.details.length == 0) return false;

        return $scope.support.details.some(item => item.plan_id === planId && item.plan.calc_method == 1);
    };

    $scope.showPersonList = (_selectedMode) => {
        /** Set default depart of persons list to same user's depart */
        $scope.cboDepart = $('#depart_id').val();

        $('#persons-list').modal('show');

        $scope.getPersons();

        $scope.selectedMode = _selectedMode;
    };

    $scope.getPersons = async () => {
        $scope.loading = true;
        $scope.persons = [];
        $scope.persons_pager = null;

        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;
        let name = !$scope.searchKey ? '' : $scope.searchKey;

        $http.get(`${CONFIG.baseUrl}/persons/search?depart=${depart}&name=${name}`)
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
        let name = !$scope.searchKey ? '' : $scope.searchKey;

        $http.get(`${url}&depart=${depart}&name=${name}`)
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
            if (parseInt(mode) === 1) {
                $scope.support.spec_committee.push(person)
            } else if (parseInt(mode) == 2) {
                $scope.support.insp_committee.push(person)
            } else if (parseInt(mode) == 3) {
                $scope.support.env_committee.push(person)
            } else if (parseInt(mode) == 4) {
                $scope.support.contact_detail = person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname + ' โทร.' + person.person_tel;
                $scope.support.contact_person = person.person_id;
            } else  if (parseInt(mode) == 5) {
                $scope.support.head_of_depart_detail = person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname;
                $scope.support.head_of_depart = person.person_id;
            } else {
                $scope.support.head_of_faction_detail = person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname;
                $scope.support.head_of_faction = person.person_id;
            }
        }

        $('#persons-list').modal('hide');
        $scope.selectedMode = '';
    };

    $scope.removePersonItem = (mode, person) => {
        if (parseInt(mode) === 1) {
            $scope.support.spec_committee = $scope.support.spec_committee.filter(sc => {
                return sc.person_id !== person.person_id
            });
        } else if (parseInt(mode) === 2) {
            $scope.support.insp_committee = $scope.support.insp_committee.filter(ic => {
                return ic.person_id !== person.person_id
            });
        } else if (parseInt(mode) === 3) {
            $scope.support.env_committee = $scope.support.env_committee.filter(ic => {
                return ic.person_id !== person.person_id
            });
        }
    };

    $scope.getById = function(id, cb) {
        $scope.loading = true;
        
        $http.get(`${CONFIG.apiUrl}/supports/${id}`)
        .then(function(res) {
            cb(res.data.support, res.data.committees);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setEditControls = function(support, committees) {
        if (support) {
            $scope.support.id               = support.id;

            if (support.doc_no) {
                const [prefix, doc_no]      = support.doc_no.split("/");
                $scope.support.doc_prefix   = prefix;
                $scope.support.doc_no       = doc_no;
            }

            $scope.support.doc_date         = support.doc_date ? StringFormatService.convFromDbDate(support.doc_date) : '';
            $scope.support.year             = support.year.toString();
            $scope.support.plan_type_id     = support.plan_type_id.toString();
            $scope.support.category_id      = support.category_id.toString();
            $scope.support.topic            = support.topic;
            $scope.support.is_plan_group    = support.is_plan_group;
            $scope.support.plan_group_desc  = support.plan_group_desc;
            $scope.support.plan_group_amt   = support.plan_group_amt;
            $scope.support.total            = support.total;
            $scope.support.reason           = support.reason;

            $scope.support.contact_person   = support.contact.person_id;
            $scope.support.contact_detail   = `${support.contact.person_firstname} ${support.contact.person_lastname} โทร.${support.contact.person_tel}`;
            $scope.support.head_of_depart_detail = support.head_of_depart_detail;
            $scope.support.head_of_depart   = support.head_of_depart;
            $scope.support.head_of_faction_detail = support.head_of_faction_detail;
            $scope.support.head_of_faction  = support.head_of_faction;

            $scope.support.depart_id        = support.depart_id.toString();
            $scope.support.division_id      = support.division_id ? support.division_id.toString() : '';
            $scope.support.details          = support.details;
            $scope.support.remark           = support.remark;
            $scope.support.status           = support.status;

            $scope.support.returned_date    = support.returned_date;
            $scope.support.returned_reason  = support.returned_reason;

            /** Set each committees by filtering from responsed committees data */
            $scope.support.spec_committee   = committees
                                                .filter(com => com.committee_type_id == 1)
                                                .map(com => com.person);
            $scope.support.insp_committee   = committees
                                                .filter(com => com.committee_type_id == 2)
                                                .map(com => com.person);
            $scope.support.env_committee    = committees
                                                .filter(com => com.committee_type_id == 3)
                                                .map(com => com.person);

            /** Set date value to datepicker input of doc_date */
            $('#doc_date').datepicker(dtpDateOptions).datepicker('update', moment(support.doc_date).toDate());

            /** Initial model values in mainCtrl */
            $scope.onPlanTypeSelected(support.plan_type_id);
            $scope.setPlanType(support.plan_type_id);
            $scope.setCboCategory(support.category_id.toString());
        }
    };

    $scope.showSendForm = function(support) {
        if (support) {
            $('#support-from').modal('show');
        }
    };

    $scope.send = function(e) {
        $scope.loading = true;

        $http.post(`${CONFIG.baseUrl}/supports/send`, $scope.support)
        .then(function(res) {
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "ส่งบันทึกขอสนับสนุนเรียบร้อย !!!");

                window.location.href = `${CONFIG.baseUrl}/supports/list`;
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถส่งบันทึกขอสนับสนุนได้ !!!");
            }

            $scope.loading = false;
        }, function(err) {
            $scope.loading = false;

            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถส่งบันทึกขอสนับสนุนได้ !!!");
        });
    };

    $scope.onValidateForm = function(e, form, cb) {
        e.preventDefault();

        $scope.support.depart_id = $('#depart_id').val();
        $scope.support.division_id = $('#division_id').val();

        $rootScope.formValidate(e, '/supports/validate', $scope.support, 'frmNewSupport', $scope.store)
    };

    $scope.store = function() {
        $scope.loading = true;
        
        /** Set user props of support model by logged in user */
        $scope.support.user = $('#user').val();

        $http.post(`${CONFIG.baseUrl}/supports/store`, $scope.support)
        .then(function(res) {
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");

                window.location.href = `${CONFIG.baseUrl}/supports/list`;
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
            }
        }, function(err) {
            $scope.loading = false;

            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
        });
    };

    $scope.update = function(e, form) {
        e.preventDefault();

        if(confirm(`คุณต้องแก้ไขบันทึกขอสนับสนุน รหัส ${$scope.support.id} ใช่หรือไม่?`)) {
            $scope.loading = true;

            /** Set user props of support model by logged in user */
            $scope.support.user = $('#user').val();

            $http.post(`${CONFIG.baseUrl}/supports/update/${$scope.support.id}`, $scope.support)
            .then(function(res) {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลเรียบร้อย !!!");

                    /** TODO: Reset supports model */
                    $scope.setSupports(res);

                    window.location.href = `${CONFIG.baseUrl}/supports/list`;
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถแก้ไขข้อมูลได้ !!!");
                }
            }, function(err) {
                $scope.loading = false;

                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถแก้ไขข้อมูลได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();

        if(confirm(`คุณต้องลบบันทึกขอสนับสนุน รหัส ${id} ใช่หรือไม่?`)) {
            $scope.loading = true;

            $http.post(`${CONFIG.baseUrl}/supports/delete/${id}`)
            .then(res => {
                console.log(res);
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลบข้อมูลเรียบร้อย !!!");

                    /** TODO: Reset supports model */
                    $scope.setSupports(res);
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลบข้อมูลได้ !!!");
                }
            }, err => {
                console.log(err);
                $scope.loading = false;
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลบข้อมูลได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.cancel = function(e, id) {
        $scope.loading = true;

        if(confirm(`คุณต้องการยกเลิกการส่งบันทึกขอสนับสนุน รหัส ${id} ใช่หรือไม่?`)) {
            $http.put(`${CONFIG.apiUrl}/supports/${id}/cancel-sent`, { status: 0 })
            .then(function(res) {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ยกเลิกส่งบันทึกขอสนับสนุนเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/supports/list`;
                } else {
                    toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถยกเลิกส่งบันทึกขอสนับสนุนได้ !!!");
                }

                $scope.loading = false;
            }, function(err) {
                $scope.loading = false;

                console.log(err);
                toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถยกเลิกส่งบันทึกขอสนับสนุนได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.setTopicByPlanType = function() {
        $scope.support.topic = `ขอรับการสนับสนุน${$('#category_id option:selected').text().trim()}`;
    };
});