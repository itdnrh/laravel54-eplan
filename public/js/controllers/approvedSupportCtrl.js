app.controller('approvedSupportCtrl', function(CONFIG, $scope, $http, toaster, StringFormatService) {
  $scope.loading = false;
  $scope.receiveds = [];
  $scope.receiveds_pager = null;
  $scope.supports = [];
  $scope.supports_pager = null;

  $scope.cboYear = '2569'; //(moment().year() + 543).toString();
  $scope.cboInPlan = '';
  $scope.txtSupportNo = '';
  $scope.txtReceivedNo = '';
  $scope.cboSupportStatus = '11';

  $scope.receive = {
    support_id: '',
    received_no: '',
    received_date: '',
    officer: '',
    remark: ''
  };

  $scope.returnData = {
    reason: '',
    support_id: '',
    user: ''
  };

  /** DatePicker options */
  let dtpDateOptions = {
    autoclose: true,
    language: 'th',
    format: 'dd/mm/yyyy',
    thaiyear: true,
    todayBtn: true,
    todayHighlight: true,
    orientation: "bottom"
  };


  $scope.getReceiveds = (status) => {
    $scope.loading = true;
    $scope.receiveds = [];
    $scope.receiveds_pager = null;

    let year = $scope.cboYear == '' ? '' : $scope.cboYear;
    let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
    let depart = !$scope.cboDepart ? '' : $scope.cboDepart;
    let doc_no = $scope.txtSupportNo == '' ? '' : $scope.txtSupportNo;
    let received_no = $scope.txtReceivedNo == '' ? '' : $scope.txtReceivedNo;

    $http.get(`${CONFIG.baseUrl}/supports/search?year=${year}&type=${type}&depart=${depart}&doc_no=${doc_no}&received_no=${received_no}&status=${status}&plan_approved=approved`)
    .then(function(res) {
        $scope.setReceiveds(res);

        $scope.loading = false;
    }, function(err) {
        console.log(err);
        $scope.loading = false;
    });
  };

  $scope.getReceivedsWithUrl = function(e, url, status, cb) {
    /** Check whether parent of clicked a tag is .disabled just do nothing */
    if ($(e.currentTarget).parent().is('li.disabled')) return;

    $scope.loading = true;
    $scope.receiveds = [];
    $scope.receiveds_pager = null;

    let year = $scope.cboYear == '' ? '' : $scope.cboYear;
    let type = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
    let depart = !$scope.cboDepart ? '' : $scope.cboDepart;
    let doc_no = $scope.txtSupportNo == '' ? '' : $scope.txtSupportNo;
    let received_no = $scope.txtReceivedNo == '' ? '' : $scope.txtReceivedNo;

    $http.get(`${url}&year=${year}&type=${type}&depart=${depart}&doc_no=${doc_no}&received_no=${received_no}&status=${status}&plan_approved=approved`)
    .then(function(res) {
        cb(res);

        $scope.loading = false;
    }, function(err) {
        console.log(err);
        $scope.loading = false;
    });
};

  $scope.setReceiveds = function(res) {
    const { data, ...pager } = res.data.supports;

    $scope.receiveds = data;
    $scope.receiveds_pager = pager;
  };

  $scope.getSupports = function(res) {
    $scope.loading = true;
    $scope.supports = [];
    $scope.supports_pager = null;

    let year    = $scope.cboYear == '' ? '' : $scope.cboYear;
    let type    = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
    // let cate = !$scope.cboCategory ? '' : $scope.cboCategory;
    let depart  = !$scope.cboDepart ? '' : $scope.cboDepart;
    let doc_no  = $scope.txtSupportNo == '' ? '' : $scope.txtSupportNo;
    let in_plan = $scope.cboInPlan === '' ? '' : $scope.cboInPlan;

    $http.get(`${CONFIG.baseUrl}/supports/search?year=${year}&type=${type}&depart=${depart}&doc_no=${doc_no}&in_plan=${in_plan}&status=10&plan_approved=wait_approved&plan_send=Y`)
    .then(function(res) {
        $scope.loading = false;

        $scope.setSupports(res);

        $('#supports-receive').modal('show');
    }, function(err) {
        $scope.loading = false;
        console.log(err);
    });
  };

  $scope.getSupportsWithUrl = function(e, url, cb) {
    /** Check whether parent of clicked a tag is .disabled just do nothing */
    if ($(e.currentTarget).parent().is('li.disabled')) return;

    $scope.loading = true;
    $scope.supports = [];
    $scope.supports_pager = null;

    let year    = $scope.cboYear == '' ? '' : $scope.cboYear;
    let type    = $scope.cboPlanType == '' ? '' : $scope.cboPlanType;
    // let cate = !$scope.cboCategory ? '' : $scope.cboCategory;
    let depart  = !$scope.cboDepart ? '' : $scope.cboDepart;
    let doc_no  = $scope.txtSupportNo == '' ? '' : $scope.txtSupportNo;
    let in_plan = $scope.cboInPlan === '' ? '' : $scope.cboInPlan;

    $http.get(`${url}&year=${year}&type=${type}&depart=${depart}&doc_no=${doc_no}&in_plan=${in_plan}&status=10&plan_approved=wait_approved&plan_send=Y`)
    .then(function(res) {
        cb(res);

        $scope.loading = false;
    }, function(err) {
        console.log(err);
        $scope.loading = false;
    });
};

  $scope.setSupports = function(res) {
    const { data, ...pager } = res.data.supports;

    $scope.supports = data;
    $scope.supports_pager = pager;
  };

  // เปิด Modal ขึ้นมา
  $scope.showReceiveSupportForm = function(e, support) {
    // const balance = $scope.checkAllBalance(support.details);

    // if (balance > 0) {
    //     toaster.pop('error', "ผลการตรวจสอบ", "พบรายการที่มีงบประมาณไม่เพียงพอ !!!");
    //     return;
    // }

    $scope.receive.support_id = support.id;
    $('#plan_approved_date')
        .datepicker(dtpDateOptions)
        .datepicker('update', new Date())
        .on('changeDate', function(event) {
            console.log(event.date);
        });
    $('#receive-form').modal('show');
  };

  //บันทึกการรับเอกสาร
  $scope.onReceiveSupport = function(e, form, support) {
    e.preventDefault();
   
    if (form.$invalid) {
        toaster.pop('error', "ผลการตรวจสอบ", "กรุณากรอกข้อมูลให้ครบ !!!");
        return;
    }

    if (support) {
        $http.post(`${CONFIG.baseUrl}/supports/planOnReceive`, $scope.receive, $scope.detail)
        .then(function(res) {
            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "อนุมัติรายการสำเร็จ !!!");

                /** Remove support data that has been received */
                $scope.supports = $scope.supports.filter(el => el.id !== res.data.support.id);

                $scope.getReceiveds(11);
                $scope.clearReceive();
                form.$submitted = false;

                $('#receive-form').modal('hide');
            } else {
                toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถอนุมัติรายการได้ !!!");
            }
        }, function(err) {
            console.log(err);
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถอนุมัติรายการได้ !!!");
        });
      }
  };

  $scope.clearReceive = function() {
    $scope.receive = {
        support_id: '',
        received_no: '',
        received_date: '',
        officer: '',
        remark: ''
    };
  };
  
  $scope.showReturnSupportForm = function(e, support) {
    if (support) {
        $('#plan_bounced_date')
          .datepicker(dtpDateOptions)
          .datepicker('update', new Date())
          .on('changeDate', function(event) {
              console.log(event.date);
        });

        $scope.returnData.support_id = support.id;

        $('#return-form').modal('show');
    }
  };

