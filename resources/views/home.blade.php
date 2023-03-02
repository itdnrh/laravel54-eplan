@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <!-- <small>Control panel</small> -->
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" ng-controller="homeCtrl">
        <!-- /** Filtering Tools */ -->
        <div class="row">
            <div class="col-md-12">
                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center;">
                        <label for="">ประจำปี :</label>
                        <input
                            type="text"
                            id="dtpYear"
                            name="dtpYear"
                            ng-model="dtpYear"
                            class="form-control"
                            style="width: 180px; margin-left: 5px;"
                        />
                    </div>

                    <div class="btn-group btn-group-toggle" data-toggle="buttons" style="margin-left: 10px;">
                        <label class="btn btn-default" ng-click="onApprovedToggle($event)">
                            <input type="radio" id="all" name="approved" value="" autocomplete="off" /> ทั้งหมด
                        </label>
                        <label class="btn btn-default" ng-click="onApprovedToggle($event)">
                            <input type="radio" id="none" name="approved" value="1" autocomplete="off" /> ไม่อนุมัติ
                        </label>
                        </label>
                        <label class="btn btn-default active" ng-click="onApprovedToggle($event)">
                            <input type="radio" id="approved" name="approved" value="2" autocomplete="off" checked /> อนุมัติ
                        </label>
                    </div>

                    <div style="display: flex; gap: 10px; align-items: center; margin-left: 20px;">
                        <label for="">
                            <input type="radio" name="inPlan" value="I" checked ng-click="onInPlanToggle($event)" /> ในแผน
                        </label>
                        <label for="">
                            <input type="radio" name="inPlan" value="O" ng-click="onInPlanToggle($event)" /> นอกแผน
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <!-- /** Filtering Tools */ -->

        @include('dashboard._stat-cards')

        @include('dashboard._stat-cards2')

        <div class="row">
            <section class="col-lg-6 connectedSortable">

                @include('dashboard._assets')

                @include('dashboard._services')

                @include('dashboard._constructs')

            </section>
            <section class="col-lg-6 connectedSortable">
    
                @include('dashboard._materials')

            </section>
        </div>
        <div class="row">
            <section class="col-lg-6 connectedSortable">

                @include('dashboard._pie-chart')
                
            </section>
            <section class="col-lg-6 connectedSortable">

                @include('dashboard._project-pie-chart')

            </section>
        </div>
        <div class="row">
            <section class="col-lg-12 connectedSortable">

                @include('dashboard._latest-po')

            </section>
        </div>
    </section>

    <script>
        $(function () {
            $('.select2').select2();
        });
    </script>
@endsection