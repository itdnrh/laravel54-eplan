app.controller('receivingCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService) {
    /*
    |-----------------------------------------------------------------------------
    | Local variables and constraints initialization
    |-----------------------------------------------------------------------------
    */
    $scope.loading = false;
    $scope.receiveds = [];
    $scope.receiveds_pager = null;
    $scope.supports = [];
    $scope.supports_pager = null;

    $scope.cboYear = '2566'; //(moment().year() + 543).toString();
    $scope.cboInPlan = '';
    $scope.txtSupportNo = '';
    $scope.txtReceivedNo = '';

    $scope.receive = {
        support_id: '',
        received_no: '',
        received_date: '',
        officer: '',
        remark: ''
    };

    $scope.returnData = {
        reason: '',
        support_id: '',
        user: ''
    };

    /*
    |-----------------------------------------------------------------------------
    | Receiving processes
    |-----------------------------------------------------------------------------
    */
    $scope.getReceiveds = (status) => {
        $scope.loading = true;
        $scope.receiveds = [];
        $scope.receiveds_pager = null;

        let year = $scope.cboYear == '' ? '' : $scope.cboYear;
        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        let depart = !$scope.cboDepart ? '' : $scope.cboDepart;
        let doc_no = $scope.txtSupportNo == '' ? '' : $scope.txtSupportNo;
        let received_no = $scope.txtReceivedNo == '' ? '' : $scope.txtReceivedNo;

        $http.get(`${CONFIG.baseUrl}/supports/search?year=${year}&type=${type}&depart=${depart}&doc_no=${doc_no}&received_no=${received_no}&status=${status}`)
        .then(function(res) {
            $scope.setReceiveds(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getReceivedsWithUrl = function(e, url, status, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.receiveds = [];
        $scope.receiveds_pager = null;

        let year = $scope.cboYear == '' ? '' : $scope.cboYear;
        let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        let depart = !$scope.cboDepart ? '' : $scope.cboDepart;
        let doc_no = $scope.txtSupportNo == '' ? '' : $scope.txtSupportNo;
        let received_no = $scope.txtReceivedNo == '' ? '' : $scope.txtReceivedNo;

        $http.get(`${url}&year=${year}&type=${type}&depart=${depart}&doc_no=${doc_no}&received_no=${received_no}&status=${status}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setReceiveds = function(res) {
        const { data, ...pager } = res.data.supports;

        $scope.receiveds = data;
        $scope.receiveds_pager = pager;
    };

    $scope.clearReceive = function() {
        $scope.receive = {
            support_id: '',
            received_no: '',
            received_date: '',
            officer: '',
            remark: ''
        };
    };

    $scope.showReceiveSupportForm = function(e, support) {
        $scope.receive.support_id = support.id;

        $('#received_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            console.log(event.date);
        });

        $('#receive-form').modal('show');
    };

    $scope.onReceivePlan = function(e, plan) {
        $http.post(`${CONFIG.baseUrl}/orders/received/1`, { id: plan.id })
        .then(function(res) {
            console.log(res);
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "ลงรับเอกสารเรียบร้อย !!!");

                $scope.getReceiveds(2);
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลงรับเอกสารได้ !!!");
            }
        }, function(err) {
            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลงรับเอกสารได้ !!!");
        });
    };

    $scope.onReceiveSupport = function(e, form, support) {
        e.preventDefault();

        if (form.$invalid) {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณากรอกข้อมูลให้ครบ !!!");
            return;
        }

        if (support) {
            $http.post(`${CONFIG.baseUrl}/orders/received/2`, $scope.receive)
            .then(function(res) {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลงรับเอกสารเรียบร้อย !!!");

                    /** Remove support data that has been received */
                    $scope.supports = $scope.supports.filter(el => el.id !== res.data.support.id);

                    $scope.getReceiveds(2);
                    $scope.clearReceive();
                    form.$submitted = false;

                    $('#receive-form').modal('hide');
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลงรับเอกสารได้ !!!");
                }
            }, function(err) {
                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลงรับเอกสารได้ !!!");
            });
        }
    };

    $scope.cancel = function(e, id) {
        $scope.loading = true;

        if(confirm(`คุณต้องการยกเลิกรับเอกสารขอสนับสนุน รหัส ${id} ใช่หรือไม่?`)) {
            $http.put(`${CONFIG.apiUrl}/supports/${id}/cancel-received`, { status: 1 })
            .then(function(res) {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ยกเลิกรับบันทึกขอสนับสนุนเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/orders/received`;
                } else {
                    toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถยกเลิกรับบันทึกขอสนับสนุนได้ !!!");
                }

                $scope.loading = false;
            }, function(err) {
                $scope.loading = false;

                console.log(err);
                toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถยกเลิกรับบันทึกขอสนับสนุนได้ !!!");
            });
        }
    };

    /*
    |-----------------------------------------------------------------------------
    | Fetching supports data process
    |-----------------------------------------------------------------------------
    */
    $scope.getSupports = function(res) {
        $scope.loading = true;
        $scope.supports = [];
        $scope.supports_pager = null;

        let year    = $scope.cboYear == '' ? '' : $scope.cboYear;
        let type    = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        // let cate = !$scope.cboCategory ? '' : $scope.cboCategory;
        let depart  = !$scope.cboDepart ? '' : $scope.cboDepart;
        let doc_no  = $scope.txtSupportNo == '' ? '' : $scope.txtSupportNo;
        let in_plan = $scope.cboInPlan === '' ? '' : $scope.cboInPlan;

        $http.get(`${CONFIG.baseUrl}/supports/search?year=${year}&type=${type}&depart=${depart}&doc_no=${doc_no}&in_plan=${in_plan}&status=1`)
        .then(function(res) {
            $scope.loading = false;

            $scope.setSupports(res);

            $('#supports-receive').modal('show');
        }, function(err) {
            $scope.loading = false;
            console.log(err);
        });
    };

    $scope.getSupportsWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.supports = [];
        $scope.supports_pager = null;

        let year    = $scope.cboYear == '' ? '' : $scope.cboYear;
        let type    = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
        // let cate = !$scope.cboCategory ? '' : $scope.cboCategory;
        let depart  = !$scope.cboDepart ? '' : $scope.cboDepart;
        let doc_no  = $scope.txtSupportNo == '' ? '' : $scope.txtSupportNo;
        let in_plan = $scope.cboInPlan === '' ? '' : $scope.cboInPlan;

        $http.get(`${url}&year=${year}&type=${type}&depart=${depart}&doc_no=${doc_no}&in_plan=${in_plan}&status=1`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setSupports = function(res) {
        const { data, ...pager } = res.data.supports;

        $scope.supports = data;
        $scope.supports_pager = pager;
    };

    /*
    |-----------------------------------------------------------------------------
    | Returned process
    |-----------------------------------------------------------------------------
    */
    $scope.showReturnSupportForm = function(e, support) {
        console.log(support);
        if (support) {
            $scope.returnData.support_id = support.id;
    
            $('#return-form').modal('show');
        }
    };

    $scope.onReturnSupport = function(e, form, id) {
        e.preventDefault();

        if (form.$invalid) {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณากรอกข้อมูลให้ครบ !!!");
            return;
        }

        /** Add user's id from laravel auth user */
        $scope.returnData.user = $('#user').val();

        $http.put(`${CONFIG.apiUrl}/supports/${id}/return`, $scope.returnData)
            .then(function(res) {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ตีกลับเอกสารเรียบร้อย !!!");

                    $scope.getSupports();
                    $scope.getReceiveds(2);

                    $('#return-form').modal('hide');
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถตีกลับเอกสารได้ !!!");
                }
            }, function(err) {
                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถตีกลับเอกสารได้ !!!");
            });
    };
});