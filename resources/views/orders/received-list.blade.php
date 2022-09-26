@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รับใบขอสนับสนุน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รับใบขอสนับสนุน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="orderCtrl"
        ng-init="
            getSupports();
            getReceiveds(2);
            initForms({
                departs: {{ $departs }},
                categories: {{ $categories }}
            });"
    >

        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">รับใบขอสนับสนุน</h3>
                            </div>
                        </div>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                {{ session('status') }}
                            </div>
                        @endif

                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#supports-list" data-toggle="tab">
                                    <i class="fa fa-history text-primary" aria-hidden="true"></i>
                                    ใบขอสนับสนุนรอลงรับ
                                    <span class="badge badge-light">@{{ supportsToReceives.length }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="#received-list" data-toggle="tab">
                                    <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                    ใบขอสนับสนุนลงรับแล้ว
                                    <span class="badge badge-light">@{{ leaves.length }}</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content tab__container-bordered">
                            <div class="active tab-pane" id="supports-list">

                                @include('orders._supports-list')

                            </div><!-- /.tab-pane -->
                            <div class="tab-pane" id="received-list">

                                @include('orders._received-list')

                            </div><!-- /.tab-pane -->
                        </div><!-- /.tab-content -->
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
        @include('orders._receive-list')
        @include('orders._receive-form')
        @include('orders._return-form')

    </section>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()
        });
    </script>

@endsection