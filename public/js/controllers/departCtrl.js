app.controller('departCtrl', function($scope, $http, toaster, CONFIG, ModalService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.cboFaction = '';
    $scope.txtKeyword = '';

    $scope.departs = [];
    $scope.pager = null;

    $scope.faction = {
        prename_id: '',
        supplier_name: '',
    };

    $scope.setFaction = function(faction) {
        $scope.cboFaction = faction.toString();
    };

    $scope.getDeparts = function(event) {
        $scope.departs = [];
        $scope.pager = null;
        $scope.loading = true;

        let faction = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let name = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;
        
        $http.get(`${CONFIG.apiUrl}/departs?faction=${faction}&name=${name}`)
        .then(function(res) {
            $scope.setDeparts(res);
            console.log(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.getDepartsWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.departs = [];
        $scope.pager = null;
        $scope.loading = true;

        let faction = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let name = $scope.txtKeyword === '' ? 0 : $scope.txtKeyword;

        $http.get(`${url}&faction=${faction}&name=${name}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.setDeparts = function(res) {
        const { data, ...pager } = res.data.departs;

        $scope.departs = data;
        $scope.pager = pager;
    };

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/factions/edit/${id}`;
    };

    $scope.getById = function(id, cb) {
        $scope.loading = true;

        $http.get(`${CONFIG.apiUrl}/factions/${id}`)
        .then(function(res) {
            cb(res.data.supplier);

            $scope.loading = false;
        }, function(err) {
            console.log(err);

            $scope.loading = false;
        });
    };

    $scope.setEditControls = function(supplier) {
        if (supplier) {
            console.log(supplier);
            $scope.supplier.id                  = supplier.supplier_id;
            $scope.supplier.prename_id          = supplier.prename_id ? supplier.prename_id.toString() : '';
            $scope.supplier.supplier_name       = supplier.supplier_name;
            $scope.supplier.supplier_address1   = supplier.supplier_address1;
            $scope.supplier.supplier_address2   = supplier.supplier_address2;
            $scope.supplier.supplier_address3   = supplier.supplier_address3;
            $scope.supplier.chw_id              = supplier.chw_id ? supplier.chw_id.toString() : '';
            $scope.supplier.supplier_zipcode    = supplier.supplier_zipcode;
            $scope.supplier.supplier_phone      = supplier.supplier_phone;
            $scope.supplier.supplier_fax        = supplier.supplier_fax;
            $scope.supplier.supplier_email      = supplier.supplier_email;
            $scope.supplier.supplier_agent_name = supplier.supplier_agent_name;
            $scope.supplier.supplier_agent_contact = supplier.supplier_agent_contact;
            $scope.supplier.supplier_agent_email = supplier.supplier_agent_email;
            $scope.supplier.supplier_bank_acc   = supplier.supplier_bank_acc;
            $scope.supplier.supplier_credit     = supplier.supplier_credit;
            $scope.supplier.supplier_taxid      = supplier.supplier_taxid;
            $scope.supplier.supplier_taxrate    = supplier.supplier_taxrate;
            $scope.supplier.supplier_note       = supplier.supplier_note;

            /** Set date value to datepicker input of doc_date */
            $('#prename_id').val(supplier.prename_id).trigger('change.select2');
            $('#chw_id').val(supplier.chw_id).trigger('change.select2');
        }
    };

    $scope.store = function(event, form) {
        event.preventDefault();
        $scope.loading = true;

        $http.post(`${CONFIG.baseUrl}/factions/store`, $scope.supplier)
        .then(function(res) {
            console.log(res);

            if (res.data.status == 1) {
                toaster.pop('success', "", 'บันทึกข้อมูลเรียบร้อยแล้ว !!!');

                window.location.href = `${CONFIG.baseUrl}/system/factions`;
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

        if(confirm("คุณต้องแก้ไขรายการหนี้เลขที่ " + $scope.supplier.id + " ใช่หรือไม่?")) {
            $scope.supplier.user = $('#user').val();

            $http.post(`${CONFIG.baseUrl}/factions/update/${$scope.supplier.id}`, $scope.supplier)
            .then(function(res) {
                console.log(res);

                if (res.data.status == 1) {
                    toaster.pop('success', "", 'แก้ไขข้อมูลเรียบร้อยแล้ว !!!');

                setTimeout(function (){
                    window.location.href = `${CONFIG.baseUrl}/system/factions`;
                }, 2000); 
                } else {
                    toaster.pop('error', "", 'พบข้อผิดพลาด !!!');
                }

                $scope.loading = false;
            }, function(err) {
                console.log(err);
                toaster.pop('error', "", 'พบข้อผิดพลาด !!!');

                $scope.loading = false;
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.delete = function(id) {
        $scope.loading = true;

        if(confirm("คุณต้องลบรายการหนี้เลขที่ " + id + " ใช่หรือไม่?")) {
            $http.post(`${CONFIG.baseUrl}/factions/delete/${id}`)
            .then(function(res) {
                console.log(res);

                if (res.data.status == 1) {
                    toaster.pop('success', "", 'ลบข้อมูลเรียบร้อยแล้ว !!!');

                    window.location.href = `${CONFIG.baseUrl}/system/factions`;
                } else {
                    toaster.pop('error', "", 'พบข้อผิดพลาด !!!');
                }

                $scope.loading = false;
            }, function(err) {
                console.log(err);
                toaster.pop('error', "", 'พบข้อผิดพลาด !!!');

                $scope.loading = false;
            });
        } else {
            $scope.loading = false;
        }
    };
});