app.controller('approvalCtrl', function($scope, $http, toaster, CONFIG, ModalService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.plans = [];
    $scope.pager = null;
    $scope.cboYear = "";
    $scope.cboPlanType = "";
    $scope.cboCategory = "";
    $scope.cboFaction = "";
    $scope.cboDepart = "";
    $scope.cboStatus = "";
    $scope.txtKeyword = "";

    $scope.toApprovesList = [];
    $scope.onSelectedCheckBox = (e, plan) => {
        let newList = [];
        if (e.target.checked) {
            newList = [...$scope.toApprovesList, plan.id];
        } else {
            newList = $scope.toApprovesList.filter(app => app !== plan.id);
        }

        $scope.toApprovesList = [...new Set(newList)];
    };

    $scope.approveAll = () => {
        if (confirm('คุณต้องการอนุมัติทุกรายการที่หน่วยงานร้องขอใช่หรือไม่?')) {
            // $http.post(`${CONFIG.baseUrl}/approvals/2565/year`, $scope.toApprovesList)
            // .then(function(res) {
            //     console.log(res);

            //     if (res.data.status == 1) {
            //         toaster.pop('success', "ผลการทำงาน", "บันทึกตรวจรับเรียบร้อย !!!");
            //     } else {
            //         toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกตรวจรับได้ !!!");
            //     }

            //     $scope.loading = false;
            // }, function(err) {
            //     console.log(err);
            //     $scope.loading = false;
            // });
        }
    };

    $scope.approve = (e, plan) => {
        e.preventDefault();

        if (confirm(`คุณต้องการอนุมัติรายการรหัส ${plan.plan_no} ใช่หรือไม่?`)) {
            // $http.post(`${CONFIG.baseUrl}/approvals`, { id: plan.id })
            // .then(function(res) {
            //     console.log(res);

            //     if (res.data.status == 1) {
            //         toaster.pop('success', "ผลการทำงาน", "บันทึกตรวจรับเรียบร้อย !!!");
            //     } else {
            //         toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกตรวจรับได้ !!!");
            //     }

            //     $scope.loading = false;
            // }, function(err) {
            //     console.log(err);
            //     $scope.loading = false;
            // });
        }
    };

    $scope.approveByList = () => {
        if ($scope.toApprovesList.length == 0) {
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกการอนุมัติได้ กรุณาเลือกรายการก่อน!!!");
        } else {
            console.log($scope.toApprovesList);
            // $http.post(`${CONFIG.baseUrl}/approvals/lists`, { plans: $scope.toApprovesList })
            // .then(function(res) {
            //     console.log(res);

            //     if (res.data.status == 1) {
            //         toaster.pop('success', "ผลการทำงาน", "บันทึกตรวจรับเรียบร้อย !!!");
            //     } else {
            //         toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกตรวจรับได้ !!!");
            //     }

            //     $scope.loading = false;
            // }, function(err) {
            //     console.log(err);
            //     $scope.loading = false;
            // });
        }
    };

    $scope.getAll = function(type) {
        $scope.loading = true;
        $scope.plans = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;

        $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&year=${year}&cate=${cate}&status=${status}&depart=${depart}`)
        .then(function(res) {
            console.log(res);
            $scope.setPlans(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setPlans = function(res) {
        const { data, ...pager } = res.data.plans;

        $scope.plans = data;
        $scope.pager = pager;
    };

    // TODO: Duplicated method
    $scope.getDataWithURL = function(e, URL, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;

        $http.get(URL)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.onApproveLoad = function(e) {
        $scope.cboYear = '2565';
        $scope.cboLeaveStatus = $scope.showAllApproves ? '2&3&4&8&9' : '2';
        $scope.cboQuery = `month=${moment().format('YYYY-MM')}`;
        $scope.cboMenu = "1";

        $scope.getAll();
        $scope.getCancellation(true);
    };

    $scope.showApproveForm = function(leave, type) {
        $scope.leave = leave;

        if (type === 1) {
            $('#approve-form').modal('show');
        } else {
            $('#cancel-approval-form').modal('show');
        }
    };
});