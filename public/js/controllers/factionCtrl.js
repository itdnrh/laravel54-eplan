app.controller('factionCtrl', function($scope, $http, toaster, CONFIG, ModalService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.cboChangwat = '';
    $scope.txtKeyword = "";

    $scope.factions = [];
    $scope.pager = null;

    $scope.faction = {
        faction_id: '',
        faction_name: '',
        is_actived: false,
    };

    $scope.getFactions = function(event) {
        $scope.loading = true;

        $scope.factions = [];
        $scope.pager = null;

        let name = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;

        $http.get(`${CONFIG.apiUrl}/factions?name=${name}`)
        .then(function(res) {
            $scope.setFactions(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.getFactionsWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;

        $scope.factions = [];
        $scope.pager = null;

        let name = $scope.txtKeyword === '' ? 0 : $scope.txtKeyword;

        $http.get(`${url}&name=${name}&changwat=${changwat}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.setFactions = function(res) {
        const { data, ...pager } = res.data.factions;

        $scope.factions = data;
        $scope.pager = pager;
    };

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/factions/edit/${id}`;
    };

    $scope.getById = function(id, cb) {
        $scope.loading = true;

        $http.get(`${CONFIG.apiUrl}/factions/${id}`)
        .then(function(res) {
            cb(res.data.faction);

            $scope.loading = false;
        }, function(err) {
            console.log(err);

            $scope.loading = false;
        });
    };

    $scope.setEditControls = function(faction) {
        if (faction) {
            $scope.faction.faction_id   = faction.faction_id;
            $scope.faction.faction_name = faction.faction_name;
            $scope.faction.is_actived   = faction.is_actived;
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
        
        if(confirm("คุณต้องแก้ไขกลุ่มภารกิจ รหัส " + $scope.faction.faction_id + " ใช่หรือไม่?")) {
            $scope.loading = true;
            $scope.faction.user = $('#user').val();

            $http.put(`${CONFIG.apiUrl}/factions/${$scope.faction.faction_id}`, $scope.faction)
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
        }
    };

    $scope.delete = function(id) {
        
        if(confirm("คุณต้องลบรายการหนี้เลขที่ " + id + " ใช่หรือไม่?")) {
            $scope.loading = true;

            $http.delete(`${CONFIG.baseUrl}/factions/${id}`)
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
        }
    };

    $scope.active = function(event, id, isActive) {
        event.preventDefault();
        console.log(id, isActive);

        if(confirm(`คุณต้องแก้ไขสถานะหน่วยงาน รหัส ${id} ใช่หรือไม่?`)) {
            $scope.loading = true;

            const data = { user: $('#user').val(), is_actived: isActive };

            $http.put(`${CONFIG.apiUrl}/factions/${id}/active`, data)
            .then(function(res) {
                if (res.data.status == 1) {
                    toaster.pop('success', "", 'แก้ไขสถานะหน่วยงานเรียบร้อยแล้ว !!!');

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
        }
    };
});