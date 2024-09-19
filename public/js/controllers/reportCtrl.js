app.controller(
    "reportCtrl",
    function (CONFIG, $scope, $http, $timeout, toaster, StringFormatService, ChartService, ExcelService) {
    /*
    |-----------------------------------------------------------------------------
    | Initial properties
    |-----------------------------------------------------------------------------
    */
    $scope.leaves = [];
    $scope.data = [];
    $scope.pager = [];
    $scope.initFormValues = null;
    $scope.filteredDeparts = [];
    $scope.filteredDivisions = [];
    $scope.loading = false;

    $scope.cboStrategic = '';
    $scope.cboFaction = '';
    $scope.cboDepart = '';
    $scope.cboDivision = '';
    $scope.dtpDate = StringFormatService.convFromDbDate(moment().format('YYYY-MM-DD'));
    $scope.dtpMonth = StringFormatService.convToThMonth(moment().format('YYYY-MM-DD'));
    $scope.budgetYearRange = [2560,2561,2562,2563,2564,2565,2566,2567,2568];
    $scope.cboPlanType = '';
    $scope.cboProjectType = '';
    $scope.cboCategory = '';
    $scope.cboApproved = 'A';
    $scope.cboPrice = '';
    $scope.cboSort = '';
    $scope.chkIsFixcost = false;
    $scope.cboInPlan = 'I';

    let dtpDateOptions = {
        autoclose: true,
        language: 'th',
        format: 'dd/mm/yyyy',
        thaiyear: true,
        todayBtn: true,
        todayHighlight: true
    };

    let dtpMonthOptions = {
        autoclose: true,
        format: 'mm/yyyy',
        viewMode: "months", 
        minViewMode: "months",
        language: 'th',
        thaiyear: true,
        orientation: 'bottom'
    };

    $('#dtpDate')
        .datepicker(dtpDateOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            $('#dtpDate').datepicker('update', moment(event.date).toDate());

            $scope.getDaily();
        });

    $('#dtpMonth')
        .datepicker(dtpMonthOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            $('#dtpDate').datepicker('update', moment(event.date).toDate());

            $scope.dtpMonth = StringFormatService.convToThMonth(moment(event.date).format('YYYY-MM-DD'));
        });

    /*
    |-----------------------------------------------------------------------------
    | Global methods
    |-----------------------------------------------------------------------------
    */
    $scope.setIsFixcost = function(e) {
        $scope.chkIsFixcost = e.target.checked;

        $scope.getPlanByItem(e);
    };

    $scope.setInitialState = function(cate) {
        $scope.cboCategory = cate.toString();
    };

    $scope.exportToExcel = function (tableId) {
        var exportHref = ExcelService.tableToExcel(tableId, 'WireWorkbenchDataExport');
        $timeout(function() {
            location.href = exportHref;
        },100); // trigger download
    };

    $scope.getDaily = function () {
        let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let division = $scope.cboDivision === '' ? '' : $scope.cboDivision;
        let date = $scope.dtpDate === ''
                    ? moment().format('YYYY-MM-DD')
                    : StringFormatService.convToDbDate($scope.dtpDate);

        $http.get(`${CONFIG.baseUrl}/reports/daily-data?depart=${depart}&division=${division}&date=${date}`)
        .then(function (res) {
            const { data, ...pager } = res.data.leaves;

            $scope.data = data;
            $scope.pager = pager;

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    /*
    |-----------------------------------------------------------------------------
    | Plan reports
    |-----------------------------------------------------------------------------
    */
    $scope.plans = [];
    $scope.totalByPlanTypes = {
        asset: 0,
        construct: 0,
        material: 0,
        service: 0,
        total: 0,
    };

    $scope.getPlanByFaction = function () {
        $scope.totalByPlanTypes = {
            asset: 0,
            construct: 0,
            material: 0,
            service: 0,
            total: 0,
        };

        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543 
                    : $scope.cboYear;
        let in_plan = $scope.cboInPlan !== '' ? $scope.cboInPlan : '';
        let approved = !$scope.cboApproved ? '' : 'A';

        $http.get(`${CONFIG.apiUrl}/reports/plan-faction?year=${year}&approved=${approved}&in_plan=${in_plan}`)
        .then(function (res) {
            let departs = res.data.plans.map(plan => {
                let dep = res.data.departs.find(d => d.depart_id === plan.depart_id);
                plan.depart_name = dep.depart_name;
                plan.faction_id = dep.faction_id;

                return plan;
            });

            let admin = {
                asset: 0,
                material: 0,
                service: 0,
                construct: 0,
                total: 0,
            };
            let doctor = {
                asset: 0,
                material: 0,
                service: 0,
                construct: 0,
                total: 0,
            };
            let primary = {
                asset: 0,
                material: 0,
                service: 0,
                construct: 0,
                total: 0,
            };
            let prs = {
                asset: 0,
                material: 0,
                service: 0,
                construct: 0,
                total: 0,
            };
            let nurse = {
                asset: 0,
                material: 0,
                service: 0,
                construct: 0,
                total: 0,
            };
            let strategic = {
                asset: 0,
                material: 0,
                service: 0,
                construct: 0,
                total: 0,
            };

            departs.forEach(dep => {
                if (dep.faction_id == 1) {
                    admin.asset         += dep.asset && parseInt(dep.asset);
                    admin.material      += dep.material && parseInt(dep.material);
                    admin.service       += dep.service && parseInt(dep.service);
                    admin.construct     += dep.construct && parseInt(dep.construct);
                    admin.total         += dep.total && parseInt(dep.total);
                } else if (dep.faction_id == 2) {
                    doctor.asset        += dep.asset && parseInt(dep.asset);
                    doctor.material     += dep.material && parseInt(dep.material);
                    doctor.service      += dep.service && parseInt(dep.service);
                    doctor.construct    += dep.construct && parseInt(dep.construct);
                    doctor.total        += dep.total && parseInt(dep.total);
                } else if (dep.faction_id == 3) {
                    primary.asset       += dep.asset && parseInt(dep.asset);
                    primary.material    += dep.material && parseInt(dep.material);
                    primary.service     += dep.service && parseInt(dep.service);
                    primary.construct   += dep.construct && parseInt(dep.construct);
                    primary.total       += dep.total && parseInt(dep.total);
                } else if (dep.faction_id == 7) {
                    prs.asset       += dep.asset && parseInt(dep.asset);
                    prs.material    += dep.material && parseInt(dep.material);
                    prs.service     += dep.service && parseInt(dep.service);
                    prs.construct   += dep.construct && parseInt(dep.construct);
                    prs.total       += dep.total && parseInt(dep.total);
                } else if (dep.faction_id == 5) {
                    nurse.asset     += dep.asset && parseInt(dep.asset);
                    nurse.material  += dep.material && parseInt(dep.material);
                    nurse.service   += dep.service && parseInt(dep.service);
                    nurse.construct += dep.construct && parseInt(dep.construct);
                    nurse.total     += dep.total && parseInt(dep.total);
                } else if (dep.faction_id == 13) {
                    strategic.asset     += dep.asset && parseInt(dep.asset);
                    strategic.material  += dep.material && parseInt(dep.material);
                    strategic.service   += dep.service && parseInt(dep.service);
                    strategic.construct += dep.construct && parseInt(dep.construct);
                    strategic.total     += dep.total && parseInt(dep.total);
                }
            });

            $scope.plans = res.data.factions.map(faction => {
                if (faction.faction_id == 1) {
                    return { ...faction, ...admin };
                } else if (faction.faction_id == 2) {
                    return { ...faction, ...doctor };
                } else if (faction.faction_id == 3) {
                    return { ...faction, ...primary };
                } else if (faction.faction_id == 7) {
                    return { ...faction, ...prs };
                } else if (faction.faction_id == 5) {
                    return { ...faction, ...nurse };
                } else if (faction.faction_id == 13) {
                    return { ...faction, ...strategic };
                }
            });

            /** Render chart */
            // const typeName = type === '' ? '' : `(${$('#cboPlanType option:selected').text()})`;
            $scope.pieOptions = ChartService.initPieChart("pieChartContainer", `สัดส่วนแผนเงินบำรุง ตามกลุ่มภารกิจ`, "บาท", "สัดส่วนแผนเงินบำรุง");
            $scope.pieOptions.series[0].data.push({ name: 'อำนวยการ', y: parseInt(admin.total) });
            $scope.pieOptions.series[0].data.push({ name: 'ทุติย/ตติย', y: parseInt(doctor.total) });
            $scope.pieOptions.series[0].data.push({ name: 'ปฐมภูมิ', y: parseInt(primary.total) });
            $scope.pieOptions.series[0].data.push({ name: 'พรส.', y: parseInt(prs.total) });
            $scope.pieOptions.series[0].data.push({ name: 'พยาบาล', y: parseInt(nurse.total) });
            $scope.pieOptions.series[0].data.push({ name: 'ยุทธศาสตร์', y: parseInt(strategic.total) });
            let chart = new Highcharts.Chart($scope.pieOptions);

            /** Sum total of plan by plan_type */
            if (res.data.plans.length > 0) {
                res.data.plans.forEach(plan => {
                    $scope.totalByPlanTypes.asset       += plan.asset ? plan.asset : 0;
                    $scope.totalByPlanTypes.construct   += plan.construct ? plan.construct : 0;
                    $scope.totalByPlanTypes.material    += plan.material ? plan.material : 0;
                    $scope.totalByPlanTypes.service     += plan.service ? plan.service : 0;
                    $scope.totalByPlanTypes.total       += plan.total ? plan.total : 0;
                });
            } else {
                $scope.totalByPlanTypes = {
                    asset: 0,
                    construct: 0,
                    material: 0,
                    service: 0,
                    total: 0,
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPlanByDepart = function () {
        $scope.loading = true;
        $scope.totalByPlanTypes = {
            asset: 0,
            asset_budget: 0,
            construct: 0,
            construct_budget: 0,
            material: 0,
            material_budget: 0,
            service: 0,
            service_budget: 0,
            total: 0,
            total_budget: 0,
        };

        let faction = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543 
                    : $scope.cboYear;
        let in_plan = $scope.cboInPlan !== '' ? $scope.cboInPlan : '';
        let approved = !$scope.cboApproved ? '' : 'A';

        $http.get(`${CONFIG.apiUrl}/reports/plan-depart?year=${year}&faction=${faction}&approved=${approved}&in_plan=${in_plan}`)
        .then(function (res) {
            $scope.plans = res.data.plans.map(plan => {
                let dep = res.data.departs.find(d => d.depart_id === plan.depart_id);
                plan.depart_name = dep.depart_name;

                return plan;
            });

            /** Sum total of plan by plan_type */
            if (res.data.plans.length > 0) {
                res.data.plans.forEach(plan => {
                    $scope.totalByPlanTypes.asset += plan.asset ? plan.asset : 0;
                    $scope.totalByPlanTypes.asset_budget += plan.asset_budget ? plan.asset_budget : 0;
                    $scope.totalByPlanTypes.construct += plan.construct ? plan.construct : 0;
                    $scope.totalByPlanTypes.construct_budget += plan.construct_budget ? plan.construct_budget : 0;
                    $scope.totalByPlanTypes.material += plan.material ? plan.material : 0;
                    $scope.totalByPlanTypes.material_budget += plan.material_budget ? plan.material_budget : 0;
                    $scope.totalByPlanTypes.service += plan.service ? plan.service : 0;
                    $scope.totalByPlanTypes.service_budget += plan.service_budget ? plan.service_budget : 0;
                    $scope.totalByPlanTypes.total += plan.total ? plan.total : 0;
                    $scope.totalByPlanTypes.total_budget += plan.total_budget ? plan.total_budget : 0;
                });
            } else {
                $scope.totalByPlanTypes = {
                    asset: 0,
                    asset_budget: 0,
                    construct: 0,
                    construct_budget: 0,
                    material: 0,
                    material_budget: 0,
                    service: 0,
                    service_budget: 0,
                    total: 0,
                    total_budget: 0,
                };
            }

            /** Render chart */
            const faction = $scope.cboFaction === '' ? '' : $('#cboFaction option:selected').text();
            const inPlan = $scope.cboInPlan === '' ? '' : `(${$('#cboInPlan option:selected').text()})`;
            $scope.pieOptions = ChartService.initPieChart("pieChartContainer", `สัดส่วนแผนเงินบำรุง ${inPlan} ${faction} ตามประเภทแผน`, "บาท", "สัดส่วนแผนเงินบำรุง");
            $scope.pieOptions.series[0].data.push({ name: 'ครุภัณฑ์', y: parseFloat($scope.totalByPlanTypes.asset) });
            $scope.pieOptions.series[0].data.push({ name: 'วัสดุ', y: parseFloat($scope.totalByPlanTypes.material) });
            $scope.pieOptions.series[0].data.push({ name: 'จ้างบริการ', y: parseFloat($scope.totalByPlanTypes.service) });
            $scope.pieOptions.series[0].data.push({ name: 'ก่อสร้าง', y: parseFloat($scope.totalByPlanTypes.construct) });
            let chart = new Highcharts.Chart($scope.pieOptions);

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPlanByItem = function () {
        $scope.loading = true;
        $scope.totalByItem = {
            amount: 0,
            sum_price: 0,
            remain_amount: 0,
            remain_budget: 0
        };

        let year        = $scope.cboYear === ''
                            ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                                ? moment().year() + 544
                                : moment().year() + 543 
                            : $scope.cboYear;
        let type        = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let cate        = !$scope.cboCategory ? '' : $scope.cboCategory;
        let price       = $scope.cboPrice !== '' ? $scope.cboPrice : '';
        let isFixcost   = $scope.chkIsFixcost ? '1' : '';
        let approved    = !$scope.cboApproved ? '' : 'A';
        let in_plan     = $scope.cboInPlan !== '' ? $scope.cboInPlan : '';
        let sort        = $scope.cboSort !== '' ? $scope.cboSort : '';

        $http.get(`${CONFIG.apiUrl}/reports/plan-item?year=${year}&type=${type}&cate=${cate}&price=${price}&approved=${approved}&in_plan=${in_plan}&isFixcost=${isFixcost}&sort=${sort}`)
        .then(function (res) {
            $scope.plans = res.data.plans;

            // /** Sum total of plan by plan_type */
            if (res.data.plans.length > 0) {
                res.data.plans.forEach(plan => {
                    $scope.totalByItem.amount           += plan.amount ? plan.amount : 0;
                    $scope.totalByItem.sum_price        += plan.sum_price ? plan.sum_price : 0;
                    $scope.totalByItem.remain_amount    += plan.remain_amount ? plan.remain_amount : 0;
                    $scope.totalByItem.remain_budget    += plan.remain_budget ? plan.remain_budget : 0;
                });
            } else {
                $scope.totalByItem = {
                    amount: 0,
                    sum_price: 0,
                    remain_amount: 0,
                    remain_budget: 0
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.totalPlanByPlanTypes = {
        amount: 0,
        sum_price: 0,
        remain_amount: 0,
        remain_budget: 0
    };
    $scope.getPlanByType = function () {
        $scope.totalPlanByPlanTypes = {
            amount: 0,
            sum_price: 0,
            remain_amount: 0,
            remain_budget: 0
        };

        let year        = $scope.cboYear === ''
                            ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                                ? moment().year() + 544
                                : moment().year() + 543 
                            : $scope.cboYear;
        let type        = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let approved    = !$scope.cboApproved ? '' : 'A';
        let price       = $scope.cboPrice !== '' ? $scope.cboPrice : '';
        let sort        = $scope.cboSort !== '' ? $scope.cboSort : '';
        let in_plan     = $scope.cboInPlan !== '' ? $scope.cboInPlan : '';

        $http.get(`${CONFIG.apiUrl}/reports/plan-type?year=${year}&type=${type}&approved=${approved}&price=${price}&in_plan=${in_plan}&sort=${sort}`)
        .then(function (res) {
            $scope.plans = res.data.plans.map(plan => {
                let cate = res.data.categories.find(c => c.id === plan.category_id);
                plan.category_name = cate ? cate.name : '';

                return plan;
            });

            // /** Sum total of plan by plan_type */
            if (res.data.plans.length > 0) {
                res.data.plans.forEach(plan => {
                    $scope.totalPlanByPlanTypes.amount          += plan.amount ? plan.amount : 0;
                    $scope.totalPlanByPlanTypes.sum_price       += plan.sum_price ? plan.sum_price : 0;
                    $scope.totalPlanByPlanTypes.remain_amount   += plan.remain_amount ? plan.remain_amount : 0;
                    $scope.totalPlanByPlanTypes.remain_budget   += plan.remain_budget ? plan.remain_budget : 0;
                });
            } else {
                $scope.totalPlanByPlanTypes = {
                    amount: 0,
                    sum_price: 0,
                    remain_amount: 0,
                    remain_budget: 0
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.totalByPlanQuarters = {
        q1_amt: 0,
        q1_sum: 0,
        q2_amt: 0,
        q2_sum: 0,
        q3_amt: 0,
        q3_sum: 0,
        q4_amt: 0,
        q4_sum: 0,
        total_amt: 0,
        total_sum: 0,
    };

    $scope.getPlanByQuarter = function () {
        $scope.totalByPlanQuarters = {
            q1_amt: 0,
            q1_sum: 0,
            q2_amt: 0,
            q2_sum: 0,
            q3_amt: 0,
            q3_sum: 0,
            q4_amt: 0,
            q4_sum: 0,
            total_amt: 0,
            total_sum: 0,
        };

        let year        = $scope.cboYear === ''
                            ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                                ? moment().year() + 544
                                : moment().year() + 543 
                            : $scope.cboYear;
        let type        = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let approved    = !$scope.cboApproved ? '' : 'A';
        let price       = $scope.cboPrice !== '' ? $scope.cboPrice : '';
        let sort        = $scope.cboSort !== '' ? $scope.cboSort : '';
        let in_plan     = $scope.cboInPlan !== '' ? $scope.cboInPlan : '';

        $http.get(`${CONFIG.apiUrl}/reports/plan-quarter?year=${year}&type=${type}&approved=${approved}&price=${price}&in_plan=${in_plan}&sort=${sort}`)
        .then(function (res) {
            $scope.plans = res.data.plans.map(plan => {
                let cate = res.data.categories.find(c => c.id === plan.category_id);
                plan.category_name = cate ? cate.name : '';

                return plan;
            });

            // /** Sum total of plan by plan_type */
            if (res.data.plans.length > 0) {
                res.data.plans.forEach(plan => {
                    $scope.totalByPlanQuarters.q1_amt += plan.q1_amt ? plan.q1_amt : 0;
                    $scope.totalByPlanQuarters.q1_sum += plan.q1_sum ? plan.q1_sum : 0;
                    $scope.totalByPlanQuarters.q2_amt += plan.q2_amt ? plan.q2_amt : 0;
                    $scope.totalByPlanQuarters.q2_sum += plan.q2_sum ? plan.q2_sum : 0;
                    $scope.totalByPlanQuarters.q3_amt += plan.q3_amt ? plan.q3_amt : 0;
                    $scope.totalByPlanQuarters.q3_sum += plan.q3_sum ? plan.q3_sum : 0;
                    $scope.totalByPlanQuarters.q4_amt += plan.q4_amt ? plan.q4_amt : 0;
                    $scope.totalByPlanQuarters.q4_sum += plan.q4_sum ? plan.q4_sum : 0;
                    $scope.totalByPlanQuarters.total_amt += plan.total_amt ? plan.total_amt : 0;
                    $scope.totalByPlanQuarters.total_sum += plan.total_sum ? plan.total_sum : 0;
                });
                
                /** Render chart */
                const typeName = type === '' ? '' : `(${$('#cboPlanType option:selected').text()})`;
                $scope.pieOptions = ChartService.initPieChart("pieChartContainer", `สัดส่วนแผนเงินบำรุง ${typeName} รายไตรมาส`, "บาท", "สัดส่วนแผนเงินบำรุง");
                $scope.pieOptions.series[0].data.push({ name: 'Q1', y: parseInt($scope.totalByPlanQuarters.q1_sum) });
                $scope.pieOptions.series[0].data.push({ name: 'Q2', y: parseInt($scope.totalByPlanQuarters.q2_sum) });
                $scope.pieOptions.series[0].data.push({ name: 'Q3', y: parseInt($scope.totalByPlanQuarters.q3_sum) });
                $scope.pieOptions.series[0].data.push({ name: 'Q4', y: parseInt($scope.totalByPlanQuarters.q4_sum) });
                let chart = new Highcharts.Chart($scope.pieOptions);
            } else {
                $scope.totalByPlanQuarters = {
                    q1_amt: 0,
                    q1_sum: 0,
                    q2_amt: 0,
                    q2_sum: 0,
                    q3_amt: 0,
                    q3_sum: 0,
                    q4_amt: 0,
                    q4_sum: 0,
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.totalByPlanProcessQuarters = {
        q1_amt: 0,
        q1_sum: 0,
        q2_amt: 0,
        q2_sum: 0,
        q3_amt: 0,
        q3_sum: 0,
        q4_amt: 0,
        q4_sum: 0,
        total_amt: 0,
        total_sum: 0,
    };

    $scope.getPlanProcessByQuarter = function () {
        $scope.totalByPlanQuarters = {
            q1_amt: 0,
            q1_sum: 0,
            q2_amt: 0,
            q2_sum: 0,
            q3_amt: 0,
            q3_sum: 0,
            q4_amt: 0,
            q4_sum: 0,
            total_amt: 0,
            total_sum: 0,
        };

        let year        = $scope.cboYear === ''
                            ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                                ? moment().year() + 544
                                : moment().year() + 543 
                            : $scope.cboYear;
        let type        = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let approved    = !$scope.cboApproved ? '' : 'A';
        let price       = $scope.cboPrice !== '' ? $scope.cboPrice : '';
        let sort        = $scope.cboSort !== '' ? $scope.cboSort : '';
        let in_plan     = $scope.cboInPlan !== '' ? $scope.cboInPlan : '';

        $http.get(`${CONFIG.apiUrl}/reports/plan-process-quarter?year=${year}&type=${type}&approved=${approved}&price=${price}&in_plan=${in_plan}&sort=${sort}`)
        .then(function (res) {
            $scope.plans = res.data.plans.map(plan => {
                let cate = res.data.categories.find(c => c.id === plan.category_id);
                plan.category_name = cate ? cate.name : '';
                plan.plan_type_id = cate ? cate.plan_type_id : '';

                return plan;
            });

            // // /** Sum total of plan by plan_type */
            if (res.data.plans.length > 0) {
                res.data.plans.forEach(plan => {
                    $scope.totalByPlanQuarters.q1_sum += plan.q1_sum ? plan.q1_sum : 0;
                    $scope.totalByPlanQuarters.q1_amt += plan.q1_amt ? plan.q1_amt : 0;
                    $scope.totalByPlanQuarters.q2_sum += plan.q2_sum ? plan.q2_sum : 0;
                    $scope.totalByPlanQuarters.q2_amt += plan.q2_amt ? plan.q2_amt : 0;
                    $scope.totalByPlanQuarters.q3_sum += plan.q3_sum ? plan.q3_sum : 0;
                    $scope.totalByPlanQuarters.q3_amt += plan.q3_amt ? plan.q3_amt : 0;
                    $scope.totalByPlanQuarters.q4_sum += plan.q4_sum ? plan.q4_sum : 0;
                    $scope.totalByPlanQuarters.q4_amt += plan.q4_amt ? plan.q4_amt : 0;
                    $scope.totalByPlanQuarters.total_sum += plan.total_sum ? plan.total_sum : 0;
                    $scope.totalByPlanQuarters.total_amt += plan.total_amt ? plan.total_amt : 0;
                });
                
            //     /** Render chart */
                const typeName = type === '' ? '' : `(${$('#cboPlanType option:selected').text()})`;
                $scope.pieOptions = ChartService.initPieChart("pieChartContainer", `สัดส่วนแผนเงินบำรุง ${typeName} รายไตรมาส`, "บาท", "สัดส่วนแผนเงินบำรุง");
                $scope.pieOptions.series[0].data.push({ name: 'Q1', y: parseInt($scope.totalByPlanQuarters.q1_sum) });
                $scope.pieOptions.series[0].data.push({ name: 'Q2', y: parseInt($scope.totalByPlanQuarters.q2_sum) });
                $scope.pieOptions.series[0].data.push({ name: 'Q3', y: parseInt($scope.totalByPlanQuarters.q3_sum) });
                $scope.pieOptions.series[0].data.push({ name: 'Q4', y: parseInt($scope.totalByPlanQuarters.q4_sum) });
                let chart = new Highcharts.Chart($scope.pieOptions);
            } else {
                $scope.totalByPlanQuarters = {
                    q1_amt: 0,
                    q1_sum: 0,
                    q2_amt: 0,
                    q2_sum: 0,
                    q3_amt: 0,
                    q3_sum: 0,
                    q4_amt: 0,
                    q4_sum: 0,
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPlanProcessByDetails = function (type, quarter) {
        $scope.totalPlanProcessByDetails = {
            amount: 0,
            sum_price: 0,
        };

        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543 
                    : $scope.cboYear;
        let cate = $scope.cboCategory == '' ? '' : $scope.cboCategory;
        let approved = !$scope.cboApproved ? '' : 'A';

        $http.get(`${CONFIG.apiUrl}/reports/plan-process-details/${type}?year=${year}&cate=${cate}&quarter=${quarter}`)
        .then(function (res) {
            $scope.plans = res.data.plans;

            // /** Sum total of plan by plan_type */
            if (res.data.plans.length > 0) {
                res.data.plans.forEach(plan => {
                    $scope.totalPlanProcessByDetails.amount += plan.amount ? plan.amount : 0;
                    $scope.totalPlanProcessByDetails.sum_price += plan.sum_price ? plan.sum_price : 0;
                });
            } else {
                $scope.totalPlanProcessByDetails = {
                    amount: 0,
                    sum_price: 0,
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPlanProcessByRequests = function (type, quarter) {
        $scope.totalPlanProcessByRequests = {
            amount: 0,
            sum_price: 0,
        };

        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543 
                    : $scope.cboYear;
        let cate = $scope.cboCategory == '' ? '' : $scope.cboCategory;

        $http.get(`${CONFIG.apiUrl}/reports/plan-process-requests/${type}?year=${year}&cate=${cate}&quarter=${quarter}`)
        .then(function (res) {
            console.log(res);
            $scope.plans = res.data.plans;

            // /** Sum total of plan by plan_type */
            if (res.data.plans.length > 0) {
                res.data.plans.forEach(plan => {
                    $scope.totalPlanProcessByRequests.amount += plan.amount ? plan.amount : 0;
                    $scope.totalPlanProcessByRequests.sum_price += plan.sum_price ? plan.sum_price : 0;
                });
            } else {
                $scope.totalPlanProcessByRequests = {
                    amount: 0,
                    sum_price: 0,
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.totalAssetByCategories = {
        vehicle: 0,
        office: 0,
        computer: 0,
        medical: 0,
        home: 0,
        construct: 0,
        agriculture: 0,
        ads: 0,
        electric: 0,
        total: 0
    };

    $scope.getAssetByDepart = function () {
        $scope.totalAssetByCategories = {
            vehicle: 0,
            office: 0,
            computer: 0,
            medical: 0,
            home: 0,
            construct: 0,
            agriculture: 0,
            ads: 0,
            electric: 0,
            total: 0
        };

        let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let division = $scope.cboDivision === '' ? '' : $scope.cboDivision;
        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543 
                    : $scope.cboYear;
        let approved = !$scope.cboApproved ? '' : 'A';

        $http.get(`${CONFIG.apiUrl}/reports/asset-depart?year=${year}&approved=${approved}`)
        .then(function (res) {
            $scope.plans = res.data.plans.map(plan => {
                let dep = res.data.departs.find(d => d.depart_id === plan.depart_id);
                plan.depart_name = dep.depart_name;

                return plan;
            });

            /** Sum total of plan by plan_type */
            if (res.data.plans.length > 0) {
                res.data.plans.forEach(plan => {
                    $scope.totalAssetByCategories.vehicle       += plan.vehicle;
                    $scope.totalAssetByCategories.office        += plan.office;
                    $scope.totalAssetByCategories.computer      += plan.computer;
                    $scope.totalAssetByCategories.medical       += plan.medical;
                    $scope.totalAssetByCategories.home          += plan.home;
                    $scope.totalAssetByCategories.construct     += plan.construct;
                    $scope.totalAssetByCategories.agriculture   += plan.agriculture;
                    $scope.totalAssetByCategories.ads           += plan.ads;
                    $scope.totalAssetByCategories.electric      += plan.electric;
                    $scope.totalAssetByCategories.total         += plan.total;
                });
            } else {
                $scope.totalAssetByCategories = {
                    vehicle: 0,
                    office: 0,
                    computer: 0,
                    medical: 0,
                    home: 0,
                    construct: 0,
                    agriculture: 0,
                    ads: 0,
                    electric: 0,
                    total: 0
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getAssetByFaction = function () {
        $scope.totalAssetByFaction = {
            vehicle: 0,
            office: 0,
            computer: 0,
            medical: 0,
            home: 0,
            construct: 0,
            agriculture: 0,
            ads: 0,
            electric: 0,
            total: 0
        };

        let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let division = $scope.cboDivision === '' ? '' : $scope.cboDivision;
        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543 
                    : $scope.cboYear;
        let approved = !$scope.cboApproved ? '' : 'A';

        $http.get(`${CONFIG.apiUrl}/reports/asset-faction?year=${year}&approved=${approved}`)
        .then(function (res) {
            let departs = res.data.plans.map(plan => {
                let dep = res.data.departs.find(d => d.depart_id === plan.depart_id);
                plan.depart_name = dep.depart_name;
                plan.faction_id = dep.faction_id;

                return plan;
            });

            let admin = {
                vehicle: 0,
                office: 0,
                computer: 0,
                medical: 0,
                home: 0,
                construct: 0,
                agriculture: 0,
                ads: 0,
                electric: 0,
                total: 0
            };
            let doctor = {
                vehicle: 0,
                office: 0,
                computer: 0,
                medical: 0,
                home: 0,
                construct: 0,
                agriculture: 0,
                ads: 0,
                electric: 0,
                total: 0
            };
            let primary = {
                vehicle: 0,
                office: 0,
                computer: 0,
                medical: 0,
                home: 0,
                construct: 0,
                agriculture: 0,
                ads: 0,
                electric: 0,
                total: 0
            };
            let prs = {
                vehicle: 0,
                office: 0,
                computer: 0,
                medical: 0,
                home: 0,
                construct: 0,
                agriculture: 0,
                ads: 0,
                electric: 0,
                total: 0
            };
            let nurse = {
                vehicle: 0,
                office: 0,
                computer: 0,
                medical: 0,
                home: 0,
                construct: 0,
                agriculture: 0,
                ads: 0,
                electric: 0,
                total: 0
            };

            departs.forEach(dep => {
                if (dep.faction_id == 1) {
                    admin.vehicle       += dep.vehicle && parseInt(dep.vehicle);
                    admin.office        += dep.office && parseInt(dep.office);
                    admin.computer      += dep.computer && parseInt(dep.computer);
                    admin.medical       += dep.medical && parseInt(dep.medical);
                    admin.home          += dep.home && parseInt(dep.home);
                    admin.construct     += dep.construct && parseInt(dep.construct);
                    admin.agriculture   += dep.agriculture && parseInt(dep.agriculture);
                    admin.ads           += dep.ads && parseInt(dep.ads);
                    admin.electric      += dep.electric && parseInt(dep.electric);
                    admin.total         += dep.total && parseInt(dep.total);
                } else if (dep.faction_id == 2) {
                    doctor.vehicle      += dep.vehicle && parseInt(dep.vehicle);
                    doctor.office       += dep.office && parseInt(dep.office);
                    doctor.computer     += dep.computer && parseInt(dep.computer);
                    doctor.medical      += dep.medical && parseInt(dep.medical);
                    doctor.home         += dep.home && parseInt(dep.home);
                    doctor.construct    += dep.construct && parseInt(dep.construct);
                    doctor.agriculture  += dep.agriculture && parseInt(dep.agriculture);
                    doctor.ads          += dep.ads && parseInt(dep.ads);
                    doctor.electric     += dep.electric && parseInt(dep.electric);
                    doctor.total        += dep.total && parseInt(dep.total);
                } else if (dep.faction_id == 3) {
                    primary.vehicle     += dep.vehicle && parseInt(dep.vehicle);
                    primary.office      += dep.office && parseInt(dep.office);
                    primary.computer    += dep.computer && parseInt(dep.computer);
                    primary.medical     += dep.medical && parseInt(dep.medical);
                    primary.home        += dep.home && parseInt(dep.home);
                    primary.construct   += dep.construct && parseInt(dep.construct);
                    primary.agriculture += dep.agriculture && parseInt(dep.agriculture);
                    primary.ads         += dep.ads && parseInt(dep.ads);
                    primary.electric    += dep.electric && parseInt(dep.electric);
                    primary.total       += dep.total && parseInt(dep.total);
                } else if (dep.faction_id == 7) {
                    prs.vehicle         += dep.vehicle && parseInt(dep.vehicle);
                    prs.office          += dep.office && parseInt(dep.office);
                    prs.computer        += dep.computer && parseInt(dep.computer);
                    prs.medical         += dep.medical && parseInt(dep.medical);
                    prs.home            += dep.home && parseInt(dep.home);
                    prs.construct       += dep.construct && parseInt(dep.construct);
                    prs.agriculture     += dep.agriculture && parseInt(dep.agriculture);
                    prs.ads             += dep.ads && parseInt(dep.ads);
                    prs.electric        += dep.electric && parseInt(dep.electric);
                    prs.total           += dep.total && parseInt(dep.total);
                } else if (dep.faction_id == 5) {
                    nurse.vehicle       += dep.vehicle && parseInt(dep.vehicle);
                    nurse.office        += dep.office && parseInt(dep.office);
                    nurse.computer      += dep.computer && parseInt(dep.computer);
                    nurse.medical       += dep.medical && parseInt(dep.medical);
                    nurse.home          += dep.home && parseInt(dep.home);
                    nurse.construct     += dep.construct && parseInt(dep.construct);
                    nurse.agriculture   += dep.agriculture && parseInt(dep.agriculture);
                    nurse.ads           += dep.ads && parseInt(dep.ads);
                    nurse.electric      += dep.electric && parseInt(dep.electric);
                    nurse.total         += dep.total && parseInt(dep.total);
                }
            });

            $scope.plans = res.data.factions.map(faction => {
                if (faction.faction_id == 1) {
                    return { ...faction, ...admin };
                } else if (faction.faction_id == 2) {
                    return { ...faction, ...doctor };
                } else if (faction.faction_id == 3) {
                    return { ...faction, ...primary };
                } else if (faction.faction_id == 7) {
                    return { ...faction, ...prs };
                } else if (faction.faction_id == 5) {
                    return { ...faction, ...nurse };
                }
            });

            /** Sum total of plan by plan_type */
            if (res.data.plans.length > 0) {
                res.data.plans.forEach(plan => {
                    $scope.totalAssetByFaction.vehicle       += plan.vehicle && parseInt(plan.vehicle);
                    $scope.totalAssetByFaction.office        += plan.office && parseInt(plan.office);
                    $scope.totalAssetByFaction.computer      += plan.computer && parseInt(plan.computer);
                    $scope.totalAssetByFaction.medical       += plan.medical && parseInt(plan.medical);
                    $scope.totalAssetByFaction.home          += plan.home && parseInt(plan.home);
                    $scope.totalAssetByFaction.construct     += plan.construct && parseInt(plan.construct);
                    $scope.totalAssetByFaction.agriculture   += plan.agriculture && parseInt(plan.agriculture);
                    $scope.totalAssetByFaction.ads           += plan.ads && parseInt(plan.ads);
                    $scope.totalAssetByFaction.electric      += plan.electric && parseInt(plan.electric);
                    $scope.totalAssetByFaction.total         += plan.total && parseInt(plan.total);
                });
            } else {
                $scope.totalByFaction = {
                    asset: 0,
                    construct: 0,
                    material: 0,
                    service: 0,
                    total: 0,
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.totalMaterialByCategories = {
        medical: 0,
        science: 0,
        dent: 0,
        office: 0,
        computer: 0,
        home: 0,
        clothes: 0,
        fuel: 0,
        sticker: 0,
        electric: 0,
        vehicle: 0,
        ads: 0,
        construct: 0,
        agriculture: 0,
        total: 0
    };

    $scope.getMaterialByDepart = function () {
        $scope.totalMaterialByCategories = {
            medical: 0,
            science: 0,
            dent: 0,
            office: 0,
            computer: 0,
            home: 0,
            clothes: 0,
            fuel: 0,
            sticker: 0,
            electric: 0,
            vehicle: 0,
            ads: 0,
            construct: 0,
            agriculture: 0,
            total: 0
        };

        let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let division = $scope.cboDivision === '' ? '' : $scope.cboDivision;
        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543
                    : $scope.cboYear;
        let approved = !$scope.cboApproved ? '' : 'A';

        $http.get(`${CONFIG.apiUrl}/reports/material-depart?year=${year}&approved=${approved}`)
        .then(function (res) {
            $scope.plans = res.data.plans.map(plan => {
                let dep = res.data.departs.find(d => d.depart_id === plan.depart_id);
                plan.depart_name = dep.depart_name;

                return plan;
            });

            if (res.data.plans.length > 0) {
                /** Sum total of plan by plan_type */
                res.data.plans.forEach(plan => {
                    $scope.totalMaterialByCategories.medical    += plan.medical;
                    $scope.totalMaterialByCategories.science    += plan.science;
                    $scope.totalMaterialByCategories.dent       += plan.dent;
                    $scope.totalMaterialByCategories.office     += plan.office;
                    $scope.totalMaterialByCategories.computer   += plan.computer;
                    $scope.totalMaterialByCategories.home       += plan.home;
                    $scope.totalMaterialByCategories.clothes    += plan.clothes;
                    $scope.totalMaterialByCategories.fuel       += plan.fuel;
                    $scope.totalMaterialByCategories.sticker    += plan.sticker;
                    $scope.totalMaterialByCategories.electric   += plan.electric;
                    $scope.totalMaterialByCategories.vehicle    += plan.vehicle;
                    $scope.totalMaterialByCategories.ads        += plan.ads;
                    $scope.totalMaterialByCategories.construct  += plan.construct;
                    $scope.totalMaterialByCategories.agriculture    += plan.agriculture;
                    $scope.totalMaterialByCategories.total      += plan.total;
                });
            } else {
                $scope.totalAssetByCategories = {
                    medical: 0,
                    science: 0,
                    dent: 0,
                    office: 0,
                    computer: 0,
                    home: 0,
                    clothes: 0,
                    fuel: 0,
                    sticker: 0,
                    electric: 0,
                    vehicle: 0,
                    ads: 0,
                    construct: 0,
                    agriculture: 0,
                    total: 0
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    /*
    |-----------------------------------------------------------------------------
    | Project reports
    |-----------------------------------------------------------------------------
    */
    $scope.factions = [];
    $scope.getProjectSummary = function() {
        let year = '2565';

        $http.get(`${CONFIG.apiUrl}/reports/project-summary?year=${year}`)
        .then(function(res) {
            $scope.factions = res.data.factions.map(fac => {
                const projects = res.data.projects.filter(project => project.depart.faction_id === fac.faction_id);

                fac.projects = projects;
                fac.done = projects.filter(project => project.status == 4);
                fac.total_budget = projects.reduce((budget, curVal) => budget = budget + curVal.total_budget, 0);
                fac.total_actual = projects.reduce((actual, curVal) => actual = actual + curVal.total_actual, 0);
                fac.patment = projects.reduce((payment, curVal) => {
                    const paid = curVal.payments.reduce((sum, pay) => sum = sum + pay.net_total, 0);

                    return payment = payment + paid;
                }, 0);

                return fac;
            });

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.projects = [];
    $scope.pager = null;
    $scope.getProjects = function(event) {
        $scope.loading = true;
        $scope.projects = [];
        $scope.pager = null;

        let year = $scope.cboYear === '' ? '' : $scope.cboYear;
        let strategic = $scope.cboStrategic === '' ? '' : $scope.cboStrategic;
        let strategy = $scope.cboStrategy === '' ? '' : $scope.cboStrategy;
        let kpi = $scope.cboKpi === '' ? '' : $scope.cboKpi;
        let faction = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let name = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;

        $http.get(`${CONFIG.baseUrl}/projects/search?year=${year}&status=3`)
        .then(function(res) {
            const { data, ...pager } = res.data.projects;

            $scope.projects = data.map(project => {
                if (project.payments) {
                    project.actual = project.payments.reduce((sum, curVal) => {
                        return sum = sum + parseFloat(curVal.net_total);
                    }, 0);
                }

                return project;
            });

            $scope.pager = pager;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.totalByFaction = {
        cup_amount: 0,
        cup_budget: 0,
        hos_amount: 0,
        hos_budget: 0,
        tam_amount: 0,
        tam_budget: 0,
        total_amount: 0,
        total_budget: 0,
    };

    $scope.getProjectByFaction = function () {
        $scope.totalByFaction = {
            hos_amount: 0,
            hos_budget: 0,
            hos_paid: 0,
            cup_amount: 0,
            cup_budget: 0,
            cup_paid: 0,
            tam_amount: 0,
            tam_budget: 0,
            tam_paid: 0,
            total_amount: 0,
            total_budget: 0,
            total_paid: 0,
        };

        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543 
                    : $scope.cboYear;
        let approved = !$scope.cboApproved ? '' : 'A';

        $http.get(`${CONFIG.apiUrl}/reports/project-faction?year=${year}&approved=${approved}`)
        .then(function (res) {
            let departs = res.data.projects.map(plan => {
                let dep = res.data.departs.find(d => d.depart_id === plan.depart_id);

                plan.depart_name = dep ? dep.depart_name : '';
                plan.faction_id = dep ? dep.faction_id : '';

                let payment = res.data.payments.find(p => p.depart_id === plan.depart_id);
                plan.hos_paid   = payment ? payment.hos_paid : 0;
                plan.cup_paid   = payment ? payment.cup_paid : 0;
                plan.tam_paid   = payment ? payment.tam_paid : 0;
                plan.total_paid = payment ? payment.total_paid : 0;

                return plan;
            });

            let admin = {
                hos_amount: 0,
                hos_budget: 0,
                hos_paid: 0,
                cup_amount: 0,
                cup_budget: 0,
                cup_paid: 0,
                tam_amount: 0,
                tam_budget: 0,
                tam_paid: 0,
                total_amount: 0,
                total_budget: 0,
                total_paid: 0,
            };
            let doctor = {
                hos_amount: 0,
                hos_budget: 0,
                hos_paid: 0,
                cup_amount: 0,
                cup_budget: 0,
                cup_paid: 0,
                tam_amount: 0,
                tam_budget: 0,
                tam_paid: 0,
                total_amount: 0,
                total_budget: 0,
                total_paid: 0,
            };
            let primary = {
                hos_amount: 0,
                hos_budget: 0,
                hos_paid: 0,
                cup_amount: 0,
                cup_budget: 0,
                cup_paid: 0,
                tam_amount: 0,
                tam_budget: 0,
                tam_paid: 0,
                total_amount: 0,
                total_budget: 0,
                total_paid: 0,
            };
            let prs = {
                hos_amount: 0,
                hos_budget: 0,
                hos_paid: 0,
                cup_amount: 0,
                cup_budget: 0,
                cup_paid: 0,
                tam_amount: 0,
                tam_budget: 0,
                tam_paid: 0,
                total_amount: 0,
                total_budget: 0,
                total_paid: 0,
            };
            let nurse = {
                hos_amount: 0,
                hos_budget: 0,
                hos_paid: 0,
                cup_amount: 0,
                cup_budget: 0,
                cup_paid: 0,
                tam_amount: 0,
                tam_budget: 0,
                tam_paid: 0,
                total_amount: 0,
                total_budget: 0,
                total_paid: 0,
            };
            let strategic = {
                hos_amount: 0,
                hos_budget: 0,
                hos_paid: 0,
                cup_amount: 0,
                cup_budget: 0,
                cup_paid: 0,
                tam_amount: 0,
                tam_budget: 0,
                tam_paid: 0,
                total_amount: 0,
                total_budget: 0,
                total_paid: 0,
            };

            departs.forEach(dep => {
                if (dep.faction_id == 1) {
                    admin.hos_amount    += dep.hos_amount && parseInt(dep.hos_amount);
                    admin.hos_budget    += dep.hos_budget && parseInt(dep.hos_budget);
                    admin.hos_paid      += dep.hos_paid && parseInt(dep.hos_paid);
                    admin.cup_amount    += dep.cup_amount && parseInt(dep.cup_amount);
                    admin.cup_budget    += dep.cup_budget && parseInt(dep.cup_budget);
                    admin.cup_paid      += dep.cup_paid && parseInt(dep.cup_paid);
                    admin.tam_amount    += dep.tam_amount && parseInt(dep.tam_amount);
                    admin.tam_budget    += dep.tam_budget && parseInt(dep.tam_budget);
                    admin.tam_paid      += dep.tam_paid && parseInt(dep.tam_paid);
                    admin.total_amount  += dep.total_amount && parseInt(dep.total_amount);
                    admin.total_budget  += dep.total_budget && parseInt(dep.total_budget);
                    admin.total_paid    += dep.total_paid && parseInt(dep.total_paid);
                } else if (dep.faction_id == 2) {
                    doctor.hos_amount   += dep.hos_amount && parseInt(dep.hos_amount);
                    doctor.hos_budget   += dep.hos_budget && parseInt(dep.hos_budget);
                    doctor.hos_paid     += dep.hos_paid && parseInt(dep.hos_paid);
                    doctor.cup_amount   += dep.cup_amount && parseInt(dep.cup_amount);
                    doctor.cup_budget   += dep.cup_budget && parseInt(dep.cup_budget);
                    doctor.cup_paid     += dep.cup_paid && parseInt(dep.cup_paid);
                    doctor.tam_amount   += dep.tam_amount && parseInt(dep.tam_amount);
                    doctor.tam_budget   += dep.tam_budget && parseInt(dep.tam_budget);
                    doctor.tam_paid     += dep.tam_paid && parseInt(dep.tam_paid);
                    doctor.total_amount += dep.total_amount && parseInt(dep.total_amount);
                    doctor.total_budget += dep.total_budget && parseInt(dep.total_budget);
                    doctor.total_paid   += dep.total_paid && parseInt(dep.total_paid);
                } else if (dep.faction_id == 3) {
                    primary.hos_amount      += dep.hos_amount && parseInt(dep.hos_amount);
                    primary.hos_budget      += dep.hos_budget && parseInt(dep.hos_budget);
                    primary.hos_paid        += dep.hos_paid && parseInt(dep.hos_paid);
                    primary.cup_amount      += dep.cup_amount && parseInt(dep.cup_amount);
                    primary.cup_budget      += dep.cup_budget && parseInt(dep.cup_budget);
                    primary.cup_paid        += dep.cup_paid && parseInt(dep.cup_paid);
                    primary.tam_amount      += dep.tam_amount && parseInt(dep.tam_amount);
                    primary.tam_budget      += dep.tam_budget && parseInt(dep.tam_budget);
                    primary.tam_paid        += dep.tam_paid && parseInt(dep.tam_paid);
                    primary.total_amount    += dep.total_amount && parseInt(dep.total_amount);
                    primary.total_budget    += dep.total_budget && parseInt(dep.total_budget);
                    primary.total_paid      += dep.total_paid && parseInt(dep.total_paid);
                } else if (dep.faction_id == 7) {
                    prs.hos_amount      += dep.hos_amount && parseInt(dep.hos_amount);
                    prs.hos_budget      += dep.hos_budget && parseInt(dep.hos_budget);
                    prs.hos_paid        += dep.hos_paid && parseInt(dep.hos_paid);
                    prs.cup_amount      += dep.cup_amount && parseInt(dep.cup_amount);
                    prs.cup_budget      += dep.cup_budget && parseInt(dep.cup_budget);
                    prs.cup_paid        += dep.cup_paid && parseInt(dep.cup_paid);
                    prs.tam_amount      += dep.tam_amount && parseInt(dep.tam_amount);
                    prs.tam_budget      += dep.tam_budget && parseInt(dep.tam_budget);
                    prs.tam_paid        += dep.tam_paid && parseInt(dep.tam_paid);
                    prs.total_amount    += dep.total_amount && parseInt(dep.total_amount);
                    prs.total_budget    += dep.total_budget && parseInt(dep.total_budget);
                    prs.total_paid      += dep.total_paid && parseInt(dep.total_paid);
                } else if (dep.faction_id == 5) {
                    nurse.hos_amount    += dep.hos_amount && parseInt(dep.hos_amount);
                    nurse.hos_budget    += dep.hos_budget && parseInt(dep.hos_budget);
                    nurse.hos_paid      += dep.hos_paid && parseInt(dep.hos_paid);
                    nurse.cup_amount    += dep.cup_amount && parseInt(dep.cup_amount);
                    nurse.cup_budget    += dep.cup_budget && parseInt(dep.cup_budget);
                    nurse.cup_paid      += dep.cup_paid && parseInt(dep.cup_paid);
                    nurse.tam_amount    += dep.tam_amount && parseInt(dep.tam_amount);
                    nurse.tam_budget    += dep.tam_budget && parseInt(dep.tam_budget);
                    nurse.tam_paid      += dep.tam_paid && parseInt(dep.tam_paid);
                    nurse.total_amount  += dep.total_amount && parseInt(dep.total_amount);
                    nurse.total_budget  += dep.total_budget && parseInt(dep.total_budget);
                    nurse.total_paid    += dep.total_paid && parseInt(dep.total_paid);
                } else if (dep.faction_id == 13) {
                    strategic.hos_amount    += dep.hos_amount && parseInt(dep.hos_amount);
                    strategic.hos_budget    += dep.hos_budget && parseInt(dep.hos_budget);
                    strategic.hos_paid      += dep.hos_paid && parseInt(dep.hos_paid);
                    strategic.cup_amount    += dep.cup_amount && parseInt(dep.cup_amount);
                    strategic.cup_budget    += dep.cup_budget && parseInt(dep.cup_budget);
                    strategic.cup_paid      += dep.cup_paid && parseInt(dep.cup_paid);
                    strategic.tam_amount    += dep.tam_amount && parseInt(dep.tam_amount);
                    strategic.tam_budget    += dep.tam_budget && parseInt(dep.tam_budget);
                    strategic.tam_paid      += dep.tam_paid && parseInt(dep.tam_paid);
                    strategic.total_amount  += dep.total_amount && parseInt(dep.total_amount);
                    strategic.total_budget  += dep.total_budget && parseInt(dep.total_budget);
                    strategic.total_paid    += dep.total_paid && parseInt(dep.total_paid);
                }
            });

            $scope.projects = res.data.factions.map(faction => {
                if (faction.faction_id == 1) {
                    return { ...faction, ...admin };
                } else if (faction.faction_id == 2) {
                    return { ...faction, ...doctor };
                } else if (faction.faction_id == 3) {
                    return { ...faction, ...primary };
                } else if (faction.faction_id == 7) {
                    return { ...faction, ...prs };
                } else if (faction.faction_id == 5) {
                    return { ...faction, ...nurse };
                } else if (faction.faction_id == 13) {
                    return { ...faction, ...strategic };
                }
            });

            /** Render chart */
            $scope.pieOptions = ChartService.initPieChart("pieChartContainer", `สัดส่วนแผนงาน/โครงการ ตามกลุ่มภารกิจ`, "บาท", "สัดส่วนแผนเงินบำรุง");
            $scope.pieOptions.series[0].data.push({ name: 'อำนวยการ', y: parseInt(admin.total_budget) });
            $scope.pieOptions.series[0].data.push({ name: 'ทุติย/ตติย', y: parseInt(doctor.total_budget) });
            $scope.pieOptions.series[0].data.push({ name: 'ปฐมภูมิ', y: parseInt(primary.total_budget) });
            $scope.pieOptions.series[0].data.push({ name: 'พรส.', y: parseInt(prs.total_budget) });
            $scope.pieOptions.series[0].data.push({ name: 'พยาบาล', y: parseInt(nurse.total_budget) });
            $scope.pieOptions.series[0].data.push({ name: 'ยุทธศาสตร์', y: parseInt(strategic.total_budget) });
            let chart = new Highcharts.Chart($scope.pieOptions);

            /** Sum total of plan by plan_type */
            if (res.data.projects.length > 0) {
                res.data.projects.forEach(project => {
                    $scope.totalByFaction.cup_amount    += project.cup_amount ? project.cup_amount : 0;
                    $scope.totalByFaction.cup_budget    += project.cup_budget ? project.cup_budget : 0;
                    $scope.totalByFaction.cup_paid      += project.cup_paid ? project.cup_paid : 0;
                    $scope.totalByFaction.hos_amount    += project.hos_amount ? project.hos_amount : 0;
                    $scope.totalByFaction.hos_budget    += project.hos_budget ? project.hos_budget : 0;
                    $scope.totalByFaction.hos_paid      += project.hos_paid ? project.hos_paid : 0;
                    $scope.totalByFaction.tam_amount    += project.tam_amount ? project.tam_amount : 0;
                    $scope.totalByFaction.tam_budget    += project.tam_budget ? project.tam_budget : 0;
                    $scope.totalByFaction.tam_paid      += project.tam_paid ? project.tam_paid : 0;
                    $scope.totalByFaction.total_amount  += project.total_amount ? project.total_amount : 0;
                    $scope.totalByFaction.total_budget  += project.total_budget ? project.total_budget : 0;
                    $scope.totalByFaction.total_paid    += project.total_paid ? project.total_paid : 0;
                });
            } else {
                $scope.totalByFaction = {
                    hos_amount: 0,
                    hos_budget: 0,
                    hos_paid: 0,
                    cup_amount: 0,
                    cup_budget: 0,
                    cup_paid: 0,
                    tam_amount: 0,
                    tam_budget: 0,
                    tam_paid: 0,
                    total_amount: 0,
                    total_budget: 0,
                    total_paid: 0,
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.totalByDepart = {
        cup_amount: 0,
        cup_budget: 0,
        hos_amount: 0,
        hos_budget: 0,
        tam_amount: 0,
        tam_budget: 0,
        total_amount: 0,
        total_budget: 0,
    };

    $scope.getProjectByDepart = function () {
        $scope.totalByDepart = {
            cup_amount: 0,
            cup_budget: 0,
            hos_amount: 0,
            hos_budget: 0,
            tam_amount: 0,
            tam_budget: 0,
            total_amount: 0,
            total_budget: 0,
        };

        let faction = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543 
                    : $scope.cboYear;
        let approved = !$scope.cboApproved ? '' : 'A';

        $http.get(`${CONFIG.apiUrl}/reports/project-depart?year=${year}&faction=${faction}&approved=${approved}`)
        .then(function (res) {
            $scope.projects = res.data.projects.map(project => {
                let dep = res.data.departs.find(d => d.depart_id === project.depart_id);
                project.depart_name = dep.depart_name;

                return project;
            });

            /** Sum total of plan by plan_type */
            if (res.data.projects.length > 0) {
                res.data.projects.forEach(project => {
                    $scope.totalByDepart.cup_amount    += project.cup_amount ? project.cup_amount : 0;
                    $scope.totalByDepart.cup_budget    += project.cup_budget ? project.cup_budget : 0;
                    $scope.totalByDepart.hos_amount    += project.hos_amount ? project.hos_amount : 0;
                    $scope.totalByDepart.hos_budget    += project.hos_budget ? project.hos_budget : 0;
                    $scope.totalByDepart.tam_amount    += project.tam_amount ? project.tam_amount : 0;
                    $scope.totalByDepart.tam_budget    += project.tam_budget ? project.tam_budget : 0;
                    $scope.totalByDepart.total_amount  += project.total_amount ? project.total_amount : 0;
                    $scope.totalByDepart.total_budget  += project.total_budget ? project.total_budget : 0;
                });
            } else {
                $scope.totalByDepart = {
                    cup_amount: 0,
                    cup_budget: 0,
                    hos_amount: 0,
                    hos_budget: 0,
                    tam_amount: 0,
                    tam_budget: 0,
                    total_amount: 0,
                    total_budget: 0,
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.totalProjectByStrategics = {
        hos_amount: 0,
        hos_budget: 0,
        cup_amount: 0,
        cup_budget: 0,
        tam_amount: 0,
        tam_budget: 0,
        total_amount: 0,
        total_budget: 0
    };

    $scope.getProjectByStrategic = function () {
        $scope.totalProjectByStrategics = {
            hos_amount: 0,
            hos_budget: 0,
            cup_amount: 0,
            cup_budget: 0,
            tam_amount: 0,
            tam_budget: 0,
            total_amount: 0,
            total_budget: 0
        };

        let strategic = $scope.cboStrategic === '' ? '' : $scope.cboStrategic;
        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543 
                    : $scope.cboYear;
        let approved = !$scope.cboApproved ? '' : 'A';

        $http.get(`${CONFIG.apiUrl}/reports/project-strategic?year=${year}&strategic=${strategic}&approved=${approved}`)
        .then(function (res) {
            $scope.projects = res.data.projects.map(project => {
                let stg = res.data.strategies.find(s => s.id === project.strategy_id);
                project.strategic_id = stg.strategic_id;

                return project;
            }).sort((a, b) => a.strategic_id - b.strategic_id);

            /** Sum total of plan by plan_type */
            if (res.data.projects.length > 0) {
                res.data.projects.forEach(project => {
                    $scope.totalProjectByStrategics.hos_amount += project.hos_amount ? project.hos_amount : 0;
                    $scope.totalProjectByStrategics.hos_budget += project.hos_budget ? project.hos_budget : 0;
                    $scope.totalProjectByStrategics.cup_amount += project.cup_amount ? project.cup_amount : 0;
                    $scope.totalProjectByStrategics.cup_budget += project.cup_budget ? project.cup_budget : 0;
                    $scope.totalProjectByStrategics.tam_amount += project.tam_amount ? project.tam_amount : 0;
                    $scope.totalProjectByStrategics.tam_budget += project.tam_budget ? project.tam_budget : 0;
                    $scope.totalProjectByStrategics.total_amount += project.total_amount ? project.total_amount : 0;
                    $scope.totalProjectByStrategics.total_budget += project.total_budget ? project.total_budget : 0;
                });
            } else {
                $scope.totalProjectByStrategics = {
                    hos_amount: 0,
                    hos_budget: 0,
                    cup_amount: 0,
                    cup_budget: 0,
                    total_amount: 0,
                    tam_budget: 0,
                    total_amount: 0,
                    total_budget: 0
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.totalProjectByQuarters = {
        q1_amt: 0,
        q1_bud: 0,
        q2_amt: 0,
        q2_bud: 0,
        q3_amt: 0,
        q3_bud: 0,
        q4_amt: 0,
        q4_bud: 0,
        total_amt: 0,
        total_bud: 0,
    };

    $scope.getProjectByQuarters = function () {
        $scope.totalProjectByQuarters = {
            q1_amt: 0,
            q1_bud: 0,
            q2_amt: 0,
            q2_bud: 0,
            q3_amt: 0,
            q3_bud: 0,
            q4_amt: 0,
            q4_bud: 0,
            total_amt: 0,
            total_bud: 0,
        };

        let strategic = $scope.cboStrategic === '' ? '' : $scope.cboStrategic;
        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543 
                    : $scope.cboYear;
        let type = $scope.cboProjectType === '' ? '' : $scope.cboProjectType;
        let approved = !$scope.cboApproved ? '' : 'A';

        $http.get(`${CONFIG.apiUrl}/reports/project-quarter?year=${year}&strategic=${strategic}&type=${type}&approved=${approved}`)
        .then(function (res) {
            $scope.projects = res.data.projects.map(project => {
                let stg = res.data.strategies.find(s => s.id === project.strategy_id);

                if (stg) {
                    project.strategic_id = stg.strategic_id;
                }

                return project;
            }).sort((a, b) => a.strategic_id - b.strategic_id);

            /** Sum total of plan by plan_type */
            if (res.data.projects.length > 0) {
                res.data.projects.forEach(project => {
                    $scope.totalProjectByQuarters.q1_amt += project.q1_amt ? project.q1_amt : 0;
                    $scope.totalProjectByQuarters.q1_bud += project.q1_bud ? project.q1_bud : 0;
                    $scope.totalProjectByQuarters.q2_amt += project.q2_amt ? project.q2_amt : 0;
                    $scope.totalProjectByQuarters.q2_bud += project.q2_bud ? project.q2_bud : 0;
                    $scope.totalProjectByQuarters.q3_amt += project.q3_amt ? project.q3_amt : 0;
                    $scope.totalProjectByQuarters.q3_bud += project.q3_bud ? project.q3_bud : 0;
                    $scope.totalProjectByQuarters.q4_amt += project.q4_amt ? project.q4_amt : 0;
                    $scope.totalProjectByQuarters.q4_bud += project.q4_bud ? project.q4_bud : 0;
                    $scope.totalProjectByQuarters.total_amt += project.total_amt ? project.total_amt : 0;
                    $scope.totalProjectByQuarters.total_bud += project.total_bud ? project.total_bud : 0;
                });

                /** Render chart */
                const typeName = type === '' ? '' : `(${$('#cboProjectType option:selected').text()})`;
                $scope.pieOptions = ChartService.initPieChart("pieChartContainer", `สัดส่วนแผนงาน/โครงการ ${typeName} รายไตรมาส`, "บาท", "สัดส่วนแผนงาน/โครงการ");
                $scope.pieOptions.series[0].data.push({ name: 'Q1', y: parseInt($scope.totalProjectByQuarters.q1_bud) });
                $scope.pieOptions.series[0].data.push({ name: 'Q2', y: parseInt($scope.totalProjectByQuarters.q2_bud) });
                $scope.pieOptions.series[0].data.push({ name: 'Q3', y: parseInt($scope.totalProjectByQuarters.q3_bud) });
                $scope.pieOptions.series[0].data.push({ name: 'Q4', y: parseInt($scope.totalProjectByQuarters.q4_bud) });
                let chart = new Highcharts.Chart($scope.pieOptions);
            } else {
                $scope.totalProjectByQuarters = {
                    q1_amt: 0,
                    q1_bud: 0,
                    q2_amt: 0,
                    q2_bud: 0,
                    q3_amt: 0,
                    q3_bud: 0,
                    q4_amt: 0,
                    q4_bud: 0,
                    total_amt: 0,
                    total_bud: 0,
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getProjectProcessByQuarter = function () {
        $scope.totalProjectByQuarters = {
            q1_amt: 0,
            q1_bud: 0,
            q2_amt: 0,
            q2_bud: 0,
            q3_amt: 0,
            q3_bud: 0,
            q4_amt: 0,
            q4_bud: 0,
            total_amt: 0,
            total_bud: 0,
        };

        let strategic = $scope.cboStrategic === '' ? '' : $scope.cboStrategic;
        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543 
                    : $scope.cboYear;
        let type = $scope.cboProjectType === '' ? '' : $scope.cboProjectType;
        let approved = !$scope.cboApproved ? '' : 'A';

        $http.get(`${CONFIG.apiUrl}/reports/project-process-quarter?year=${year}&strategic=${strategic}&type=${type}&approved=${approved}`)
        .then(function (res) {
            $scope.projects = res.data.projects.map(project => {
                let stg = res.data.strategies.find(s => s.id === project.strategy_id);
                if (stg) {
                    project.strategic_id = stg.strategic_id;
                }

                let paidTotal = res.data.payments.find(p => p.strategy_id === project.strategy_id);
                if (paidTotal) {
                    project.q1_amt = paidTotal.q1_amt ? paidTotal.q1_amt : 0;
                    project.q2_amt = paidTotal.q2_amt ? paidTotal.q2_amt : 0;
                    project.q3_amt = paidTotal.q3_amt ? paidTotal.q3_amt : 0;
                    project.q4_amt = paidTotal.q4_amt ? paidTotal.q4_amt : 0;
                    project.total_amt = paidTotal.total_amt ? paidTotal.total_amt : 0;
                }

                return project;
            }).sort((a, b) => a.strategic_id - b.strategic_id);

            /** Sum total of plan by plan_type */
            if (res.data.projects.length > 0) {
                res.data.projects.forEach(project => {
                    $scope.totalProjectByQuarters.q1_amt += project.q1_amt ? project.q1_amt : 0;
                    $scope.totalProjectByQuarters.q1_bud += project.q1_bud ? project.q1_bud : 0;
                    $scope.totalProjectByQuarters.q2_amt += project.q2_amt ? project.q2_amt : 0;
                    $scope.totalProjectByQuarters.q2_bud += project.q2_bud ? project.q2_bud : 0;
                    $scope.totalProjectByQuarters.q3_amt += project.q3_amt ? project.q3_amt : 0;
                    $scope.totalProjectByQuarters.q3_bud += project.q3_bud ? project.q3_bud : 0;
                    $scope.totalProjectByQuarters.q4_amt += project.q4_amt ? project.q4_amt : 0;
                    $scope.totalProjectByQuarters.q4_bud += project.q4_bud ? project.q4_bud : 0;
                    $scope.totalProjectByQuarters.total_amt += project.total_amt ? project.total_amt : 0;
                    $scope.totalProjectByQuarters.total_bud += project.total_bud ? project.total_bud : 0;
                });

                /** Render chart */
                const typeName = type === '' ? '' : `(${$('#cboProjectType option:selected').text()})`;
                $scope.pieOptions = ChartService.initPieChart("pieChartContainer", `สัดส่วนแผนงาน/โครงการ ${typeName} รายไตรมาส`, "บาท", "สัดส่วนแผนงาน/โครงการ");
                $scope.pieOptions.series[0].data.push({ name: 'Q1', y: parseInt($scope.totalProjectByQuarters.q1_bud) });
                $scope.pieOptions.series[0].data.push({ name: 'Q2', y: parseInt($scope.totalProjectByQuarters.q2_bud) });
                $scope.pieOptions.series[0].data.push({ name: 'Q3', y: parseInt($scope.totalProjectByQuarters.q3_bud) });
                $scope.pieOptions.series[0].data.push({ name: 'Q4', y: parseInt($scope.totalProjectByQuarters.q4_bud) });
                let chart = new Highcharts.Chart($scope.pieOptions);
            } else {
                $scope.totalProjectByQuarters = {
                    q1_amt: 0,
                    q1_bud: 0,
                    q2_amt: 0,
                    q2_bud: 0,
                    q3_amt: 0,
                    q3_bud: 0,
                    q4_amt: 0,
                    q4_bud: 0,
                    total_amt: 0,
                    total_bud: 0,
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getProjectStrategyByQuarter = function (strategy) {
        $scope.totalProjectStrategyByQuarters = {
            total_budget: 0,
            total_paid: 0
        };

        // let strategic = $scope.cboStrategic === '' ? '' : $scope.cboStrategic;
        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543 
                    : $scope.cboYear;
        let type = $scope.cboProjectType === '' ? '' : $scope.cboProjectType;
        let approved = !$scope.cboApproved ? '' : 'A';

        $http.get(`${CONFIG.apiUrl}/reports/project-strategy-quarter/${strategy}?year=${year}&type=${type}&approved=${approved}`)
        .then(function (res) {
            console.log(res.data);
            $scope.projects = res.data.projects.map(project => {
                let stg = res.data.strategies.find(s => s.id === project.strategy_id);
                if (stg) {
                    project.strategic_id = stg.strategic_id;
                }

                let paidTotal = res.data.payments.find(p => p.project_id === project.id);
                project.total_paid = paidTotal ? paidTotal.total_paid : 0;

                return project;
            }); //.sort((a, b) => a.strategic_id - b.strategic_id);

            /** Sum total of plan by plan_type */
            if (res.data.projects.length > 0) {
                res.data.projects.forEach(project => {
                    $scope.totalProjectStrategyByQuarters.total_budget += project.total_budget;
                    $scope.totalProjectStrategyByQuarters.total_paid += project.total_paid;
                });
            } else {
                $scope.totalProjectStrategyByQuarters = {
                    total_budget: 0,
                    total_paid: 0
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    /*
    |-----------------------------------------------------------------------------
    | Order reports
    |-----------------------------------------------------------------------------
    */
    $scope.supports = [];
    $scope.totalOrderCompareSupport = {
        sent: 0,
        received: 0,
        ordered: 0
    };
    $scope.getOrderCompareSupport = function() {
        $scope.totalOrderCompareSupport = {
            sent: 0,
            received: 0,
            ordered: 0
        };

        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543 
                    : $scope.cboYear;
        let month = $scope.dtpMonth === '' ? '' : StringFormatService.thMonthToDbMonth($scope.dtpMonth);
        let type = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let approved = !$scope.cboApproved ? '' : 'A';

        $http.get(`${CONFIG.apiUrl}/reports/order-compare-support?year=${year}&month=${month}&type=${type}&approved=${approved}`)
        .then(function (res) {
            $scope.supports = res.data.supports;

            /** Sum total of plan by plan_type */
            if (res.data.supports.length > 0) {
                res.data.supports.forEach(support => {
                    $scope.totalOrderCompareSupport.sent += support.sent;
                    $scope.totalOrderCompareSupport.received += support.received;
                    $scope.totalOrderCompareSupport.ordered += support.ordered;
                });
            } else {
                $scope.totalOrderCompareSupport = {
                    sent: 0,
                    received: 0,
                    ordered: 0
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.orders = [];
    $scope.totalOrderBackwardMonth = {
        all_po: 0,
        all_net: 0,
        back_po: 0,
        back_net: 0
    };
    $scope.getOrderBackwardMonth = function() {
        $scope.totalOrderBackwardMonth = {
            all_po: 0,
            all_net: 0,
            back_po: 0,
            back_net: 0
        };

        let year = $scope.cboYear === ''
                    ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? moment().year() + 544
                        : moment().year() + 543 
                    : $scope.cboYear;
        let month = $scope.dtpMonth === '' ? '' : StringFormatService.thMonthToDbMonth($scope.dtpMonth);
        let type = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let approved = !$scope.cboApproved ? '' : 'A';

        $http.get(`${CONFIG.apiUrl}/reports/order-backward-month?year=${year}&month=${month}&type=${type}&approved=${approved}`)
        .then(function (res) {
            $scope.orders = res.data.orders;

            /** Sum total of plan by plan_type */
            if (res.data.orders.length > 0) {
                res.data.orders.forEach(order => {
                    $scope.totalOrderBackwardMonth.all_po += order.all_po;
                    $scope.totalOrderBackwardMonth.all_net += order.all_net;
                    $scope.totalOrderBackwardMonth.back_po += order.back_po;
                    $scope.totalOrderBackwardMonth.back_net += order.back_net;
                });
            } else {
                $scope.totalOrderBackwardMonth = {
                    all_po: 0,
                    all_net: 0,
                    back_po: 0,
                    back_net: 0
                };
            }

            $scope.loading = false;
        }, function (err) {
            console.log(err);
            $scope.loading = false;
        });
    };
});
