app.controller('supplierCtrl', function($scope, $http, toaster, CONFIG, ModalService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.cboAssetCate = '';
    $scope.searchKeyword = "";

    $scope.pager = [];
    $scope.types = [];
    $scope.type = {
        type_id: '',
        type_no: '',
        type_name: '',
        life_y: '',
        deprec_rate_y: '',
        cate_id: '',
        cate_no: '0000',
    };

    $scope.getData = function(event) {
        $scope.types = [];
        $scope.loading = true;
        
        let assetCate = $scope.cboAssetCate === '' ? 0 : $scope.cboAssetCate;
        let searchKey = $scope.searchKeyword === '' ? 0 : $scope.searchKeyword;
        
        $http.get(`${CONFIG.baseUrl}/asset-type/search/${assetCate}/${searchKey}`)
        .then(function(res) {
            console.log(res);
            $scope.types = res.data.types.data;
            $scope.pager = res.data.types;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.getDataWithURL = function(URL) {
        console.log(URL);
        $scope.types = [];
        $scope.loading = true;

    	$http.get(URL)
    	.then(function(res) {
    		console.log(res);
            $scope.types = res.data.types.data;
            $scope.pager = res.data.types;

            $scope.loading = false;
    	}, function(err) {
    		console.log(err);
            $scope.loading = false;
    	});
    }

    $scope.getAssetType = function(typeId) {
        $http.get(CONFIG.baseUrl + '/asset-type/get-ajax-byid/' +typeId)
        .then(function(res) {
            console.log(res);
            $scope.type = res.data.type;

            let [cateNo, typeNo] = $scope.type.type_no.split('-');
            $scope.type.cate_no = cateNo;
            $scope.type.type_no = typeNo;
        }, function(err) {
            console.log(err);
        });
    }

    $scope.getAssetNo = (cateId) => {
        $scope.loading = true;

        $http.get(CONFIG.baseUrl+ '/asset-type/get-ajax-no/' +cateId)
        .then(function(res) {
            console.log(res);
            let tmpNo = res.data.typeNo;
            let [cate, no] = tmpNo.split('-');
            let newNo = (parseInt(no)+1).toString().padStart(3, "0");
            
            $scope.type.cate_no = `${cate}`;
            $scope.type.type_no = `${newNo}`;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

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