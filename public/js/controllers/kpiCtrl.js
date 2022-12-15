app.controller('kpiCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService, PaginateService) {
/** ################################################################################## */
    $scope.loading = false;

    $scope.cboStrategic = '';
    $scope.cboStrategy = '';
    $scope.cboKpi = '';
    $scope.cboFaction = '';
    $scope.cboDepart = '';
    $scope.txtKeyword = '';
    $scope.searchKey = '';

    $scope.kpis = [];
    $scope.pager = null;

    $scope.persons = [];
    $scope.persons_pager = null;

    $scope.kpi = {
        id: '',
        kpi_no: '',
        kpi_name: '',
        strategy_id: '',
        year: '',
        target_total: '',
        faction_id: '',
        owner_depart: '',
        owner: null,
        owner_person: '',
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

    // $('#received_date')
    //     .datepicker(dtpOptions)
    //     .datepicker('update', new Date())
    //     .on('show', function (e) {
    //         console.log(e);
    //     })
    //     .on('changeDate', function(event) {
    //         console.log(event.date);
    //     });

    $('#kpi_no').prop('disabled', true);

    const clearKpi = function() {
        $scope.kpi = {
            id: '',
            kpi_no: '',
            kpi_name: '',
            strategy_id: '',
            year: '',
            target_total: '',
            faction_id: '',
            owner_depart: '',
            owner_person: '',
            attachment: '',
            remark: '',
        };
    };

    $scope.getAll = function(event) {
        $scope.loading = true;
        $scope.kpis = [];
        $scope.pager = null;

        let year = $scope.cboYear === '' ? '' : $scope.cboYear;
        let strategic = $scope.cboStrategic === '' ? '' : $scope.cboStrategic;
        let strategy = $scope.cboStrategy === '' ? '' : $scope.cboStrategy;
        let faction = $scope.cboFaction === '' ? '' : $scope.cboFaction;
        let depart = $scope.cboDepart === '' ? '' : $scope.cboDepart;
        let status = $scope.cboStatus === '' ? '' : $scope.cboStatus;
        let name = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;

        $http.get(`${CONFIG.apiUrl}/kpis?year=${year}&strategic=${strategic}&strategy=${strategy}&faction=${faction}&depart=${depart}&name=${name}&status=${status}`)
        .then(function(res) {
            $scope.setKpis(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setKpis = function(res) {
        const { data, ...pager } = res.data.kpis;

        $scope.kpis = data;
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

        $http.get(`${CONFIG.baseUrl}/persons/search?depart=${depart}&name=${keyword}`)
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

        $http.get(`${url}&depart=${depart}&name=${keyword}`)
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
            $scope.kpi.owner = person;
            $scope.kpi.owner_person = person.person_id;
            $('#owner_person').val(person.person_id);
        } else {
            $scope.kpi.owner = null;
            $scope.kpi.owner_person = '';
            $('#owner_person').val('');
        }

        $('#persons-list').modal('hide');
    };

    $scope.getById = function(id, cb) {
        $http.get(`${CONFIG.apiUrl}/kpis/${id}`)
        .then(function(res) {
            cb(res.data.kpi);
        }, function(err) {
            console.log(err);
        });
    }

    $scope.setEditControls = function(kpi) {
        if (kpi) {
            $scope.kpi.id           = kpi.id;
            $scope.kpi.kpi_no       = kpi.kpi_no;
            $scope.kpi.kpi_name     = kpi.kpi_name;
            $scope.kpi.total_target = kpi.total_target;
            $scope.kpi.owner_depart = kpi.owner_depart;
            $scope.kpi.depart       = kpi.depart;
            $scope.kpi.owner_person = kpi.owner_person;
            $scope.kpi.owner        = kpi.owner;
            $scope.kpi.attachment   = kpi.attachment;
            $scope.kpi.remark       = kpi.remark;
            $scope.kpi.status       = kpi.status;

            /** Convert int value to string */
            $scope.kpi.year         = kpi.year.toString();
            $scope.kpi.strategy_id  = kpi.strategy_id.toString();
            $scope.kpi.owner_depart = kpi.owner_depart.toString();
        }
    };

    $scope.store = function(event, form) {
        event.preventDefault();

        $(`#${form}`).submit();
    }

    $scope.edit = function(id) {
        window.location.href = `${CONFIG.baseUrl}/kpis/edit/${id}`;
    };

    $scope.update = function(e) {
        e.preventDefault();
    
        if(confirm(`คุณต้องแก้ไขโครงการเลขที่ ${$scope.kpi.id} ใช่หรือไม่?`)) {
            $('#frmEditKpi').submit();
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();

        if(confirm(`คุณต้องลบโครงการเลขที่ ${id} ใช่หรือไม่?`)) {
            $http.delete(`${CONFIG.apiUrl}/kpis/${id}`)
            .then(res => {
                console.log(res);
            }, err => {
                console.log(err);
            });
        } else {
            $scope.loading = false;
        }
    };
});