$scope.onReturnSupport = function(e, form, id) {
  e.preventDefault();

  if (form.$invalid) {
      toaster.pop('error', "ผลการตรวจสอบ", "กรุณากรอกข้อมูลให้ครบ !!!");
      return;
  }

  /** Add user's id from laravel auth user */
  $scope.returnData.user = $('#user').val();

  $http.put(`${CONFIG.apiUrl}/supports/${id}/plan_return`, $scope.returnData)
      .then(function(res) {
          if (res.data.status == 1) {
              toaster.pop('success', "ผลการทำงาน", "ตีกลับเอกสารเรียบร้อย !!!");

              $scope.getSupports();
              $scope.getReceiveds(10);

              $('#return-form').modal('hide');
          } else {
              toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถตีกลับเอกสารได้ !!!");
              console.log(res);
          }
      }, function(err) {
          console.log(err);
          toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถตีกลับเอกสารได้ !!!");
      });
};

$scope.planCancel = function(e, id){
  $scope.loading = true;
  if(confirm(`คุณต้องการยกเลิกการอนุมัติงบประมาณ รหัส ${id} ใช่หรือไม่  ?`)){
    $http.put(`${CONFIG.apiUrl}/supports/${id}/plan-cancel-received` , { status: 10 })
    .then(function(res){
      if(res.data.status == 1){
        toaster.pop('success', "ผลการทำงาน", "ยกเลิกการอนุมัติงบประมาณเรียบร้อย !!!");
        window.location.href = `${CONFIG.baseUrl}/approvesupports/received_supports`;
      } else {
        toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถยกเลิกการอนุมัติงบประมาณ !!!");
      }
      $scope.loading = false;
    }, function(err){
      $scope.loading = false;

      console.log(err);
      toaster.pop('error', "ผลการทำงาน", "พบข้อผิดพลาด ไม่สามารถยกเลิกการอนุมัติงบประมาณ !!!");
    });
  }
};



});
