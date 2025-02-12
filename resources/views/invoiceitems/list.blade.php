@extends('layouts.main')
@section('content')
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>รายการบิล</h1>

    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
      <li class="breadcrumb-item active">รายการบิล</li>
    </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="invoiceDetailCtrl"
        ng-init="
            initForms({
                invoice_item: {{ $invoiceItem }},
                invoice_item_detail: {{ $invoiceItemDetail }},
            }, 2);
        "
    >
        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><span class="glyphicon glyphicon-filter"></span> ตัวกรอง</h3>
                    </div>

                    <form id="frmSearch" name="frmSearch" role="form">
                        

                        <div class="box-body">
                            <div class="row">
                              
                                <div class="form-group col-md-3">
                                    <label>ประเภทบิล</label>
                                    <select
                                        id="cboInvoiceItem"
                                        name="cboInvoiceItem"
                                        class="form-control"
                                        ng-model="cboInvoiceItem"
                                        ng-change="
                                            onFilterInvoiceItemDetail(cboInvoiceItem);
                                            getAll($event);
                                        "
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        @foreach($invoiceItem as $ivi)
                                            <option value="{{ $ivi->invoice_item_id }}">
                                                {{ $ivi->invoice_item_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- <div class="form-group col-md-5">
                                    <label>รายการบิล</label>
                                    <select
                                        id="cboInvoiceItemDetail"
                                        name="cboInvoiceItemDetail"
                                        ng-model="cboInvoiceItemDetail"
                                        class="form-control"
                                        ng-change="getAll($event);"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="ivd in forms.invoice_item_detail" value="@{{ ivd.invoice_detail_id }}">
                                            @{{ ivd.invoice_detail_name }}
                                        </option>
                                    </select>
                                </div> -->
                            </div>


                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">รายการบิล</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/invoiceitem/add') }}" class="btn btn-primary pull-right">
                                    เพิ่มรายการ
                                </a>
                                <a href="#" ng-show="invoicedetail.length" ng-click="exportListToExcel($event)" class="btn btn-success pull-right" style="margin-right: 5px;">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                    Excel
                                </a>
                            </div>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="tableInvoiceDetail" style="font-size: 14px; margin-bottom: 10px;">
                            <thead>
                                <tr>
                                    <th style="width: 5%; text-align: center;">#</th>
                                    <th style="width: 25%; text-align: center;">ประเภทบิล</th>
                                    <th style="width: 45%; text-align: center;">รายการบิล</th>
                                    <th style="width: 10%; text-align: center;">create_at</th>
                                    <th style="width: 10%; text-align: center;">update_at</th>
                                    <th style="width: 10%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                        
                    </div><!-- /.box-body -->
                    <!-- Loading (remove the following to stop the loading)-->
                    <div ng-show="loading" class="overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <!-- end loading -->

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->



    </section>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2();
        });

        angular.element(document).ready(function () {
            $("#cboYear").change(function(){
                tableInvoiceDetail.draw();
            });
            $("#cboStatus").change(function(){
                tableInvoiceDetail.draw();
            });
            $("#cboInvoiceItem").change(function(){
                tableInvoiceDetail.draw();
            });
            $("#cboInvoiceItemDetail").change(function(){
                tableInvoiceDetail.draw();
            });
            $("#cboFaction").change(function(){
                tableInvoiceDetail.draw();
            });
            $("#cboDepart").change(function(){
                tableInvoiceDetail.draw();
            });
                let tableInvoiceDetail = $('#tableInvoiceDetail').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "stateSave": true,
                    "ajax": {
                        "url": `{{ url('/invoiceitem/getInvoiceItemDetail') }}`,  // API endpoint for server-side processing
                        "type": "GET",
                        "data": function (d) {
                            // Pass filter data along with DataTables request
                            //d.cboInvoiceItem = $('#cboInvoiceItem').val();
                            // d.user = $('#user').val();
                            // d.faction = $('#cboFaction').val();
                            // d.depart = $('#cboDepart').val();
                            // d.status = $('#cboStatus').val();
                            d.invoice_item = $('#cboInvoiceItem').val();
                            // d.invoice_item_detail = $('#cboInvoiceItemDetail').val();
                        }
                    },
                    "columns": [
                        { "data": "invoice_detail_id" },
                        { "data": "invoice_item_name" },
                        { "data": "invoice_detail_name", 
                            render: function(data,type,row){
                                return (data);
                            }
                        },
                        { "data": "created_at" },
                        { "data": "updated_at" },
                        { "data": "can_add_detail",
                            render: function(data, type, row){
                            let linkEdit = '';
                            let linkDelete = '';
                            let linkView = '';

                            //linkView = `<a href="{{ url('/invoicedetail/detail') }}/${row['ivd_id']}" class="btn btn-primary btn-xs" title="รายละเอียด"><i class="fa fa-search"></i></a>`;
                            if(data == 'Y'){
                                linkEdit = `<a href="{{ url('/invoiceitem/edit') }}/${row['invoice_detail_id']}"
                                            class="btn btn-warning btn-xs" title="แก้ไขรายการ">
                                            <i class="fa fa-edit"></i>
                                        </a>`;
                               // linkDelete=`<a href="#" onclick="deleteInvoiceDetail(${row['ivd_id']})" class="btn btn-danger btn-xs" title="แก้ไขรายการ"><i class="fa fa-trash"></i></a>`;
                            }
                            return '<center>'+linkView+' '+linkEdit+' '+linkDelete+'</center>';
                            }
                        }
                    ],
                    "order": [[1, 'asc']]
                });
            });
            function fm_number(numb){
                let new_number = numb.toLocaleString("th-TH", {style:"currency", currency:"THB"});
                return new_number;
            }

            function genLabel(status){
                let label_status = '';
                if(status == 0){
                    label_status = `<span class="label label-default">รอดำเนินการ</span>`;
                } else if(status == 1){
                    label_status = `<span class="label label-info">ส่งเอกสารแล้ว</span>`;
                } else if(status == 2){
                    label_status = `<span class="label label-warning">ตีกลับ</span>`;
                } else if(status == 3){
                    label_status = `<span class="label label-success">ดำเนินการแล้ว</span>`;
                }
                return label_status;
            }
            function deleteInvoiceDetail(ivd_id){
                if (confirm('ต้องการลบรายการคำขอใช่หรือไม่ ?')) {
                    $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                    });
                    $.ajax({ 
                        type: "POST", 
                        url:`{{url('invoicedetail/delete')}}/${ivd_id}`, 
                        data:{ivd_id:ivd_id},
                        success: function(result) {
                            if(result.status == 1){
                                alert('ลบรายการสำเร็จ');
                                $('#tableInvoiceDetail').DataTable().ajax.reload();
                            } else {
                                alert('ไม่สามารถลบรายการได้ !!');
                                //$('#tableInvoice').DataTable().ajax.reload();
                            }
                            
                        },
                        error: function(xhr) {
                            alert('เกิดข้อผอดพลาดไม่สามารถลบรายการได้');
                        }
                    }); 
                }
                
            }

            function getThaiMonth(month) {
                const thaiMonths = [
                    'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                    'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
                ];
                // ลบ 1 เพราะ array เริ่มที่ 0 แต่เดือนเริ่มที่ 1
                return thaiMonths[month - 1] || '';
            }

            // ฟังก์ชันที่มีความยืดหยุ่นมากขึ้น
function convertDateFormat(dateString, options = {}) {
    const {
        inputFormat = 'YYYY-MM-DD',
        outputFormat = 'DD/MM/YYYY',
        separator = '/',
        buddhist = false // true สำหรับพ.ศ.
    } = options;

    if (!dateString) return '';

    try {
        // แยกส่วนประกอบของวันที่
        const date = new Date(dateString);
        if (isNaN(date)) return '';

        let day = date.getDate().toString().padStart(2, '0');
        let month = (date.getMonth() + 1).toString().padStart(2, '0');
        let year = date.getFullYear();

        // แปลงเป็นพ.ศ. ถ้าต้องการ
        if (buddhist) {
            year += 543;
        }

        // จัดรูปแบบตาม outputFormat
        let result = outputFormat;
        result = result.replace('DD', day);
        result = result.replace('MM', month);
        result = result.replace('YYYY', year);

        return result;
    } catch (e) {
        console.error('Error converting date:', e);
        return '';
    }
}
    </script>

@endsection