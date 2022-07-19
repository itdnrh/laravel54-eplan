app.controller('repairCtrl', function(CONFIG, $rootScope, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;

    $scope.supports = [];
    $scope.pager = [];

    $scope.plans = [];
    $scope.plans_pager = null;

    $scope.items = [];
    $scope.items_pager = null;

    $scope.persons = [];
    $scope.persons_pager = null;

    $scope.support = {
        doc_no: '',
        doc_date: '',
        topic: '',
        depart_id: '',
        division_id: '',
        year: (moment().year() + 543).toString(),
        plan_type_id: '3',
        total: '',
        contact_detail: '',
        contact_person: '',
        reason: '',
        remark: '',
        details: [],
        spec_committee: [],
        env_committee: [],
        insp_committee: [],
    };

    $scope.newItem = {
        plan_no: '',
        plan_detail: '',
        plan_depart: '',
        plan_id: '',
        item_id: '',
        desc: '',
        price_per_unit: '',
        unit: null,
        unit_id: '9',
        amount: '1',
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
    $('#po_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('show', function (e) {
            console.log(e);
        })
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
            price_per_unit: '',
            unit_id: '9',
            amount: '1',
            sum_price: ''
        };
    };

    $scope.calculateSumPrice = function(price) {
        const total = parseFloat(price) * parseFloat($scope.newItem.amount);
        $scope.newItem.sum_price = total;
    };

    $scope.showSpecForm = function() {
        $('#spec-form').modal('show');
    };

    $scope.addSpec = function() {
        $('#spec-form').modal('hide');
    };

    $scope.getAll = function() {
        $scope.loading = true;
        $scope.supports = [];
        $scope.pager = null;

        let year = $scope.cboYear === '' ? '' : $scope.cboYear;
        let depart = $('#user').val() == '1300200009261' ? '' : $('#depart').val();

        $http.get(`${CONFIG.baseUrl}/repairs/search?year=${year}&stype=2&depart=${depart}&status=0-3`)
        .then(function(res) {
            $scope.setSupports(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.getDataWithUrl = function(e, url, cb) {
		/** Check whether parent of clicked a tag is .disabled just do nothing */
		if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.supports = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let depart = $('#user').val() == '1300200009261' ? '' : $('#depart').val();

        $http.get(`${url}&depart=${depart}$year=${year}`)
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

    $scope.showSupportDetails = function(details) {
        if (details) {
            $scope.items = details;

            $('#support-details').modal('show');
        }
    };

    $scope.showPlansList = () => {
        $scope.loading = true;
        $scope.plans = [];
        $scope.plans_pager = null;

        let type = $scope.support.plan_type_id === '' ? 3 : $scope.support.plan_type_id;
        let depart = $('#user').val() == '1300200009261' ? '' : $('#depart').val();

        $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&depart=${depart}&status=0&approved=A&have_subitem=1`)
        .then(function(res) {
            $scope.setPlans(res);

            $scope.loading = false;

            $('#plans-list').modal('show');
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPlans = (type, status) => {
        $scope.loading = true;
        $scope.plans = [];
        $scope.plans_pager = null;

        let cate = $scope.cboCategory == '' ? '' : $scope.cboCategory;
        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;

        $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&cate=${cate}&depart=${depart}&status=${status}&approved=A`)
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

        let type = $scope.support.plan_type_id === '' ? 3 : $scope.support.plan_type_id;
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
            $scope.newItem.plan_no = plan.plan_no;
            $scope.newItem.plan_detail = `${plan.plan_item.item.item_name} (${plan.plan_item.item.category.name})`;
            $scope.newItem.plan_depart = plan.division ? plan.division.ward_name : plan.depart.depart_name;
            $scope.newItem.plan_id = plan.id;
            $scope.newItem.item_id = plan.plan_item.item_id;

            $scope.support.topic = `ขอสนับสนุน${plan.plan_item.item.item_name} (${plan.plan_item.item.category.name})`;
        }

        $('#plans-list').modal('hide');
    };

    $scope.calculateTotal = () => {
        let total = 0;

        total = $scope.support.details.reduce((sum, curVal) => {
            return sum = sum + curVal.sum_price;
        }, 0);

        $scope.support.total = total;
        $('#total').val(total);
    };

    $scope.addItem = () => {
        console.log($scope.newItem);
        $scope.support.details.push({ ...$scope.newItem });

        $scope.calculateTotal();
        $scope.clearNewItem();
    };

    $scope.removeOrderItem = (index) => {
        console.log(index);
        // $scope.order.details.push({ ...$scope.newItem });

        $scope.calculateTotal();
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
        let keyword = $scope.searchKey == '' ? '' : $scope.searchKey;

        $http.get(`${CONFIG.baseUrl}/persons/search?depart=${depart}&searchKey=${keyword}`)
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
        let keyword = $scope.searchKey == '' ? '' : $scope.searchKey;

        $http.get(`${url}&depart=${depart}&searchKey=${keyword}`)
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
        $http.get(`${CONFIG.baseUrl}/supports/get-ajax-byid/${id}`)
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
            $scope.support.id = support.id;
            $scope.support.doc_no = support.doc_no;
            $scope.support.doc_date = support.doc_date;
            $scope.support.topic = support.topic;
            $scope.support.total = support.total;
            $scope.support.reason = support.reason;
            $scope.support.remark = support.remark;
            $scope.support.contact_person = support.contact.person_id;
            $scope.support.contact_detail = `${support.contact.person_firstname} ${support.contact.person_lastname} โทร.${support.contact.person_tel}`;
            $scope.support.details = support.details;
            
            $scope.support.year = support.year.toString();
            $scope.support.plan_type_id = support.plan_type_id.toString();
            $scope.support.depart_id = support.depart_id.toString();
            $scope.support.division_id = support.division_id ? support.division_id.toString() : '';

            $scope.support.spec_committee = committees.filter(com => com.committee_type_id == 1);
            $scope.support.insp_committee = committees.filter(com => com.committee_type_id == 2);
            $scope.support.env_committee = committees.filter(com => com.committee_type_id == 3);
        }
    };

    $scope.sendSupport = function(e) {
        $scope.loading = true;
        console.log($scope.support);

        $http.post(`${CONFIG.baseUrl}/supports/send`, $scope.support)
        .then(function(res) {
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "ส่งบันทึกขอสนับสนุนเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถส่งบันทึกขอสนับสนุนได้ !!!");
            }
        }, function(err) {
            $scope.loading = false;

            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถส่งบันทึกขอสนับสนุนได้ !!!");
        });
    };

    $scope.onValidateForm = function(e) {
        e.preventDefault();

        $scope.support.depart_id = $('#depart_id').val();
        $scope.support.division_id = $('#division_id').val();

        $rootScope.formValidate(e, '/supports/validate', $scope.support, 'frmNewSupport', $scope.store)
    };

    $scope.store = function() {
        $scope.loading = true;

        $http.post(`${CONFIG.baseUrl}/repairs/store`, $scope.support)
        .then(function(res) {
            $scope.loading = false;

            console.log(res);
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
            }
        }, function(err) {
            $scope.loading = false;

            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
        });
    };

    $scope.setTopicByPlanType = function(planType) {
        $scope.support.topic = `ขอรับการสนับสนุน${$('#plan_type_id option:selected').text().trim()}`;
    };
});