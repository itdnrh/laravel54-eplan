app.controller('homeCtrl', function(CONFIG, $scope, $http, StringFormatService, ChartService) {
/** ################################################################################## */
    $scope.assets_pager = null;
    $scope.materials_pager = null;

    $scope.pieOptions = {};
    $scope.barOptions = {};

    $scope.approved = '2';
    $scope.inPlan = 'I';
    $scope.dtpYear = moment().month() < 8 ? moment().year() + 543 : moment().add(1, 'years').year() + 543;

    const dtpYearOptions = {
        autoclose: true,
        format: 'yyyy',
        viewMode: "years", 
        minViewMode: "years",
        language: 'th',
        thaiyear: true
    };

    const dtpMonthOptions = {
        autoclose: true,
        format: 'yyyy',
        viewMode: "years", 
        minViewMode: "years",
        language: 'th',
        thaiyear: true
    };

    $('#dtpYear').datepicker(dtpYearOptions)
    .datepicker('update', moment(moment().add(1, 'years').toDate()).toDate())
    .on('changeDate', function(event) {
        $scope.dtpYear = moment(event.date).year() + 543;

        $scope.getStat1();
        $scope.getStat2();
        $scope.getSummaryAssets();
        $scope.getSummaryMaterials();
        $scope.getSummaryServices();
        $scope.getSummaryConstructs();
        $scope.getProjectTypeRatio();
    });

    $('#cboAssetDate').datepicker(dtpMonthOptions)
    .datepicker('update', moment().toDate())
    .on('changeDate', function(event) {
        console.log(moment(event.date).year());
    });

    $('#cboMaterialDate').datepicker(dtpMonthOptions)
    .datepicker('update', moment().toDate())
    .on('changeDate', function(event) {
        console.log(moment(event.date).year());
    });

    $scope.onApprovedToggle = function(e) {
        $scope.approved = $(e.target).children().val();

        $scope.getStat1();
        $scope.getStat2();
        $scope.getSummaryAssets();
        $scope.getSummaryMaterials();
        $scope.getSummaryServices();
        $scope.getSummaryConstructs();
        $scope.getProjectTypeRatio();
    };

    $scope.onInPlanToggle = function(e) {
        $scope.inPlan = $(e.target).val();

        $scope.getStat1();
        $scope.getStat2();
        $scope.getSummaryAssets();
        $scope.getSummaryMaterials();
        $scope.getSummaryServices();
        $scope.getSummaryConstructs();
        $scope.getProjectTypeRatio();
    };

    $scope.stat1Cards = null;
    $scope.stat2Cards = [];
    $scope.getStat1 = function () {
        $scope.loading = true;

        let year = $scope.dtpYear;

        $http.get(`${CONFIG.baseUrl}/dashboard/stat1/${year}?approved=${$scope.approved}&in_plan=${$scope.inPlan}`)
        .then(function(res) {
            const { ...plans } = res.data.plans;
            const { ...supports } = res.data.supports;

            $scope.stat1Cards = { ...plans, ...supports };

            $scope.loading = false;
        }, function(err) {
            console.log(err);

            $scope.loading = false;
        });
    };

    $scope.getStatCardById = function(id) {
        if (!$scope.stat2Cards) return 0;
        
        let type = $scope.stat2Cards.find(st => st.plan_type_id == id);
        if (!type) return 0;

        return type.sum_all;
    };

    $scope.getStat2 = function () {
        $scope.loading = true;

        let year = $scope.dtpYear;

        $http.get(`${CONFIG.baseUrl}/dashboard/stat2/${year}?approved=${$scope.approved}&in_plan=${$scope.inPlan}`)
        .then(function(res) {
            $scope.stat2Cards = res.data.stats;

            $scope.getPlanTypeRatio(res.data.stats);

            $scope.loading = false;
        }, function(err) {
            console.log(err);

            $scope.loading = false;
        });
    };

    $scope.assets = [];
    $scope.totalAsset = {
        budget: 0,
        request: 0,
        sent: 0,
        received: 0,
        po: 0,
        inspect: 0,
        withdraw: 0,
        debt: 0,
    };
    $scope.getSummaryAssets = function() {
        $scope.loading = true;
        $scope.assets = [];
        $scope.assets_pager = null;
        $scope.totalAsset = {
            budget: 0,
            request: 0,
            sent: 0,
            received: 0,
            po: 0,
            inspect: 0,
            withdraw: 0,
            debt: 0,
        };

        // let date = $('#cboAssetDate').val() !== ''
        //             ? StringFormatService.convToDbDate($('#cboAssetDate').val())
        //             : moment().format('YYYY-MM-DD');
        let year = $scope.dtpYear

        $http.get(`${CONFIG.apiUrl}/dashboard/summary-assets?year=${year}&approved=${$scope.approved}&in_plan=${$scope.inPlan}`)
        .then(function(res) {
            const { plans, supports, budgets, categories } = res.data;

            /** รวมข้อมูล plans กับ supports เข้าด้วยกันห */
            const { data, ...pager } = plans;
            let tmpPlans = data.map(plan => {
                let support = supports.find(support => plan.category_id === support.category_id);

                if (support) {
                    return { ...plan, ...support };
                }

                return plan;
            });

            let cates = categories.map(cate => {
                const summary = budgets.find(bud => bud.expense_id === cate.expense_id);
                cate.budget = summary ? summary.budget : 0;

                return cate;
            });

            $scope.assets = tmpPlans.map(plan => {
                const cateInfo = cates.find(cate => cate.id === plan.category_id);
                if (cateInfo) {
                    plan.category_name = cateInfo.name;
                    plan.budget = cateInfo.budget;
                }

                return plan;
            });

            $scope.assets.forEach(asset => {
                $scope.totalAsset.budget    += asset.budget || 0;
                $scope.totalAsset.request   += asset.request || 0;
                $scope.totalAsset.sent      += asset.sent || 0;
                $scope.totalAsset.received  += asset.received || 0;
                $scope.totalAsset.po        += asset.po || 0;
                $scope.totalAsset.inspect   += asset.inspect || 0;
                $scope.totalAsset.withdraw  += asset.withdraw || 0;
                $scope.totalAsset.debt      += asset.debt || 0;
            });

            $scope.assets_pager = pager;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.materials = [];
    $scope.totalMaterial = {
        budget: 0,
        request: 0,
        sent: 0,
        received: 0,
        po: 0,
        inspect: 0,
        withdraw: 0,
        debt: 0,
    };
    $scope.getSummaryMaterials = function() {
        $scope.materials = [];
        $scope.materials_pager = null;
        $scope.totalMaterial = {
            budget: 0,
            request: 0,
            sent: 0,
            received: 0,
            po: 0,
            inspect: 0,
            withdraw: 0,
            debt: 0,
        };

        // let date = $('#cboAssetDate').val() !== ''
        //             ? StringFormatService.convToDbDate($('#cboAssetDate').val())
        //             : moment().format('YYYY-MM-DD');
        let year = $scope.dtpYear;

        $http.get(`${CONFIG.apiUrl}/dashboard/summary-materials?year=${year}&approved=${$scope.approved}&in_plan=${$scope.inPlan}`)
        .then(function(res) {
            $scope.setMaterials(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.setMaterials = function(res) {
        const { plans, supports, budgets, categories } = res.data;

        /** รวมข้อมูล plans กับ supports เข้าด้วยกันห */
        const { data, ...pager } = plans;
        let tmpPlans = data.map(plan => {
            let support = supports.find(support => plan.category_id === support.category_id);

            if (support) {
                return { ...plan, ...support };
            }

            return plan;
        });

        let cates = categories.map(cate => {
            const summary = budgets.find(bud => bud.expense_id === cate.expense_id);
            cate.budget = summary ? summary.budget : 0;

            return cate;
        });

        $scope.materials = tmpPlans.map(plan => {
            const cateInfo = cates.find(cate => cate.id === plan.category_id);

            plan.category_name = cateInfo ? cateInfo.name : '';
            plan.budget = cateInfo ? cateInfo.budget : '';

            return plan;
        });

        $scope.materials.forEach(material => {
            $scope.totalMaterial.budget    += material.budget || 0;
            $scope.totalMaterial.request   += material.request || 0;
            $scope.totalMaterial.sent      += material.sent || 0;
            $scope.totalMaterial.received  += material.received || 0;
            $scope.totalMaterial.po        += material.po || 0;
            $scope.totalMaterial.inspect   += material.inspect || 0;
            $scope.totalMaterial.withdraw  += material.withdraw || 0;
            $scope.totalMaterial.debt      += material.debt || 0;
        });

        $scope.materials_pager = pager;
    };

    $scope.getMaterialsWithUrl = function(e, url, cb) {
        /** Check whether parent of clicked a tag is .disabled just do nothing */
        if ($(e.currentTarget).parent().is('li.disabled')) return;

        $scope.loading = true;
        $scope.materials = [];
        $scope.pager = null;
        $scope.totalMaterial = {
            budget: 0,
            request: 0,
            sent: 0,
            received: 0,
            po: 0,
            inspect: 0,
            withdraw: 0,
            debt: 0,
        };

        let year = $scope.dtpYear

        $http.get(`${url}&year=${year}&approved=${$scope.approved}&in_plan=${$scope.inPlan}`)
        .then(function(res) {
            cb(res);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.services = [];
    $scope.totalService = {
        budget: 0,
        request: 0,
        sent: 0,
        received: 0,
        po: 0,
        inspect: 0,
        withdraw: 0,
        debt: 0,
    };
    $scope.getSummaryServices = function() {
        $scope.loading = true;
        $scope.services = [];
        $scope.totalService = {
            budget: 0,
            request: 0,
            sent: 0,
            received: 0,
            po: 0,
            inspect: 0,
            withdraw: 0,
            debt: 0,
        };

        let year = $scope.dtpYear

        $http.get(`${CONFIG.apiUrl}/dashboard/summary-services?year=${year}&approved=${$scope.approved}&in_plan=${$scope.inPlan}`)
        .then(function(res) {
            const { plans, supports, budgets, categories } = res.data;

            /** รวมข้อมูล plans กับ supports เข้าด้วยกันห */
            let tmpPlans = plans.map(plan => {
                let support = supports.find(support => plan.category_id === support.category_id);

                if (support) {
                    return { ...plan, ...support };
                }

                return plan;
            });

            let cates = categories.map(cate => {
                const summary = budgets.find(bud => bud.expense_id === cate.expense_id);
                cate.budget = summary ? summary.budget : 0;

                return cate;
            });

            $scope.services = tmpPlans.map(plan => {
                const cateInfo = cates.find(cate => cate.id === plan.category_id);
                if (cateInfo) {
                    plan.category_name = cateInfo.name;
                    plan.budget = cateInfo.budget;
                }

                return plan;
            });

            $scope.services.forEach(service => {
                $scope.totalService.budget    += service.budget || 0;
                $scope.totalService.request   += service.request || 0;
                $scope.totalService.sent      += service.sent || 0;
                $scope.totalService.received  += service.received || 0;
                $scope.totalService.po        += service.po || 0;
                $scope.totalService.inspect   += service.inspect || 0;
                $scope.totalService.withdraw  += service.withdraw || 0;
                $scope.totalService.debt      += service.debt || 0;
            });

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.constructs = [];
    $scope.totalConstruct = {
        budget: 0,
        request: 0,
        sent: 0,
        received: 0,
        po: 0,
        inspect: 0,
        withdraw: 0,
        debt: 0,
    };
    $scope.getSummaryConstructs = function() {
        $scope.loading = true;
        $scope.constructs = [];
        $scope.totalConstruct = {
            budget: 0,
            request: 0,
            sent: 0,
            received: 0,
            po: 0,
            inspect: 0,
            withdraw: 0,
            debt: 0,
        };

        let year = $scope.dtpYear

        $http.get(`${CONFIG.apiUrl}/dashboard/summary-constructs?year=${year}&approved=${$scope.approved}&in_plan=${$scope.inPlan}`)
        .then(function(res) {
            const { plans, supports, budgets, categories } = res.data;

            /** รวมข้อมูล plans กับ supports เข้าด้วยกันห */
            let tmpPlans = plans.map(plan => {
                let support = supports.find(support => plan.category_id === support.category_id);

                if (support) {
                    return { ...plan, ...support };
                }

                return plan;
            });

            let cates = categories.map(cate => {
                const summary = budgets.find(bud => bud.expense_id === cate.expense_id);
                cate.budget = summary ? summary.budget : 0;

                return cate;
            });

            $scope.constructs = tmpPlans.map(plan => {
                const cateInfo = cates.find(cate => cate.id === plan.category_id);
                if (cateInfo) {
                    plan.category_name = cateInfo.name;
                    plan.budget = cateInfo.budget;
                }

                return plan;
            });

            $scope.constructs.forEach(construct => {
                $scope.totalConstruct.budget    += construct.budget || 0;
                $scope.totalConstruct.request   += construct.request || 0;
                $scope.totalConstruct.sent      += construct.sent || 0;
                $scope.totalConstruct.received  += construct.received || 0;
                $scope.totalConstruct.po        += construct.po || 0;
                $scope.totalConstruct.inspect   += construct.inspect || 0;
                $scope.totalConstruct.withdraw  += construct.withdraw || 0;
                $scope.totalConstruct.debt      += construct.debt || 0;
            });

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.orders = [];
    $scope.orders_pager = null;
    $scope.getLatestOrders = function() {
        $scope.loading = true;
        $scope.orders = [];
        $scope.orders_pager = null;

        let year = $scope.cboYear === '' ? '' : $scope.cboYear;
        let status = $scope.cboStatus === '' ? '' : $scope.cboStatus;

        $http.get(`${CONFIG.baseUrl}/orders/search?year=${year}&status=0`)
        .then(function(res) {
            const { data, ...pager } = res.data.orders;

            $scope.orders = data;
            $scope.orders_pager = pager;

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getPlanTypeRatio = function (data) {
        $scope.pieOptions = ChartService.initPieChart("pieChartContainer", "", "บาท", "สัดส่วนแผนเงินบำรุง");
        $scope.pieOptions.series[0].data.push({ name: 'ครุุภัณฑ์', y: $scope.getStatCardById(1) });
        $scope.pieOptions.series[0].data.push({ name: 'วัสดุ', y: $scope.getStatCardById(2) });
        $scope.pieOptions.series[0].data.push({ name: 'จ้างบริการ', y: $scope.getStatCardById(3) });
        $scope.pieOptions.series[0].data.push({ name: 'ก่อสร้าง', y: $scope.getStatCardById(4) });

        var chart = new Highcharts.Chart($scope.pieOptions);
    };

    $scope.getProjectTypeRatio = function () {
        $scope.loading = true;

        let year = $scope.dtpYear;

        $http.get(`${CONFIG.apiUrl}/dashboard/project-type?year=${year}&approved=${$scope.approved}`)
        .then(function(res) {
            let dataSeries = res.data.projects.map(type => {
                return { name: type.name, y: parseInt(type.budget) }
            });
            $scope.pieOptions = ChartService.initPieChart("projectPieChartContainer", "", "บาท", "สัดส่วนสัดส่วนแผนงาน/โครงการ");
            $scope.pieOptions.series[0].data = dataSeries;

            var chart = new Highcharts.Chart($scope.pieOptions);

            $scope.loading = false;
        }, function(err) {
            console.log(err);
            $scope.loading = false;
        });
    };

    $scope.getSumMonthData = function () {
        var month = '2018';
        console.log(month);

        ReportService.getSeriesData('/report/sum-month-chart/', month)
        .then(function(res) {
            var debtSeries = [];
            var paidSeries = [];
            var setzeroSeries = [];

            angular.forEach(res.data, function(value, key) {
                let debt = (value.debt) ? parseFloat(value.debt.toFixed(2)) : 0;
                let paid = (value.paid) ? parseFloat(value.paid.toFixed(2)) : 0;
                let setzero = (value.setzero) ? parseFloat(value.setzero.toFixed(2)) : 0;

                debtSeries.push(debt);
                paidSeries.push(paid);
                setzeroSeries.push(setzero);
            });

            var categories = ['ตค', 'พย', 'ธค', 'มค', 'กพ', 'มีค', 'เมย', 'พค', 'มิย', 'กค', 'สค', 'กย']
            $scope.barOptions = ReportService.initBarChart("barContainer1", "รายงานยอดหนี้ทั้งหมด ปีงบ 2561", categories, 'จำนวน');
            $scope.barOptions.series.push({
                name: 'หนี้คงเหลือ',
                data: debtSeries
            }, {
                name: 'ชำระแล้ว',
                data: paidSeries
            }, {
                name: 'ลดหนี้ศูนย์',
                data: setzeroSeries
            });

            var chart = new Highcharts.Chart($scope.barOptions);
        }, function(err) {
            console.log(err);
        });
    };

    $scope.getSumYearData = function () {       
        var month = '2018';
        console.log(month);

        ReportService.getSeriesData('/report/sum-year-chart/', month)
        .then(function(res) {
            console.log(res);
            var debtSeries = [];
            var paidSeries = [];
            var setzeroSeries = [];
            var categories = [];

            angular.forEach(res.data, function(value, key) {
                let debt = (value.debt) ? parseFloat(value.debt.toFixed(2)) : 0;
                let paid = (value.paid) ? parseFloat(value.paid.toFixed(2)) : 0;
                let setzero = (value.setzero) ? parseFloat(value.setzero.toFixed(2)) : 0;

                categories.push(parseInt(value.yyyy) + 543);
                debtSeries.push(debt);
                paidSeries.push(paid);
                setzeroSeries.push(setzero);
            });

            $scope.barOptions = ReportService.initBarChart("barContainer2", "รายงานยอดหนี้รายปี", categories, 'จำนวน');
            $scope.barOptions.series.push({
                name: 'หนี้คงเหลือ',
                data: debtSeries
            }, {
                name: 'ชำระแล้ว',
                data: paidSeries
            }, {
                name: 'ลดหนี้ศูนย์',
                data: setzeroSeries
            });

            var chart = new Highcharts.Chart($scope.barOptions);
        }, function(err) {
            console.log(err);
        });
    };

    $scope.getPeriodData = function () {
        var selectMonth = document.getElementById('selectMonth').value;
        var month = (selectMonth == '') ? moment().format('YYYY-MM') : selectMonth;
        console.log(month);

        ReportService.getSeriesData('/report/period-chart/', month)
        .then(function(res) {
            console.log(res);
            
            var categories = [];
            var nSeries = [];
            var mSeries = [];
            var aSeries = [];
            var eSeries = [];

            angular.forEach(res.data, function(value, key) {
                categories.push(value.d);
                nSeries.push(value.n);
                mSeries.push(value.m);
                aSeries.push(value.a);
                eSeries.push(value.e);
            });

            $scope.barOptions = ReportService.initStackChart("barContainer", "รายงานการให้บริการ ตามช่วงเวลา", categories, 'จำนวนการให้บริการ');
            $scope.barOptions.series.push({
                name: '00.00-08.00น.',
                data: nSeries
            }, {
                name: '08.00-12.00น.',
                data: mSeries
            }, {
                name: '12.00-16.00น.',
                data: aSeries
            }, {
                name: '16.00-00.00น.',
                data: eSeries
            });

            var chart = new Highcharts.Chart($scope.barOptions);
        }, function(err) {
            console.log(err);
        });
    };
});