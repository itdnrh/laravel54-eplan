app.controller('utilityCtrl', function(CONFIG, $rootScope, $scope, $http, toaster, StringFormatService, ChartService) {
/** ################################################################################## */
    $scope.loading = false;

    $scope.cboUtilityType = '';

    $scope.utilities = [];
    $scope.pager = [];

    $scope.plans = [];
    $scope.plans_pager = null;

    $scope.items = [];
    $scope.items_pager = null;

    $scope.persons = [];
    $scope.persons_pager = null;

    $scope.utility = {
        bill_no: '',
        bill_date: '',
        supplier_id: '',
        supplier: null,
        year: '',
        month: '',
        utility_type_id: '',
        utility_type: null,
        desc: '',
        quantity: '',
        net_total: '',
        remark: '',
        user: ''
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
    $('#bill_date')
        .datepicker(dtpOptions)
        .datepicker('update', new Date())
        .on('show', function (e) {
            console.log(e);
        })
        .on('changeDate', function(event) {
            console.log(event.date);
        });

    $scope.clearUtility = () => {
        $scope.utility = {
            bill_no: '',
            bill_date: '',
            supplier_id: '',
            year: '',
            month: '',
            utility_type_id: '',
            desc: '',
            quantity: '',
            net_total: '',
            remark: '',
            user: ''
        };
    };

    $scope.getAll = function() {
        $scope.loading = true;
        $scope.utilities = [];
        $scope.pager = null;

        let year = $scope.cboYear === '' ? '' : $scope.cboYear;
        let type = $scope.cboUtilityType === '' ? '' : $scope.cboUtilityType;

        $http.get(`${CONFIG.baseUrl}/utilities/search?year=${year}&type=${type}`)
        .then(function(res) {
            $scope.setUtilities(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    }

    $scope.getDataWithUrl = function(e, url, cb) {
		/** Check whether parent of clicked a tag is .disabled just do nothing */
		if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.utilities = [];
        $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let depart = $('#user').val() == '1300200009261' ? '' : $('#depart').val();

        $http.get(`${url}&depart=${depart}$year=${year}`)
        .then(function(res) {
            $scope.setUtilities(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setUtilities = function(res) {
        const { data, ...pager } = res.data.utilities;

        $scope.utilities = data;
        $scope.pager = pager;
    };

    $scope.summary = [];
    $scope.getSummary = function() {
        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;

        $http.get(`${CONFIG.apiUrl}/utilities/${year}/summary`)
        .then(function(res) {
            const { monthly, budget } = res.data;

            $scope.summary = monthly.map(mon => {
                const summary = budget.find(b => b.expense_id === mon.expense_id);
                if (summary) {
                    mon.budget = summary.budget;
                } else {
                    mon.budget = 0;
                }

                return mon;
            });

            const sola = $scope.summary.find(mon => mon.utility_type_id === 5);
            $scope.summary = $scope.summary
                .filter(mon => mon.utility_type_id !== 5)
                .map(mon => {
                    if (mon.utility_type_id === 1) {
                        mon.budget = mon.budget + sola.budget;
                        mon.total = mon.total + sola.total;
                        mon.jan_total = mon.jan_total + sola.jan_total;
                        mon.feb_total = mon.feb_total + sola.feb_total;
                        mon.mar_total = mon.mar_total + sola.mar_total;
                        mon.apr_total = mon.apr_total + sola.apr_total;
                        mon.may_total = mon.may_total + sola.may_total;
                        mon.jun_total = mon.jun_total + sola.jun_total;
                        mon.jul_total = mon.jul_total + sola.jul_total;
                        mon.aug_total = mon.aug_total + sola.aug_total;
                        mon.sep_total = mon.sep_total + sola.sep_total;
                        mon.oct_total = mon.oct_total + sola.oct_total
                        mon.nov_total = mon.nov_total + sola.nov_total;
                        mon.dec_total = mon.dec_total + sola.dec_total;
                    }

                    return mon;
                });

            $scope.renderBarChartSummary($scope.summary);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.renderBarChartSummary = function (data) {
        const remainData = data.map(util => parseFloat(util.budget) - parseFloat(util.total));
        const totalData = data.map(util => parseFloat(util.total));
        let categories = data.map(util => util.name);
        let series = [
            {
                name: 'คงเหลือ',
                data: remainData,
                color: '#7ebc59',
            }, {
                name: 'ใช้ไป',
                data: totalData,
                color: '#F43E71',
            }
        ];

        $scope.barOptions = ChartService.initPercentageChart("barChartContainer", "ร้อยละการใช้จ่ายค่าสาธารณูปโภค", categories, 'ร้อยละ');
        $scope.barOptions.series = series;

        let chart = new Highcharts.Chart($scope.barOptions);
    };

    $scope.suppliers = [];
    $scope.getById = function(id, cb) {
        $scope.loading = true;

        $http.get(`${CONFIG.apiUrl}/utilities/${id}`)
        .then(function(res) {
            cb(res.data.utility);
            $scope.suppliers = res.data.suppliers;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setEditControls = function(utility) {
        console.log(utility);

        if (utility) {
            $scope.utility.id = utility.id;
            $scope.utility.bill_no = utility.bill_no;
            /** Convert db date to thai date. */
            $scope.utility.bill_date = StringFormatService.convFromDbDate(utility.bill_date);
            $scope.utility.utility_type = utility.utility_type;
            $scope.utility.supplier = utility.supplier;
            $scope.utility.desc = utility.desc;
            $scope.utility.month = utility.month;
            $scope.utility.quantity = utility.quantity;
            $scope.utility.net_total = utility.net_total;
            $scope.utility.remark = utility.remark;
            $scope.utility.status = utility.status;
            
            $scope.utility.utility_type_id = utility.utility_type_id.toString();
            $scope.utility.supplier_id = utility.supplier_id.toString();
            $scope.utility.year = utility.year.toString();
        }
    };

    $scope.store = function() {
        $scope.loading = true;
        $scope.utility.user = $('#user').val();

        $http.post(`${CONFIG.baseUrl}/utilities/store`, $scope.utility)
        .then(function(res) {
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
            }
        }, function(err) {
            $scope.loading = false;

            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
        });
    };

    $scope.update = function(event, form) {
        event.preventDefault();
        $scope.utility.user = $('#user').val();
    
        if(confirm(`คุณต้องแก้ไขรายการค่าสาธารณูปโภค รหัส ${$scope.utility.id} ใช่หรือไม่?`)) {
            $http.post(`${CONFIG.baseUrl}/utilities/update/${$scope.utility.id}`, $scope.utility)
            .then(function(res) {
                $scope.loading = false;

                if (res.data.status == 1) {
                    toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลเรียบร้อย !!!");
                } else {
                    toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถแก้ไขข้อมูลได้ !!!");
                }
            }, function(err) {
                $scope.loading = false;

                console.log(err);
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถแก้ไขข้อมูลได้ !!!");
            });
        } else {
            $scope.loading = false;
        }
    };

    $scope.delete = function(e, id) {
        e.preventDefault();

        if(confirm(`คุณต้องลบรายการค่าสาธารณูปโภค รหัส ${id} ใช่หรือไม่?`)) {
            $http.delete(`${CONFIG.baseUrl}/plans/${id}`)
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