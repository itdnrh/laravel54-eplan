app.controller('supportCtrl', function(CONFIG, $scope, $http, toaster, ModalService, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;

    $scope.supports = [];
    $scope.pager = [];

    $scope.plans = [];
    $scope.plans_pager = null;

    $scope.persons = [];
    $scope.persons_pager = null;

    $scope.support = {
        doc_no: '',
        doc_date: '',
        depart_id: '',
        division_id: '',
        year: '',
        plan_type_id: '',
        total: '',
        contact_detail: '',
        contact_person: '',
        remark: '',
        details: [],
        spec_committee: [],
        insp_committee: [],
    };

    $scope.newItem = {
        plan_no: '',
        plan_detail: '',
        plan_depart: '',
        plan_id: '',
        item_id: '',
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
            unit_id: '',
            amount: '',
            sum_price: ''
        };
    };

    $scope.getAll = function() {
        $scope.loading = true;
        $scope.supports = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let depart = $('#user').val() == '1300200009261' ? '' : $('#depart').val();

        $http.get(`${CONFIG.baseUrl}/supports/search?depart=${depart}$year=${year}`)
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
        const { data, ...pager } = res.data.leaves;

        $scope.supports = data;
        $scope.pager = pager;
    };

    $scope.showPlansList = () => {
        $scope.loading = true;
        $scope.plans = [];
        $scope.plans_pager = null;

        let type = $scope.support.plan_type_id === '' ? 1 : $scope.support.plan_type_id;
        let depart = $('#user').val() == '1300200009261' ? '' : $('#depart').val();

        $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&depart=${depart}&status=0`)
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
                price_per_unit: plan.price_per_unit,
                unit_id: `${plan.plan_item.unit_id}`,
                unit: plan.plan_item.unit,
                amount: plan.amount,
                sum_price: plan.sum_price
            };
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

    $scope.addOrderItem = () => {
        $scope.support.details.push({ ...$scope.newItem });

        $scope.calculateTotal();
        $scope.clearNewItem();
    };

    $scope.removeOrderItem = (index) => {
        console.log(index);
        // $scope.order.details.push({ ...$scope.newItem });

        $scope.calculateTotal();
    };

    $scope.store = function() {

    };

    $scope.showPersonList = (_selectedMode) => {
        $('#persons-list').modal('show');

        $scope.getPersons();

        $scope.selectedMode = _selectedMode;
    };

    $scope.getPersons = async () => {
        $scope.loading = true;
        $scope.persons = [];
        $scope.persons_pager = null;

        let depart = $scope.cboDepart == '' ? 0 : $scope.cboDepart;
        let keyword = $scope.searchKey == '' ? 0 : $scope.searchKey;

        $http.get(`${CONFIG.baseUrl}/persons/search/${depart}/${keyword}`)
        .then(function(res) {
            $scope.setPersons(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPersonsWithUrl = function(e, url, status, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.persons = [];
        $scope.persons_pager = null;

        let depart = $scope.cboDepart == '' ? 0 : $scope.cboDepart;
        let keyword = $scope.searchKey == '' ? 0 : $scope.searchKey;

        $http.get(`${url}`)
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
            } else {
                $scope.support.contact_detail = person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname;
                $scope.support.contact_person = person.person_id;
            }
        }

        $('#persons-list').modal('hide');
        $scope.selectedMode = '';
    };
});