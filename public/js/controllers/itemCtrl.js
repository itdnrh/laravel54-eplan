app.controller('itemCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.cboYear = '';
    $scope.cboPlanType = '';
    $scope.cboCategory = '';
    $scope.cboGroup = '';
    $scope.txtItemName = '';

    $scope.items = [];
    $scope.pager = null;

    $scope.item = {
        Item_id: '',
        parcel_no: '',
        plan_type_id: '',
        category_id: '',
        group_id: '',
        item_name: '',
        price_per_unit: '',
        unit_id: '',
        in_stock: 0,
        first_year: '2565',
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

    // $('#doc_date')
    //     .datepicker(dtpOptions)
    //     .datepicker('update', new Date());
        // .on('show', function (e) {
        //     $('.day').click(function(event) {
        //         event.preventDefault();
        //         event.stopPropagation();
        //     });
        // });

    const clearItem = function() {
        $scope.item = {
            Item_id: '',
            parcel_no: '',
            plan_type_id: '',
            category_id: '',
            group_id: '',
            item_name: '',
            price_per_unit: '',
            unit_id: '',
            in_stock: 0,
            first_year: '2565',
            remark: '',
        };
    };

    $scope.getAll = function(event) {
        $scope.loading = true;
        $scope.items = [];
        $scope.pager = null;

        let type = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let cate = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let status = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let name = $scope.txtItemName === '' ? '' : $scope.txtItemName;

        $http.get(`${CONFIG.apiUrl}/items?type=${type}&cate=${cate}&name=${name}&status=${status}`)
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

        $scope.items = data;
        $scope.pager = pager;
    };

    $scope.getDataWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.items = [];
        $scope.pager = null;

        let type = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let name  = $scope.txtItemName === '' ? '' : $scope.txtItemName;

        $http.get(`${url}&type=${type}&cate=${cate}&status=${status}&name=${name}`)
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

        $scope.loading = true;

        $http.post(`${CONFIG.apiUrl}/items`, $scope.item)
        .then((res) => {
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");

                window.location.href = `${CONFIG.baseUrl}/system/items`;
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
            }
        }, function(err) {
            $scope.loading = false;

            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
        });
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