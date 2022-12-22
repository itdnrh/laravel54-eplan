app.controller('divisionCtrl', function($scope, $http, toaster, CONFIG, ModalService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.cboFaction = '';
    $scope.cboDepart = '';
    $scope.txtKeyword = "";

    $scope.divisions = [];
    $scope.pager = null;

    $scope.division = {
        ward_id: '',
        ward_name: '',
        faction_id: '',
        depart_id: '',
        memo_no: '',
        tel_no: '',
        is_actived: false
    };

    $scope.setDepart = function(faction, depart) {
        $scope.onFactionSelected(faction);

        $scope.cboFaction = faction ? faction.toString() : '';
        $scope.cboDepart = depart ? depart.toString() : '';
    };

    $scope.getDivisions = function(event) {
        $scope.divisions = [];
        $scope.pager = null;
        $scope.loading = true;

        let faction = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart = !$scope.cboDepart ? '' : $scope.cboDepart;
        let name = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;
        
        $http.get(`${CONFIG.apiUrl}/divisions?faction=${faction}&depart=${depart}&name=${name}`)
        .then(function(res) {
            $scope.setDivisions(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.getDivisionsWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.divisions = [];
        $scope.pager = null;
        $scope.loading = true;

        let faction = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart = !$scope.cboDepart ? '' : $scope.cboDepart;
        let name = $scope.txtKeyword === '' ? 0 : $scope.txtKeyword;

        $http.get(`${url}&faction=${faction}&depart=${depart}&name=${name}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.setDivisions = function(res) {
        const { data, ...pager } = res.data.divisions;

        $scope.divisions = data;
        $scope.pager = pager;
    };

    $scope.getById = function(id, cb) {
        $scope.loading = true;

        $http.get(`${CONFIG.apiUrl}/divisions/${id}`)
        .then(function(res) {
            cb(res.data.division);

            $scope.loading = false;
        }, function(err) {
            console.log(err);

            $scope.loading = false;
        });
    };

    $scope.setEditControls = function(division) {
        if (division) {
            $scope.onFactionSelected(division.faction_id);

            $scope.division.ward_id     = division.ward_id;
            $scope.division.ward_name   = division.ward_name;
            $scope.division.faction_id  = division.faction_id.toString();
            $scope.division.depart_id   = division.depart_id.toString();
            $scope.division.memo_no     = division.memo_no;
            $scope.division.tel_no      = division.tel_no;
            $scope.division.is_actived  = division.is_actived == '1' ? true : false;
        }
    };

    $scope.store = function(event, form) {
        event.preventDefault();
        $scope.loading = true;

        $http.post(`${CONFIG.baseUrl}/divisions/store`, $scope.division)
        .then(function(res) {
            if (res.data.status == 1) {
                toaster.pop('success', "", 'บันทึกข้อมูลเรียบร้อยแล้ว !!!');

                window.location.href = `${CONFIG.baseUrl}/divisions/list?faction=&depart=`;
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

        if(confirm("คุณต้องแก้ไขรายการหน่วยงาน รหัส " + $scope.division.ward_id + " ใช่หรือไม่?")) {
            $scope.loading = true;
            $scope.division.user = $('#user').val();

            $http.put(`${CONFIG.apiUrl}/divisions/${$scope.division.ward_id}`, $scope.division)
            .then(function(res) {
                if (res.data.status == 1) {
                    toaster.pop('success', "", 'แก้ไขข้อมูลเรียบร้อยแล้ว !!!');

                setTimeout(function (){
                    window.location.href = `${CONFIG.baseUrl}/divisions/list?faction=&depart=`;
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
        if(confirm("คุณต้องลบรายการหน่วยงาน รหัส " + id + " ใช่หรือไม่?")) {
            $scope.loading = true;

            $http.delete(`${CONFIG.apiUrl}/divisions/${id}`)
            .then(function(res) {
                if (res.data.status == 1) {
                    toaster.pop('success', "", 'ลบข้อมูลเรียบร้อยแล้ว !!!');

                    window.location.href = `${CONFIG.baseUrl}/divisions/list?faction=&depart=`;
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