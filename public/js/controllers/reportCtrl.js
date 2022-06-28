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
        $scope.dtpYear = parseInt(moment().format('MM')) > 9
                            ? (moment().year() + 544).toString()
                            : (moment().year() + 543).toString();
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
        $scope.getSummaryByDepart = function () {
            let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
            let division = $scope.cboDivision === '' ? '' : $scope.cboDivision;
            let year = $scope.dtpYear === ''
                        ? $scope.dtpYear = parseInt(moment().format('MM')) > 9
                            ? moment().year() + 544
                            : moment().year() + 543 
                        : $scope.dtpYear;

            $http.get(`${CONFIG.apiUrl}/reports/summary-depart?year=${year}`)
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

        $scope.getAssetByDepart = function () {
            let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
            let division = $scope.cboDivision === '' ? '' : $scope.cboDivision;
            let year = $scope.dtpYear === ''
                        ? $scope.dtpYear = parseInt(moment().format('MM')) > 9
                            ? moment().year() + 544
                            : moment().year() + 543 
                        : $scope.dtpYear;

            $http.get(`${CONFIG.apiUrl}/reports/asset-depart?year=${year}`)
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

        $scope.getMaterialByDepart = function () {
            let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
            let division = $scope.cboDivision === '' ? '' : $scope.cboDivision;
            let year = $scope.dtpYear === ''
                        ? $scope.dtpYear = parseInt(moment().format('MM')) > 9
                            ? moment().year() + 544
                            : moment().year() + 543 
                        : $scope.dtpYear;

            $http.get(`${CONFIG.apiUrl}/reports/material-depart?year=${year}`)
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
            let year = $scope.dtpYear === ''
                        ? $scope.dtpYear = parseInt(moment().format('MM')) > 9
                            ? moment().year() + 544
                            : moment().year() + 543 
                        : $scope.dtpYear;

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

        $scope.debttypeToExcel = function (URL) {
            console.log($scope.debts);

            if ($scope.debts.length == 0) {
                toaster.pop("warning", "", "ไม่พบข้อมูล !!!");
            } else {
                var debtDate = $("#debtDate").val().split(",");
                var sDate = debtDate[0].trim();
                var eDate = debtDate[1].trim();
                var debtType =
                    $("#debtType").val() == "" ? "0" : $("#debtType").val();
                var showAll = $("#showall:checked").val() == "on" ? 1 : 0;

                window.location.href = `${CONFIG.baseUrl}${URL}/${debtType}/${sDate}/${eDate}/${showAll}`;
            }
        };
    }
);
