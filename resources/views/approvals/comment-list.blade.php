@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            หัวหน้ากลุ่มงาน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">หัวหน้ากลุ่มงาน</li>
        </ol>
    </section>

    <!-- Main content -->
    <?php $divisions =  count(Auth::user()->delegations) > 0 ? Auth::user()->delegations[0]->divisions : '' ?>
    <section
        class="content"
        ng-controller="approvalCtrl"
        ng-init="onCommentLoad({{ Auth::user()->memberOf->depart_id }}, '{{ $divisions }}')"
    >
        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div>

                    <form id="frmSearch" name="frmSearch" role="form">
                        <div class="box-body">
                            <div class="row">

                                <div class="form-group col-md-6">
                                    <label>ประเภทการลา</label>
                                    <select
                                        id="cboLeaveType"
                                        name="cboLeaveType"
                                        ng-model="cboLeaveType"
                                        class="form-control"
                                        ng-change="getAll($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        @foreach($leave_types as $type)

                                            <option value="{{ $type->id }}">
                                                {{ $type->name }}
                                            </option>

                                        @endforeach
                                    </select>
                                </div><!-- /.form group -->

                            </div><!-- /.row -->
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-body">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#approve" data-toggle="tab">
                                <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                รายการขออนุมัติใบลา
                                <span class="badge badge-light">@{{ leaves.length }}</span>
                            </a></li>
                            <li><a href="#cancel" data-toggle="tab">
                                <i class="fa fa-ban" aria-hidden="true"></i>
                                รายการขอยกเลิกวันลา
                                <span class="badge badge-light">@{{ cancellations.length }}</span>
                            </a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane" id="approve">

                                @if (session('status'))
                                    <div class="alert alert-success alert-dismissible" style="margin: 10px 10px;">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <div class="alert alert-warning alert-dismissible" style="margin: 10px 10px;">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fa fa-warning"></i>
                                    การแสดงรายการที่ลงความเห็นแล้ว จะแสดงเฉพาะรายที่รอการลงรับเอกสารเท่านั้น !!
                                </div>

                                <div class="card">
                                    <div class="card-body" style="padding: 10px 10px;">
                                        <input
                                            type="checkbox"
                                            name="showAllApproves"
                                            id="showAllApproves"
                                            ng-model="showAllApproves"
                                            ng-change="onCommentLoad({{ Auth::user()->memberOf->depart_id }}, '{{ $divisions }}')"
                                        />
                                        <span>แสดงรายการที่ลงความเห็นแล้ว</span>
                                    </div>
                                </div>

                                @include('approvals._comment-approves')

                            </div><!-- /.tab-pane -->
                            <div class="tab-pane" id="cancel">

                                <!-- <div class="card">
                                    <div class="card-body" style="padding: 10px 10px;">
                                        <input
                                            type="checkbox"
                                            name="showAllCancels"
                                            id="showAllCancels"
                                            ng-model="showAllCancels"
                                        />
                                        <span>แสดงรายการที่ลงความเห็นแล้ว</span>
                                    </div>
                                </div> -->

                                @include('approvals._comment-cancels')

                            </div><!-- /.tab-pane -->
                        </div><!-- /.tab-content -->

                        @include('approvals._comment-form')
                        @include('approvals._cancel-comment-form')
                        @include('approvals._approval-detail')

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
            $('.select2').select2()

            //Date range picker with time picker
            $('#debtDate').daterangepicker({
                timePickerIncrement: 30,
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: " , ",
                }
            }, function(e) {
                console.log(e);
            });
        });
    </script>

@endsection