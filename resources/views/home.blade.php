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

        @include('dashboard._stat-cards')

        @include('dashboard._stat-cards2')

        <div class="row">
            <section class="col-lg-6 connectedSortable">

                @include('dashboard._assets')

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