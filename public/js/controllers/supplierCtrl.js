app.controller('supplierCtrl', function($scope, $http, toaster, CONFIG, ModalService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.txtKeyword = "";

    $scope.suppliers = [];
    $scope.pager = null;
    $scope.supplier = {
        type_id: '',
        type_no: '',
        type_name: '',
        life_y: '',
        deprec_rate_y: '',
        cate_id: '',
        cate_no: '0000',
    };

    $scope.getAll = function(event) {
        $scope.suppliers = [];
        $scope.pager = null;
        $scope.loading = true;

        let name = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;
        
        $http.get(`${CONFIG.apiUrl}/suppliers?name=${name}`)
        .then(function(res) {
            const { data, ...pager } = res.data.suppliers;

            $scope.suppliers = data;
            $scope.pager = pager;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.getSuppliersWithUrl = function(e, url) {
        $scope.suppliers = [];
        $scope.pager = null;
        $scope.loading = true;

        let name = $scope.txtKeyword === '' ? 0 : $scope.txtKeyword;

        $http.get(url+ `&name=${name}`)
        .then(function(res) {
            const { data, ...pager } = res.data.suppliers;

            $scope.suppliers = data;
            $scope.pager = pager;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.getById = function(id) {
        $scope.loading = true;

        $http.get(`${CONFIG.baseUrl}/suppliers/${id}`)
        .then(function(res) {
            console.log(res);
            $scope.supplier = res.data.supplier;

            $scope.loading = false;
        }, function(err) {
            console.log(err);

            $scope.loading = false;
        });
    }

    $scope.edit = function(typeId) {
        console.log(typeId);

        window.location.href = CONFIG.baseUrl + '/asset-type/edit/' + typeId;
    };


    $scope.add = function(event, form) {
        event.preventDefault();

        $http.post(CONFIG.baseUrl + '/asset-type/store', $scope.type)
        .then(function(res) {
            console.log(res);
            toaster.pop('success', "", 'บันทึกข้อมูลเรียบร้อยแล้ว !!!');
        }, function(err) {
            console.log(err);
            toaster.pop('error', "", 'พบข้อผิดพลาด !!!');
        });

        document.getElementById(form).reset();
    }

    $scope.update = function(event, form) {
        event.preventDefault();

        if(confirm("คุณต้องแก้ไขรายการหนี้เลขที่ " + $scope.type.type_id + " ใช่หรือไม่?")) {
            $scope.type.cate_id = $('#cate_id option:selected').val();

            $http.put(CONFIG.baseUrl + '/asset-type/update', $scope.type)
            .then(function(res) {
                console.log(res);
                toaster.pop('success', "", 'แก้ไขข้อมูลเรียบร้อยแล้ว !!!');
            }, function(err) {
                console.log(err);
                toaster.pop('error', "", 'พบข้อผิดพลาด !!!');
            });
        }

        setTimeout(function (){
            window.location.href = CONFIG.baseUrl + '/asset-type/list';
        }, 2000);        
    };

    $scope.delete = function(typeId) {
        console.log(typeId);

        if(confirm("คุณต้องลบรายการหนี้เลขที่ " + typeId + " ใช่หรือไม่?")) {
            $http.delete(CONFIG.baseUrl + '/asset-type/delete/' +typeId)
            .then(function(res) {
                console.log(res);
                toaster.pop('success', "", 'ลบข้อมูลเรียบร้อยแล้ว !!!');
                $scope.getData();
            }, function(err) {
                console.log(err);
                toaster.pop('error', "", 'พบข้อผิดพลาด !!!');
            });
        }
    };
});