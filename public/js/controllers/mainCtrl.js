app.controller('mainCtrl', function($scope, $http, $location, $routeParams, CONFIG) {
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
    $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? (moment().year() + 544).toString()
                        : (moment().year() + 543).toString();
    $scope.cboMonth = moment().format('MM');
    $scope.cboPlanType = "";
    $scope.cboSupplier = "";
    $scope.cboCategory = "";
    $scope.cboDepart = "";
    $scope.cboStatus = "";
    $scope.searchKey = "";
    $scope.cboQuery = "";
    $scope.cboMenu = "";

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
        item_name: '',
        plan_type_id: '',
        category_id: '',
        group_id: '',
        price_per_unit: '',
        unit_id: '',
        in_stock: 0,
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
    
    $scope.inStock = 0;
    $scope.setInStock = function(value) {
        $scope.inStock = value;
    };

    $scope.getMonthName = function(month) {
        const monthObj = $scope.monthLists.find(m => m.id == month);

        return monthObj.name;
    };

    $scope.handleInputChange = function(name, value) {
        $scope[name] = value;
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

    $scope.onFilterCategories = function(type) {
        $scope.forms.categories = $scope.temps.categories.filter(cate => cate.plan_type_id === parseInt(type));
    };

    $scope.onFilterExpenses = function(type) {
        $scope.forms.expenses = $scope.temps.expenses.filter(ex => ex.expense_type_id === parseInt(type));
    };

    $scope.onPlanTypeSelected = function(type) {
        $scope.forms.categories = $scope.temps.categories.filter(cate => cate.plan_type_id === parseInt(type));

        if ([3,4].includes(parseInt(type))) {
            $scope.forms.groups = $scope.temps.groups.filter(group => group.plan_type_id === parseInt(type));

            $('#group_id').attr('disabled', false)
        } else {
            $('#group_id').attr('disabled', true)
        }
    };

    $scope.showItemsList = function() {
        $scope.forms.categories = $scope.temps.categories.filter(cate => cate.plan_type_id === $scope.planType);

        $scope.getItems();
    };

    $scope.getItems = function() {
        $scope.loading = true;
        $scope.items = [];
        $scope.items_pager = null;

        let type = $scope.planType === '' ? '' : $scope.planType;
        let cate = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let name = $scope.searchKey === '' ? '' : $scope.searchKey;
        let in_stock = $scope.inStock === '' ? '' : $scope.inStock;

        $http.get(`${CONFIG.baseUrl}/items/search?type=${type}&cate=${cate}&name=${name}&in_stock=${in_stock}`)
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

    $scope.getItemsWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.items = [];
        $scope.items_pager = null;

        let type = $scope.planType === '' ? '' : $scope.planType;
        let cate = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let name = $scope.searchKey === '' ? '' : $scope.searchKey;
        let in_stock = $scope.inStock === '' ? '' : $scope.inStock;

        $http.get(`${url}&type=${type}&cate=${cate}&name=${name}&in_stock=${in_stock}`)
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
                /** ถ้าบันทึกสำเร็จให้เซตค่า desc และ item_id จาก responsed data  */
                cb(event, res.data.item);

                clearNewItem();
            }, err => {
                console.log(err);
            })

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
            item_name: '',
            plan_type_id: '',
            category_id: '',
            group_id: '',
            price_per_unit: '',
            unit_id: '',
            in_stock: '',
            remark: '',
            error: {}
        };
    };

    $scope.isMaterial = function(planType) {
        if ([2,6].includes(parseInt(planType))) {
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
});
