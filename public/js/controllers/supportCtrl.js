app.controller('supportCtrl', function(CONFIG, $rootScope, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.cboYear = '2566'; //(moment().year() + 543).toString();
    $scope.cboPlanType = '';
    $scope.cboFaction = '';
    $scope.cboDepart = '';
    $scope.cboDivision = '';
    $scope.txtKeyword = '';
    $scope.searchKey == '';

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
        total: '',
        contact_detail: '',
        contact_person: '',
        reason: '',
        remark: '',
        details: [],
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
        error: null
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
        if ($('#duty').val() == '1') {
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
            error: null
        };
    };

    $scope.getAll = function() {
        $scope.loading = true;
        $scope.supports = [];
        $scope.pager = null;

        let year = $scope.cboYear === '' ? '' : $scope.cboYear;
        let type = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;        
        let faction = $('#user').val() == '1300200009261' ? $scope.cboFaction : $('#faction').val();
        let depart = ($('#user').val() == '1300200009261' || $('#duty').val() == '1')
                        ? $scope.cboDepart
                        : $('#depart').val();
        let division = $scope.cboDivision != '' ? $scope.cboDivision : '';

        $http.get(`${CONFIG.baseUrl}/supports/search?year=${year}&stype=1&type=${type}&faction=${faction}&depart=${depart}&division=${division}&status=0-3`)
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
        let faction = $('#user').val() == '1300200009261' ? $scope.cboFaction : $('#faction').val();
        let depart = ($('#user').val() == '1300200009261' || $('#duty').val() == '1')
                        ? $scope.cboDepart
                        : $('#depart').val();
        let division = $scope.cboDivision != '' ? $scope.cboDivision : '';

        $http.get(`${url}&year=${year}&stype=1&type=${type}&faction=${faction}&depart=${depart}&division=${division}&status=0-3`)
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
    };

    $scope.supportDetails = [];
    $scope.showDetailsList = function(e, details) {
        e.preventDefault();

        console.log(details);
        if (details.length > 0) {
            $scope.supportDetails = details;

            $('#details-list').modal('show');
        }
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
            let depart = $('#user').val() == '1300200009261' ? '' : $('#depart').val();
    
            $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&cate=${cate}&depart=${depart}&status=0&approved=A`)
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
        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;

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
        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;

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
            $scope.newItem.price_per_unit = plan.calc_method == 1 ? plan.price_per_unit : '';
            $scope.newItem.unit_id      = plan.calc_method == 1 ? `${plan.plan_item.unit_id}` : '';
            $scope.newItem.unit_name    = plan.calc_method == 1 ? plan.plan_item.unit.name : '';
            $scope.newItem.amount       = plan.calc_method == 1 ? plan.remain_amount : '';
            $scope.newItem.sum_price    = plan.calc_method == 1 ? plan.remain_budget : '';

            if (plan.calc_method == 1) {
                $('#unit_id').val(plan.plan_item.unit_id).trigger("change.select2");
            }
        }


        $('#plans-list').modal('hide');
    };

    $scope.calculateSumPrice = function(price, amount) {
        $scope.newItem.sum_price = parseFloat($scope.currencyToNumber(price)) * parseFloat($scope.currencyToNumber(amount));
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

    $scope.removeAddedItem = (planId) => {
        $scope.support.details = $scope.support.details.filter(d => d.plan_id !== planId);

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
        let name = $scope.searchKey == '' ? '' : $scope.searchKey;

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
        let name = $scope.searchKey == '' ? '' : $scope.searchKey;

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
            } else {
                $scope.support.contact_detail = person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname + ' โทร.' + person.person_tel;
                $scope.support.contact_person = person.person_id;
            }
        }

        $('#persons-list').modal('hide');
        $scope.selectedMode = '';
    };

    $scope.removePersonItem = (mode, person) => {
        console.log(person);
        console.log(parseInt(mode));
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
        } else {

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
            if (support.doc_no) {
                const [prefix, doc_no] = support.doc_no.split("/");
                $scope.support.doc_prefix = prefix;
                $scope.support.doc_no = doc_no;
            }

            $scope.support.id = support.id;
            $scope.support.doc_date = support.doc_date;
            $scope.support.topic = support.topic;
            $scope.support.total = support.total;
            $scope.support.reason = support.reason;
            $scope.support.remark = support.remark;
            $scope.support.contact_person = support.contact.person_id;
            $scope.support.contact_detail = `${support.contact.person_firstname} ${support.contact.person_lastname} โทร.${support.contact.person_tel}`;
            $scope.support.details = support.details;
            $scope.support.status = support.status;
            
            $scope.support.year = support.year.toString();
            $scope.support.plan_type_id = support.plan_type_id.toString();
            $scope.support.category_id = support.category_id.toString();
            $scope.support.depart_id = support.depart_id.toString();
            $scope.support.division_id = support.division_id ? support.division_id.toString() : '';

            /** Set each committees by filtering from responsed committees data */
            $scope.support.spec_committee = committees.filter(com => com.committee_type_id == 1);
            $scope.support.insp_committee = committees.filter(com => com.committee_type_id == 2);
            $scope.support.env_committee = committees.filter(com => com.committee_type_id == 3);

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

            console.log(res);
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
            // $scope.loading = true;

            /** Set only person data to all committee sets */
            $scope.support.insp_committee = $scope.support.insp_committee.map(insp => insp.person);
            $scope.support.env_committee = $scope.support.env_committee.map(env => env.person);
            $scope.support.spec_committee = $scope.support.spec_committee.map(spec => spec.person);

            /** Set user props of support model by logged in user */
            $scope.support.user = $('#user').val();

            $http.post(`${CONFIG.baseUrl}/supports/update/${$scope.support.id}`, $scope.support)
            .then(function(res) {
                $scope.loading = false;

                console.log(res);
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
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();

        if(confirm(`คุณต้องลบบันทึกขอสนับสนุน รหัส ${id} ใช่หรือไม่?`)) {
            $http.post(`${CONFIG.baseUrl}/supports/delete/${id}`)
            .then(res => {
                console.log(res);
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลบข้อมูลเรียบร้อย !!!");

                    /** TODO: Reset supports model */
                    $scope.setSupports(res);
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลบข้อมูลได้ !!!");
                }
            }, err => {
                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลบข้อมูลได้ !!!");
            });
        }
    };

    $scope.setTopicByPlanType = function() {
        $scope.support.topic = `ขอรับการสนับสนุน${$('#category_id option:selected').text().trim()}`;
    };
});