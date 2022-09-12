app.controller('approvalCtrl', function($scope, $http, toaster, CONFIG, ModalService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.plans = [];
    $scope.pager = null;
    $scope.cboYear = '2566', //(moment().year() + 543).toString(),
    $scope.cboPlanType = "";
    $scope.cboCategory = "";
    $scope.cboFaction = "";
    $scope.cboDepart = "";
    $scope.cboStatus = "";
    $scope.cboPrice = '';
    $scope.txtItemName = '';
    $scope.isApproved = false;

    $scope.cboStrategic = '';
    $scope.cboStrategy = '';
    $scope.cboKpi = '';
    $scope.txtKeyword = '';

    $scope.plansToApproveList = [];
    $scope.onCheckedPlan = (e, plan) => {
        let newList = [];
        if (e.target.checked) {
            newList = [...$scope.plansToApproveList, plan.id];
        } else {
            newList = $scope.plansToApproveList.filter(app => app !== plan.id);
        }

        $scope.plansToApproveList = [...new Set(newList)];
    };

    $scope.approveAll = () => {
        if (confirm('คุณต้องการอนุมัติทุกรายการที่หน่วยงานร้องขอใช่หรือไม่?')) {
            // $http.post(`${CONFIG.baseUrl}/approvals/2565/year`, $scope.plansToApproveList)
            // .then(function(res) {
            //     console.log(res);

            //     if (res.data.status == 1) {
            //         toaster.pop('success', "ผลการทำงาน", "อนุมัติแผนฯเรียบร้อย !!!");
            //     } else {
            //         toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถอนุมัติแผนฯได้ !!!");
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
            $scope.loading = true;

            $http.post(`${CONFIG.baseUrl}/approvals`, { id: plan.id })
            .then((res) => {
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
        } else {
            $scope.loading = false;
        }
    };

    $scope.approveByList = () => {
        if ($scope.plansToApproveList.length == 0) {
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกการอนุมัติได้ กรุณาเลือกรายการก่อน!!!");
        } else {
            if (confirm(`คุณต้องการอนุมัติแผนตามรายการที่เลือกใช่หรือไม่?`)) {
                $scope.loading = true;

                $http.post(`${CONFIG.baseUrl}/approvals/lists`, { plans: $scope.plansToApproveList })
                .then(function(res) {
                    if (res.data.status == 1) {
                        toaster.pop('success', "ผลการทำงาน", "อนุมัติแผนฯเรียบร้อย !!!");

                        res.data.plans.forEach(lst => {
                            $scope.plans.forEach(plan => {
                                if (plan.id === lst.id) {
                                    plan.plan_no = lst.plan_no;
                                    plan.approved = lst.approved;
                                }
                            });
                        });
                    } else {
                        toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถอนุมัติแผนฯได้ !!!");
                    }

                    /** Reset data */
                    $scope.loading = false;
                    $scope.plansToApproveList = [];
                    $('#chkAll').prop("checked", false);

                    let checkboxes = document.querySelectorAll('table td > input[type="checkbox"]');
                    checkboxes.forEach(chk => {
                        $(chk).prop("checked", false);
                    });
                }, function(err) {
                    console.log(err);
                    $scope.loading = false;
                });
            } else {
                $scope.loading = false;
            }
        }
    };

    $scope.cancel = (e, plan) => {
        e.preventDefault();

        if (confirm(`คุณต้องการยกเลิกอนุมัติรายการรหัส ${plan.plan_no} ใช่หรือไม่?`)) {
            $scope.loading = true;

            $http.put(`${CONFIG.apiUrl}/approvals/${plan.id}/cancel`, { id: plan.id })
            .then((res) => {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ยกเลิกอนุมัติรายการเรียบร้อย !!!");

                    $scope.plans.forEach(plan => {
                        if (plan.id === res.data.plan.id) {
                            plan.plan_no = res.data.plan.plan_no;
                            plan.approved = res.data.plan.approved;
                        }
                    });
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถยกเลิกอนุมัติรายการได้ !!!");
                }

                $scope.loading = false;
            }, (err) => {
                console.log(err);
                $scope.loading = false;
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.onCheckedAll = function(e) {
        if (e.target.checked) {
            $scope.plans.forEach(plan => {
                if (plan.approved != 'A' && !$scope.plansToApproveList.includes(plan.id)) {
                    newList = [...$scope.plansToApproveList, plan.id];
                } else {
                    newList = $scope.plansToApproveList.filter(app => app !== plan.id);
                }

                $scope.plansToApproveList = [...new Set(newList)];
            });

            let checkboxes = document.querySelectorAll('table td > input[type="checkbox"]');
            checkboxes.forEach(chk => {
                $(chk).prop("checked", true);
            });
        } else {
            $scope.plansToApproveList = [];

            let checkboxes = document.querySelectorAll('table td > input[type="checkbox"]');
            checkboxes.forEach(chk => {
                $(chk).prop("checked", false);
            });
        }
    };

    $scope.getAll = function(type, inStock) {
        $scope.loading = true;
        $scope.plans = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let faction = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let name    = $scope.txtItemName === '' ? '' : $scope.txtItemName;
        let price   = $scope.cboPrice === '' ? '' : $scope.cboPrice;
        let in_stock = inStock != undefined ? `&in_stock=${inStock}` : '';

        $http.get(`${CONFIG.baseUrl}/plans/search?type=${type}&year=${year}&cate=${cate}&faction=${faction}&depart=${depart}&name=${name}&price=${price}&&status=${status}${in_stock}`)
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
        let faction = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let name    = $scope.txtItemName === '' ? '' : $scope.txtItemName;
        let price   = $scope.cboPrice === '' ? '' : $scope.cboPrice;
        let in_stock = params.inStock != undefined ? `&in_stock=${params.inStock}` : '';

        $http.get(`${url}&type=${params.type}&year=${year}&cate=${cate}&faction=${faction}&depart=${depart}&name=${name}&price=${price}&&status=${status}${in_stock}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    
    $scope.getProjects = function(event) {
        $scope.loading = true;
        $scope.projects = [];
        $scope.pager = null;

        let year        = $scope.cboYear === '' ? '' : $scope.cboYear;
        let strategic   = $scope.cboStrategic === '' ? '' : $scope.cboStrategic;
        let strategy    = !$scope.cboStrategy ? '' : $scope.cboStrategy;
        let kpi         = !$scope.cboKpi ? '' : $scope.cboKpi;
        let faction     = !$scope.cboFaction ? '' : $scope.cboFaction;
        let depart      = !$scope.cboDepart ? '' : $scope.cboDepart;
        let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let name        = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;

        $http.get(`${CONFIG.baseUrl}/projects/search?year=${year}&strategic=${strategic}&strategy=${strategy}&kpi=${kpi}&faction=${faction}&depart=${depart}&name=${name}&status=${status}`)
        .then(function(res) {
            $scope.setProjects(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setProjects = function(res) {
        const { data, ...pager } = res.data.projects;

        $scope.projects = data;
        $scope.pager = pager;
    };

    $scope.getProjectsWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.projects = [];
        $scope.pager = null;

        let year        = $scope.cboYear === '' ? '' : $scope.cboYear;
        let strategic   = $scope.cboStrategic === '' ? '' : $scope.cboStrategic;
        let strategy    = !$scope.cboStrategy ? '' : $scope.cboStrategy;
        let kpi         = !$scope.cboKpi ? '' : $scope.cboKpi;
        let faction     = !$scope.cboFaction ? '' : $scope.cboFaction;
        let depart      = !$scope.cboDepart ? '' : $scope.cboDepart;
        let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let name        = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;

        $http.get(`${url}&year=${year}&strategic=${strategic}&strategy=${strategy}&kpi=${kpi}&faction=${faction}&depart=${depart}&name=${name}&status=${status}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.approveProject = (e, project) => {
        e.preventDefault();

        if (confirm(`คุณต้องการอนุมัติแผนงาน/โครงการรหัส ${project.id} ใช่หรือไม่?`)) {
            $scope.loading = true;

            $http.put(`${CONFIG.apiUrl}/projects/${project.id}/approve`, { id: project.id })
            .then((res) => {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "อนุมัติแผนงาน/โครงการเรียบร้อย !!!");

                    $scope.projects.forEach(project => {
                        if (project.id === res.data.project.id) {
                            // project.project_no = res.data.project.project_no;
                            project.approved = res.data.project.approved;
                        }
                    });
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถอนุมัติแผนงาน/โครงการได้ !!!");
                }

                $scope.loading = false;
            }, (err) => {
                console.log(err);
                $scope.loading = false;
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.cancelProject = (e, project) => {
        e.preventDefault();

        if (confirm(`คุณต้องการยกเลิกอนุมัติแผนงาน/โครงการรหัส ${project.project_no} ใช่หรือไม่?`)) {
            $scope.loading = true;

            $http.put(`${CONFIG.apiUrl}/projects/${project.id}/cancel`, { id: project.id })
            .then((res) => {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ยกเลิกอนุมัติแผนงาน/โครงการเรียบร้อย !!!");

                    $scope.projects.forEach(project => {
                        if (project.id === res.data.project.id) {
                            // project.project_no = res.data.project.project_no;
                            project.approved = res.data.project.approved;
                        }
                    });
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถยกเลิกอนุมัติแผนงาน/โครงการได้ !!!");
                }

                $scope.loading = false;
            }, (err) => {
                console.log(err);
                $scope.loading = false;
            });
        } else {
            $scope.loading = false;
        }
    };
});