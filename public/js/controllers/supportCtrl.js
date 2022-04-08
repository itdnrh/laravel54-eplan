app.controller('supportCtrl', function(CONFIG, $scope, $http, toaster, ModalService, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.leaves = [];
    $scope.pager = [];
    $scope.loading = false;
    $scope.cboLeaveType = '';
    $scope.cboYear = parseInt(moment().format('MM')) > 9
                        ? (moment().year() + 544).toString()
                        : (moment().year() + 543).toString();
    $scope.budgetYearRange = [2560,2561,2562,2563,2564,2565,2566,2567];
    $scope.histories = null;
    $scope.vacation = null;

    $scope.getSummary = function(personId) {
        $scope.loading = true;

        $http.get(`${CONFIG.baseUrl}/histories/stat/${personId}/${$scope.cboYear}`)
        .then(function(res) {
            $scope.histories = res.data.histories;
            $scope.vacation = res.data.vacation;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.getPersonHistories = function(personId) {
        $scope.loading = true;

        const type = $scope.cboLeaveType === '' ? '' : $scope.cboLeaveType;

        $http.get(`${CONFIG.baseUrl}/histories/${personId}/${$scope.cboYear}/person?type=${type}`)
        .then(function(res) {
            $scope.loading = false;

            $scope.setLeaves(res);
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setLeaves = function(res) {
        const { data, ...pager } = res.data.leaves;

        $scope.leaves = data;
        $scope.pager = pager;
    };

    $scope.getDataWithURL = function(e, URL, cb) {
		/** Check whether parent of clicked a tag is .disabled just do nothing */
		if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.leaves = [];
        $scope.pager = [];

        $scope.loading = true;

        const type = $scope.cboLeaveType === '' ? '' : $scope.cboLeaveType;

        console.log(`${URL}&type=${type}`);

        $http.get(`${URL}&type=${type}`)
        .then(function(res) {
            $scope.setLeaves(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.arrearToExcel = function(URL) {
        console.log($scope.debts);

        if($scope.debts.length == 0) {
            toaster.pop('warning', "", "ไม่พบข้อมูล !!!");
        } else {
            var debtDate = ($("#debtDate").val()).split(",");
            var sDate = debtDate[0].trim();
            var eDate = debtDate[1].trim();
            var debtType = ($("#debtType").val() == '') ? '0' : $("#debtType").val();
            var creditor = ($("#creditor").val() == '') ? '0' : $("#creditor").val();
            var showAll = ($("#showall:checked").val() == 'on') ? 1 : 0;

            window.location.href = CONFIG.BASE_URL +URL+ '/' +debtType+ '/' +creditor+ '/' +sDate+ '/' +eDate+ '/' + showAll;
        }
    };
});