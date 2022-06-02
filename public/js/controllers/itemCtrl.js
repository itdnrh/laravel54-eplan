app.controller('itemCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.Items = [];
    $scope.pager = null;

    $scope.item = {
        Item_id: '',
        plan_type_id: '',
        category_id: '',
        group_id: '',
        item_name: '',
        price_per_unit: '',
        unit_id: '',
        in_stock: 'I',
        first_year: '',
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

    $('#doc_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date());
        // .on('show', function (e) {
        //     $('.day').click(function(event) {
        //         event.preventDefault();
        //         event.stopPropagation();
        //     });
        // });

    const clearItemObj = function() {
        $scope.item = {
            item_id: '',
            year: '',
            in_plan: 'I',
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
        $scope.loading = true;
        $scope.items = [];
        $scope.pager = null;

        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;

        $http.get(`${CONFIG.apiUrl}/items?type=1&cate=${cate}&status=${status}`)
        .then(function(res) {
            $scope.setItems(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setItems = function(res) {
        const { data, ...pager } = res.data.items;
        console.log(data, pager);

        $scope.items = data;
        $scope.pager = pager;
    };

    $scope.onSelectedItem = function(event, item) {
        if (item) {
            $('#item_id').val(item.id);
            $scope.item.item_id = item.id;
            $scope.item.desc = item.item_name;
            $scope.item.price_per_unit = item.price_per_unit;
            $scope.item.unit_id = item.unit_id.toString();
            $scope.item.category_id = item.category_id.toString();
        }

        $('#items-list').modal('hide');
    };

    $scope.getDataWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.items = [];
        $scope.pager = null;

        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;

        $http.get(`${url}&type=1&cate=${cate}&status=${status}`)
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
        /** Global data */
        $scope.planId                   = plan.id;
        $scope.planType                 = 1;

        /** ข้อมูลครุภัณฑ์ */
        $scope.asset.asset_id           = plan.id;
        $scope.asset.in_plan            = plan.in_plan;
        $scope.asset.year               = plan.year;
        // $scope.asset.plan_no            = plan.plan_no;
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