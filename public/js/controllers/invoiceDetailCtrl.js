app.controller('invoiceDetailCtrl', function(CONFIG, $rootScope, $scope, $http, toaster, StringFormatService) {
  $scope.cboYear = parseInt(moment().format('MM')) > 9 ? (moment().year() + 544).toString() : (moment().year() + 543).toString();
  $scope.cboInvoiceItem = '';
  $scope.cboInvoiceItemDetail = '';
  $scope.cboFaction = '';
  $scope.cboDepart = '';
  $scope.cboDivision = '';
  $scope.cboInPlan = '';

  $scope.invoiceDetail = {
    year:2568,
    start_month:"",
    invoice_item_id:'',
    invoice_detail_id:'',
    sum_price:'',
    remain_price:'',
    use_price:'',
    reason:'',
    detail:'',
    remark:'',
    depart_id: '',
    division_id: '',
    contact_detail: '',
    contact_person: '',
    head_of_depart_detail: '',
    head_of_depart: '',
    head_of_faction_detail: '',
    head_of_faction: '',
    user: null,
    ivh_id: "",
    ivd_id: "",
    topic: "",
    doc_no: "",
    doc_date: "",
    status: "",
  }

  $scope.results = [];
  $scope.totalInvoice = {
    jan_amount: 0,
    feb_amount: 0,
    mar_amount: 0,
    apr_amount: 0,
    may_amount: 0,
    jun_amount: 0,
    jul_amount: 0,
    aug_amount: 0,
    sep_amount: 0,
    oct_amount: 0,
    nov_amount: 0,
    dece_amount: 0,
};

  // $scope.apiInvoiceResponse = {
  //   sum_price: "",
  //   remain_price: "",
  // }

      /** ============================== Form initialize elements ============================== */
      let dtpDateOptions = {
        autoclose: true,
        language: 'th',
        format: 'dd/mm/yyyy',
        thaiyear: true,
        todayBtn: true,
        todayHighlight: true
    };

  $scope.onValidateForm = function(e, form, cb) {
    e.preventDefault();

    $scope.invoiceDetail.depart_id = $('#depart_id').val();
    $scope.invoiceDetail.division_id = $('#division_id').val();

    $rootScope.formValidate(e, '/invoicedetail/validate', $scope.invoiceDetail, 'frmNewInvoiceDetail', $scope.store)
  };

  $scope.store = function() {
  
    $scope.loading = true;
    
    /** Set user props of support model by logged in user */
    $scope.invoiceDetail.user = $('#user').val();

    $http.post(`${CONFIG.baseUrl}/invoicedetail/store`, $scope.invoiceDetail)
    .then(function(res) {
        $scope.loading = false;

        if (res.data.status == 1) {
            toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");

            window.location.href = `${CONFIG.baseUrl}/invoicedetail/list`;
        } else {
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
        }
    }, function(err) {
        $scope.loading = false;

        console.log(err);
        toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
    });
  };

  $scope.onInvoiceDetailSelect = function(e, form, c){
    if(e !== null){
    // Example API endpoint: /api/get-data/{id}
    var url = `${CONFIG.apiUrl}/invoicedetail/getInvoiceDetailDataById/` + e;

    $http.get(url)
      .then(function(response) {
        // Handle success
        $scope.apiInvoiceResponse = response.data;
        $scope.invoiceDetail.sum_price = response.data.sum_price;
        $scope.invoiceDetail.remain_price = response.data.remain_price;
        //console.log(response.data);
        
      }, function(error) {
        // Handle error
        console.error('Error fetching data:', error);
        $scope.apiInvoiceResponse = { error: 'Failed to fetch data' };
    });
    }
  }

  // Edit Part 
//   $scope.getById = function(id, cb) {
//     $scope.loading = true;
    
//     $http.get(`${CONFIG.apiUrl}/invoicedetail/${id}`)
//     .then(function(res) {
//       cb(res.data.invoiceDetail);
//         //console.log(res.data.invoicedetail);
//         $scope.loading = false;
//     }, function(err) {
//         console.log(err);
//         $scope.loading = false;
//     });
// };

$scope.getById = function(id, cb) {
  $scope.loading = true;
  
  $http.get(`${CONFIG.apiUrl}/invoicedetail/${id}`)
  .then(function(res) {
      cb(res.data.invoicedetail);
      //console.log(res.data.invoicedetail);
      
      $scope.loading = false;
  }, function(err) {
      console.log(err);
      $scope.loading = false;
  });
};


$scope.setEditControls = function(invoiceDetail) {   
 // console.log(invoiceDetail);
   
    if (invoiceDetail) {
      if (invoiceDetail.doc_no) {
        const [prefix, doc_no]      = invoiceDetail.doc_no.split("/");
        $scope.invoiceDetail.doc_prefix   = prefix;
        $scope.invoiceDetail.doc_no       = doc_no;
      }

        $scope.invoiceDetail.ivd_id              = invoiceDetail.ivd_id;
        $scope.invoiceDetail.ivh_id              = invoiceDetail.ivh_id.toString();
        $scope.invoiceDetail.year                = invoiceDetail.ivd_year.toString();
        $scope.invoiceDetail.start_month         = invoiceDetail.ivd_month.toString();
        $scope.invoiceDetail.remark              = invoiceDetail.ivd_remark.toString();
        $scope.invoiceDetail.reason              = invoiceDetail.ivd_reason.toString();
        $scope.invoiceDetail.detail              = invoiceDetail.ivd_detail.toString();
        $scope.invoiceDetail.sum_price           = invoiceDetail.sum_price;
        $scope.invoiceDetail.remain_price        = invoiceDetail.remain_price;
        $scope.invoiceDetail.use_price           = invoiceDetail.ivd_use_price;
        $scope.invoiceDetail.invoice_item_id     = invoiceDetail.invoice_item_id.toString();
        $scope.invoiceDetail.invoice_detail_id   = invoiceDetail.invoice_detail_id.toString();
        //$scope.invoiceDetail.doc_no              = invoiceDetail.doc_no.toString();
        $scope.invoiceDetail.doc_date            = invoiceDetail.doc_date ? StringFormatService.convFromDbDate(invoiceDetail.doc_date) : '';
        $scope.invoiceDetail.status              = invoiceDetail.ivd_status;
        $scope.invoiceDetail.topic               = "ขออนุมัติจ่ายเงินค่าบริการตามแผนเงินบำรุงโรงพยาบาล ปีงบประมาณ ๒๕๖๘";

        $scope.invoiceDetail.depart_id        = invoiceDetail.depart_id;
        $scope.invoiceDetail.division_id      = invoiceDetail.division_id ? invoice.division_id.toString() : '';
        /** Set date value to datepicker input of doc_date */
        $('#doc_date').datepicker(dtpDateOptions).datepicker('update', moment(invoiceDetail.doc_date).toDate());
        $scope.onInvoiceSelected(invoiceDetail.invoice_item_id);
        $scope.setcboInvoice(invoiceDetail.invoice_item_id);
        $scope.setcboInvoiceItemDetail(invoiceDetail.invoice_detail_id.toString());
    }
};

$scope.update = function(e, form) {
  e.preventDefault();

  if(confirm(`คุณต้องแก้ไขบันทึกขอสนับสนุน รหัส ${$scope.invoiceDetail.ivd_id} ใช่หรือไม่?`)) {
      $scope.loading = true;

      /** Set user props of support model by logged in user */
      $scope.invoiceDetail.user = $('#user').val();

      $http.post(`${CONFIG.baseUrl}/invoicedetail/update/${$scope.invoiceDetail.ivd_id}`, $scope.invoiceDetail)
      .then(function(res) {
          $scope.loading = false;

          if (res.data.status == 1) {
              toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลเรียบร้อย !!!");

              /** TODO: Reset supports model */
              //$scope.setInvoiceDetail(res);
              //alert('OKOKOK');
              window.location.href = `${CONFIG.baseUrl}/invoicedetail/list`;
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
  

$scope.calculateSumPrice = function(use_price, remain_price) {

  let chk_use_price = parseFloat($scope.currencyToNumber(use_price));
  let chk_remain_price = parseFloat($scope.currencyToNumber(remain_price));
  if (chk_use_price > chk_remain_price) {
      toaster.pop('error', "ผลการตรวจสอบ", `ไม่สามารถระบุยอดรวมเป็นเงินเกินงบประมาณที่ขอได้!!! (คงเหลือ ${remain_price} บาท)`);
      $scope.invoiceDetail.use_price = 0;
      return;
  }
};

    /*
    |-----------------------------------------------------------------------------
    | Send processes
    |-----------------------------------------------------------------------------
    */
  $scope.showPlanSendForm = function(invoiceDetail) {
    if (invoiceDetail) {
      $('#support-form-plan-invoice').modal('show');
    } 
   //console.log(invoiceDetail);
      
  };

  $scope.sendDocPlan = function(e) {
    $scope.loading = true;

    $http.post(`${CONFIG.baseUrl}/invoicedetail/sendDocPlan`, $scope.invoiceDetail)
    .then(function(res) {
        if (res.data.status == 1) {
            toaster.pop('success', "ผลการทำงาน", "ส่งบันทึกขอสนับสนุนเรียบร้อย !!!");

            window.location.href = `${CONFIG.baseUrl}/invoicedetail/list`;
        } else {
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถส่งบันทึกขอสนับสนุนได้ !!!");
        }

        $scope.loading = false;
    }, function(err) {
        $scope.loading = false;

        console.log(err);
        toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถส่งบันทึกขอสนับสนุนได้ !!!");
    });
  };

  $scope.cancelSendPlan = function(e, id) {
    $scope.loading = true;

    if(confirm(`คุณต้องการยกเลิกการส่งบันทึกขอสนับสนุน รหัส ${id} ใช่หรือไม่?`)) {
        $http.put(`${CONFIG.apiUrl}/invoicedetail/${id}/cancel-sent-plan`, { ivd_status: 0 })
        .then(function(res) {
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "ยกเลิกส่งบันทึกขอสนับสนุนเรียบร้อย !!!");

                window.location.href = `${CONFIG.baseUrl}/invoicedetail/list`;
            } else {
                toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถยกเลิกส่งบันทึกขอสนับสนุนได้ !!!");
            }

            $scope.loading = false;
        }, function(err) {
            $scope.loading = false;

            console.log(err);
            toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถยกเลิกส่งบันทึกขอสนับสนุนได้ !!!");
        });
    } else {
        $scope.loading = false;
    }
};

$scope.getReportSummaryByInovice = function () {
  $scope.loading = true;
  $scope.totalInvoice = {
    jan_amount: 0,
    feb_amount: 0,
    mar_amount: 0,
    apr_amount: 0,
    may_amount: 0,
    jun_amount: 0,
    jul_amount: 0,
    aug_amount: 0,
    sep_amount: 0,
    oct_amount: 0,
    nov_amount: 0,
    dece_amount: 0,
};

  let faction = $scope.cboFaction === '' ? '' : $scope.cboFaction;
  let year = $scope.cboYear === ''
              ? $scope.cboYear = parseInt(moment().format('MM')) > 9
                  ? moment().year() + 544
                  : moment().year() + 543 
              : $scope.cboYear;

  //let in_plan = $scope.cboInPlan !== '' ? $scope.cboInPlan : '';
  //let approved = !$scope.cboApproved ? '' : 'A';

  $http.get(`${CONFIG.apiUrl}/invoicedetails/get-invoice?year=${year}&faction=${faction}`)
  .then(function (res) {
    $scope.results = res.data.results.map(result => {
         //let invoiceitem = res.data.departs.find(d => d.invoice_item_id === result.results);
         //result.invoice_item_name = invoiceitem.invoice_item_name;

        return result;
    });
    
     if (res.data.results.length > 0) {
        res.data.results.forEach(result => {
          //console.log(result.jan);
          
            $scope.totalInvoice.jan_amount += result.jan ? (+result.jan) : 0;
            $scope.totalInvoice.feb_amount += result.feb ? (+result.feb) : 0;
            $scope.totalInvoice.mar_amount += result.mar ? (+result.mar) : 0;
            $scope.totalInvoice.apr_amount += result.apr ? (+result.apr) : 0;
            $scope.totalInvoice.may_amount += result.may ? (+result.may) : 0;
            $scope.totalInvoice.jun_amount += result.jun ? (+result.jun) : 0;
            $scope.totalInvoice.jul_amount += result.jul ? (+result.jul) : 0;
            $scope.totalInvoice.aug_amount += result.aug ? (+result.aug) : 0;
            $scope.totalInvoice.sep_amount += result.sep ? (+result.sep) : 0;
            $scope.totalInvoice.oct_amount += result.oct ? (+result.oct) : 0;
            $scope.totalInvoice.nov_amount += result.nov ? (+result.nov) : 0;
            $scope.totalInvoice.dece_amount += result.dece ? (+result.dece) : 0;
        });
    } else {
      $scope.totalInvoice = {
          jan_amount: 0,
          feb_amount: 0,
          mar_amount: 0,
          apr_amount: 0,
          may_amount: 0,
          jun_amount: 0,
          jul_amount: 0,
          aug_amount: 0,
          sep_amount: 0,
          oct_amount: 0,
          nov_amount: 0,
          dece_amount: 0,
      };
    }
    $scope.loading = false;
  }, function (err) {
    console.log(err);
    $scope.loading = false;
  });
};



});