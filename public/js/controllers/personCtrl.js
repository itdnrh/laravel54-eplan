app.controller('personCtrl', function($scope, $http, toaster, CONFIG, ModalService) {
/** ################################################################################## */
    $scope.loading = false;

    /** Input control model */
    $scope.cboFaction = '';
    $scope.cboDepart = '';
    $scope.cboDivision = '';
    $scope.keyword = '';
    $scope.queryStr = '';

    /** Data models */
    $scope.factions = [];
    $scope.departs = [];
    $scope.divisions = [];
    $scope.persons = [];
    $scope.pager = null;

    $scope.initValues = function(initValues) {
        $scope.factions = initValues.factions;
        $scope.departs = initValues.departs;
        $scope.divisions = initValues.divisions;
    };

    $scope.getPersons = function() {
        $scope.loading = true;

        $scope.persons = [];
        $scope.pager = null;

        let depart = $scope.cboDepart === '' ? 0 : $scope.cboDepart;
        let searchKey = $scope.keyword === '' ? 0 : $scope.keyword;
        let queryStr = $scope.queryStr === '' ? '' : $scope.queryStr;

        $http.get(`${CONFIG.baseUrl}/persons/search/${depart}/${searchKey}${queryStr}`)
        .then(function(res) {
            const { data, ...pager } = res.data.persons;

            $scope.persons = data;
            $scope.pager = pager;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getHeadOfDeparts = function() {
        $scope.loading = true;

        $scope.persons = [];
        $scope.pager = null;

        let faction = $scope.cboFaction === '' ? 0 : $scope.cboFaction;
        let searchKey = $scope.keyword === '' ? 0 : $scope.keyword;
        let queryStr = $scope.queryStr === '' ? '' : $scope.queryStr;

        $http.get(`${CONFIG.baseUrl}/persons/departs/head?faction=${faction}&searchKey=${searchKey}${queryStr}`)
        .then(function(res) {
            const { data, ...pager } = res.data.persons;

            $scope.persons = data;
            $scope.pager = pager;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.getDataWithURL = function(e, URL, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        
        $scope.persons = [];
        $scope.pager = null;

        let faction = $scope.cboFaction === '' ? 0 : $scope.cboFaction;
        let depart = $scope.cboDepart === '' ? 0 : $scope.cboDepart;
        let searchKey = $scope.keyword === '' ? 0 : $scope.keyword;
        let queryStr = $scope.queryStr === '' ? '' : $scope.queryStr;

        $http.get(`${URL}&depart=${depart}&faction=${faction}&searchKey=${searchKey}${queryStr}`)
        .then(function(res) {
            const { data, ...pager } = res.data.persons;

            $scope.persons = data;
            $scope.pager = pager;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.edit = function(typeId) {
        console.log(typeId);

        window.location.href = CONFIG.baseUrl + '/asset-type/edit/' + typeId;
    };


    $scope.add = function(event, form) {
        event.preventDefault();

        $http.post(CONFIG.baseUrl + '/asset-type/store', $scope.type)
        .then(function(res) {
            console.log(res);
            toaster.pop('success', "", 'บันทึกข้อมูลเรียบร้อยแล้ว !!!');
        }, function(err) {
            console.log(err);
            toaster.pop('error', "", 'พบข้อผิดพลาด !!!');
        });

        document.getElementById(form).reset();
    }

    $scope.update = function(event, form) {
        event.preventDefault();

        if(confirm("คุณต้องแก้ไขรายการหนี้เลขที่ " + $scope.type.type_id + " ใช่หรือไม่?")) {
            $scope.type.cate_id = $('#cate_id option:selected').val();

            $http.put(CONFIG.baseUrl + '/asset-type/update', $scope.type)
            .then(function(res) {
                console.log(res);
                toaster.pop('success', "", 'แก้ไขข้อมูลเรียบร้อยแล้ว !!!');
            }, function(err) {
                console.log(err);
                toaster.pop('error', "", 'พบข้อผิดพลาด !!!');
            });
        }

        setTimeout(function (){
            window.location.href = CONFIG.baseUrl + '/asset-type/list';
        }, 2000);        
    };

    $scope.delete = function(typeId) {
        console.log(typeId);

        if(confirm("คุณต้องลบรายการหนี้เลขที่ " + typeId + " ใช่หรือไม่?")) {
            $http.delete(CONFIG.baseUrl + '/asset-type/delete/' +typeId)
            .then(function(res) {
                console.log(res);
                toaster.pop('success', "", 'ลบข้อมูลเรียบร้อยแล้ว !!!');
                $scope.getData();
            }, function(err) {
                console.log(err);
                toaster.pop('error', "", 'พบข้อผิดพลาด !!!');
            });
        }
    };
});