@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            แก้ไขกลุ่มภารกิจ ID ({{ $faction->faction_id }})
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">แก้ไขกลุ่มภารกิจ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="factionCtrl"
        ng-init="getById('{{ $faction->faction_id }}', setEditControls);"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">แก้ไขกลุ่มภารกิจ ID: {{ $faction->faction_id }}</h3>
                    </div>

                    <form
                        method="post"
                        id="frmEditFaction"
                        name="frmEditFaction"
                        action="{{ url('/factions/update/'.$faction->faction_id) }}"
                        class="form-horizontal"
                        novalidate
                        role="form"
                    >
                        <!-- <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}" /> -->
                        {{ csrf_field() }}
                        
                        <div class="box-body" style="min-height: 70vh; padding: 40px 0;">
                            <div class="row">
                                <div class="form-group" ng-class="{ 'has-error' : checkValidate(faction, 'faction_name')}">
                                    <label class="col-sm-4 col-md-2 control-label">ชื่อกลุ่มภารกิจ :</label>
                                    <div class="col-sm-6 col-md-8">
                                        <input
                                            type="text"
                                            id="faction_name"
                                            name="faction_name"
                                            ng-model="faction.faction_name"
                                            class="form-control"
                                        />
                                        <div class="help-block" ng-show="checkValidate(faction, 'faction_name')">
                                            กรุณากรอกชื่อกลุ่มภารกิจ
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="display: flex; align-items: center;">
                                    <label class="col-sm-4 col-md-2 control-label">Actived :</label>
                                    <div class="col-sm-6 col-md-8">
                                        <div style="height: 34px; display: flex; align-items: center;">
                                            <input
                                                type="checkbox"
                                                id="is_actived"
                                                name="is_actived"
                                                ng-model="faction.is_actived"
                                                ng-checked="faction.is_actived == 1"
                                                ng-true-value="1"
                                                ng-false-value="0"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.row -->
                        </div><!-- /.box-body -->
                        <div class="box-footer clearfix">
                            <button
                                class="btn btn-warning pull-right"
                                ng-click="formValidate($event, '/factions/validate', faction, 'frmEditFaction', update)"
                            >
                                บันทึกการแก้ไข
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

@endsection