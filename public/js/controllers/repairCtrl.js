app.controller('repairCtrl', function(CONFIG, $rootScope, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;

    $scope.searchKey = '';
    $scope.txtDesc = '';

    $scope.supports = [];
    $scope.pager = [];

    $scope.plans = [];
    $scope.plans_pager = null;

    $scope.items = [];
    $scope.items_pager = null;

    $scope.persons = [];
    $scope.persons_pager = null;

    $scope.support = {
        doc_no: '',
        doc_prefix: '',
        doc_date: '',
        topic: '',
        depart_id: '',
        division_id: '',
        year: '2566', //(moment().year() + 543).toString(),
        plan_type_id: '3',
        plan_id: '',
        total: '',
        contact_detail: '',
        contact_person: '',
        reason: '',
        remark: '',
        details: [],
        removed: [],
        spec_committee: [],
        env_committee: [],
        insp_committee: [],
    };

    $scope.newItem = {
        desc: '',
        price_per_unit: '',
        unit: null,
        unit_id: '9',
        amount: '1',
        sum_price: ''
    };

    $scope.spec = {
        repair_type: '',
        parcel_no: '',
        reg_no: '',
        desc: '',
        cause: ''
    };

    /** ============================== Init Form elements ============================== */
    let dtpDateOptions = {
        autoclose: true,
        language: 'th',
        format: 'dd/mm/yyyy',
        thaiyear: true,
        todayBtn: true,
        todayHighlight: true
    };
    $('#po_date')
        .datepicker(dtpDateOptions)
        .datepicker('update', new Date())
        .on('show', function (e) {
            console.log(e);
        })
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $scope.clearNewItem = () => {
        $scope.newItem = {
            desc: '',
            price_per_unit: '',
            unit_id: '9',
            amount: '1',
            sum_price: ''
        };
    };

    $scope.getAll = function() {
        $scope.loading = true;
        $scope.supports = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let doc_no  = $scope.searchKey === '' ? '' : $scope.searchKey;
        let desc    = $scope.txtDesc === '' ? '' : $scope.txtDesc;
        let depart  = $('#user').val() == '1300200009261' ? '' : $('#depart').val();

        $http.get(`${CONFIG.baseUrl}/repairs/search?year=${year}&stype=2&depart=${depart}&doc_no=${doc_no}&desc=${desc}&status=0-5`)
        .then(function(res) {
            $scope.setSupports(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.getRepairsWithUrl = function(e, url, cb) {
		/** Check whether parent of clicked a tag is .disabled just do nothing */
		if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.supports = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let doc_no  = $scope.searchKey === '' ? '' : $scope.searchKey;
        let desc    = $scope.txtDesc === '' ? '' : $scope.txtDesc;
        let depart  = $('#user').val() == '1300200009261' ? '' : $('#depart').val();

        $http.get(`${url}&year=${year}&stype=2&depart=${depart}&doc_no=${doc_no}&desc=${desc}&status=0-5`)
        .then(function(res) {
            $scope.setSupports(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setSupports = function(res) {
        const { data, ...pager } = res.data.supports;

        $scope.supports = data;
        $scope.pager = pager;
    };

    $scope.calculateSumPrice = function(price, amount) {
        $scope.newItem.sum_price = parseFloat($scope.currencyToNumber(price)) * parseFloat($scope.currencyToNumber(amount));
    };

    $scope.showSpecForm = function() {
        if ($scope.support.plan_id == '') {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณาระบุรายการแผนจ้างบริการก่อน !!!");
        } else {
            $('#spec-form').modal('show');
        }
    };

    $scope.addSpec = function(e, form) {
        if (form.$invalid) {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณากรอกข้อมูลให้ครบ !!!");
            return;
        }

        if ($scope.spec.repair_type == 1) {
            $scope.newItem.desc = `${$scope.spec.desc}, หมายเลขครุภัณฑ์: ${$scope.spec.parcel_no}, รายละเอียดการซ่อม: ${$scope.spec.cause}`
        } else if ($scope.spec.repair_type == 2) {
            $scope.newItem.desc = `รถราชการ (${$scope.spec.desc}), ทะเบียน: ${$scope.spec.reg_no}, รายละเอียดการซ่อม: ${$scope.spec.cause}`
        } else {
            $scope.newItem.desc = `${$scope.spec.desc}, รายละเอียดการซ่อม: ${$scope.spec.cause}`
        }

        $('#spec-form').modal('hide');
    };

    $scope.calculateTotal = () => {
        let total = 0;

        total = $scope.support.details.reduce((sum, curVal) => {
            return sum = sum + $scope.currencyToNumber(curVal.sum_price);
        }, 0);

        $scope.support.total = total;
        $('#total').val(total);
    };

    $scope.addItem = () => {
        console.log($scope.newItem);
        if ($scope.newItem.desc == '' || $scope.newItem.sum_price == '') {
            toaster.pop('error', "ผลการตรวจสอบ", "กรุณาระบุรายละเอีนดการจ้างซ่อมก่อน !!!");
        } else {
            $scope.support.details.push({ ...$scope.newItem });
    
            $scope.calculateTotal();
            $scope.clearNewItem();
        }
    };

    $scope.removeOrderItem = (selectedIndex) => {
        const rm = $scope.support.details.find((d, index) => index === selectedIndex);

        if (rm) {
            $scope.support.removed = [...new Set([...$scope.support.removed, rm.id])];
        }

        $scope.support.details = $scope.support.details.filter((d, index) => index !== selectedIndex);
        $scope.calculateTotal();
    };

    $scope.showPersonList = (_selectedMode) => {
        /** Set default depart of persons list to same user's depart */
        $scope.cboDepart = $('#depart_id').val();

        $('#persons-list').modal('show');

        $scope.getPersons();

        $scope.selectedMode = _selectedMode;
    };

    $scope.getPersons = async () => {
        $scope.loading = true;
        $scope.persons = [];
        $scope.persons_pager = null;

        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;
        let name = !$scope.searchKey ? '' : $scope.searchKey;

        $http.get(`${CONFIG.baseUrl}/persons/search?depart=${depart}&name=${name}`)
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

        $scope.loading = true;
        $scope.persons = [];
        $scope.persons_pager = null;

        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;
        let name = !$scope.searchKey ? '' : $scope.searchKey;

        $http.get(`${url}&depart=${depart}&name=${name}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setPersons = function(res) {
        const { data, ...pager } = res.data.persons;

        $scope.persons = data;
        $scope.persons_pager = pager;
    };

    $scope.selectedMode = '';
    $scope.onSelectedPerson = (mode, person) => {
        if (person) {
            if (parseInt(mode) === 1) {
                $scope.support.spec_committee.push(person)
            } else if (parseInt(mode) == 2) {
                $scope.support.insp_committee.push(person)
            } else if (parseInt(mode) == 3) {
                $scope.support.env_committee.push(person)
            } else {
                $scope.support.contact_detail = person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname + ' โทร.' + person.person_tel;
                $scope.support.contact_person = person.person_id;
            }
        }

        $('#persons-list').modal('hide');
        $scope.selectedMode = '';
    };

    $scope.removePersonItem = (mode, person) => {
        console.log(person);
        console.log(parseInt(mode));
        if (parseInt(mode) === 1) {
            $scope.support.spec_committee = $scope.support.spec_committee.filter(sc => {
                return sc.person_id !== person.person_id
            });
        } else if (parseInt(mode) === 2) {
            $scope.support.insp_committee = $scope.support.insp_committee.filter(ic => {
                return ic.person_id !== person.person_id
            });
        } else if (parseInt(mode) === 3) {
            $scope.support.env_committee = $scope.support.env_committee.filter(ic => {
                return ic.person_id !== person.person_id
            });
        } else {

        }
    };

    $scope.getById = function(id, cb) {
        $scope.loading = true;

        $http.get(`${CONFIG.apiUrl}/supports/${id}`)
        .then(function(res) {
            cb(res.data.support, res.data.committees);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setEditControls = function(support, committees) {
        if (support) {
            $scope.support.id               = support.id;

            if (support.doc_no) {
                const [prefix, doc_no]      = support.doc_no.split("/");
                $scope.support.doc_prefix   = prefix;
                $scope.support.doc_no       = doc_no;
            }

            $scope.support.doc_date         = support.doc_date ? StringFormatService.convFromDbDate(support.doc_date) : '';
            $scope.support.topic            = support.topic;
            $scope.support.total            = support.total;
            $scope.support.reason           = support.reason;
            $scope.support.remark           = support.remark;
            $scope.support.contact_person   = support.contact.person_id;
            $scope.support.contact_detail   = `${support.contact.person_firstname} ${support.contact.person_lastname} โทร.${support.contact.person_tel}`;
            $scope.support.details          = support.details;
            $scope.support.plan_id          = support.details.length > 0 ? support.details[0].plan_id.toString() : '';
            $scope.support.status           = support.status;

            $scope.support.year             = support.year.toString();
            $scope.support.plan_type_id     = support.plan_type_id.toString();
            $scope.support.depart_id        = support.depart_id.toString();
            $scope.support.division_id      = support.division_id ? support.division_id.toString() : '';

            /** Set each committees by filtering from responsed committees data */
            $scope.support.spec_committee   = committees
                                                .filter(com => com.committee_type_id == 1)
                                                .map(com => com.person);
            $scope.support.insp_committee   = committees
                                                .filter(com => com.committee_type_id == 2)
                                                .map(com => com.person);
            $scope.support.env_committee    = committees
                                                .filter(com => com.committee_type_id == 3)
                                                .map(com => com.person);

            /** Set value of .select2 dropdown input */
            $('#plan_id').val($scope.support.plan_id).trigger('change.select2');
            /** Set value of datepicker */
            $('#doc_date').datepicker(dtpDateOptions).datepicker('update', moment(support.doc_date).toDate());
        }
    };

    $scope.showSendForm = function(support) {
        if (support) {
            $('#support-from').modal('show');
        }
    };

    $scope.send = function(e) {
        $scope.loading = true;

        $http.post(`${CONFIG.baseUrl}/supports/send`, $scope.support)
        .then(function(res) {
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "ส่งบันทึกขอจ้างซ่อมเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถส่งบันทึกขอจ้างซ่อมได้ !!!");
            }
        }, function(err) {
            $scope.loading = false;

            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถส่งบันทึกขอจ้างซ่อมได้ !!!");
        });
    };

    $scope.onValidateForm = function(e, cb) {
        e.preventDefault();

        $scope.support.depart_id = $('#depart_id').val();
        $scope.support.division_id = $('#division_id').val();

        $rootScope.formValidate(e, '/supports/validate', $scope.support, 'frmNewSupport', cb)
    };

    $scope.cancel = function(e, id) {
        $scope.loading = true;

        if(confirm(`คุณต้องการยกเลิกการส่งบันทึกขอจ้างซ่อม รหัส ${id} ใช่หรือไม่?`)) {
            $http.put(`${CONFIG.apiUrl}/supports/${id}/cancel-sent`, { status: 0 })
            .then(function(res) {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ยกเลิกส่งบันทึกขอจ้างซ่อมเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/repairs/list`;
                } else {
                    toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถยกเลิกส่งบันทึกขอจ้างซ่อมได้ !!!");
                }

                $scope.loading = false;
            }, function(err) {
                $scope.loading = false;

                console.log(err);
                toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถยกเลิกส่งบันทึกขอจ้างซ่อมได้ !!!");
            });
        }
    };

    $scope.store = function() {
        $scope.loading = true;

        /** Set user props of support model by logged in user */
        $scope.support.user = $('#user').val();

        $http.post(`${CONFIG.baseUrl}/repairs/store`, $scope.support)
        .then(function(res) {
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");

                window.location.href = `${CONFIG.baseUrl}/repairs/list`;
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ !!!");
            }
        }, function(err) {
            $scope.loading = false;

            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ !!!");
        });
    };

    $scope.update = function(e, form) {
        e.preventDefault();

        if(confirm(`คุณต้องแก้ไขบันทึกขอจ้างซ่อม รหัส ${$scope.support.id} ใช่หรือไม่?`)) {
            $scope.loading = true;

            /** Set user props of support model by logged in user */
            $scope.support.user = $('#user').val();

            $http.post(`${CONFIG.baseUrl}/repairs/update/${$scope.support.id}`, $scope.support)
            .then(function(res) {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/repairs/list`;
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถแก้ไขข้อมูลได้ !!!");
                }
            }, function(err) {
                $scope.loading = false;

                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถแก้ไขข้อมูลได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();

        if(confirm(`คุณต้องลบบันทึกขอจ้างซ่อม รหัส ${id} ใช่หรือไม่?`)) {
            $scope.loading = true;

            $http.post(`${CONFIG.baseUrl}/repairs/delete/${id}`)
            .then(res => {
                $scope.loading = false;
                console.log(res);

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลบข้อมูลเรียบร้อย !!!");

                    window.location.href = `${CONFIG.baseUrl}/repairs/list`;
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถลบข้อมูลได้ !!!");
                }
            }, err => {
                $scope.loading = false;
                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถลบข้อมูลได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.setTopicByPlanType = function() {
        $scope.support.topic = `ขอรับการสนับสนุน${$('#plan_id option:selected').text().trim()}`;
    };
});