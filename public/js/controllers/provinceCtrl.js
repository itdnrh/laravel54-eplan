app.controller('provinceCtrl', function(CONFIG, $rootScope, $scope, $http, toaster, StringFormatService) {
/** ################################################################################## */
    $scope.loading = false;

    $scope.cboYear = '';
    $scope.cboStatus = '';
    $scope.txtKeyword = '';

    $scope.provinces = [];
    $scope.pager = [];

    $scope.province = {
        year: '',
        order_no: '',
        order_date: '',
        type_id: '',
        detail: '',
        is_activated: false
    };

    /** ============================== Init Form elements ============================== */
    let dtpOptions = {
        autoclose: true,
        language: 'th',
        format: 'dd/mm/yyyy',
        thaiyear: true,
        todayBtn: true,
        todayHighlight: true
    };
    $('#order_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('show', function (e) {
            console.log(e);
        })
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $scope.clearProvince = () => {
        $scope.province = {
            year: '',
            order_no: '',
            order_date: '',
            type_id: '',
            detail: '',
            is_activated: false
        };
    };

    $scope.getProvinces = function() {
        $scope.loading = true;
        $scope.provinces = [];
        $scope.pager = null;

        let year = $scope.cboYear === '' ? '' : $scope.cboYear;
        let status = $scope.cboStatus === '' ? '1' : $scope.cboStatus;
        let order_no = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;

        $http.get(`${CONFIG.baseUrl}/provinces/search?year=${year}&status=${status}&order_no=${order_no}`)
        .then(function(res) {
            $scope.setProvinces(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.getProvincesWithUrl = function(e, url, cb) {
		/** Check whether parent of clicked a tag is .disabled just do nothing */
		if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.provinces = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let status = $scope.cboStatus === '' ? '1' : $scope.cboStatus;

        $http.get(`${url}&depart=${depart}$year=${year}`)
        .then(function(res) {
            $scope.setProvinces(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setProvinces = function(res) {
        const { data, ...pager } = res.data.provinces;

        $scope.provinces = data;
        $scope.pager = pager;
    };

    $scope.getById = function(id, cb) {
        $scope.loading = true;

        $http.get(`${CONFIG.apiUrl}/provinces/${id}`)
        .then(function(res) {
            cb(res.data.province);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setEditControls = function(province) {
        if (province) {
            $scope.province.id          = province.id;
            $scope.province.year        = province.year.toString();
            $scope.province.order_no    = province.order_no;
            $scope.province.order_date  = StringFormatService.convFromDbDate(province.order_date);
            $scope.province.type_id     = province.type_id;
            $scope.province.detail      = province.detail;
            $scope.province.is_activated = province.is_activated;
        }
    };

    $scope.store = function() {
        $scope.loading = true;
        $scope.province.user = $('#user').val();

        $http.post(`${CONFIG.baseUrl}/provinces/store`, $scope.province)
        .then(function(res) {
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ !!!");
            }
        }, function(err) {
            $scope.loading = false;

            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ !!!");
        });
    };

    $scope.update = function(event, form) {
        event.preventDefault();

        $scope.loading = true;
        $scope.province.user = $('#user').val();
    
        if(confirm(`คุณต้องแก้ไขรายการคำสั่งจังหวัด รหัส ${$scope.province.id} ใช่หรือไม่?`)) {
            $http.post(`${CONFIG.apiUrl}/provinces/update/${$scope.province.id}`, $scope.province)
            .then(function(res) {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลเรียบร้อย !!!");
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถแก้ไขข้อมูลได้ !!!");
                }
            }, function(err) {
                $scope.loading = false;

                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถแก้ไขข้อมูลได้ !!!");
            });
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();
        $scope.loading = true;

        if(confirm(`คุณต้องลบรายการคำสั่งจังหวัด รหัส ${id} ใช่หรือไม่?`)) {
            $http.delete(`${CONFIG.apiUrl}/provinces/${id}`)
            .then(res => {
                console.log(res);
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลบข้อมูลเรียบร้อย !!!");
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถลบข้อมูลได้ !!!");
                }
            }, err => {
                console.log(err);
                $scope.loading = false;

                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถลบข้อมูลได้ !!!");
            });
        }
    };
});