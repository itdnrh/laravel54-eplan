app.controller('supplierCtrl', function($scope, $http, toaster, CONFIG, ModalService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.cboChangwat = '';
    $scope.txtKeyword = "";

    $scope.suppliers = [];
    $scope.pager = null;
    $scope.supplier = {
        prename_id: '',
        supplier_name: '',
        supplier_address1: '',
        supplier_address2: '',
        supplier_address3: '',
        chw_id: '',
        supplier_zipcode: '',
        supplier_phone: '',
        supplier_fax: '',
        supplier_email: '',
        supplier_agent_name: '',
        supplier_agent_contact: '',
        supplier_agent_email: '',
        supplier_payto: '',
        supplier_bank_acc: '',
        supplier_credit: '',
        supplier_taxid: '',
        supplier_taxrate: '',
        supplier_note: ''
    };

    $scope.onSelectedChangwat = function(e) {
        $scope.supplier.supplier_address3 = $('#chw_id option:selected').text().replaceAll(/\s/g,'');
    };

    $scope.getSuppliers = function(event) {
        $scope.loading = true;

        $scope.suppliers = [];
        $scope.pager = null;

        let name = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;
        let changwat = $scope.cboChangwat === '' ? '' : $scope.cboChangwat;
        
        $http.get(`${CONFIG.apiUrl}/suppliers?name=${name}&changwat=${changwat}`)
        .then(function(res) {
            $scope.setSuppliers(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.getSuppliersWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;

        $scope.suppliers = [];
        $scope.pager = null;

        let name = $scope.txtKeyword === '' ? 0 : $scope.txtKeyword;
        let changwat = $scope.cboChangwat === '' ? '' : $scope.cboChangwat;

        $http.get(`${url}&name=${name}&changwat=${changwat}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.setSuppliers = function(res) {
        const { data, ...pager } = res.data.suppliers;

        $scope.suppliers = data;
        $scope.pager = pager;
    };

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


    $scope.store = function(event, form) {
        event.preventDefault();
        $scope.loading = true;

        $http.post(CONFIG.baseUrl + '/suppliers/store', $scope.supplier)
        .then(function(res) {
            console.log(res);

            if (res.data.status == 1) {
                toaster.pop('success', "", 'บันทึกข้อมูลเรียบร้อยแล้ว !!!');

                window.location.href = `${CONFIG.baseUrl}/system/suppliers`;
            } else {
                toaster.pop('error', "", 'พบข้อผิดพลาด !!!');
            }

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            toaster.pop('error', "", 'พบข้อผิดพลาด !!!');

            $scope.loading = false;
        });
    }

    $scope.update = function(event, form) {
        event.preventDefault();
        $scope.loading = true;

        if(confirm("คุณต้องแก้ไขรายการหนี้เลขที่ " + $scope.type.type_id + " ใช่หรือไม่?")) {
            $scope.type.cate_id = $('#cate_id option:selected').val();

            $http.put(CONFIG.baseUrl + '/suppliers/update', $scope.type)
            .then(function(res) {
                console.log(res);

                if (res.data.status == 1) {
                    toaster.pop('success', "", 'แก้ไขข้อมูลเรียบร้อยแล้ว !!!');

                    window.location.href = `${CONFIG.baseUrl}/system/suppliers`;
                } else {
                    toaster.pop('error', "", 'พบข้อผิดพลาด !!!');
                }

                $scope.loading = false;
            }, function(err) {
                console.log(err);
                toaster.pop('error', "", 'พบข้อผิดพลาด !!!');

                $scope.loading = false;
            });
        }

        setTimeout(function (){
            window.location.href = CONFIG.baseUrl + '/suppliers/list';
        }, 2000);        
    };

    $scope.delete = function(id) {
        console.log(id);
        $scope.loading = true;

        if(confirm("คุณต้องลบรายการหนี้เลขที่ " + id + " ใช่หรือไม่?")) {
            $http.delete(CONFIG.baseUrl + '/suppliers/delete/' +id)
            .then(function(res) {
                console.log(res);

                if (res.data.status == 1) {
                    toaster.pop('success', "", 'ลบข้อมูลเรียบร้อยแล้ว !!!');

                    window.location.href = `${CONFIG.baseUrl}/system/suppliers`;
                } else {
                    toaster.pop('error', "", 'พบข้อผิดพลาด !!!');
                }

                $scope.loading = false;
            }, function(err) {
                console.log(err);
                toaster.pop('error', "", 'พบข้อผิดพลาด !!!');

                $scope.loading = false;
            });
        }
    };
});