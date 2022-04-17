app.controller('planMaterialCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? (moment().year() + 544).toString()
                        : (moment().year() + 543).toString();
    $scope.cboMonth = moment().format('MM');
    $scope.cboCategory = "";
    $scope.cboDepart = "";
    $scope.cboStatus = "";
    $scope.cboMenu = "";
    $scope.searchKeyword = "";
    $scope.cboQuery = "";
    $scope.budgetYearRange = [2560,2561,2562,2563,2564,2565,2566,2567];
    $scope.monthLists = [
        { id: '01', name: 'มกราคม' },
        { id: '02', name: 'กุมภาพันธ์' },
        { id: '03', name: 'มีนาคม' },
        { id: '04', name: 'เมษายน' },
        { id: '05', name: 'พฤษภาคม' },
        { id: '06', name: 'มิถุนายน' },
        { id: '07', name: 'กรกฎาคม' },
        { id: '08', name: 'สิงหาคม' },
        { id: '09', name: 'กันยายน' },
        { id: '10', name: 'ตุลาคม' },
        { id: '11', name: 'พฤศจิกายน' },
        { id: '12', name: 'ธันวาคม' },
    ];

    $scope.materials = [];
    $scope.pager = [];

    $scope.forms = {
        depart: [],
        division: [],
        categories: [],
        groups: [],
    };

    let tmpDeparts = [];
    let tmpDivisions = [];
    let tmpCategories = [];
    let tmpGroups = [];

    $scope.material = {
        material_id: '',
        year: '',
        plan_no: '',
        faction_id: '',
        depart_id: '',
        division_id: '',
        category_id: '',
        desc: '',
        price_per_unit: '',
        unit_id: '',
        amount: '',
        sum_price: '',
        start_month: '',
        reason: '',
        remark: '',
        in_stock: '0',
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

    $('#doc_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date());
        // .on('show', function (e) {
        //     $('.day').click(function(event) {
        //         event.preventDefault();
        //         event.stopPropagation();
        //     });
        // });

    $('#sent_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date());

    $('#po_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date());

    $scope.initForms = (data) => {
        if (data) {
            tmpDeparts = data.departs ? data.departs : [];
            tmpDivisions = data.divisions ? data.divisions : [];
            tmpCategories = data.categories ? data.categories : [];
            tmpGroups = data.groups ? data.groups : [];

            $scope.forms.categories = data.categories
                                        ? data.categories.filter(cate => cate.plan_type_id === 2)
                                        : [];
        }
    };

    $scope.onFactionSelected = function(faction) {
        $scope.forms.departs = tmpDeparts.filter(dep => dep.faction_id == faction);
    };

    $scope.onDepartSelected = function(depart) {
        $scope.forms.divisions = tmpDivisions.filter(div => div.depart_id == depart);
    };

    $scope.clearMaterialObj = function() {
        $scope.material = {
            asset_id: '',
            plan_no: '',
            faction_id: '',
            depart_id: '',
            division_id: '',
            category_id: '',
            desc: '',
            price_per_unit: '',
            unit_id: '',
            amount: '',
            sum_price: '',
            start_month: '',
            reason: '',
            remark: '',
            in_stock: '0',
        };
    };

    $scope.calculateSumPrice = async function() {
        let price = $(`#price_per_unit`).val() == '' ? 0 : parseFloat($(`#price_per_unit`).val());
        let amount = $(`#amount`).val() == '' ? 0 : parseFloat($(`#amount`).val());

        $scope.material.sum_price = price * amount;
        $('#sum_price').val(price * amount);
    };

    $scope.getAll = function(event) {
        $scope.materials = [];
        $scope.loading = true;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let menu    = $scope.cboMenu === '' ? '' : $scope.cboMenu;

        $http.get(`${CONFIG.baseUrl}/plans/search?type=2&year${year}&cate=${cate}&status=${status}&depart=${depart}`)
        .then(function(res) {
            $scope.setMaterials(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setMaterials = function(res) {
        const { data, ...pager } = res.data.plans;

        $scope.materials = data;
        $scope.pager = pager;
    };

    $scope.setPersons = function(res) {
        let { data, ...pager } = res.data.persons;
        $scope.persons  = data;
        $scope.pager    = pager;
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

    $scope.getById = function(id, cb) {
        $http.get(`${CONFIG.baseUrl}/materials/get-ajax-byid/${id}`)
        .then(function(res) {
            cb(res.data);
        }, function(err) {
            console.log(err);
        });
    }

    $scope.setEditControls = function(data) {
        $scope.material.material_id     = data.plan.id;
        $scope.material.year            = data.plan.year;
        $scope.material.plan_no         = data.plan.plan_no;
        $scope.material.desc            = data.plan.material.desc;
        $scope.material.spec            = data.plan.material.spec;
        $scope.material.price_per_unit  = data.plan.material.price_per_unit;
        $scope.material.amount          = data.plan.material.amount;
        $scope.material.sum_price       = data.plan.material.sum_price;
        $scope.material.start_month     = $scope.monthLists.find(m => m.id == data.plan.start_month).name;
        $scope.material.reason          = data.plan.reason;
        $scope.material.remark          = data.plan.remark;
        $scope.material.status          = data.plan.status;

        /** Convert int value to string */
        $scope.material.category_id     = data.plan.material.category_id.toString();
        $scope.material.unit_id         = data.plan.material.unit_id.toString();
        $scope.material.depart_id       = data.plan.depart_id.toString();
        $scope.material.division_id     = data.plan.division_id ? data.plan.division_id.toString() : '';
        /** Convert db date to thai date. */            
        // $scope.leave.leave_date         = StringFormatService.convFromDbDate(data.leave.leave_date);
    };

    $scope.showSupportedForm = function() {
        $('#supported-from').modal('show');
    };

    $scope.sendSupportedDoc = (e) => {
        e.preventDefault();

        let data = {
            doc_no: $('#doc_no').val(),
            doc_date: $('#doc_date').val(),
            sent_date: $('#sent_date').val(),
            sent_user: $('#sent_user').val(),
        };

        $http.post(`${CONFIG.baseUrl}/plans/send-supported/${$scope.material.material_id}`, data)
        .then(function(res) {
            console.log(res.data);
        }, function(err) {
            console.log(err);
        });

        /** Redirect to list view */
        window.location.href = `${CONFIG.baseUrl}/plans/materials`;
    };

    $scope.showPoForm = function() {
        $('#po-form').modal('show');
    };

    $scope.createPO = (e) => {
        e.preventDefault();

        let data = {
            po_no: $('#po_no').val(),
            po_date: $('#po_date').val(),
            po_net_total: $('#po_net_total').val(),
            po_user: $('#po_user').val(),
        };

        $http.post(`${CONFIG.baseUrl}/plans/create-po/${$scope.material.material_id}`, data)
        .then(function(res) {
            console.log(res.data);
        }, function(err) {
            console.log(err);
        });

        /** Redirect to list view */
        window.location.href = `${CONFIG.baseUrl}/plans/materials`;
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $(`#${form}`).submit();
    }

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/leaves/edit/${id}`;
    };

    $scope.update = function(event) {
        event.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขใบลาเลขที่ ${$scope.leave.leave_id} ใช่หรือไม่?`)) {
            $('#frmEditLeave').submit();
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();

        const actionUrl = $('#frmDelete').attr('action');
        $('#frmDelete').attr('action', `${actionUrl}/${id}`);

        if(confirm(`คุณต้องลบใบลาเลขที่ ${id} ใช่หรือไม่?`)) {
            $('#frmDelete').submit();
        }
    };

    $scope.approval = null;
    $scope.showApprovalDetail = function(id) {
        $scope.getById(id, function(data) {
            console.log(data);
            $scope.approval = data.leave;
        });

        $('#approval-detail').modal('show');
    };
});