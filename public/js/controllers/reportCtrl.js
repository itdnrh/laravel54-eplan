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

        $scope.onSelectedFaction = function (faction) {
            $scope.filteredDeparts = $scope.initFormValues.departs.filter(depart => {
                return depart.faction_id === parseInt(faction);
            });
        };

        $scope.onSelectedDepart = function (depart) {
            $scope.filteredDivisions = $scope.initFormValues.divisions.filter(division => {
                return division.depart_id === parseInt(depart);
            });
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

        $scope.getSummary = function () {
            let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
            let division = $scope.cboDivision === '' ? '' : $scope.cboDivision;
            let year = $scope.dtpYear === ''
                        ? $scope.dtpYear = parseInt(moment().format('MM')) > 9
                            ? moment().year() + 544
                            : moment().year() + 543 
                        : $scope.dtpYear;

            $http.get(`${CONFIG.baseUrl}/reports/summary-data?depart=${depart}&division=${division}&year=${year}`)
            .then(function (res) {
                const { leaves, histories, persons } = res.data;
                const { data, ...pager } = persons;

                $scope.data = data;
                $scope.pager = pager;

                /** Set each history's days instead of leave_days value */
                leaves.map(leave => {
                    const leaveHistory = histories.find(history => history.person_id === leave.leave_person);

                    leave['ill_days'] = leaveHistory['ill_days'];
                    leave['per_days'] = leaveHistory['per_days'];
                    leave['vac_days'] = leaveHistory['vac_days'];
                    leave['lab_days'] = leaveHistory['lab_days'];
                    leave['hel_days'] = leaveHistory['hel_days'];
                    leave['ord_days'] = leaveHistory['ord_days'];

                    return leave;
                });

                /** Append leave data to each person */
                $scope.data = data.map(person => {
                    const leave = leaves.find((leave) =>
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
