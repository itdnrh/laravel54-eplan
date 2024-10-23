app.controller('invoiceCtrl', function(CONFIG, $rootScope, $scope, $http, toaster, StringFormatService) {
    $scope.cboYear = parseInt(moment().format('MM')) > 9 ? (moment().year() + 544).toString() : (moment().year() + 543).toString();
    $scope.cboInvoiceItem = '';
    $scope.cboInvoiceItemDetail = '';
    $scope.cboFaction = '';
    $scope.cboDepart = '';
    $scope.cboDivision = '';

    $scope.sumInvoices = 0;
    $scope.invoices = [];
    $scope.pager = [];
    

    $scope.invoice = {
        invoice_item_id:'',
        invoice_detail_id:'',
        sum_price:'',
        remark:'',
        depart_id: '',
        division_id: '',
        year: 2568,
        ivh_year: 2568,
        ivh_id: '',
        contact_detail: '',
        contact_person: '',
        head_of_depart_detail: '',
        head_of_depart: '',
        head_of_faction_detail: '',
        head_of_faction: '',
        user: null
    }
   
    
    //console.log($scope.invoice.depart_id);
    
    
 
    $scope.getAll = function() {
        //  $scope.loading = true;
        //  $scope.invoice = [];
        //  $scope.pager = null;

        let year    = $scope.cboYear === '' ? '' : $scope.cboYear;
        let type    = $scope.cboPlanType === '' ? '' : $scope.cboPlanType;
        let faction = $('#depart').val() == '4' ? $scope.cboFaction : $('#faction').val();
        let depart  = ($('#duty').val() == '1' || ['4','65'].includes($('#depart').val()))
                        ? !$scope.cboDepart ? '' : $scope.cboDepart
                        : $('#depart').val();
        let division = $scope.cboDivision != '' ? $scope.cboDivision : '';
        //let doc_no  = $scope.txtKeyword === '' ? '' : $scope.txtKeyword;
        //let desc    = $scope.txtDesc === '' ? '' : $scope.txtDesc;
        //let cate    = !$scope.cboCategory ? '' : $scope.cboCategory;
        //let in_plan = $scope.cboInPlan === '' ? '' : $scope.cboInPlan;
        let status  = $scope.cboStatus === '' ? '0-89' : $scope.cboStatus; ///0-9
        //let sdate   = $scope.dtpSdate === '' ? '' : $scope.dtpSdate;
        //let edate   = $scope.dtpEdate === '' ? '' : $scope.dtpEdate;

        // $http.get(`${CONFIG.baseUrl}/invoice/search?year=${year}`)
        // .then(function(res) {
        //   //console.log(res);
          
        //     $scope.setInvoice(res);

        //     $scope.loading = false;
        // }, function(err) {
        //     console.log(err);
        //     $scope.loading = false;
        // });
        console.log(year);
        
    }

    $scope.setInvoice = function(res) {
      const { data, ...pager } = res.data.invoices;

      $scope.invoices = data;
      $scope.pager = pager;

      $scope.sumInvoices = res.data.sumInvoices;
  } ;

    $scope.initFiltered = () => {
        if ($('#duty').val() == '1' || $('#depart').val() == '65') {
            let faction = $('#faction').val();
    
            $scope.cboFaction = faction;
            $scope.onFactionSelected(faction);
        }
    };

    $scope.clearNewItem = () => {
        $scope.newItem = {
            plan: null,
            plan_id: '',
            item_id: '',
            item: null,
            subitem_id: '',
            desc: '',
            price_per_unit: '',
            unit_id: '',
            unit_name: '',
            amount: '',
            sum_price: '',
            error: null,
            planItem: null,
        };
    };



    $scope.onValidateForm = function(e, form, cb) {
      e.preventDefault();

      $scope.invoice.depart_id = $('#depart_id').val();
      $scope.invoice.division_id = $('#division_id').val();

      $rootScope.formValidate(e, '/invoice/validate', $scope.invoice, 'frmNewInvoice', $scope.store)
  };

  $scope.store = function() {
  
    $scope.loading = true;
    
    /** Set user props of support model by logged in user */
    $scope.invoice.user = $('#user').val();

    $http.post(`${CONFIG.baseUrl}/invoice/store`, $scope.invoice)
    .then(function(res) {
        $scope.loading = false;

        if (res.data.status == 1) {
            toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");

            window.location.href = `${CONFIG.baseUrl}/invoice/list`;
        } else {
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
        }
    }, function(err) {
        $scope.loading = false;

        console.log(err);
        toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
    });
    };

    $scope.delete = function(e, id) {
      e.preventDefault();

      if(confirm(`คุณต้องลบรายการคำขอ รหัส ${id} ใช่หรือไม่?`)) {
          $scope.loading = true;

          $http.post(`${CONFIG.baseUrl}/invoice/delete/${id}`)
          .then(res => {
              console.log(res);
              $scope.loading = false;

              if (res.data.status == 1) {
                  toaster.pop('success', "ผลการทำงาน", "ลบข้อมูลเรียบร้อย !!!");

                  /** TODO: Reset supports model */
                  $scope.setInvoice(res);
              } else {
                  toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลบข้อมูลได้ !!!");
              }
          }, err => {
              console.log(err);
              $scope.loading = false;
              toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถลบข้อมูลได้ !!!");
          });
      } else {
          $scope.loading = false;
      }
  };


  // Edit Part 
  $scope.getById = function(id, cb) {
    $scope.loading = true;
    
    $http.get(`${CONFIG.apiUrl}/invoice/${id}`)
    .then(function(res) {
        cb(res.data.invoice, res.data.committees);
        //console.log(res.data.invoice);
        $scope.loading = false;
    }, function(err) {
        console.log(err);
        $scope.loading = false;
    });
};

$scope.setEditControls = function(invoice, committees) {
    if (invoice) {
        $scope.invoice.ivh_id              = invoice.ivh_id;
        $scope.invoice.sum_price           = invoice.sum_price;
        $scope.invoice.remark              = invoice.remark;
        $scope.invoice.year                = invoice.ivh_year.toString();
        $scope.invoice.invoice_item_id     = invoice.invoice_item_id.toString();
        $scope.invoice.invoice_detail_id   = invoice.invoice_detail_id.toString();
        // $scope.invoice.contact_person   = invoice.contact.person_id;
        // $scope.invoice.contact_detail   = `${invoice.contact.person_firstname} ${support.contact.person_lastname} โทร.${support.contact.person_tel}`;
        // $scope.invoice.head_of_depart_detail = invoice.head_of_depart_detail;
        // $scope.invoice.head_of_depart   = invoice.head_of_depart;
        // $scope.invoice.head_of_faction_detail = invoice.head_of_faction_detail;
        // $scope.invoice.head_of_faction  = invoice.head_of_faction;

        $scope.invoice.depart_id        = invoice.depart_id.toString();
        $scope.invoice.division_id      = invoice.division_id ? invoice.division_id.toString() : '';
        
        // $scope.support.doc_date         = support.doc_date ? StringFormatService.convFromDbDate(support.doc_date) : '';
        // $scope.support.year             = support.year.toString();
        // $scope.support.plan_type_id     = support.plan_type_id.toString();
        // $scope.support.category_id      = support.category_id.toString();
        // $scope.support.topic            = support.topic;
        // $scope.support.is_plan_group    = support.is_plan_group;
        // $scope.support.plan_group_desc  = support.plan_group_desc;
        // $scope.support.plan_group_amt   = support.plan_group_amt;
        // $scope.support.total            = support.total;
        // $scope.support.reason           = support.reason;

        // $scope.support.contact_person   = support.contact.person_id;
        // $scope.support.contact_detail   = `${support.contact.person_firstname} ${support.contact.person_lastname} โทร.${support.contact.person_tel}`;
        // $scope.support.head_of_depart_detail = support.head_of_depart_detail;
        // $scope.support.head_of_depart   = support.head_of_depart;
        // $scope.support.head_of_faction_detail = support.head_of_faction_detail;
        // $scope.support.head_of_faction  = support.head_of_faction;

        // $scope.support.depart_id        = support.depart_id.toString();
        // $scope.support.division_id      = support.division_id ? support.division_id.toString() : '';
        // $scope.support.details          = support.details;
        // $scope.support.remark           = support.remark;
        // $scope.support.status           = support.status;

        // $scope.support.returned_date    = support.returned_date;
        // $scope.support.returned_reason  = support.returned_reason;

        // /** Set each committees by filtering from responsed committees data */
        // $scope.support.spec_committee   = committees
        //                                     .filter(com => com.committee_type_id == 1)
        //                                     .map(com => com.person);
        // $scope.support.insp_committee   = committees
        //                                     .filter(com => com.committee_type_id == 2)
        //                                     .map(com => com.person);
        // $scope.support.env_committee    = committees
        //                                     .filter(com => com.committee_type_id == 3)
        //                                     .map(com => com.person);

        // /** Set date value to datepicker input of doc_date */
        // $('#doc_date').datepicker(dtpDateOptions).datepicker('update', moment(support.doc_date).toDate());

        // /** Initial model values in mainCtrl */
        $scope.onInvoiceSelected(invoice.invoice_item_id);
        $scope.setcboInvoice(invoice.invoice_item_id);
        $scope.setcboInvoiceItemDetail(invoice.invoice_detail_id.toString());
        //$scope.onPlanTypeSelected(support.plan_type_id);
        //$scope.setPlanType(support.plan_type_id);
        //$scope.setCboCategory(support.category_id.toString());
        
    }
};


$scope.update = function(e, form) {
    e.preventDefault();

    if(confirm(`คุณต้องแก้ไขบันทึกขอสนับสนุน รหัส ${$scope.invoice.ivh_id} ใช่หรือไม่?`)) {
        $scope.loading = true;

        /** Set user props of support model by logged in user */
        $scope.invoice.user = $('#user').val();

        $http.post(`${CONFIG.baseUrl}/invoice/update/${$scope.invoice.ivh_id}`, $scope.invoice)
        .then(function(res) {
            $scope.loading = false;

            if (res.data.status == 1) {
                toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลเรียบร้อย !!!");

                /** TODO: Reset supports model */
                $scope.setInvoice(res);
                //alert('OKOKOK');
                window.location.href = `${CONFIG.baseUrl}/invoice/list`;
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

    $scope.setInvoice = function(res) {
        const { data, ...pager } = res.data.invoice;

        $scope.invoices = data;
        $scope.pager = pager;

        $scope.sumSupports = res.data.sumInvoices;
    };

  /*
    |-----------------------------------------------------------------------------
    | Person selection processes
    |-----------------------------------------------------------------------------
    */
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
            } else if (parseInt(mode) == 4) {
                $scope.support.contact_detail = person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname + ' โทร.' + person.person_tel;
                $scope.support.contact_person = person.person_id;
            } else  if (parseInt(mode) == 5) {
                $scope.support.head_of_depart_detail = person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname;
                $scope.support.head_of_depart = person.person_id;
            } else {
                $scope.support.head_of_faction_detail = person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname;
                $scope.support.head_of_faction = person.person_id;
            }
        }

        $('#persons-list').modal('hide');
        $scope.selectedMode = '';
    };

    $scope.removePersonItem = (mode, person) => {
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
        }
    };
    


});