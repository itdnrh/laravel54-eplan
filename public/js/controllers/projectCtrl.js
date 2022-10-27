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
    $scope.pager = null;
    $scope.persons = [];
    $scope.persons_pager = null;

    $scope.project = {
        project_id: '',
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

    const clearProject = function() {
        $scope.project = {
            project_id: '',
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
            attachment: '',
            remark: '',
        };
    };

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
        let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let name        = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;

        $http.get(`${CONFIG.baseUrl}/projects/search?year=${year}&strategic=${strategic}&strategy=${strategy}&kpi=${kpi}&faction=${faction}&depart=${depart}&name=${name}&status=${status}`)
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
        let status      = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let name        = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;

        $http.get(`${url}&year=${year}&strategic=${strategic}&strategy=${strategy}&kpi=${kpi}&faction=${faction}&depart=${depart}&name=${name}&status=${status}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.showPersonList = (_selectedMode) => {
        $('#persons-list').modal('show');

        $scope.getPersons();
    };

    $scope.setCboDepartFromOwnerDepart = function(depart) {
        $scope.cboDepart = depart;
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

    $scope.getById = function(id, cb) {
        $http.get(`${CONFIG.apiUrl}/projects/${id}`)
        .then(function(res) {
            cb(res.data.project);
        }, function(err) {
            console.log(err);
        });
    }

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
            $scope.project.project_type_id  = project.project_type_id.toString();
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

    $scope.updateTimeline = (id, projectId, fieldName) => {
        $http.post(`${CONFIG.baseUrl}/projects/${projectId}/${id}/timeline`, { fieldName })
        .then(res => {
            $scope.timeline = res.data.timeline;

            $scope.loading = false;
        }, err => {
            console.log(err);

            $scope.loading = false;
        });
    };

    $scope.payments = [];
    $scope.totalPayment = 0;
    $scope.newPayment = {
        project_id: '',
        received_date: '',
        pay_date: '',
        net_total: '',
        have_aar: '0',
        remark: '',
        user: ''
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

    $scope.showPaymentForm = () => {
        $('#payment-form').modal('show');
    };

    $scope.createNewPayment = (e, id) => {
        $scope.newPayment.user = $('#user').val();

        $http.post(`${CONFIG.baseUrl}/projects/${id}/payments`, $scope.newPayment)
        .then(res => {
            console.log(res);
            $scope.payments = res.data.payments;
            $scope.totalPayment = $scope.calculateTotalPayment(res.data.payments);

            $scope.loading = false;
        }, err => {
            console.log(err);

            $scope.loading = false;
        });

        $('#payment-form').modal('hide');
    };
    
    $scope.showCloseProjectForm = () => {
        $('#close-form').modal('show');
    };

    $scope.onCloseProject = () => {

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