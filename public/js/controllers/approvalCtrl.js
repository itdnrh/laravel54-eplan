app.controller('approvalCtrl', function($scope, $http, toaster, CONFIG, ModalService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.plans = [];
    $scope.pager = null;
    $scope.cboYear = (moment().year() + 543).toString();
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

        if (confirm(`คุณต้องการอนุมัติรายการรหัส ${plan.id} ใช่หรือไม่?`)) {
            $http.post(`${CONFIG.baseUrl}/approvals`, { id: plan.id })
            .then((res) => {
                console.log(res);

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "อนุมัติรายการเรียบร้อย !!!");

                    $scope.plans.forEach(plan => {
                        if (plan.id === res.data.plan.id) {
                            plan.plan_no = res.data.plan.plan_no;
                            plan.approved = res.data.plan.approved;
                        }
                    });
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถอนุมัติรายการได้ !!!");
                }

                $scope.loading = false;
            }, (err) => {
                console.log(err);
                $scope.loading = false;
            });
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

    $scope.getAll = function(type, inStock) {
        $scope.loading = true;
        $scope.plans = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let faction  = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let in_stock = inStock != undefined ? `&in_stock=${inStock}` : '';

        $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&year=${year}&cate=${cate}&status=${status}&faction=${faction}&depart=${depart}${in_stock}`)
        .then(function(res) {
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

    $scope.getDataWithUrl = function(e, url, params, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.plans = [];
        $scope.pager = null;
        $scope.loading = true;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let faction  = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let in_stock = params.inStock != undefined ? `&in_stock=${params.inStock}` : '';

        $http.get(`${url}&type=${params.type}&year=${year}&cate=${cate}&status=${status}&faction=${faction}&depart=${depart}${in_stock}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };
});