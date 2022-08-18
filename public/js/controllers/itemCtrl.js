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
        id: '',
        asset_no: '',
        plan_type_id: '',
        category_id: '',
        group_id: '',
        item_name: '',
        en_name: '',
        price_per_unit: '',
        unit_id: '',
        in_stock: 0,
        first_year: '2565',
        have_subitem: 0,
        calc_method: 1,
        is_fixcost: 0,
        remark: '',
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

    // $('#doc_date')
    //     .datepicker(dtpDateOptions)
    //     .datepicker('update', new Date());
        // .on('show', function (e) {
        //     $('.day').click(function(event) {
        //         event.preventDefault();
        //         event.stopPropagation();
        //     });
        // });

    const clearItem = function() {
        $scope.item = {
            id: '',
            asset_no: '',
            plan_type_id: '',
            category_id: '',
            group_id: '',
            item_name: '',
            en_name: '',
            price_per_unit: '',
            unit_id: '',
            in_stock: 0,
            first_year: '2565',
            have_subitem: 0,
            calc_method: 1,
            is_fixcost: 0,
            remark: '',
        };
    };

    $scope.getAll = function(event) {
        $scope.loading = true;
        $scope.items = [];
        $scope.pager = null;

        let type    = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let cate    = !$scope.cboCategory ? '' : $scope.cboCategory;
        let group   = !$scope.cboGroup ? '' : $scope.cboGroup;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let name    = $scope.txtItemName === '' ? '' : $scope.txtItemName;

        $http.get(`${CONFIG.apiUrl}/items?type=${type}&cate=${cate}&group=${group}&name=${name}&status=${status}`)
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

    $scope.getItemsWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.items = [];
        $scope.pager = null;

        let type    = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let cate    = !$scope.cboCategory ? '' : $scope.cboCategory;
        let group   = !$scope.cboGroup ? '' : $scope.cboGroup;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let name    = $scope.txtItemName === '' ? '' : $scope.txtItemName;

        $http.get(`${url}&type=${type}&cate=${cate}&group=${group}&status=${status}&name=${name}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getById = function(id, cb) {
        $scope.loading = false;

        $http.get(`${CONFIG.apiUrl}/items/${id}`)
        .then(function(res) {
            cb(res.data.item);

            $scope.loading = false;
        }, function(err) {
            console.log(err);

            $scope.loading = false;
        });
    }

    $scope.setEditControls = function(item) {
        if (item) {
            console.log(item);
            $scope.item.id              = item.id;
            $scope.item.asset_no        = item.asset_no;
            $scope.item.item_name       = item.item_name;
            $scope.item.en_name         = item.en_name;
            $scope.item.price_per_unit  = item.price_per_unit;
            $scope.item.in_stock        = item.in_stock;
            $scope.item.first_year      = item.first_year;
            $scope.item.have_subitem    = item.have_subitem;
            $scope.item.calc_method     = item.calc_method;
            $scope.item.is_fixcost      = item.is_fixcost;
            $scope.item.remark          = item.remark;
            $scope.item.status          = item.status;
    
            /** Convert int value to string */
            $scope.item.plan_type_id    = item.plan_type_id.toString();
            $scope.item.category_id     = item.category_id.toString();
            $scope.item.group_id        = item.group_id ? item.group_id.toString() : '';
            $scope.item.unit_id         = item.unit_id.toString();

            /** */
            $scope.onPlanTypeSelected(item.plan_type_id);
        }
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $scope.loading = true;

        $http.post(`${CONFIG.apiUrl}/items`, $scope.item)
        .then((res) => {
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");

                setTimeout(() => {
                    window.location.href = `${CONFIG.baseUrl}/system/items`;
                }, 1000);
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
        window.location.href = `${CONFIG.baseUrl}/items/edit/${id}`;
    };

    $scope.update = function(event) {
        event.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขสินค้า/บริการ รหัส ${$scope.item.id} ใช่หรือไม่?`)) {
            $scope.loading = true;

            $http.post(`${CONFIG.baseUrl}/items/update/${$scope.item.id}`, $scope.item)
            .then((res) => {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลเรียบร้อย !!!");

                    setTimeout(() => {
                        window.location.href = `${CONFIG.baseUrl}/system/items`;
                    }, 1000);
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

        if(confirm(`คุณต้องลบสินค้า/บริการ รหัส ${id} ใช่หรือไม่?`)) {
            $scope.loading = true;

            $http.post(`${CONFIG.baseUrl}/items/delete/${id}`)
            .then(res => {
                console.log(res);
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลบข้อมูลเรียบร้อย !!!");

                    setTimeout(() => {
                        window.location.href = `${CONFIG.baseUrl}/system/items`;
                    }, 1000);
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลบข้อมูลได้ !!!");
                }
            }, err => {
                console.log(err);
                $scope.loading = false;

                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลบข้อมูลได้ !!!");
            });
        }
    };
});