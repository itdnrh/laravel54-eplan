app.controller('personCtrl', function($scope, $http, toaster, CONFIG, ModalService) {
/** ################################################################################## */
    $scope.loading = false;

    /** Input control model */
    $scope.cboFaction = '';
    $scope.cboDepart = '';
    $scope.cboDivision = '';
    $scope.keyword = '';

    $scope.persons = [];
    $scope.pager = null;

    $scope.movings = [];

    $scope.moving = {
        person_id: '',
        move_doc_no: '',
        move_doc_date: '',
        move_date: '',
        move_duty: '',
        move_faction: '',
        move_depart: '',
        move_division: '',
        move_reason: '',
        in_out: 'O',
        remark: ''
    };

    $scope.transferring = {
        person_id: '',
        transfer_date: '',
        transfer_doc_no: '',
        transfer_doc_date: '',
        transfer_to: '',
        transfer_reason: '',
        in_out: 'O',
        remark: ''
    };

    $scope.leaving = {
        person_id: '',
        leave_doc_no: '',
        leave_doc_date: '',
        leave_date: '',
        leave_type: '',
        leave_reason: '',
        remark: ''
    };

    $scope.getPersons = function() {
        $scope.loading = true;
        $scope.persons = [];
        $scope.pager = null;

        let faction = $scope.cboFaction ? $scope.cboFaction : '';
        let depart = $scope.cboDepart ? $scope.cboDepart : '';
        let division = $scope.cboDivision ? $scope.cboDivision : '';
        let keyword = $scope.keyword ? $scope.keyword : '';
        let status = $scope.cboStatus ? $scope.cboStatus : '';

        $http.get(`${CONFIG.baseUrl}/persons/search?faction=${faction}&depart=${depart}&division=${division}&name=${keyword}&status=${status}`)
        .then(function(res) {
            $scope.setPersons(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPersonsWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.persons = [];
        $scope.pager = null;
        $scope.loading = true;

        let faction = $scope.cboFaction ? $scope.cboFaction : '';
        let depart = $scope.cboDepart ? $scope.cboDepart : '';
        let division = $scope.cboDivision ? $scope.cboDivision : '';
        let keyword = $scope.keyword ? $scope.keyword : '';
        let status = $scope.cboStatus ? $scope.cboStatus : '';

        $http.get(`${url}&faction=${faction}&depart=${depart}&division=${division}&name=${keyword}&status=${status}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.setPersons = function(res) {
        const { data, ...pager } = res.data.persons;

        $scope.persons = data.map(person => {
            person.duty_of = person.duty_of.sort((a, b) => a.duty_id - b.duty_id);
            return person;
        });

        $scope.pager = pager;
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
    };

    $scope.edit = function(typeId) {
        console.log(typeId);

        window.location.href = CONFIG.baseUrl + '/persons/edit/' + typeId;
    };


    $scope.store = function(event, form) {
        event.preventDefault();
        $scope.loading = true;

        $http.post(CONFIG.baseUrl + '/persons/store', $scope.type)
        .then(function(res) {
            console.log(res);
            toaster.pop('success', "", 'บันทึกข้อมูลเรียบร้อยแล้ว !!!');

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            toaster.pop('error', "", 'พบข้อผิดพลาด !!!');

            $scope.loading = false;
        });

        document.getElementById(form).reset();
    };

    $scope.update = function(event, form) {
        event.preventDefault();
        $scope.loading = true;

        if(confirm("คุณต้องแก้ไขรายการหนี้เลขที่ " + $scope.type.type_id + " ใช่หรือไม่?")) {
            $scope.type.cate_id = $('#cate_id option:selected').val();

            $http.put(CONFIG.baseUrl + '/persons/update', $scope.type)
            .then(function(res) {
                console.log(res);
                toaster.pop('success', "", 'แก้ไขข้อมูลเรียบร้อยแล้ว !!!');

                $scope.loading = false;
            }, function(err) {
                console.log(err);
                toaster.pop('error', "", 'พบข้อผิดพลาด !!!');

                $scope.loading = false;
            });
        }

        setTimeout(function (){
            window.location.href = CONFIG.baseUrl + '/persons/list';
        }, 2000);        
    };

    $scope.delete = function(typeId) {
        console.log(typeId);
        $scope.loading = true;

        if(confirm("คุณต้องลบรายการหนี้เลขที่ " + typeId + " ใช่หรือไม่?")) {
            $http.delete(CONFIG.baseUrl + '/persons/delete/' +typeId)
            .then(function(res) {
                console.log(res);
                toaster.pop('success', "", 'ลบข้อมูลเรียบร้อยแล้ว !!!');
                $scope.getData();

                $scope.loading = false;
            }, function(err) {
                console.log(err);
                toaster.pop('error', "", 'พบข้อผิดพลาด !!!');
                $scope.loading = false;
            });
        }
    };

    $scope.calcAge = function(birthdate, type) {
        return moment().diff(moment(birthdate), type);
    };

    $scope.getMovings = (id) => {
        $scope.loading = true;

        $http.get(`${CONFIG.apiUrl}/persons/${id}/movings`)
        .then(res => {
            $scope.movings = res.data.movings;

            $scope.loading = false;
        }, (err) => {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.showMoveForm = function(e, type, faction, person_id) {
        e.preventDefault();
        $scope.moving.person_id = person_id;

        if (type == 'S') {
            $scope.onFactionSelected(faction);
            $scope.moving.move_faction = faction;

            $('#shiftForm').modal('show');
        } else if (type == 'M') {
            $('#moveForm').modal('show');
        }
    };

    $scope.move = (e) => {
        if(e) e.preventDefault();
        $scope.loading = true;

        console.log($scope.moving);

        $http.put(`${CONFIG.apiUrl}/persons/${$scope.moving.person_id}/move`, $scope.moving)
        .then(res => {
            console.log(res);

            /** Clear values */
            $scope.moving = {
                person_id: '',
                move_doc_no: '',
                move_doc_date: '',
                move_date: '',
                move_duty: '',
                move_faction: '',
                move_depart: '',
                move_division: '',
                move_reason: '',
                in_out: 'O',
                remark: '',
            };

            $('#moveForm').modal('hide');
            $('#shiftForm').modal('hide');

            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลเได้ !!!");
            }
        }, err => {
            console.log(err)
            $scope.loading = false;
        });
    };

    $scope.showTransferForm = function(e, nurse) {
        e.preventDefault();

        $scope.transfering.nurse = nurse;

        $('#transferForm').modal('show');
    };

    $scope.transfer = (e) => {
        if(e) e.preventDefault();

        console.log($scope.transferring);

        $http.put(`${CONFIG.apiUrl}/persons/${$scope.transferring.person_id}/transfer`, $scope.transferring)
        .then(res => {
            console.log(res);

            /** Clear values */
            $scope.transferring = {
                person_id: '',
                transfer_date: '',
                transfer_doc_no: '',
                transfer_doc_date: '',
                transfer_to: '',
                transfer_reason: '',
                in_out: 'O',
                remark: '',
            };

            $('#transferForm').modal('hide');
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลเได้ !!!");
            }
        }, err => {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.showLeaveForm = function(e, person) {
        e.preventDefault();

        $scope.leaving.person_id = person;

        $('#leaveForm').modal('show');
    };

    $scope.leave = (e) => {
        if(e) e.preventDefault();

        const id = $scope.leaving.person_id;

        $http.put(`${CONFIG.apiUrl}/persons/${id}/leave`, $scope.leaving)
        .then(res => {
            $scope.data = $scope.data.filter(person => person.person_id !== id);

            /** Clear values */
            $scope.leaving = {
                person_id: '',
                leave_doc_no: '',
                leave_doc_date: '',
                leave_date: '',
                leave_type: '',
                leave_reason: '',
                remark: ''
            };

            $('#leaveForm').modal('hide');

            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลเได้ !!!");
            }
        }, err => {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.status = (e, id, status) => {
        if(e) e.preventDefault();
        $scope.loading = true;
        console.log(id);

        $http.put(`${CONFIG.apiUrl}/persons/${id}/status`, { status })
        .then(res => {
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลเได้ !!!");
            }
        }, err => {
            console.log(err);
            $scope.loading = false;

            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลเได้ !!!");
        });
    };
});