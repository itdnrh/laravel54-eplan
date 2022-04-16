app.controller('planAssetCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
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
    $scope.searchKey = "";
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
    $scope.pager = null;

    $scope.items = [];
    $scope.items_pager = null;

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

    $scope.asset = {
        asset_id: '',
        year: '',
        plan_no: '',
        faction_id: '',
        depart_id: '',
        division_id: '',
        category_id: '',
        item_id: '',
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
        }
    };

    $scope.onFactionSelected = function(faction) {
        $scope.forms.departs = tmpDeparts.filter(dep => dep.faction_id == faction);
    };

    $scope.onDepartSelected = function(depart) {
        $scope.forms.divisions = tmpDivisions.filter(div => div.depart_id == depart);
    };

    $scope.onPlanTypeSelected = function(type) {
        $scope.forms.categories = tmpCategories.filter(cate => cate.plan_type_id === parseInt(type));

        if ([3,4].includes(parseInt(type))) {
            $scope.forms.groups = tmpGroups.filter(group => group.plan_type_id === parseInt(type));

            $('#group_id').attr('disabled', false)
        } else {
            $('#group_id').attr('disabled', true)
        }

        if (parseInt(type) === 2) {
            $scope.newItem.in_stock = 0;
        } else if (parseInt(type) === 6) {
            $scope.newItem.in_stock = 1;
        } else {
            $scope.newItem.in_stock = '';
        }
    };

    const clearAssetObj = function() {
        $scope.asset = {
            asset_id: '',
            plan_no: '',
            faction_id: '',
            depart_id: '',
            division_id: '',
            category_id: '',
            item_id: '',
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

    $scope.getAll = function(event) {
        $scope.assets = [];
        $scope.loading = true;

        let year    = $scope.cboYear === '' ? 0 : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? 0 : $scope.cboCategory;
        let depart  = $scope.cboDepart === '' ? 0 : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '-' : $scope.cboStatus;
        let menu    = $scope.cboMenu === '' ? 0 : $scope.cboMenu;

        $http.get(`${CONFIG.baseUrl}/assets/search?year=${year}&cate=${cate}&status=${status}&depart=${depart}&menu=${menu}`)
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

    $scope.showItemsList = function() {
        $scope.forms.categories = tmpCategories.filter(cate => cate.plan_type_id === 1);

        $scope.getItems();
    };

    $scope.getItems = function() {
        $scope.items = [];
        $scope.loading = true;

        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let name  = $scope.searchKey === '' ? '' : $scope.searchKey;

        $http.get(`${CONFIG.baseUrl}/items/search?type=1&cate=${cate}&name=${name}`)
        .then(function(res) {
            $scope.setItems(res);

            $scope.loading = false;

            $('#items-list').modal('show');
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setItems = function(res) {
        let { data, ...pager } = res.data.items;

        $scope.items = data;
        $scope.items_pager = pager;
    };

    $scope.onSelectedItem = function(event, item) {
        if (item) {
            $('#item_id').val(item.id);
            $scope.asset.item_id = item.id;
            $scope.asset.desc = item.item_name;
            $scope.asset.price_per_unit = item.price_per_unit;
            $scope.asset.unit_id = item.unit_id.toString();
            $scope.asset.category_id = item.category_id.toString();
        }

        $('#items-list').modal('hide');
    };

    $scope.getDataWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.items = [];
        $scope.loading = true;

        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let name  = $scope.searchKey === '' ? '' : $scope.searchKey;

        $http.get(`${url}&type=1&cate=${cate}&name=${name}`)
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
            cb(res.data.plan);
        }, function(err) {
            console.log(err);
        });
    }

    $scope.setEditControls = function(plan) {
        $scope.asset.asset_id           = plan.id;
        $scope.asset.year               = plan.year;
        $scope.asset.plan_no            = plan.plan_no;
        $scope.asset.desc               = plan.plan_item.item.item_name;
        $scope.asset.spec               = plan.plan_item.spec;
        $scope.asset.price_per_unit     = plan.plan_item.price_per_unit;
        $scope.asset.amount             = plan.plan_item.amount;
        $scope.asset.sum_price          = plan.plan_item.sum_price;
        $scope.asset.start_month        = $scope.monthLists.find(m => m.id == plan.start_month).name;
        $scope.asset.reason             = plan.reason;
        $scope.asset.remark             = plan.remark;
        $scope.asset.status             = plan.status;

        /** Convert int value to string */
        $scope.asset.category_id        = plan.plan_item.item.category_id.toString();
        $scope.asset.unit_id            = plan.plan_item.unit_id.toString();
        $scope.asset.depart_id          = plan.depart_id.toString();
        $scope.asset.division_id        = plan.division_id ? plan.division_id.toString() : '';
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

        $http.post(`${CONFIG.baseUrl}/plans/send-supported/${$scope.asset.asset_id}`, data)
        .then(function(res) {
            console.log(res.data);
        }, function(err) {
            console.log(err);
        });

        /** Redirect to list view */
        window.location.href = `${CONFIG.baseUrl}/assets/list`;
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

        $http.post(`${CONFIG.baseUrl}/plans/create-po/${$scope.asset.asset_id}`, data)
        .then(function(res) {
            console.log(res.data);
        }, function(err) {
            console.log(err);
        });

        /** Redirect to list view */
        window.location.href = `${CONFIG.baseUrl}/assets/list`;
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

        if(confirm(`คุณต้องลบแผนครุภัณฑ์รหัส ${id} ใช่หรือไม่?`)) {
            $http.delete(`${CONFIG.baseUrl}/plans/${id}`)
            .then(res => {
                console.log(res);
            }, err => {
                console.log(err);
            });
        }
    };
});