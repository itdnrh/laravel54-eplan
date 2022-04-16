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

    $scope.showNewItemForm = function() {
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

});
