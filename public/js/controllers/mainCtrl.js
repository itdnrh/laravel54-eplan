app.controller('mainCtrl', function(CONFIG, $scope, $http, toaster, $location, $routeParams) {
/** ################################################################################## */
    console.log(CONFIG);
/** ################################################################################## */
    //################## autocomplete ##################
    // $scope.maintenanceList = [];
    // $scope.fillinMaintenanceList = function(event) {
    //     console.log(event.keyCode);
    //     if (event.which === 13) {
    //         event.preventDefault();
    //         $scope.maintenanceList.push($(event.target).val());

    //         //เคลียร์ค่าใน text searchProduct
    //         $(event.target).val('');

    //         var maindetained_detail = "";
    //         var count = 0;
    //         angular.forEach($scope.maintenanceList, function(maintained) {
    //             if(count != $scope.maintenanceList.length - 1){
    //                 maindetained_detail += maintained + ",";
    //             } else {
    //                 maindetained_detail += maintained
    //             }

    //             count++;
    //         });

    //         $('#detail').val(maindetained_detail);
    //     }
    // }

/** ################################################################################## */
    /** MENU */
    $scope.menu = 'assets';
    $scope.submenu = 'list';
    $scope.setActivedMenu = function() {
        let routePath = $location.$$absUrl.replace(`${CONFIG.baseUrl}/`, '');
        let [mnu, submnu, ...params] = routePath.split('/');

        $scope.menu = mnu; 
        $scope.submenu = submnu;
    }

    $scope.redirectTo = function(e, path) {
        e.preventDefault();
        window.location.href = `${CONFIG.baseUrl}/${path}`;
    };
/** ################################################################################## */
    $scope.cboYear = '2566';
    // $scope.cboYear = parseInt(moment().format('MM')) > 9
    //                     ? (moment().year() + 544).toString()
    //                     : (moment().year() + 543).toString();
    $scope.cboMonth = moment().format('MM');
    $scope.cboPlanType = "";
    $scope.cboSupplier = "";
    $scope.cboCategory = "";
    $scope.cboGroup = "";
    $scope.cboFaction = "";
    $scope.cboDepart = "";
    $scope.cboDivision = "";
    $scope.cboStatus = "";
    $scope.txtKeyword = "";
    $scope.collapseBox = true;

    $scope.budgetYearRange = [2560,2561,2562,2563,2564,2565,2566,parseInt($scope.cboYear)+2];
    $scope.monthLists = [
        { id: '10', name: 'ตุลาคม' },
        { id: '11', name: 'พฤศจิกายน' },
        { id: '12', name: 'ธันวาคม' },
        { id: '01', name: 'มกราคม' },
        { id: '02', name: 'กุมภาพันธ์' },
        { id: '03', name: 'มีนาคม' },
        { id: '04', name: 'เมษายน' },
        { id: '05', name: 'พฤษภาคม' },
        { id: '06', name: 'มิถุนายน' },
        { id: '07', name: 'กรกฎาคม' },
        { id: '08', name: 'สิงหาคม' },
        { id: '09', name: 'กันยายน' },        
    ];

    $scope.items = [];
    $scope.items_pager = null;

    $scope.newItem = {
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
        error: {}
    };

    $scope.forms = {
        depart: [],
        division: [],
        categories: [],
        groups: [],
        expenses: [],
    };

    $scope.temps = {
        departs: [],
        divisions: [],
        categories: [],
        groups: [],
        expenses: [],
    }

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

    $scope.toggleBox = function() {
        $scope.collapseBox = !$scope.collapseBox;
    };

    $scope.initForms = (data, planType) => {
        if (data) {
            $scope.temps.departs = data.departs ? data.departs : [];
            $scope.temps.divisions = data.divisions ? data.divisions : [];
            $scope.temps.categories = data.categories ? data.categories : [];
            $scope.temps.groups = data.groups ? data.groups : [];
            $scope.temps.strategics = data.strategics ? data.strategics : [];
            $scope.temps.strategies = data.strategies ? data.strategies : [];
            $scope.temps.kpis = data.kpis ? data.kpis : [];
            $scope.temps.expenses = data.expenses ? data.expenses : [];

            $scope.forms.categories = data.categories
                                        ? data.categories.filter(cate => cate.plan_type_id === parseInt(planType))
                                        : [];
        }

        $scope.planType = planType;
    };

    $scope.formatReadableTime = function(time) {
        let days = moment().diff(moment(time), "days");
        let hours = moment().diff(moment(time), "hours");
        let months = moment().diff(moment(time), "months");
        let restHours = hours - (days * 24);

        if (months == 0) {
            if (days > 0) {
                if (days == 1) {
                    return 'เมื่อวาน';
                }
    
                return ` ${days} วันที่แล้ว`;
            } else {
                return ` ${hours} ชม.ที่แล้ว`;
            }
        } else if (months == 1) {
            return 'เดือนที่แล้ว';
        } else {
            return ` ${months} เดือนที่แล้ว`;
        }
    }

    $scope.inStock = 0;
    $scope.setInStock = function(value) {
        $scope.inStock = value;
    };

    $scope.setPlanType = function(planType) {
        $scope.planType = planType;
    };

    $scope.setCboCategory = function(cate) {
        $scope.cboCategory = cate;
    };

    $scope.getMonthName = function(month) {
        const monthObj = $scope.monthLists.find(m => m.id == month);

        return monthObj.name;
    };

    $scope.handleInputChange = function(name, value) {
        $scope[name] = value;
    }

    $scope.isRenderWardInsteadDepart = function(departId) {
        return [19,20,68].includes(departId);
    }

    $scope.onStrategicSelected = function(strategic) {
        $scope.forms.strategies = $scope.temps.strategies.filter(stg => stg.strategic_id == strategic);
    };

    $scope.onStrategySelected = function(strategy) {
        $scope.forms.kpis = $scope.temps.kpis.filter(kpi => kpi.strategy_id == strategy);
    };

    $scope.onFactionSelected = function(faction) {
        $scope.forms.departs = $scope.temps.departs.filter(dep => dep.faction_id == faction);
    };

    $scope.onDepartSelected = function(depart) {
        $scope.forms.divisions = $scope.temps.divisions.filter(div => div.depart_id == depart);
    };

    $scope.onCategorySelected = function(cate) {
        if ($scope.temps.groups.some(group => group.category_id === parseInt(cate))) {
            $scope.forms.groups = $scope.temps.groups.filter(group => group.category_id === parseInt(cate));
        } else {
            $scope.forms.groups = $scope.temps.groups.filter(group => group.plan_type_id === $scope.planType);
        }
    };

    $scope.onFilterExpenses = function(type) {
        $scope.forms.expenses = $scope.temps.expenses.filter(ex => ex.expense_type_id === parseInt(type));
    };

    $scope.onFilterCategories = function(type) {
        $scope.forms.categories = $scope.temps.categories.filter(cate => cate.plan_type_id === parseInt(type));
    };

    $scope.onPlanTypeSelected = function(type) {
        $scope.forms.categories = $scope.temps.categories.filter(cate => cate.plan_type_id === parseInt(type));

        if ([1,3,4].includes(parseInt(type))) {
            $scope.forms.groups = $scope.temps.groups.filter(group => group.plan_type_id === parseInt(type));

            $('#group_id').attr('disabled', false)
        } else {
            // $('#group_id').attr('disabled', true)
        }
    };

    $scope.showItemsList = function(modalId, inStock='') {
        $scope.forms.categories = $scope.temps.categories.filter(cate => cate.plan_type_id === $scope.planType);

        $scope.getItems(modalId);
    };

    $scope.getItems = function(modalId, haveSubitem='') {
        $scope.loading = true;
        $scope.items = [];
        $scope.items_pager = null;

        let type = $scope.planType === '' ? '' : $scope.planType;
        let cate = !$scope.cboCategory ? '' : $scope.cboCategory;
        let group = !$scope.cboGroup ? '' : $scope.cboGroup;
        let name = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;
        let have_subitem = haveSubitem === '' ? '' : haveSubitem;
        let in_stock = $scope.inStock !== '' ? $scope.inStock : '';

        $http.get(`${CONFIG.baseUrl}/items/search?type=${type}&cate=${cate}&group=${group}&name=${name}&have_subitem=${have_subitem}&in_stock=${in_stock}`)
        .then(function(res) {
            $scope.setItems(res);

            $scope.loading = false;

            $(modalId).modal('show');
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

    $scope.getItemsWithUrl = function(e, url, cb, haveSubitem='') {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.items = [];
        $scope.items_pager = null;

        let type = $scope.planType === '' ? '' : $scope.planType;
        let cate = !$scope.cboCategory ? '' : $scope.cboCategory;
        let group = !$scope.cboGroup ? '' : $scope.cboGroup;
        let name = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;
        let have_subitem = haveSubitem === '' ? '' : haveSubitem;
        let in_stock = $scope.inStock === '' ? '' : $scope.inStock;

        $http.get(`${url}&type=${type}&cate=${cate}&group=${group}&name=${name}&have_subitem=${have_subitem}&in_stock=${in_stock}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.handleItemSelected = function(event, item, cb) {
        cb(event, item);
    };

    $scope.showNewItemForm = function() {
        $scope.newItem.plan_type_id = $scope.planType.toString();
        $scope.newItem.in_stock = $scope.inStock;

        // if (parseInt($scope.planType) === 2) {
        //     $scope.newItem.in_stock = 0;
        // } else if (parseInt($scope.planType) === 6) {
        //     $scope.newItem.in_stock = 1;
        // } else {
        //     $scope.newItem.in_stock = '';
        // }

        $scope.onPlanTypeSelected($scope.planType);

        $('#item-form').modal('show');
    };

    $scope.createNewItem = function(event, cb) {
        if (validateNewItem($scope.newItem)) {            
            $http.post(`${CONFIG.baseUrl}/items/store`, $scope.newItem)
            .then(res => {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "บันทึกสินค้า/บริการเรียบร้อย !!!");

                    /** ถ้าบันทึกสำเร็จให้เซตค่า desc และ item_id จาก responsed data  */
                    cb(event, res.data.item);
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกสินค้า/บริการได้ !!!");
                }
            }, err => {
                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกสินค้า/บริการได้ !!!");
            })

            clearNewItem();
            $('#item-form').modal('hide');
        }
    };

    const validateNewItem = () => {
        if ($scope.newItem.plan_type_id == '') {
            $scope.newItem.error = { ...$scope.newItem.error, plan_type_id: 'กรุณาเลือกประเภทแผน' }
        } else {
            if ($scope.newItem.error.hasOwnProperty('plan_type_id')) {
                const { plan_type_id, ...rest } = $scope.newItem.error;
                $scope.newItem.error = { ...rest }
            }
        }

        if ($scope.newItem.category_id == '') {
            $scope.newItem.error = { ...$scope.newItem.error, category_id: 'กรุณาเลือกประเภทสินค้า/บริการ' }
        } else {
            if ($scope.newItem.error.hasOwnProperty('category_id')) {
                const { category_id, ...rest } = $scope.newItem.error;
                $scope.newItem.error = { ...rest }
            }
        }

        if ($scope.newItem.item_name == '') {
            $scope.newItem.error = { ...$scope.newItem.error, item_name: 'กรุณาระบุชื่อสินค้า/บริการ' }
        } else {
            if ($scope.newItem.error.hasOwnProperty('item_name')) {
                const { item_name, ...rest } = $scope.newItem.error;
                $scope.newItem.error = { ...rest }
            }
        }

        if ($scope.newItem.price_per_unit == '') {
            $scope.newItem.error = { ...$scope.newItem.error, price_per_unit: 'กรุณาระบุราคาต่อหน่วย' }
        } else if (isNaN($scope.newItem.price_per_unit)) {
            $scope.newItem.error = { ...$scope.newItem.error, price_per_unit: 'กรุณาระบุราคาต่อหน่วยเป็นตัวเลข (ไม่ต้องมี comma หรือ ,)' }
        } else {
            if ($scope.newItem.error.hasOwnProperty('price_per_unit')) {
                const { price_per_unit, ...rest } = $scope.newItem.error;
                $scope.newItem.error = { ...rest }
            }
        }

        if ($scope.newItem.unit_id == '') {
            $scope.newItem.error = { ...$scope.newItem.error, unit_id: 'กรุณาเลือกหน่วยนับ' }
        } else {
            if ($scope.newItem.error.hasOwnProperty('unit_id')) {
                const { unit_id, ...rest } = $scope.newItem.error;
                $scope.newItem.error = { ...rest }
            }
        }

        return Object.keys($scope.newItem.error).length === 0;
    };

    const clearNewItem = function() {
        $scope.newItem = {
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
            error: {}
        };
    };

    $scope.isMaterial = function(planType) {
        if (parseInt(planType) === 2) {
            return true;
        } else {
            return false;
        }
    };

    $scope.isService = function(planType) {
        if (parseInt(planType) === 3) {
            return true;
        } else {
            return false;
        }
    };

    $scope.planId = "";
    $scope.showSupportedForm = function() {
        $('#supported-from').modal('show');
    };

    $scope.sendSupportedDoc = (e, type, id) => {
        e.preventDefault();

        let data = {
            doc_no: $('#doc_no').val(),
            doc_date: $('#doc_date').val(),
            sent_date: $('#sent_date').val(),
            sent_user: $('#sent_user').val(),
        };

        $http.post(`${CONFIG.baseUrl}/plans/send-supported/${id}`, data)
        .then(function(res) {
            console.log(res.data);
        }, function(err) {
            console.log(err);
        });

        /** Redirect to list view */
        if (type == 1) {
            window.location.href = `${CONFIG.baseUrl}/plans/assets`;
        } else if (type == 2) {
            window.location.href = `${CONFIG.baseUrl}/plans/materials`;
        } else if (type == 3) {
            window.location.href = `${CONFIG.baseUrl}/plans/services`;
        } else if (type == 4) {
            window.location.href = `${CONFIG.baseUrl}/plans/constructs`;
        }
    };

    $scope.showPoForm = function() {
        $('#po-form').modal('show');
    };

    $scope.createPO = (e, type, id) => {
        e.preventDefault();

        let data = {
            po_no: $('#po_no').val(),
            po_date: $('#po_date').val(),
            po_net_total: $('#po_net_total').val(),
            po_user: $('#po_user').val(),
        };

        $http.post(`${CONFIG.baseUrl}/plans/create-po/${id}`, data)
        .then(function(res) {
            console.log(res.data);
        }, function(err) {
            console.log(err);
        });

        /** Redirect to list view */
        if (type == 1) {
            window.location.href = `${CONFIG.baseUrl}/plans/assets`;
        } else if (type == 2) {
            window.location.href = `${CONFIG.baseUrl}/plans/materials`;
        } else if (type == 3) {
            window.location.href = `${CONFIG.baseUrl}/plans/services`;
        } else if (type == 4) {
            window.location.href = `${CONFIG.baseUrl}/plans/constructs`;
        }
    };

    $scope.isDisabledRequest = function(e, userRole='') {
        // if (moment().isAfter(moment('2022-08-16 17:30:00')) && userRole != 4) {
        //     toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถส่งข้อมูลได้ เนื่องจากเลยกำหนดแล้ว !!!");
        //     e.preventDefault();
        //     return;
        // }
    };

    $scope.changeData = {
        plan_id: '',
        item_id: '',
        from_type: '',
        plan_type_id: '',
        category_id: '',
        group_id: '',
        user: ''
    };

    $scope.onShowChangeForm = function(e, plan) {
        e.preventDefault();
        console.log(plan);

        $scope.changeData.plan_id   = plan.id;
        $scope.changeData.item_id   = plan.item_id;
        $scope.changeData.from_type = plan.plan_type_id;

        $('#change-form').modal('show');
    };

    $scope.change = function(e, form, id) {
        e.preventDefault();
        
        if (form.$invalid) {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณากรอกข้อมูลให้ครบ !!!");
            return;
        }

        $scope.loading = true;
        const data = { ...$scope.changeData, user: $('#user').val() }

        if(confirm(`คุณต้องลบแผนครุภัณฑ์รหัส ${id} ใช่หรือไม่?`)) {
            $http.put(`${CONFIG.apiUrl}/plans/${id}/change`, data)
            .then(res => {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "เปลี่ยนหมวดเรียบร้อย !!!");
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถเปลี่ยนหมวดได้ !!!");
                }

                $scope.loading = false;

                $('#change-form').modal('hide');

                setTimeout(() => {
                    if ($scope.changeData.from_type == '1') {
                        window.location.href = `${CONFIG.baseUrl}/plans/assets`;
                    } else if ($scope.changeData.from_type == '2') {
                        window.location.href = `${CONFIG.baseUrl}/plans/materials?in_stock=0`;
                    } else if ($scope.changeData.from_type == '3') {
                        window.location.href = `${CONFIG.baseUrl}/plans/services`;
                    } else if ($scope.changeData.from_type == '4') {
                        window.location.href = `${CONFIG.baseUrl}/plans/constructs`;
                    }
                }, 1000);
            }, err => {
                console.log(err);

                $scope.loading = false;
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถเปลี่ยนหมวดได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.currencyToNumber = function(currency) {
        if (typeof currency === 'number') return currency;
        if (currency == '') return 0;

        return currency.replaceAll(',', '');
    };

    $scope.onValidateForm = function(e, endpoint, plan, frmName, callback) {
        e.preventDefault();

        plan.price_per_unit = $scope.currencyToNumber(plan.price_per_unit);
        plan.sum_price      = $scope.currencyToNumber(plan.sum_price);
        plan.amount         = $scope.currencyToNumber(plan.amount);
        plan.have_amount    = plan.have_amount ? $scope.currencyToNumber(plan.have_amount) : 0;

        $scope.formValidate(e, endpoint, plan, frmName, callback)
    };

    $scope.expandRow = '-1';
    $scope.toggleDetailsCollpse = function(selectedIndex) {
        if ($scope.expandRow === selectedIndex) {
            $scope.expandRow = '-1';
        } else {
            $scope.expandRow = selectedIndex;
        }
    };
});
