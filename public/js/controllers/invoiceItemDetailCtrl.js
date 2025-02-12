app.controller('invoiceItemDetailCtrl', function(CONFIG, $rootScope, $scope, $http, toaster, StringFormatService) {

   // Initialize AngularJS models from Blade data
   //$scope.invoiceDetails = invoiceDetails;
   //$scope.invoiceItems = invoiceItems;
   //$scope.cboInvoiceItem = '';
   //$scope.invoice_detail_name = '';
   $scope.cboInvoiceItem = '';
   // Invoice Detail model
   $scope.invoiceItemDetail = {
    cboInvoiceItem: '',
    invoice_detail_name: '',
    invoice_item_id: '',
    invoice_detail_id: ''
   };

   $scope.onValidateForm = function(e, form, cb) {
    e.preventDefault();

    $scope.invoiceItemDetail.invoice_item_id = $('#invoice_item_id').val();
    $scope.invoiceItemDetail.invoice_detail_name = $('#invoice_detail_name').val();

    $rootScope.formValidate(e, '/invoiceitem/validate', $scope.invoiceItemDetail, 'frmNewInvoiceItemDetail', $scope.store)
  };

  $scope.store = function(){
    $scope.loading = true;
    
    $http.post(`${CONFIG.baseUrl}/invoiceitem/store`, $scope.invoiceItemDetail)
    .then(function(res) {
        $scope.loading = false;

        if (res.data.status == 1) {
            toaster.pop('success', "ผลการทำงาน", "บันทึกข้อมูลเรียบร้อย !!!");

            window.location.href = `${CONFIG.baseUrl}/invoiceitem/list`;
        } else {
            toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
        }
    }, function(err) {
        $scope.loading = false;

        console.log(err);
        toaster.pop('error', "ผลการตรวจสอบ", "ไม่สามารถบันทึกข้อมูลได้ !!!");
    });
  }

  $scope.getById = function(id, cb) {
    $scope.loading = true;
    
    $http.get(`${CONFIG.apiUrl}/invoiceitem/getById/${id}`)
    .then(function(res) {
        cb(res.data.invoiceItemDetail);
        //console.log(res.data.invoiceItemDetail);
        
        $scope.loading = false;
    }, function(err) {
        console.log(err);
        $scope.loading = false;
    });
  };

  $scope.setEditControls = function(invoiceItemDetail) {   
   // console.log(invoiceItemDetail.invoice_detail_id);
    
       if (invoiceItemDetail) {
           //$scope.invoiceItemDetail.cboInvoiceItem         = invoiceItemDetail.invoice_item_id;
           $scope.invoiceItemDetail.invoice_item_id = invoiceItemDetail.invoice_item_id.toString();
           $scope.invoiceItemDetail.invoice_detail_name = invoiceItemDetail.invoice_detail_name ? invoiceItemDetail.invoice_detail_name.toString() : "";
           $scope.invoiceItemDetail.invoice_detail_id  = invoiceItemDetail.invoice_detail_id.toString();
           $scope.invoice_item_id = invoiceItemDetail.invoice_item_id;
           //$scope.setcboInvoice(invoiceItemDetail.invoice_item_id);
           //$scope.setcboInvoice(invoiceItemDetail.invoice_item_id.toString());
       }
   };

   // Save or update an invoice detail
   $scope.saveInvoiceDetail = function() {
     if ($scope.invoiceDetail.invoice_detail_id) {
       // Update
       $http.put(`/api/invoice-details/${$scope.invoiceDetail.invoice_detail_id}`, $scope.invoiceDetail)
         .then(function(response) {
           alert('Invoice Detail updated!');
           $scope.invoiceDetails.push(response.data);
         }).catch(function(error) {
           alert('Error updating invoice detail');
         });
     } else {
       // Create
       $http.post('/api/invoice-details', $scope.invoiceDetail)
         .then(function(response) {
           alert('Invoice Detail created!');
           $scope.invoiceDetails.push(response.data);
         }).catch(function(error) {
           alert('Error creating invoice detail');
         });
     }

     // Reset form
     $scope.invoiceItemDetail = {
       invoice_detail_name: '',
       invoice_item_id: ''
     };
   };

   
$scope.update = function(e, form) {
  e.preventDefault();

  if(confirm(`คุณต้องแก้ไขบันทึกขอสนับสนุน รหัส ${$scope.invoiceItemDetail.invoice_detail_id} ใช่หรือไม่?`)) {
      $scope.loading = true;

      /** Set user props of support model by logged in user */
      //$scope.invoice.user = $('#user').val();

      $http.post(`${CONFIG.baseUrl}/invoiceitem/update/${$scope.invoiceItemDetail.invoice_detail_id}`, $scope.invoiceItemDetail)
      .then(function(res) {
          $scope.loading = false;

          if (res.data.status == 1) {
              toaster.pop('success', "ผลการทำงาน", "แก้ไขข้อมูลเรียบร้อย !!!");

              /** TODO: Reset supports model */
              //$scope.setInvoice(res);
              //alert('OKOKOK');
              window.location.href = `${CONFIG.baseUrl}/invoiceitem/list`;
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

   // Delete an invoice detail
   $scope.deleteInvoiceDetail = function(id) {
     if (confirm('Are you sure you want to delete this invoice detail?')) {
       $http.delete(`/api/invoice-details/${id}`)
         .then(function(response) {
           alert('Invoice Detail deleted!');
           // Remove from the list
           $scope.invoiceDetails = $scope.invoiceDetails.filter(function(detail) {
             return detail.invoice_detail_id !== id;
           });
         }).catch(function(error) {
           alert('Error deleting invoice detail');
         });
     }
   };

   // Edit an invoice detail
   $scope.editInvoiceDetail = function(detail) {
     $scope.invoiceDetail = angular.copy(detail);  // Populate form with existing data
   };
});