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

        <!-- Main row -->
        <div class="row">

            <!-- <section class="col-lg-6 connectedSortable">
                
                <div id="barContainer1" style="width: 100%; height: 400px; margin: 0 auto; margin-top: 20px;"></div>

            </section>

            <section class="col-lg-6 connectedSortable">

                <div id="barContainer2" style="width: 100%; height: 400px; margin: 0 auto; margin-top: 20px;"></div>

            </section> -->

            <section class="col-lg-6 connectedSortable">

                @include('dashboard._head')

            </section>

            <section class="col-lg-6 connectedSortable">

                @include('dashboard._depart')

            </section>

        </div><!-- /.row (main row) -->

    </section>

    <script>
        $(function () {
            $('.select2').select2();
        });
    </script>
@endsection