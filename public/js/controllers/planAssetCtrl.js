app.controller('planAssetCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? (moment().year() + 544).toString()
                        : (moment().year() + 543).toString();
    $scope.cboMonth = moment().format('MM');
    $scope.cboCategory = "";
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

    $scope.assets = [];
    $scope.pager = [];

    $scope.forms = {
        depart: [],
        division: [],
    };

    let tmpDeparts = [];
    let tmpDivisions = [];

    $scope.asset = {
        asset_id: '',
        year: '',
        plan_no: '',
        faction_id: '',
        depart_id: '',
        division_id: '',
        category_id: '',
        desc: '',
        spec: '',
        price_per_unit: '',
        unit_id: '',
        amount: '',
        sum_price: '',
        start_month: '',
        reason: '',
        remark: '',
        owner: '',
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

    $('#leave_date').datepicker(dtpOptions).datepicker('update', new Date()).on('show', function (e) {
        $('.day').click(function(event) {
            event.preventDefault();
            event.stopPropagation();
        });
    });

    $scope.initForms = (data) => {
        if (data) {
            tmpDeparts = data.departs
            tmpDivisions = data.divisions
        }
    };

    $scope.onFactionSelected = function(faction) {
        $scope.forms.departs = tmpDeparts.filter(dep => dep.faction_id == faction);
    };

    $scope.onDepartSelected = function(depart) {
        $scope.forms.divisions = tmpDivisions.filter(div => div.depart_id == depart);
    };

    $scope.clearLeaveObj = function() {
        $scope.asset = {
            asset_id: '',
            plan_no: '',
            faction_id: '',
            depart_id: '',
            division_id: '',
            category_id: '',
            desc: '',
            spec: '',
            price_per_unit: '',
            unit_id: '',
            amount: '',
            sum_price: '',
            start_month: '',
            reason: '',
            remark: '',
            owner: '',
        };
    };

    $scope.calculateSumPrice = async function() {
        let price = $(`#price_per_unit`).val() == '' ? 0 : parseFloat($(`#price_per_unit`).val());
        let amount = $(`#amount`).val() == '' ? 0 : parseFloat($(`#amount`).val());

        $scope.asset.sum_price = price * amount;
        $('#sum_price').val(price * amount);
    };

    $scope.showSupportedForm = function() {
        $('#supported-from').modal('show');
    };

    // TODO: Duplicated method
    $scope.getAll = function(event) {
        $scope.assets = [];
        $scope.loading = true;

        let year    = $scope.cboYear === '' ? 0 : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? 0 : $scope.cboCategory;
        let status  = $scope.cboStatus === '' ? '-' : $scope.cboStatus;
        let menu    = $scope.cboMenu === '' ? 0 : $scope.cboMenu;
        let query   = $scope.cboQuery === '' ? '' : `?${$scope.cboQuery}`;

        $http.get(`${CONFIG.baseUrl}/assets/search/${year}/${cate}/${status}/${menu}${query}`)
        .then(function(res) {
            $scope.setAssets(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setAssets = function(res) {
        const { data, ...pager } = res.data.assets;
        $scope.assets = data;
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
        $http.get(`${CONFIG.baseUrl}/assets/get-ajax-byid/${id}`)
        .then(function(res) {
            cb(res.data);
        }, function(err) {
            console.log(err);
        });
    }

    $scope.setEditControls = function(data) {
        $scope.asset.asset_id           = data.plan.id;
        $scope.asset.year               = data.plan.year;
        $scope.asset.plan_no            = data.plan.plan_no;
        $scope.asset.desc               = data.plan.asset.desc;
        $scope.asset.spec               = data.plan.asset.spec;
        $scope.asset.price_per_unit     = data.plan.asset.price_per_unit;
        $scope.asset.amount             = data.plan.asset.amount;
        $scope.asset.sum_price          = data.plan.asset.sum_price;
        $scope.asset.start_month        = $scope.monthLists.find(m => m.id == data.plan.start_month).name;
        $scope.asset.reason             = data.plan.reason;
        $scope.asset.remark             = data.plan.remark;
        $scope.asset.status             = data.plan.status;

        /** Convert int value to string */
        $scope.asset.category_id        = data.plan.asset.category_id.toString();
        $scope.asset.unit_id            = data.plan.asset.unit_id.toString();
        $scope.asset.depart_id          = data.plan.depart_id.toString();
        $scope.asset.division_id        = data.plan.division_id ? data.plan.division_id.toString() : '';
        /** Convert db date to thai date. */            
        // $scope.leave.leave_date         = StringFormatService.convFromDbDate(data.leave.leave_date);
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $('#frmNewLeave').submit();
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