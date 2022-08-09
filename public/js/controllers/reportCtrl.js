app.controller(
    "reportCtrl",
    function (CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
        /** ################################################################################## */
        $scope.leaves = [];
        $scope.data = [];
        $scope.pager = [];
        $scope.initFormValues = null;
        $scope.filteredDeparts = [];
        $scope.filteredDivisions = [];
        $scope.loading = false;

        $scope.cboFaction = '';
        $scope.cboDepart = '';
        $scope.cboDivision = '';
        $scope.dtpDate = StringFormatService.convFromDbDate(moment().format('YYYY-MM-DD'));
        $scope.budgetYearRange = [2560,2561,2562,2563,2564,2565,2566,2567];

        let dtpOptions = {
            autoclose: true,
            language: 'th',
            format: 'dd/mm/yyyy',
            thaiyear: true,
            todayBtn: true,
            todayHighlight: true
        };
    
        $('#dtpDate')
            .datepicker(dtpOptions)
            .datepicker('update', new Date())
            .on('changeDate', function(event) {
                $('#dtpDate').datepicker('update', moment(event.date).toDate());

                $scope.getDaily();
            });

        $scope.initForm = function (initValues) {
            $scope.initFormValues = initValues;

            $scope.filteredDeparts = initValues.departs;
            $scope.filteredDivisions = initValues.divisions;
        };

        $scope.getDaily = function () {
            let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
            let division = $scope.cboDivision === '' ? '' : $scope.cboDivision;
            let date = $scope.dtpDate === ''
                        ? moment().format('YYYY-MM-DD')
                        : StringFormatService.convToDbDate($scope.dtpDate);

            $http.get(`${CONFIG.baseUrl}/reports/daily-data?depart=${depart}&division=${division}&date=${date}`)
            .then(function (res) {
                console.log(res);
                const { data, ...pager } = res.data.leaves;

                $scope.data = data;
                $scope.pager = pager;

                $scope.loading = false;
            }, function (err) {
                console.log(err);
                $scope.loading = false;
            });
        };

        $scope.plans = [];
        $scope.totalByPlanTypes = {
            asset: 0,
            construct: 0,
            material: 0,
            service: 0,
            total: 0,
        };

        $scope.getSummaryByDepart = function () {
            let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
            let division = $scope.cboDivision === '' ? '' : $scope.cboDivision;
            let year = $scope.cboYear === ''
                        ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                            ? moment().year() + 544
                            : moment().year() + 543 
                        : $scope.cboYear;
            let approved = !$scope.cboApproved ? '' : 'A';

            $http.get(`${CONFIG.apiUrl}/reports/summary-depart?year=${year}&approved=${approved}`)
            .then(function (res) {
                $scope.plans = res.data.plans.map(plan => {
                    let dep = res.data.departs.find(d => d.depart_id === plan.depart_id);
                    plan.depart_name = dep.depart_name;

                    return plan;
                });

                /** Sum total of plan by plan_type */
                res.data.plans.forEach(plan => {
                    $scope.totalByPlanTypes.asset       += plan.asset ? plan.asset : 0;
                    $scope.totalByPlanTypes.construct   += plan.construct ? plan.construct : 0;
                    $scope.totalByPlanTypes.material    += plan.material ? plan.material : 0;
                    $scope.totalByPlanTypes.service     += plan.service ? plan.service : 0;
                    $scope.totalByPlanTypes.total       += plan.total ? plan.total : 0;
                });

                $scope.loading = false;
            }, function (err) {
                console.log(err);
                $scope.loading = false;
            });
        };

        $scope.totalAssetByCategories = {};
        $scope.getAssetByDepart = function () {
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

                $scope.loading = false;
            }, function (err) {
                console.log(err);
                $scope.loading = false;
            });
        };

        $scope.totalMaterialByCategories = {};
        $scope.getMaterialByDepart = function () {
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

                $scope.loading = false;
            }, function (err) {
                console.log(err);
                $scope.loading = false;
            });
        };

        $scope.getDataWithURL = function (URL) {
            $scope.data = [];
            $scope.pager = [];
            $scope.loading = true;

            let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
            let division = $scope.cboDivision === '' ? '' : $scope.cboDivision;
            let year = $scope.cboYear === ''
                        ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                            ? moment().year() + 544
                            : moment().year() + 543 
                        : $scope.cboYear;

            $http.get(`${URL}&depart=${depart}&division=${division}&year=${year}`)
            .then(function (res) {
                    console.log(res);
                    const { data, ...pager } = res.data.persons;
                    $scope.data = data;
                    $scope.pager = pager;

                    $scope.data = data.map((person) => {
                        const leave = res.data.leaves.find((leave) =>
                            person.person_id === leave.leave_person
                        );
                        return {
                            ...person,
                            leave: leave,
                        };
                    });

                    $scope.loading = false;
            }, function (err) {
                    console.log(err);
                    $scope.loading = false;
            });
        };

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
    }
);
