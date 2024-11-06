@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายการขอสนับสนุน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายการขอสนับสนุน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="invoiceDetailCtrl"
        ng-init="
            initForms({
                departs: {{ $departs }}, 
                divisions: {{ $divisions }},
                invoice_item: {{ $invoiceItem }},
                invoice_item_detail: {{ $invoiceItemDetail }},
            }, 2);
            initFiltered();
        "
    >
        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><span class="glyphicon glyphicon-filter"></span> ตัวกรอง</h3>
                    </div>

                    <form id="frmSearch" name="frmSearch" role="form">
                        <input
                            type="hidden"
                            id="user"
                            name="user"
                            value="{{ Auth::user()->person_id }}"
                        />
                        <input
                            type="hidden"
                            id="duty"
                            name="duty"
                            value="{{ Auth::user()->memberOf->duty_id }}"
                        />
                        <input
                            type="hidden"
                            id="faction"
                            name="faction"
                            value="{{ Auth::user()->memberOf->faction_id }}"
                        />
                        <input
                            type="hidden"
                            id="depart"
                            name="depart"
                            value="{{ Auth::user()->memberOf->depart_id }}"
                        />

                        <div class="box-body">
                            <div class="row">
                                <div class="form-group col-md-2">
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="cboYear"
                                        name="cboYear"
                                        ng-model="cboYear"
                                        class="form-control"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                                
                                <div class="form-group col-md-2">
                                    <label>สถานะ</label>
                                    <select
                                        id="cboStatus"
                                        name="cboStatus"
                                        ng-model="cboStatus"
                                        ng-change="getAll($event)"
                                        class="form-control"
                                    >
                                        <option value="">ทั้งหมด</option>
                                        <option value="0">รอดำเนินการ</option>
                                        <option value="1">ส่งเอกสารแล้ว</option>
                                        <option value="2">รับเอกสารแล้ว</option>
                                        <option value="3-5">ออกใบสั่งซื้อแล้ว</option>
                                        <option value="4-5">ตรวจรับแล้ว</option>
                                        <option value="5">ส่งเบิกเงินแล้ว</option>
                                        <option value="9">เอกสารถูกตีกลับ</option>
                                        <!-- <option value="99">ยกเลิก</option> -->
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>ประเภทบิล</label>
                                    <select
                                        id="cboInvoiceItem"
                                        name="cboInvoiceItem"
                                        class="form-control select2"
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
                                <div class="form-group col-md-5">
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
                                </div>
                            </div>

                            <div class="row" ng-show="{{ Auth::user()->memberOf->duty_id }} == 1 || {{ Auth::user()->memberOf->depart_id }} == 4 || {{ Auth::user()->memberOf->depart_id }} == 65">
                                <div class="col-md-4" ng-show="{{ Auth::user()->memberOf->depart_id }} == 4">
                                    <div class="form-group">
                                        <label>กลุ่มภารกิจ</label>
                                        <select
                                            id="cboFaction"
                                            name="cboFaction"
                                            ng-model="cboFaction"
                                            class="form-control"
                                            ng-change="onFactionSelected(cboFaction); getAll($event);"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            @foreach($factions as $faction)
                                                <option value="{{ $faction->faction_id }}">
                                                    {{ $faction->faction_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4" ng-show="{{ Auth::user()->memberOf->depart_id }} == 4 || {{ Auth::user()->memberOf->depart_id }} == 65">
                                    <div class="form-group">
                                        <label>กลุ่มงาน</label>
                                        <select
                                            id="cboDepart"
                                            name="cboDepart"
                                            ng-model="cboDepart"
                                            class="form-control select2"
                                            ng-change="onDepartSelected(cboDepart); getAll($event);"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="dep in forms.departs" value="@{{ dep.depart_id }}">
                                                @{{ dep.depart_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>งาน</label>
                                        <select
                                            id="cboDivision"
                                            name="cboDivision"
                                            ng-model="cboDivision"
                                            class="form-control select2"
                                            ng-change="getAll($event);"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="div in forms.divisions" value="@{{ div.ward_id }}">
                                                @{{ div.ward_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="col-md-3"
                                    ng-hide="
                                        {{ Auth::user()->memberOf->duty_id }} == 1 ||
                                        {{ Auth::user()->memberOf->depart_id }} == 4
                                    "
                                >
                                    <div class="form-group">
                                        <label>งาน</label>
                                        <select
                                            id="cboDivision"
                                            name="cboDivision"
                                            ng-model="cboDivision"
                                            class="form-control select2"
                                            ng-change="getAll($event);"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            @foreach($divisions as $division)
                                                @if($division->depart_id == Auth::user()->memberOf->depart_id)
                                                    <option value="{{ $division->ward_id }}">
                                                        {{ $division->ward_name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- <div class="form-group col-md-2">
                                    <label>เลขที่บันทึกขอสนับสนุน</label>
                                    <input
                                        id="txtKeyword"
                                        name="txtKeyword"
                                        ng-model="txtKeyword"
                                        ng-keyup="getAll($event)"
                                        class="form-control"
                                    />
                                </div> -->
                                <!-- <div
                                    class="form-group"
                                    ng-class="{
                                        'col-md-6': {{ Auth::user()->memberOf->duty_id }} == 1 || {{ Auth::user()->memberOf->depart_id }} == 4,
                                        'col-md-3': {{ Auth::user()->memberOf->duty_id }} != 1 && {{ Auth::user()->memberOf->depart_id }} != 4
                                    }"
                                >
                                    <label>รายละเอียด</label>
                                    <input
                                        id="txtDesc"
                                        name="txtDesc"
                                        ng-model="txtDesc"
                                        ng-keyup="getAll($event)"
                                        class="form-control"
                                    />
                                </div> -->
                                <!-- <div class="form-group col-md-2">
                                    <label>วันที่บันทึกขอสนับสนุน</label>
                                    <div class="input-group">
                                        <input
                                            id="dtpSdate"
                                            name="dtpSdate"
                                            ng-model="dtpSdate"
                                            class="form-control"
                                        />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-danger" ng-click="clearDateValue($event, 'dtpSdate', getAll);">
                                                เคลียร์
                                            </button>
                                        </span>
                                    </div>
                                </div> -->
                                <!-- <div class="form-group col-md-2">
                                    <label>ถึงวันที่</label>
                                    <div class="input-group">
                                        <input
                                            id="dtpEdate"
                                            name="dtpEdate"
                                            ng-model="dtpEdate"
                                            class="form-control"
                                        />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-danger" ng-click="clearDateValue($event, 'dtpEdate', getAll);">
                                                เคลียร์
                                            </button>
                                        </span>
                                    </div>
                                </div> -->
                            </div>
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">รายการขอสนับสนุน</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/invoicedetail/add') }}" class="btn btn-primary pull-right">
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
                                    <th style="width: 4%; text-align: center;">#</th>
                                    <th style="width: 5%; text-align: center;">ปีงบ</th>
                                    <th style="width: 5%; text-align: center;">เดือน</th>
                                    <th style="width: 10%; text-align: center;">บันทึก</th>
                                    <th style="width: 18%;">หน่วยงาน</th>
                                    <th style="width: 17%; text-align: center;">ประเภทบิล</th>
                                    <th style="text-align: center;">รายการ</th>
                                    <th style="width: 8%; text-align: center;">ยอดการใช้</th>
                                    <th style="width: 10%; text-align: center;">สถานะ</th>
                                    <th style="width: 8%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" style="text-align:right">รวมเป็นเงิน:</th>
                                    <th colspan="8"></th>
                                </tr>
                            </tfoot>
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

        @include('supports._details-list')

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
                    'footerCallback': function ( row, data, start, end, display ) {
                        var api = this.api(), data;
            
                        // Remove the formatting to get integer data for summation
                        var intVal = function ( i ) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '')*1 :
                                typeof i === 'number' ?
                                    i : 0;
                        };
            
                        // Total over all pages
                        total = api
                            .column( 7 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
            
                        // Total over this page
                        pageTotal = api
                            .column( 7, { page: 'current'} )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                    

                        // Total filtered rows on the selected column (code part added)
                        //var sumCol3Filtered = display.map(el => data[el][3]).reduce((a, b) => intVal(a) + intVal(b), 0 );
                    
                        // Update footer
                        $( api.column( 4 ).footer() ).html(
                            //'บาท '+fm_number(pageTotal) +' ( บาท '+ fm_number(total) +' total) ($' + fm_number(sumCol3Filtered) +' filtered)'
                            ''+fm_number(pageTotal) +' ( '+ fm_number(total) +' รวมทั้งหมด)'
                        );
                    },
                    "ajax": {
                        "url": `{{ url('/invoicedetail/getInvoiceDetail') }}`,  // API endpoint for server-side processing
                        "type": "GET",
                        "data": function (d) {
                            // Pass filter data along with DataTables request
                            d.bdg_year = $('#cboYear').val();
                            d.user = $('#user').val();
                            d.faction = $('#cboFaction').val();
                            d.depart = $('#cboDepart').val();
                            d.status = $('#cboStatus').val();
                            d.invoice_item = $('#cboInvoiceItem').val();
                            d.invoice_item_detail = $('#cboInvoiceItemDetail').val();
                        }
                    },
                    "columns": [
                        { "data": "ivd_id" },
                        { "data": "ivd_year" },
                        { "data": "ivd_month", 
                            render: function(data,type,row){
                                return getThaiMonth(data);
                            }
                        },
                        { "data": "doc_no",
                            render:function(data,type,row) {
                                let date_thai = convertDateFormat(row['doc_date'], { buddhist: true });
                                let doc_label = data+' <br> <small> วันที่ :'+date_thai+'</small>';
                                return doc_label;
                            },
                         },
                        { "data": "depart_name" },
                        { "data": "invoice_item_name" },
                        { "data": "invoice_detail_name" },
                        { "data": "ivd_use_price" , render: $.fn.dataTable.render.number(',', '.', 2, '') , className: "text-right"},
                        { "data": "ivd_status",
                            render: function(data, type, row){
                                let label_status = '';
                                label_status = genLabel(data);
                                return label_status;
                            }
                        },
                        { "data": "ivd_status",
                            render: function(data, type, row){
                            let linkEdit = '';
                            let linkDelete = '';
                            let linkView = '';

                            linkView = `<a href="{{ url('/invoicedetail/detail') }}/${row['ivd_id']}" class="btn btn-primary btn-xs" title="รายละเอียด"><i class="fa fa-search"></i></a>`;
                            if(data == '0' || data == '2'){
                                linkEdit = `<a href="{{ url('/invoicedetail/edit') }}/${row['ivd_id']}"
                                            class="btn btn-warning btn-xs" title="แก้ไขรายการ">
                                            <i class="fa fa-edit"></i>
                                        </a>`;
                                linkDelete=`<a href="#" onclick="deleteInvoiceDetail(${row['ivd_id']})" class="btn btn-danger btn-xs" title="แก้ไขรายการ"><i class="fa fa-trash"></i></a>`;
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