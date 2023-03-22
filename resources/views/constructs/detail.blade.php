@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายละเอียดแผนก่อสร้าง
            <!-- <small>preview of simple tables</small> -->
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายละเอียดแผนก่อสร้าง</li>
        </ol>
    </section>

    <!-- Main content -->
    <section 
        class="content"
        ng-controller="planConstructCtrl"
        ng-init="
            initForms({
                departs: {{ $departs }},
                divisions: {{ $divisions }},
                categories: {{ $categories }},
                groups: {{ $groups }}
            }, 4);
            getById({{ $plan->id }}, setEditControls);
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">
                            รายละเอียดแผนก่อสร้าง
                            <span ng-show="{{ $plan->plan_no }}"> : เลขที่ ({{ $plan->plan_no }})</span>
                        </h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group col-md-6">
                                    <label>ในแผน/นอกแผน :</label>
                                    <div class="form-control">
                                        <div ng-show="construct.in_plan == 'I'">
                                            <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                            ในแผน 
                                        </div>
                                        <div ng-show="construct.in_plan == 'O'">
                                            <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                            นอกแผน
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>ปีงบ :</label>
                                    <div class="form-control">@{{ construct.year }}</div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>กลุ่มภารกิจ :</label>
                                    <div class="form-control">
                                        @{{ construct.faction.faction_name }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>กลุ่มงาน :</label>
                                    <div class="form-control">
                                        @{{ construct.depart.depart_name }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>งาน :</label>
                                    <div class="form-control">
                                        @{{ construct.division ? construct.division.ward_name : '-' }}
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>รายการ :</label>
                                    <div class="form-control">@{{ construct.desc }}</div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>สถานที่ :</label>
                                    <div class="form-control">@{{ construct.location }}</div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>อาคาร :</label>
                                    <div class="form-control">
                                        @{{ construct.building.building_name }}
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>เลขที่ BOQ :</label>
                                    <div class="form-control">
                                        @{{ construct.boq_no }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>ราคาต่อหน่วย :</label>
                                    <div class="form-control">
                                        @{{ construct.price_per_unit | currency:'':2 }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>จำนวนที่ขอ :</label>
                                    <div style="display: flex; gap: 5px;">
                                        <div class="form-control">
                                            @{{ construct.amount | currency:'':0 }}
                                        </div>
                                        <div class="form-control">
                                            @{{ construct.unit.name }}
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'sum_price')}"
                                >
                                    <label>รวมเป็นเงิน :</label>
                                    <div class="form-control">
                                        @{{ construct.sum_price | currency:'':2 }}
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>แหล่งเงินงบประมาณ :</label>
                                    <div class="form-control">
                                        @{{ construct.budgetSrc.name }}
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>เหตุผล :</label>
                                    <textarea
                                        id="reason" 
                                        name="reason" 
                                        ng-model="construct.reason" 
                                        class="form-control"
                                        rows="4"
                                    ></textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark" 
                                        name="remark" 
                                        ng-model="construct.remark" 
                                        class="form-control"
                                        rows="4"
                                    ></textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>เดือนที่จะดำเนินการ :</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                        <div class="form-control">
                                            @{{ construct.start_month && getMonthName(construct.start_month) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>สถานะ :</label>
                                    <div style="border: 1px solid #d2d6de; height: 34px; display: flex; align-items: center; padding: 0 5px;">
                                        <span class="label label-primary" ng-show="construct.status == 0">
                                            รอดำเนินการ
                                        </span>
                                        <span class="label label-info" ng-show="construct.status == 1">
                                            ดำเนินการแล้วบางส่วน
                                        </span>
                                        <span class="label bg-navy" ng-show="construct.status == 2">
                                            ดำเนินการครบแล้ว
                                        </span>
                                        <span class="label label-default" ng-show="construct.status == 9">
                                            อยู่ระหว่างการจัดซื้อ
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-12" style="margin-bottom: 15px;" ng-show="construct.boq_file">
                                    <label>เอกสารแนบ :</label>
                                    <div style="display: flex; flex-direction: row; justify-content: flex-start;">
                                        <a  href="{{ url('/'). '/uploads/' }}@{{ construct.boq_file }}"
                                            title="ไฟล์แนบ"
                                            target="_blank">
                                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                                            @{{ construct.boq_file }}
                                        </a>

                                        <span style="margin-left: 10px;">
                                            <a href="#">
                                                <span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>
                                            </a>
                                        </span>
                                    </div>
                                </div>

                                <!-- ======================= รายละเอียดการปรับแผน ======================= -->
                                <div class="col-md-12" ng-show="construct.is_adjust" style="padding: 10px; background-color: #EFEFEF;">
                                    @include('shared._adjust-list')
                                </div>
                                <!-- ======================= รายละเอียดการปรับแผน ======================= -->

                            </div>

                            <!-- ======================= Action buttons ======================= -->
                            <div class="col-md-2">
                                <div style="display: flex; flex-direction: column; justify-content: center; gap: 0.5rem;">
                                    <a
                                        href="#"
                                        ng-click="edit(construct.id)"
                                        ng-show="!construct.approved"
                                        class="btn btn-warning"
                                    >
                                        <i class="fa fa-edit"></i> แก้ไข
                                    </a>
                                    <a
                                        href="#"
                                        ng-click="onShowChangeForm($event, construct)"
                                        ng-show="!construct.approved && {{ Auth::user()->memberOf->depart_id }} == '4'"
                                        class="btn btn-primary"
                                    >
                                        <i class="fa fa-refresh"></i> เปลี่ยนหมวด
                                    </a>
                                    <form
                                        id="frmDelete"
                                        method="POST"
                                        action="{{ url('/constructs/delete') }}"
                                        ng-show="!construct.approved"
                                    >
                                        <input type="hidden" id="id" name="id" value="@{{ construct.id }}" />
                                        {{ csrf_field() }}
                                        <button
                                            type="submit"
                                            ng-click="delete($event, construct.id)"
                                            class="btn btn-danger btn-block"
                                        >
                                            <i class="fa fa-trash"></i> ลบ
                                        </button>
                                    </form>
                                    <div class="btn-group" style="display: flex;" ng-show="{{ Auth::user()->memberOf->depart_id }} == '4'">
                                        <button type="button" class="btn btn-primary" style="width: 100%;">เปลี่ยนสถานะ</button>
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li ng-hide="construct.status == 0">
                                                <a href="#" ng-click="setStatus($event, construct.id, '0')">
                                                    รอดำเนินการ
                                                </a>
                                            </li>
                                            <li ng-hide="construct.status == 1">
                                                <a href="#" ng-click="setStatus($event, construct.id, '1')">
                                                    ดำเนินการแล้วบางส่วน
                                                </a>
                                            </li>
                                            <li ng-hide="construct.status == 2">
                                                <a href="#" ng-click="setStatus($event, construct.id, '2')">
                                                    ดำเนินการครบแล้ว
                                                </a>
                                            </li>
                                            <!-- <li ng-hide="construct.status == 9">
                                                <a href="#" ng-click="setStatus($event, construct.id, '9')">
                                                    ยกเลิก
                                                </a>
                                            </li> -->
                                        </ul>
                                    </div>
                                    <button
                                        type="button"
                                        ng-click="showAdjustForm($event, construct)"
                                        ng-show="
                                            (construct.approved && ((construct.status == 0 || construct.status == 1) ||
                                            (construct.status == 2 && construct.have_subitem == 1))) &&
                                            {{ Auth::user()->memberOf->depart_id }} == '4'
                                        "
                                        class="btn bg-maroon"
                                    >
                                        <i class="fa fa-sliders"></i> ปรับเปลี่ยนแผน
                                    </button>
                                </div>
                                <!-- ======================= Action buttons ======================= -->

                            </div>
                            <!-- ======================= Action buttons ======================= -->

                        </div><!-- /.row -->
                    </div><!-- /.box-body -->
                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('shared._adjust-form')
        @include('shared._change-form')

    </section>

    <script>
        $(function () {
            $('.select2').select2();

            $('#unit_id').select2({ theme: 'bootstrap' });

            $('#price_per_unit').inputmask("currency", { "placeholder": "0" });

            $('#amount').inputmask("currency",{ "placeholder": "0", digits: 0 });

            $('#sum_price').inputmask("currency", { "placeholder": "0" });
        });
    </script>

@endsection