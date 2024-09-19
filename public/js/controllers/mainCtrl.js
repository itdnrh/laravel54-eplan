app.controller('mainCtrl', function(CONFIG, $rootScope, $scope, $http, toaster, $location, $routeParams) {
    /*
    |-----------------------------------------------------------------------------
    | Local variables and constraints initialization
    |-----------------------------------------------------------------------------
    */
    /** Sidebar's menus */
    $scope.menu = 'assets';
    $scope.submenu = 'list';

    $scope.collapseBox = true;
    $scope.loading = false;

    /** Filtering input models */
    $scope.cboYear = '2568'; //parseInt(moment().format('MM')) > 9
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
    $scope.txtItemName = '';
    $scope.cboPrice = '';
    $scope.cboBudget = '';
    $scope.isApproved = false;
    $scope.isInPlan = 'I';
    $scope.isAdjust = '';

    /** Input control iteration models */
    $scope.budgetYearRange = $rootScope.range(2565, parseInt($scope.cboYear) + 3);
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

    /** Data selection models */
    $scope.plans = [];
    $scope.plans_pager = null;
    $scope.items = [];
    $scope.items_pager = null;

    /** Data insertion models */
    $scope.newItem = {
        plan_type_id: '',
        category_id: '',
        group_id: '',
        item_name: '',
        en_name: '',
        price_per_unit: '',
        unit_id: '',
        in_stock: 0,
        calc_method: 1,
        have_subitem: 0,
        is_fixcost: 0,
        is_addon: 0,
        first_year: '2565',
        remark: '',
        error: {}
    };

    $scope.plansTotal = 0;

    let dtpOptions = {
        autoclose: true,
        language: 'th',
        format: 'dd/mm/yyyy',
        thaiyear: true,
        todayBtn: true,
        todayHighlight: true
    };

    /*
    |-----------------------------------------------------------------------------
    | Shared methods Initialization
    |-----------------------------------------------------------------------------
    */
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

    $scope.toggleBox = function() {
        $scope.collapseBox = !$scope.collapseBox;
    };

    $scope.currencyToNumber = function(currency) {
        if (typeof currency === 'number') return currency;
        if (currency == '') return 0;

        return currency.replaceAll(',', '');
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

        return monthObj ? monthObj.name : '';
    };

    $scope.handleInputChange = function(name, value) {
        $scope[name] = value;
    }

    $scope.isRenderWardInsteadDepart = function(departId) {
        return [19,20,68].includes(departId);
    }

    $scope.isDisabledRequest = function(e, userRole='') {
        // if (moment().isAfter(moment('2022-08-16 17:30:00')) && userRole != 4) {
        //     toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถส่งข้อมูลได้ เนื่องจากเลยกำหนดแล้ว !!!");
        //     e.preventDefault();
        //     return;
        // }
    };

    $scope.calculatePlansTotal = function(plans) {
        return plans.reduce((sum, curVal) => {
            return sum + curVal.plan_item.sum_price;
        }, 0);
    };

    /*
    |-----------------------------------------------------------------------------
    | Filtering methods Initialization
    |-----------------------------------------------------------------------------
    */
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

    /*
    |-----------------------------------------------------------------------------
    | Item selection processes
    |-----------------------------------------------------------------------------
    */
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

    /*
    |-----------------------------------------------------------------------------
    | Item insertion processes
    |-----------------------------------------------------------------------------
    */
    $scope.showNewItemForm = function() {
        $scope.newItem.plan_type_id = $scope.planType.toString();
        $scope.newItem.in_stock = $scope.inStock;

        $scope.onPlanTypeSelected($scope.planType);

        $(`#item_unit_id`).select2({ theme: 'bootstrap' });

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
            calc_method: 1,
            have_subitem: 0,
            is_fixcost: 0,
            is_addon: 0,
            first_year: '2565',
            remark: '',
            error: {}
        };
    };

    /*
    |-----------------------------------------------------------------------------
    | Change plan's plan_type_id processes
    |-----------------------------------------------------------------------------
    */
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

    /*
    |-----------------------------------------------------------------------------
    | Item validation processes
    |-----------------------------------------------------------------------------
    */
    $scope.onValidateForm = function(e, endpoint, plan, frmName, callback) {
        e.preventDefault();

        plan.price_per_unit = $scope.currencyToNumber(plan.price_per_unit);
        plan.sum_price      = $scope.currencyToNumber(plan.sum_price);
        plan.amount         = $scope.currencyToNumber(plan.amount);
        plan.have_amount    = plan.have_amount ? $scope.currencyToNumber(plan.have_amount) : 0;

        $scope.formValidate(e, endpoint, plan, frmName, callback)
    };

    /*
    |-----------------------------------------------------------------------------
    | Detail collapse processes
    |-----------------------------------------------------------------------------
    */
    $scope.expandRow = '-1';
    $scope.toggleDetailsCollpse = function(selectedIndex) {
        if ($scope.expandRow === selectedIndex) {
            $scope.expandRow = '-1';
        } else {
            $scope.expandRow = selectedIndex;
        }
    };

    /*
    |-----------------------------------------------------------------------------
    | Plan adjustment processes
    |-----------------------------------------------------------------------------
    */
    $scope.adjustment = {
        id: '',
        plan_id: '',
        adjust_type: 1,
        price_per_unit: '',
        unit_id: '',
        amount: '',
        sum_price: '',
        remark: '',
        plan: null
    };
    $scope.setAdjustType = function(type) {
        $scope.adjustment.adjust_type = type;
    };

    $scope.showAdjustForm = function(e, plan, adjustId='') {
        if (plan) {
            $scope.adjustment.plan = plan;
            $scope.adjustment.plan_id = plan.id;

            if (adjustId) {
                const adjust = plan.adjustments.find(adj => adj.id == adjustId);

                $scope.adjustment.id            = adjustId;
                $scope.adjustment.adjust_type   = adjust.adjust_type;
                $scope.adjustment.price_per_unit = adjust.price_per_unit;
                $scope.adjustment.unit_id       = adjust.unit_id ? adjust.unit_id.toString() : '';
                $scope.adjustment.amount        = adjust.amount;
                $scope.adjustment.sum_price     = adjust.sum_price;
                $scope.adjustment.remark        = adjust.remark;

                $('#unit_id').val(adjust.unit_id).trigger('change.select2');
            }
    
            $('#adjust-form').modal('show');
        }
    };

    $scope.adjust = function(e, form, adjustId) {
        if (form.$invalid) {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณากรอกข้อมูลให้ครบ !!!");
            return;
        }

        if (!adjustId) {
            $scope.loading = true;

            /** เพิ่มรายการใหม่ */
            $http.put(`${CONFIG.apiUrl}/plans/${$scope.adjustment.plan_id}/adjust`, $scope.adjustment)
            .then(res => {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "บันทึกปรับแผนเรียบร้อย !!!");

                    window.location.href = window.location.href;
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถบันทึกปรับแผนได้ !!!");
                }

                $scope.loading = false;
            }, (err) => {
                console.log(err);

                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถบันทึกปรับแผนได้ !!!");

                $scope.loading = false;
            });
        } else {
            /** แก้ไขรายการ */
            if (confirm(`คุณต้องแก้ไขข้อมูลการปรับแผนใช่หรือไม่?`)) {
                $scope.loading = true;

                $http.put(`${CONFIG.apiUrl}/plans/${$scope.adjustment.plan_id}/${adjustId}/adjust`, $scope.adjustment)
                .then(res => {
                    if (res.data.status == 1) {
                        toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลการปรับแผนเรียบร้อย !!!");

                        window.location.href = window.location.href;
                    } else {
                        toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถบันทึกปรับแผนได้ !!!");
                    }

                    $scope.loading = false;
                }, (err) => {
                    console.log(err);

                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถแก้ไขข้อมูลการปรับแผนได้ !!!");

                    $scope.loading = false;
                });
            }
        }
    };

    $scope.deleteAdjust = function(e, adjustId) {
        if (!adjustId) {
            if (confirm(`คุณต้องลบข้อมูลการปรับแผนใช่หรือไม่?`)) {
                // $scope.loading = true;

                // $http.delete(`${CONFIG.apiUrl}/plans/${$scope.adjustment.plan_id}/adjust`, $scope.adjustment)
                // .then(res => {
                    // if (res.data.status == 1) {
                        // toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลการปรับแผนเรียบร้อย !!!");

                        // window.location.href = window.location.href;
                    // } else {
                    //     toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถบันทึกปรับแผนได้ !!!");
                    // }

                    // $scope.loading = false;
                // }, (err) => {
                    // toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถแก้ไขข้อมูลการปรับแผนได้ !!!");

                    // $scope.loading = false;
                // });
            }
        }
    };

    $scope.calcSumPriceOfAdjustment = async function(price, amount) {
        let sumPrice = parseFloat($scope.currencyToNumber(price)) * parseFloat($scope.currencyToNumber(amount));

        $scope.adjustment.sum_price = sumPrice;
        $('#sum_price').val(sumPrice);
    };

    $scope.inPlan = function(e, plan) {
        if (!plan) {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณากรอกข้อมูลให้ครบ !!!");
            return;
        }

        if (confirm(`คุณต้องการปรับรายการแผน${plan.item.category.name} เลขที่ ${plan.plan_no} เข้าในแผนใช่หรือไม่?`)) {
            $scope.loading = true;

            $http.put(`${CONFIG.apiUrl}/plans/${plan.id}/inplan`, {})
            .then(res => {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลการปรับแผนเรียบร้อย !!!");

                    window.location.href = window.location.href;
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถบันทึกปรับแผนได้ !!!");
                }

                $scope.loading = false;
            }, (err) => {
                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถแก้ไขข้อมูลการปรับแผนได้ !!!");

                $scope.loading = false;
            })
        }
    };

    /*
    |-----------------------------------------------------------------------------
    | Plan balance checking processes
    |-----------------------------------------------------------------------------
    */
    $scope.checkBalance = function(remain, sumPrice) {
        return !(remain < 0 || remain < sumPrice);
    };

    $scope.checkAllBalance = function(plans=[]) {
        let balance = 0;

        plans.forEach(detail => {
            const addon = detail.addon ? detail.addon.plan_item.sum_price : 0;
            const remain = detail.addon ? (detail.plan.plan_item.remain_budget + addon) : detail.plan.plan_item.remain_budget;

            balance = balance + (!$scope.checkBalance(remain, detail.sum_price) ? 1 : 0);
        });

        return balance;
    };

    /*
    |-----------------------------------------------------------------------------
    | Plan selection processes
    |-----------------------------------------------------------------------------
    */
    $scope.showAllPlansList = (type, status, depart) => {
        if (!depart) {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณาเลือกหน่วยงานก่อน !!!");
            return;
        }

        $scope.handleInputChange('cboDepart', depart);
        $scope.getAllPlans(type, status, true);
    };

    $scope.getAllPlans = (type, status, toggleModal=false) => {
        $scope.loading = true;
        $scope.plans = [];
        $scope.plans_pager = null;

        let name = $scope.txtKeyword == '' ? '' : $scope.txtKeyword;
        let depart = ($('#user').val() == '1300200009261' || $('#depart_id').val() == 4 || $('#duty_id').val() == 1) 
                        ? $scope.cboDepart
                        : $('#depart_id').val();

        $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&name=${name}&depart=${depart}&status=${status}&approved=A&addon=0`)
        .then(function(res) {
            if (toggleModal) $('#plans-list').modal('show');

            $scope.setAllPlans(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getAllPlansWithUrl = function(e, url, type, status, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.plans = [];
        $scope.plans_pager = null;

        let name = $scope.txtKeyword == '' ? '' : $scope.txtKeyword;
        let depart = ($('#user').val() == '1300200009261' || $('#depart_id').val() == 4 || $('#duty_id').val() == 1) 
                        ? $scope.cboDepart
                        : $('#depart_id').val();

        $http.get(`${url}&type=${type}&name=${name}&depart=${depart}&status=${status}&approved=A&addon=0`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setAllPlans = function(res) {
        const { data, ...pager } = res.data.plans;

        $scope.plans = data;
        $scope.plans_pager = pager;
    };

    $scope.getPlans = function(type, inStock, cb) {
        $scope.loading = true;
        $scope.plans = [];
        $scope.pager = null;

        let year        = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate        = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let faction     = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart      = !$scope.cboDepart ? '' : $scope.cboDepart;
        let division    = !$scope.cboDivision ? '' : $scope.cboDivision;
        let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let price       = $scope.cboPrice === '' ? '' : $scope.cboPrice;
        let budget      = $scope.cboBudget === '' ? '' : $scope.cboBudget;
        let name        = $scope.txtItemName === '' ? '' : $scope.txtItemName;
        let approved    = $scope.isApproved ? 'A' : '';
        let inPlan      = $scope.isInPlan === '' ? '' : $scope.isInPlan;
        let adjust      = $scope.isAdjust === '' ? '' : $scope.isAdjust;
        let in_stock    = inStock != undefined ? `&in_stock=${inStock}` : '';

        $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&year=${year}&cate=${cate}&faction=${faction}&depart=${depart}&division=${division}&budget=${budget}&status=${status}&name=${name}&price=${price}&approved=${approved}&in_plan=${inPlan}&adjust=${adjust}&show_all=1${in_stock}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPlansWithUrl = function(e, url, type, inStock, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.plans = [];
        $scope.pager = null;

        let year        = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate        = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let faction     = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart      = !$scope.cboDepart ? '' : $scope.cboDepart;
        let division    = !$scope.cboDivision ? '' : $scope.cboDivision;
        let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let price       = $scope.cboPrice === '' ? '' : $scope.cboPrice;
        let budget      = $scope.cboBudget === '' ? '' : $scope.cboBudget;
        let name        = $scope.txtItemName === '' ? '' : $scope.txtItemName;
        let approved    = $scope.isApproved ? 'A' : '';
        let inPlan      = $scope.isInPlan === '' ? '' : $scope.isInPlan;
        let adjust      = $scope.isAdjust === '' ? '' : $scope.isAdjust;
        let in_stock    = inStock != undefined ? `&in_stock=${inStock}` : '';

        $http.get(`${url}&type=${type}&year=${year}&cate=${cate}&faction=${faction}&depart=${depart}&division=${division}&budget=${budget}&status=${status}&name=${name}&price=${price}&approved=${approved}&in_plan=${inPlan}&adjust=${adjust}&show_all=1${in_stock}`)
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

        if (res.data.plansTotal) {
            $scope.plansTotal = $scope.calculatePlansTotal(res.data.plansTotal);
        }
    };

    $scope.setIsApproved = function(e, type, inStock, cb) {
        $scope.isApproved = e.target.checked;
        $scope.handleInputChange('isApproved', e.target.checked);

        $scope.getPlans(type, inStock, cb);
    };

    $scope.clearDateValue = function(e, propName, cb) {
        $scope[propName] = '';

        $(`#${propName}`)
            .datepicker(dtpOptions)
            .datepicker('update', '')

        cb(e)
    };

    $scope.iterateHashtag = function(str) {
        if (!str || str.search('#') == -1) return [];

        return str.split(' ');
    };
});
