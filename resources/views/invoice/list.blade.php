@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายการคำขอบิลเรียกเก็บจากภาครัฐ
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายการคำขอบิลเรียกเก็บจากภาครัฐ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="invoiceCtrl"
        ng-init="
            getAll();
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
                                <h3 class="box-title">รายการคำขอบิลเรียกเก็บจากภาครัฐ</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/invoice/add') }}" class="btn btn-primary pull-right">
                                    เพิ่มรายการ
                                </a>
                                <a href="#" ng-show="invoice.length" ng-click="exportListToExcel($event)" class="btn btn-success pull-right" style="margin-right: 5px;">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                    Excel
                                </a>
                            </div>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="tableInvoice" style="font-size: 14px; margin-bottom: 10px;">
                            <thead>
                                <tr>
                                    <th style="width: 4%; text-align: center;">#</th>
                                    <th style="width: 5%; text-align: center;">ปีงบ</th>
                                    <th style="width: 18%;">หน่วยงาน</th>
                                    <th style="width: 17%; text-align: center;">ประเภทบิล</th>
                                    <th style="text-align: center;">รายการ</th>
                                    <th style="width: 8%; text-align: center;">ยอดรวม</th>
                                    <th style="width: 8%; text-align: center;">คงเหลือ</th>
                                    <th style="width: 10%; text-align: center;">สถานะ</th>
                                    <th style="width: 8%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, invoice) in invoices">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td style="text-align: center;">
                                        @{{ invoice.invoice_item_name }}
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ invoice.ivh_year }}
                                    </td>
                                    <td> @{{ invoice.depart_name }}</td>
                                    <td>@{{ invoice.invoice_detail_name }}</td>
                                    <td style="text-align: center;">@{{ invoice.sum_price | currency:'':2 }}</td>
                                    <td></td>
                                    <td style="text-align: center;">
                                        <a  href="{{ url('/invoice/detail') }}/@{{ invoice.ivh_id }}"
                                            class="btn btn-primary btn-xs" 
                                            title="รายละเอียด">
                                            <i class="fa fa-search"></i>
                                        </a>
                                        <a  href="{{ url('/invoice/edit') }}/@{{ invoice.ivh_id }}"
                                            class="btn btn-warning btn-xs"
                                            ng-show="invoice.ivh_status == 0 || invoice.ivh_status == 9"
                                            title="แก้ไขรายการ">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form
                                            id="frmDelete"
                                            method="POST"
                                            action="{{ url('/invoice/delete') }}"
                                            style="display: inline;"
                                            ng-show="invoice.ivh_status == 0 || invoice.ivh_status == 9"
                                        >
                                            {{ csrf_field() }}
                                            <button
                                                type="submit"
                                                ng-click="delete($event, invoice.ivh_id)"
                                                class="btn btn-danger btn-xs"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>        
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" style="text-align:right">รวมเป็นเงิน:</th>
                                    <th colspan="6"></th>
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
                tableInvoice.draw();
            });
            $("#cboStatus").change(function(){
                tableInvoice.draw();
            });
            $("#cboInvoiceItem").change(function(){
                tableInvoice.draw();
            });
            $("#cboInvoiceItemDetail").change(function(){
                tableInvoice.draw();
            });
            $("#cboFaction").change(function(){
                tableInvoice.draw();
            });
            $("#cboDepart").change(function(){
                tableInvoice.draw();
            });
                let tableInvoice = $('#tableInvoice').DataTable({
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
                            .column( 6 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
            
                        // Total over this page
                        pageTotal = api
                            .column( 6, { page: 'current'} )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                    

                        // Total filtered rows on the selected column (code part added)
                        //var sumCol3Filtered = display.map(el => data[el][3]).reduce((a, b) => intVal(a) + intVal(b), 0 );
                    
                        // Update footer
                        $( api.column( 3 ).footer() ).html(
                            //'บาท '+fm_number(pageTotal) +' ( บาท '+ fm_number(total) +' total) ($' + fm_number(sumCol3Filtered) +' filtered)'
                            ''+fm_number(pageTotal) +' ( '+ fm_number(total) +' รวมทั้งหมด)'
                        );
                    },
                    "ajax": {
                        "url": `{{ url('/invoice/getinvoice') }}`,  // API endpoint for server-side processing
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
                        { "data": "ivh_id" },
                        { "data": "ivh_year" },
                        { "data": "depart_name" },
                        { "data": "invoice_item_name" },
                        { "data": "invoice_detail_name" },
                        { "data": "sum_price" , render: $.fn.dataTable.render.number(',', '.', 2, '') , className: "text-right"},
                        { "data": "remain_price" , render: $.fn.dataTable.render.number(',', '.', 2, '') , className: "text-right"},
                        { "data": "ivh_status",
                            render: function(data, type, row){
                                let label_status = '';
                                if(data == 0){
                                    label_status = `<span class="label label-default">รอดำเนินการ</span>`;
                                } else if(data == 1){
                                    label_status = `<span class="label label-info">อยู่ระหว่างดำเนินการ</span>`;
                                } else if(data == 2){
                                    label_status = `<span class="label label-success">ดำเนินการเสร็จสิ้น</span>`;
                                }
                                return label_status;
                            }
                        },
                        { "data": "ivh_status",
                            render: function(data, type, row){
                            let linkEdit = '';
                            let linkDelete = '';
                            if(data == '0'){
                                linkEdit = `<a  href="{{ url('/invoice/edit') }}/${row['ivh_id']}"
                                            class="btn btn-warning btn-xs" title="แก้ไขรายการ">
                                            <i class="fa fa-edit"></i>
                                        </a>`;
                                        // linkEdit = `<a  href="{{ url('/invoice/edit') }}/@{{ invoice.ivh_id }}"
                                        //     class="btn btn-warning btn-xs"
                                        //     ng-show="invoice.ivh_status == 0 || invoice.ivh_status == 9"
                                        //     title="แก้ไขรายการ">
                                        //     <i class="fa fa-edit"></i>
                                        // </a>`;
                                linkDelete=`<a href="#" onclick="deleteInvoice(${row['ivh_id']})" class="btn btn-danger btn-xs" title="แก้ไขรายการ"><i class="fa fa-remove"></i></a>`;
                            }
                            return '<center>'+linkEdit+' '+linkDelete+'</center>';
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
            function deleteInvoice(ivh_id){
                if (confirm('ต้องการลบรายการคำขอใช่หรือไม่ ?')) {
                    $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                    });
                    $.ajax({ 
                        type: "POST", 
                        url:`{{url('invoice/delete')}}/${ivh_id}`, 
                        data:{ivh_id:ivh_id},
                        success: function(result) {
                            if(result.status == 1){
                                alert('ลบรายการสำเร็จ');
                                $('#tableInvoice').DataTable().ajax.reload();
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
    </script>

@endsection