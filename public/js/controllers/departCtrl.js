app.controller('departCtrl', function($scope, $http, toaster, CONFIG, ModalService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.cboFaction = '';
    $scope.txtKeyword = '';

    $scope.departs = [];
    $scope.pager = null;

    $scope.depart = {
        depart_id: '',
        depart_name: '',
        faction_id: '',
        memo_no: '',
        tel_no: '',
        is_actived: false
    };

    $scope.setFaction = function(faction) {
        $scope.cboFaction = faction ? faction.toString() : '';
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

    $scope.getById = function(id, cb) {
        $scope.loading = true;

        $http.get(`${CONFIG.apiUrl}/departs/${id}`)
        .then(function(res) {
            cb(res.data.depart);

            $scope.loading = false;
        }, function(err) {
            console.log(err);

            $scope.loading = false;
        });
    };

    $scope.setEditControls = function(depart) {
        if (depart) {
            console.log(depart);
            $scope.depart.depart_id     = depart.depart_id;
            $scope.depart.depart_name   = depart.depart_name;
            $scope.depart.faction_id    = depart.faction_id.toString();
            $scope.depart.memo_no       = depart.memo_no;
            $scope.depart.tel_no        = depart.tel_no;
            $scope.depart.is_actived    = depart.is_actived == 1 ? true : false;

            /** Set date value to datepicker input of doc_date */
            $('#faction_id').val(depart.faction_id).trigger('change.select2');
        }
    };

    $scope.store = function(event, form) {
        event.preventDefault();
        $scope.loading = true;

        $http.post(`${CONFIG.baseUrl}/departs/store`, $scope.depart)
        .then(function(res) {
            console.log(res);

            if (res.data.status == 1) {
                toaster.pop('success', "", 'บันทึกข้อมูลเรียบร้อยแล้ว !!!');

                window.location.href = `${CONFIG.baseUrl}/departs/list`;
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

        if(confirm("คุณต้องแก้ไขรายการกลุ่มงาน รหัส " + $scope.depart.depart_id + " ใช่หรือไม่?")) {
            $scope.loading = true;

            $http.put(`${CONFIG.apiUrl}/departs/${$scope.depart.depart_id}`, $scope.depart)
            .then(function(res) {
                console.log(res);

                if (res.data.status == 1) {
                    toaster.pop('success', "", 'แก้ไขข้อมูลเรียบร้อยแล้ว !!!');

                setTimeout(function (){
                    window.location.href = `${CONFIG.baseUrl}/departs/list`;
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
        }
    };

    $scope.delete = function(id) {
        if(confirm("คุณต้องลบรายการกลุ่มงาน รหัส " + id + " ใช่หรือไม่?")) {
            $scope.loading = true;

            $http.post(`${CONFIG.baseUrl}/departs/delete/${id}`)
            .then(function(res) {
                console.log(res);

                if (res.data.status == 1) {
                    toaster.pop('success', "", 'ลบข้อมูลเรียบร้อยแล้ว !!!');

                    window.location.href = `${CONFIG.baseUrl}/departs/list`;
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