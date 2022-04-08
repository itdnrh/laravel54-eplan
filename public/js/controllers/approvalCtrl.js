app.controller('approvalCtrl', function($scope, $http, toaster, CONFIG, ModalService) {
/** ################################################################################## */
    $scope.loading = false;
    $scope.leave = null;
    $scope.leaves = [];
    $scope.pager = null;
    $scope.cancellations = [];
    $scope.cancelPager = [];
    $scope.cboLeaveType = "";
    $scope.cboLeaveStatus = "";
    $scope.cboMenu = "";
    $scope.searchKeyword = "";
    $scope.cboQuery = "";
    $scope.showAllApproves = false;
    $scope.showAllCancels = false;
    $scope.leave_date = '';

    $('#leave_date').datepicker({
        autoclose: true,
        language: 'th',
        format: 'mm/yyyy',
        thaiyear: true,
        viewMode: "months", 
        minViewMode: "months",
    }).datepicker('update', new Date())

    $('#leave_date').change(function(e) {
        let [month, year] = e.target.value.split('/');

        $scope.cboQuery = `month=${parseInt(year) - 543}-${month}`;
        $scope.cboYear = '2565';
        $scope.cboLeaveStatus = $scope.showAllApproves ? '2&3&4&8&9' : '2';
        $scope.cboMenu = "1";

        $scope.getAll();
        $scope.getCancellation(true);
    });

    // TODO: Duplicated method
    $scope.getAll = function(event) {
        $scope.leaves = [];
        $scope.pager = null;
        $scope.loading = true;

        let year    = $scope.cboYear === '' ? 0 : $scope.cboYear;
        let type    = $scope.cboLeaveType === '' ? 0 : $scope.cboLeaveType;
        let status  = $scope.cboLeaveStatus === '' ? '-' : $scope.cboLeaveStatus;
        let menu    = $scope.cboMenu === '' ? 0 : $scope.cboMenu;
        let query   = $scope.cboQuery === '' ? '' : `?${$scope.cboQuery}`;

        $http.get(`${CONFIG.baseUrl}/leaves/search/${year}/${type}/${status}/${menu}${query}`)
        .then(function(res) {
            $scope.setLeaves(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    // TODO: Duplicated method
    $scope.getCancellation = function(isApproval=false) {
        $scope.cancellations = [];
        $scope.cancelPager = null;
        $scope.loading = true;

        let year    = $scope.cboYear === '' ? 0 : $scope.cboYear;
        let type    = $scope.cboLeaveType === '' ? 0 : $scope.cboLeaveType;
        let status  = $scope.showAllCancels ? '5&8&9' : '5';
        let query   = $scope.cboQuery !== '' ? `?${$scope.cboQuery}` : '';
        let menu    = "1";

        $http.get(`${CONFIG.baseUrl}/leaves/search/${year}/${type}/${status}/${menu}${query}`)
        .then(function(res) {
            const { data, ...pager } = res.data.leaves;

            // TODO: Should fetch data with pagination from backend
            if (isApproval) {
                $scope.cancellations = data;
            } else {
                $scope.cancellations = data.filter(cancel => {
                    return cancel.cancellation.every(c => c.received_date === null);
                });
            }

            $scope.cancelPager = pager;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    // TODO: Duplicated method
    $scope.setLeaves = function(res) {
        const { data, ...pager } = res.data.leaves;
        $scope.leaves = data;
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

    $scope.onCommentLoad = function(depart, division='') {
        $scope.cboYear = '2565';
        $scope.cboLeaveStatus = $scope.showAllApproves ? '0&1&7' : '0';
        $scope.cboMenu = "1";
        $scope.cboQuery = `depart=${depart}&division=${division}`;

        $scope.getAll();

        $scope.getCancellation();
    };

    $scope.showCommentForm = function(leave, type) {
        $scope.leave = leave;

        if (type === 1) {
            $('#comment-form').modal('show');
        } else {
            $('#cancel-comment-form').modal('show');
        }
    };

    $scope.showApprovalDetail = function(leave) {
        $scope.leave = leave;

        $('#approval-detail').modal('show');
    };

    $scope.onReceiveLoad = function(e) {
        $scope.cboYear = '2565';
        $scope.cboLeaveStatus = $scope.showAllApproves ? '1&2' : '1';
        $scope.cboQuery = "";
        $scope.cboMenu = "1";

        $scope.getAll();

        $scope.getCancellation();
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

    $scope.onSearchKeyChange = function(keyword) {
        $scope.cboYear = '2565';
        $scope.cboLeaveStatus = $scope.showAllApproves ? '1&2' : '1';
        $scope.cboQuery = `name=${keyword}`;
        $scope.cboMenu = "1";

        $scope.getAll();

        $scope.getCancellation();
    };
});