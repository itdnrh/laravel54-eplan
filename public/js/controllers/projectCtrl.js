app.controller('projectCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;

    $scope.projects = [];
    $scope.cboStrategic = '';
    $scope.cboStrategy = '';
    $scope.cboKpi = '';
    $scope.cboFaction = '';
    $scope.cboDepart = '';
    $scope.txtKeyword = '';
    $scope.searchKey = '';
    $scope.isApproved = '';
    $scope.pager = null;
    $scope.persons = [];
    $scope.persons_pager = null;

    $scope.project = {
        id: '',
        project_no: '',
        project_name: '',
        project_type_id: '',
        year: '2566', //(moment().year() + 543).toString(),
        strategic_id: '',
        strategy_id: '',
        kpi_id: '',
        total_budget: '',
        total_budget_str: '',
        total_actual: '',
        total_actual_str: '',
        budget_src_id: '1',
        faction_id: '',
        owner_depart: '',
        owner_person: '',
        start_month: '',
        closed_date: '',
        attachment: '',
        remark: '',
    };

    /** ============================== Init Form elements ============================== */
    let dtpOptions = {
        autoclose: true,
        language: 'th',
        format: 'dd/mm/yyyy',
        thaiyear: true,
        todayBtn: true,
        todayHighlight: true
    };

    $('#received_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('show', function (e) {
            console.log(e);
        })
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $('#pay_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('show', function (e) {
            console.log(e);
        })
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $('#dtpDate')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('show', function (e) {
            console.log(e);
        })
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $('#closed_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('show', function (e) {
            console.log(e);
        })
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    const clearProject = function() {
        $scope.project = {
            id: '',
            project_no: '',
            project_name: '',
            project_type_id: '',
            year: '2566', //(moment().year() + 543).toString(),
            strategic_id: '',
            strategy_id: '',
            kpi_id: '',
            total_budget: '',
            total_budget_str: '',
            total_actual: '',
            total_actual_str: '',
            budget_src_id: '1',
            faction_id: '',
            owner_depart: '',
            owner_person: '',
            start_month: '',
            closed_date: '',
            attachment: '',
            remark: '',
        };
    };

    $scope.setIsApproved = function(e) {
        $scope.isApproved = e.target.checked;

        $scope.getAll(e);
    };

    /*
    |-----------------------------------------------------------------------------
    | Project CRUD operations
    |-----------------------------------------------------------------------------
    */
    $scope.getAll = function(event) {
        $scope.loading = true;
        $scope.projects = [];
        $scope.pager = null;

        let year        = $scope.cboYear === '' ? '' : $scope.cboYear;
        let strategic   = $scope.cboStrategic === '' ? '' : $scope.cboStrategic;
        let strategy    = !$scope.cboStrategy ? '' : $scope.cboStrategy;
        let kpi         = !$scope.cboKpi ? '' : $scope.cboKpi;
        let faction     = !$scope.cboFaction ? '' : $scope.cboFaction;
        let depart      = !$scope.cboDepart ? '' : $scope.cboDepart;
        let name        = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;
        let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let approved    = $scope.isApproved ? 'A' : '';

        $http.get(`${CONFIG.baseUrl}/projects/search?year=${year}&strategic=${strategic}&strategy=${strategy}&kpi=${kpi}&faction=${faction}&depart=${depart}&name=${name}&approved=${approved}&status=${status}`)
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
        let name        = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;
        let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let approved    = $scope.isApproved ? 'A' : '';

        $http.get(`${url}&year=${year}&strategic=${strategic}&strategy=${strategy}&kpi=${kpi}&faction=${faction}&depart=${depart}&name=${name}&approved=${approved}&status=${status}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getById = function(id, cb) {
        $http.get(`${CONFIG.apiUrl}/projects/${id}`)
        .then(function(res) {
            cb(res.data.project);
        }, function(err) {
            console.log(err);
        });
    };

    $scope.setEditControls = function(project) {
        if (project) {
            $scope.project.id               = project.id;
            $scope.project.project_no       = project.project_no;
            $scope.project.project_name     = project.project_name;
            $scope.project.kpi              = project.kpi;
            $scope.project.total_budget     = project.total_budget;
            $scope.project.total_budget_str = project.total_budget_str;
            $scope.project.total_actual     = project.total_actual;
            $scope.project.total_actual_str = project.total_actual_str;
            $scope.project.budget_src       = project.budget_src;

            $scope.project.approved         = project.approved;
            $scope.project.attachment       = project.attachment;
            $scope.project.remark           = project.remark;
            $scope.project.status           = project.status;

            /** Convert int value to string */
            $scope.project.year             = project.year.toString();
            $scope.project.start_month      = project.start_month.toString();
            $scope.project.strategic_id     = project.strategy.strategic_id.toString();
            $scope.project.strategy_id      = project.strategy_id.toString();
            $scope.project.kpi_id           = project.kpi_id ? project.kpi_id.toString() : '';
            $scope.project.project_type     = project.project_type;
            $scope.project.project_type_id  = project.project_type_id.toString();
            $scope.project.budget_src       = project.budget_src;
            $scope.project.budget_src_id    = project.budget_src_id.toString();
            $scope.project.owner_depart     = project.owner_depart.toString();

            if (project.depart) {
                $scope.project.depart           = project.depart;
                $scope.project.faction_id       = project.depart.faction_id.toString();

                $scope.onFactionSelected(project.depart.faction_id);
            }

            $scope.project.owner_person     = project.owner_person;
            $('#owner_person').val(project.owner_person);

            if (project.owner) {
                $scope.project.owner        = project.owner;
            }

            /** Generate departs and divisions data from plan */
            $scope.onStrategicSelected(project.strategy.strategic_id);
            $scope.onStrategySelected(project.strategy_id);
            $scope.onDepartSelected(project.owner_depart);
        }
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $scope.project.total_budget_str = StringFormatService.arabicNumberToText($scope.project.total_budget);
        $('#total_budget_str').val($scope.project.total_budget_str);

        $(`#${form}`).submit();
    }

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/projects/edit/${id}`;
    };

    $scope.update = function(e, form) {
        e.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขโครงการเลขที่ ${$scope.project.id} ใช่หรือไม่?`)) {
            $scope.project.total_budget_str = StringFormatService.arabicNumberToText($scope.project.total_budget);
            $('#total_budget_str').val($scope.project.total_budget_str);

            $(`#${form}`).submit();
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();
        $scope.loading = true;

        if(confirm(`คุณต้องลบโครงการเลขที่ ${id} ใช่หรือไม่?`)) {
            $http.post(`${CONFIG.baseUrl}/projects/delete/${id}`)
            .then(res => {
                console.log(res);

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลบข้อมูลเรียบร้อย !!!");

                    /** TODO: Reset project model */
                    $scope.setProjects(res);
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลบข้อมูลได้ !!!");
                }

                $scope.loading = false;
            }, err => {
                console.log(err);
                $scope.loading = false;
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลบข้อมูลได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };

    /*
    |-----------------------------------------------------------------------------
    | Person manipulation
    |-----------------------------------------------------------------------------
    */
    $scope.setCboDepartFromOwnerDepart = function(depart) {
        $scope.cboDepart = depart;
    };

    $scope.showPersonList = (_selectedMode) => {
        $('#persons-list').modal('show');

        $scope.getPersons();
    };

    $scope.getPersons = async () => {
        $scope.loading = true;
        $scope.persons = [];
        $scope.persons_pager = null;

        let depart = $scope.cboDepart == '' ? $scope.project.owner_depart : $scope.cboDepart;
        let name = $scope.searchKey == '' ? '' : $scope.searchKey;

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
        let name = $scope.searchKey == '' ? '' : $scope.searchKey;

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

    $scope.onSelectedPerson = (mode, person) => {
        if (person) {
            $scope.project.owner = person;
            $scope.project.owner_person = person.person_id;
            $('#owner_person').val(person.person_id);
        } else {
            $scope.project.owner = null;
            $scope.project.owner_person = '';
            $('#owner_person').val('');
        }

        $('#persons-list').modal('hide');
    };

    /*
    |-----------------------------------------------------------------------------
    | Project timeline process
    |-----------------------------------------------------------------------------
    */
    $scope.timeline = null;
    $scope.getTimline = (id) => {
        $scope.payments = [];
        $scope.loading = true;
        
        $http.get(`${CONFIG.apiUrl}/projects/${id}/timeline`)
        .then(res => {
            $scope.timeline = res.data.timeline;

            $scope.loading = false;
        }, err => {
            console.log(err);

            $scope.loading = false;
        });
    };

    $scope.storeTimeline = (projectId, fieldName) => {
        $scope.loading = true;

        $http.post(`${CONFIG.baseUrl}/projects/timeline`, { projectId, fieldName })
        .then(res => {
            $scope.timeline = res.data.timeline;

            $scope.loading = false;
        }, err => {
            console.log(err);

            $scope.loading = false;
        });
    };

    $scope.timelineFieldName = '';
    $scope.showTimeline = function(fieldName) {
        $scope.timelineFieldName = fieldName;

        $('#timeline-form').modal('show');
    };

    $scope.updateTimeline = (e, form, id) => {
        let data = { fieldName: $scope.timelineFieldName, value: $('#dtpDate').val() };

        if (confirm('คุณต้องการแก้ไขข้อมูล Timeline ใช่หรือไม่?')) {
            $scope.loading = true;

            $http.post(`${CONFIG.baseUrl}/projects/${id}/timeline`, data)
            .then(res => {
                $scope.timeline = res.data.timeline;

                /** Hide modal popup */
                $('#timeline-form').modal('hide');

                /** Reset date picker input's value to default */
                $('#dtpDate')
                    .datepicker(dtpOptions)
                    .datepicker('update', new Date());

                $scope.loading = false;
            }, err => {
                console.log(err);

                $scope.loading = false;
            });
        }

        $scope.timelineFieldName = '';
    };

    $scope.nextTimeline = (e, id, projectId, fieldName) => {
        e.preventDefault();

        if (!id || id == '') {
            toaster.pop('error', "ผลการตรวจสอบ", "โครงการยังไม่ผ่านขั้นตอนการส่งงานแผน !!!");
            return;
        }

        $scope.loading = true;

        $http.post(`${CONFIG.baseUrl}/projects/timeline`, { id, fieldName, projectId })
        .then(res => {
            $scope.timeline = res.data.timeline;

            $scope.loading = false;
        }, err => {
            console.log(err);

            $scope.loading = false;
        });
    };

    /*
    |-----------------------------------------------------------------------------
    | Project payment process
    |-----------------------------------------------------------------------------
    */
    $scope.payments = [];
    $scope.totalPayment = 0;
    $scope.payment = {
        id: '',
        desc: '',
        project_id: '',
        received_date: '',
        pay_date: '',
        net_total: '',
        have_aar: 0,
        remark: '',
        user: ''
    };

    $scope.clearPayment = function() {
        $scope.payment.id = '';
        $scope.payment.desc = '';
        $scope.payment.project_id = '',
        $scope.payment.received_date = '',
        $scope.payment.pay_date = '',
        $scope.payment.net_total = '',
        $scope.payment.have_aar = 0,
        $scope.payment.remark = '',
        $scope.payment.user = '';

        $('#received_date')
            .datepicker(dtpOptions)
            .datepicker('update', new Date());

        $('#pay_date')
            .datepicker(dtpOptions)
            .datepicker('update', new Date());
    };

    $scope.calculateTotalPayment = (payments) => {
        return payments.reduce((sum, pay) => {
            return sum = sum + pay.net_total;
        }, 0)
    };

    $scope.getPayments = (id) => {
        $scope.payments = [];
        $scope.loading = true;
        
        $http.get(`${CONFIG.apiUrl}/projects/${id}/payments`)
        .then(res => {
            $scope.payments = res.data.payments;
            $scope.totalPayment = $scope.calculateTotalPayment(res.data.payments);

            $scope.loading = false;
        }, err => {
            console.log(err);

            $scope.loading = false;
        });
    };

    $scope.showPaymentForm = (e, projectId, payment) => {
        e.preventDefault();

        $scope.payment.project_id = projectId;

        /** ถ้าเป็นการแก้ไขรายการ */
        if (payment) {
            $scope.payment.id = payment.id;
            $scope.payment.net_total = payment.net_total;
            $scope.payment.have_aar = payment.have_aar;
            $scope.payment.remark = payment.remark;

            $('#received_date')
                .datepicker(dtpOptions)
                .datepicker('update', moment(payment.received_date).toDate());

            $('#pay_date')
                .datepicker(dtpOptions)
                .datepicker('update', moment(payment.pay_date).toDate());
        }

        $('#payment-form').modal('show');
    };

    $scope.onSubmitPayment = (e, form, paymentId) => {
        e.preventDefault();

        if (form.$invalid) {
            toaster.pop('error', "ผลการตรวจสอบ", "คุณกรอกข้อมูลไม่ครบ !!!");
            return;
        }

        $scope.loading = true;
        $scope.payment.user = $('#user').val();

        if (paymentId) {
            /** กรณีแก้ไขข้อมูล */
            $http.post(`${CONFIG.baseUrl}/projects/${$scope.payment.project_id}/${paymentId}/payments`, $scope.payment)
            .then(res => {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "แก้ไขรายการเรียบร้อย !!!");

                    $scope.payments = res.data.payments;
                    $scope.totalPayment = $scope.calculateTotalPayment(res.data.payments);

                    $scope.clearPayment();

                    form.$submitted = false;
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถแก้ไขรายการได้ !!!");
                }

                $('#payment-form').modal('hide');

                $scope.loading = false;
            }, err => {
                console.log(err);

                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถแก้ไขรายการได้ !!!");

                $scope.loading = false;
            });
        } else {
            /** กรณีเพิ่มข้อมูล */
            $http.post(`${CONFIG.baseUrl}/projects/${$scope.payment.project_id}/payments`, $scope.payment)
            .then(res => {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "เพิ่มรายการเรียบร้อย !!!");

                    $scope.payments = res.data.payments;
                    $scope.totalPayment = $scope.calculateTotalPayment(res.data.payments);

                    $scope.clearPayment();
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถเพิ่มรายการได้ !!!");
                }

                $('#payment-form').modal('hide');

                $scope.loading = false;
            }, err => {
                console.log(err);

                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถเพิ่มรายการได้ !!!");

                $scope.loading = false;
            });
        }
    };

    $scope.deletePayment = (e, projectId, paymentId) => {
        e.preventDefault();

        if (confirm('คุณต้องการลบรายการเบิกจ่ายเงินโครงการใช่หรือไม่?')) {
            $scope.loading = true;

            $http.post(`${CONFIG.baseUrl}/projects/${projectId}/${paymentId}/payments/delete`)
            .then(res => {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "ลบรายการเรียบร้อย !!!");

                    $scope.payments = res.data.payments;
                    $scope.totalPayment = $scope.calculateTotalPayment(res.data.payments);
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถลบรายการได้ !!!");
                }

                $scope.loading = false;
            }, err => {
                console.log(err);

                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถลบรายการได้ !!!");

                $scope.loading = false;
            });
        } else {
            $scope.loading = false;
        }
    };

    /*
    |-----------------------------------------------------------------------------
    | Project moidfication process
    |-----------------------------------------------------------------------------
    */
    $scope.modification = {
        id: '',
        project_id: '',
        doc_no: '',
        doc_date: '',
        modification_type_id: '',
        desc: ''
    };

    $scope.showModificationForm = (e, id) => {
        $scope.modification.project_id = id;

        $('#modification-form').modal('show');
    };

    $scope.onSubmitModification = (e, form, id) => {
        e.preventDefault();

        if (form.$invalid) {
            toaster.pop('error', "ผลการตรวจสอบ", "คุณกรอกข้อมูลไม่ครบ !!!");
            return;
        }

        if (!id) {
            $scope.loading = true;

            /** Create FormData object */
            let frmModification = new FormData();
            frmModification.append('doc_no', $scope.modification.doc_no);
            frmModification.append('doc_date', $scope.modification.doc_date);
            frmModification.append('modification_type_id', $scope.modification.modification_type_id);
            frmModification.append('desc', $scope.modification.desc);

            if ($('#attachment')[0]) {
                frmModification.append('attachment', $('#attachment')[0].files[0]);
            }

            $http.post(`${CONFIG.baseUrl}/projects/${$scope.modification.project_id}/modify`, frmModification, {
                headers: { 'Content-Type': undefined },
            })
            .then(res => {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "บันทึกขอเปลี่ยนแปลงเรียบร้อย !!!");
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถบันทึกขอเปลี่ยนแปลงได้ !!!");
                }

                $('#modification-form').modal('hide');

                $scope.loading = false;
            }, err => {
                console.log(err);

                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถบันทึกขอเปลี่ยนแปลงได้ !!!");

                $scope.loading = false;
            });
        } else {
            if (confirm('คุณต้องการแก้ไขขอเปลี่ยนแปลงใช่หรือไม่?')) {
                $scope.loading = true;

                $http.post(`${CONFIG.baseUrl}/projects/${id}/close`, data)
                .then(res => {
                    if (res.data.status == 1) {
                        toaster.pop('success', "ผลการทำงาน", "แก้ไขขอเปลี่ยนแปลงเรียบร้อย !!!");
                    } else {
                        toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถแก้ไขขอเปลี่ยนแปลงได้ !!!");
                    }

                    $('#modification-form').modal('hide');

                    $scope.loading = false;
                }, err => {
                    console.log(err);

                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถแก้ไขขอเปลี่ยนแปลงได้ !!!");

                    $scope.loading = false;
                });
            } else {
                $scope.loading = false;
            }
        }
    };

    /*
    |-----------------------------------------------------------------------------
    | Project closing process
    |-----------------------------------------------------------------------------
    */
    $scope.showCloseProjectForm = () => {
        $('#close-form').modal('show');
    };

    $scope.onCloseProject = (e, form, id) => {
        e.preventDefault();

        if (form.$invalid) {
            toaster.pop('error', "ผลการตรวจสอบ", "คุณกรอกข้อมูลไม่ครบ !!!");
            return;
        }

        if (confirm('คุณต้องการบันทึกปิดโครงการใช่หรือไม่?')) {
            $scope.loading = true;
            
            let data = {
                total_actual: $('#total_actual').val(),
                closed_date: $('#closed_date').val(),
                user: $('#user').val(),
            };

            $http.post(`${CONFIG.baseUrl}/projects/${id}/close`, data)
            .then(res => {
                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "บันทึกปิดโครงการเรียบร้อย !!!");
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถบันทึกปิดโครงการได้ !!!");
                }

                $('#close-form').modal('hide');

                $scope.loading = false;
            }, err => {
                console.log(err);

                toaster.pop('error', "ผลการตรวจสอบ", "พบข้อผิดพลาด ไม่สามารถบันทึกปิดโครงการได้ !!!");

                $scope.loading = false;
            });
        } else {
            $scope.loading = false;
        }
    };

    /*
    |-----------------------------------------------------------------------------
    | Project exporting process
    |-----------------------------------------------------------------------------
    */
    $scope.exportListToExcel = function(e) {
        e.preventDefault();

        if($scope.projects.length == 0) {
            toaster.pop('warning', "", "ไม่พบข้อมูล !!!");
        } else {
            let year        = $scope.cboYear === '' ? '' : $scope.cboYear;
            let strategic   = $scope.cboStrategic === '' ? '' : $scope.cboStrategic;
            let strategy    = !$scope.cboStrategy ? '' : $scope.cboStrategy;
            let kpi         = !$scope.cboKpi ? '' : $scope.cboKpi;
            let faction     = !$scope.cboFaction ? '' : $scope.cboFaction;
            let depart      = !$scope.cboDepart ? '' : $scope.cboDepart;
            let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
            let name        = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;
            
            window.location.href = `${CONFIG.baseUrl}/projects/excel?year=${year}&strategic=${strategic}&strategy=${strategy}&kpi=${kpi}&faction=${faction}&depart=${depart}&name=${name}&status=${status}`;
        }
    };
});