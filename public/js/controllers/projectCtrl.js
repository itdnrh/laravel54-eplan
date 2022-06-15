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
    $scope.pager = null;
    $scope.persons = [];
    $scope.persons_pager = null;

    $scope.project = {
        project_id: '',
        project_no: '',
        project_name: '',
        project_type_id: '',
        year: '',
        strategic_id: '',
        strategy_id: '',
        kpi_id: '',
        total_budget: '',
        total_actual: '',
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
            year: '',
            strategic_id: '',
            strategy_id: '',
            kpi_id: '',
            total_budget: '',
            total_actual: '',
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

        let year = $scope.cboYear === '' ? '' : $scope.cboYear;
        let strategic = $scope.cboStrategic === '' ? '' : $scope.cboStrategic;
        let strategy = $scope.cboStrategy === '' ? '' : $scope.cboStrategy;
        let kpi = $scope.cboKpi === '' ? '' : $scope.cboKpi;
        let faction = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let name = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;

        $http.get(`${CONFIG.apiUrl}/projects?year=${year}&strategic=${strategic}&strategy=${strategy}&kpi=${kpi}&faction=${faction}&depart=${depart}&name=${name}&status=${status}`)
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

    $scope.getDataWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.projects = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let cate    = $scope.cboCategory === '' ? '' : $scope.cboCategory;
        let depart  = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status  = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let menu    = $scope.cboMenu === '' ? '' : $scope.cboMenu;

        $http.get(`${url}&year=${year}&cate=${cate}&status=${status}&depart=${depart}&menu=${menu}`)
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

    $scope.getPersons = async () => {
        $scope.loading = true;
        $scope.persons = [];
        $scope.persons_pager = null;

        let depart = $scope.cboDepart == '' ? '' : $scope.cboDepart;
        let keyword = $scope.searchKey == '' ? '' : $scope.searchKey;

        $http.get(`${CONFIG.baseUrl}/persons/search?depart=${depart}&searchKey=${keyword}`)
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
        let keyword = $scope.searchKey == '' ? '' : $scope.searchKey;

        $http.get(`${url}&depart=${depart}&searchKey=${keyword}`)
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
            $scope.project.budget_src       = project.budget_src;
            $scope.project.owner_depart     = project.owner_depart;
            $scope.project.depart           = project.depart;
            $scope.project.owner_person     = project.owner_person;
            $scope.project.owner            = project.owner;
            $scope.project.start_month      = $scope.monthLists.find(m => m.id == project.start_month).name;
            $scope.project.approved         = project.approved;
            $scope.project.attachment       = project.attachment;
            $scope.project.remark           = project.remark;
            $scope.project.status           = project.status;

            /** Convert int value to string */
            $scope.project.year             = project.year.toString();
            $scope.project.kpi_id           = project.kpi_id.toString();
            $scope.project.project_type_id  = project.project_type_id.toString();
            $scope.project.budget_src_id    = project.budget_src_id.toString();
            $scope.project.owner_depart     = project.owner_depart.toString();
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

    $scope.getPayments = (id) => {
        $scope.payments = [];
        $scope.loading = true;
        
        $http.get(`${CONFIG.apiUrl}/projects/${id}/payments`)
        .then(res => {
            $scope.payments = res.data.payments;

            $scope.totalPayment = res.data.payments.reduce((sum, pay) => {
                return sum = sum + pay.net_total;
            }, 0)

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

            $scope.loading = false;
        }, err => {
            console.log(err);

            $scope.loading = false;
        });

        $('#payment-form').modal('hide');
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $(`#${form}`).submit();
    }

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/projects/edit/${id}`;
    };

    $scope.update = function(e) {
        e.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขโครงการเลขที่ ${$scope.project.project_id} ใช่หรือไม่?`)) {
            $('#frmEditProject').submit();
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();

        if(confirm(`คุณต้องลบโครงการเลขที่ ${id} ใช่หรือไม่?`)) {
            $http.delete(`${CONFIG.apiUrl}/projects/${id}`)
            .then(res => {
                console.log(res);
            }, err => {
                console.log(err);
            });
        }
    };
});