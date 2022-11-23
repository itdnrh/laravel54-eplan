<div class="modal fade" id="multiple-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รายละเอียดคุณลักษณะ</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="">ปีงบประมาณ</label>
                        <select id="dtpYear" ng-model="multipleData.year" class="form-control">
                            <option value="">ปีงบประมาณ</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="">เดือน</label>
                        <input type="text" id="dtpMonth" ng-model="multipleData.month" class="form-control" />
                    </div>
                    <div class="col-md-12 form-group">
                        <label for="">ประเภทแผน</label>
                        <select
                            id="cboPlanType"
                            ng-model="multipleData.plan_type_id"
                            class="form-control"
                            ng-change="getMultipleData()"
                        >
                            <option value="">ประเภทแผน</option>
                            @foreach($planTypes as $planType)
                                <option value="{{ $planType->id }}">
                                    {{ $planType->plan_type_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 3%; text-align: center;">#</th>
                                <th>ประเภทพัสดุ</th>
                                <th style="width: 15%; text-align: center;">ประมาณการ</th>
                                <th style="width: 15%; text-align: center;">ยอดการใช้</th>
                                <th style="width: 15%; text-align: center;">ยอดคงเหลือ</th>
                            </tr>
                            <tr ng-repeat="(index, expense) in multipleData.expenses">
                                <td style="text-align: center;">@{{ index+1 }}</td>
                                <td>@{{ expense.category_name }}</td>
                                <td style="text-align: right;">@{{ expense.budget | currency:'':2 }}</td>
                                <td style="text-align: right;">@{{ expense.net_total | currency:'':2 }}</td>
                                <td style="text-align: right;">@{{ expense.budget - expense.net_total | currency:'':2 }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div><!-- /.modal-body -->
            <div class="modal-footer" style="padding-bottom: 8px;">
                <button
                    ng-click="multipleStore($event)"
                    class="btn btn-primary"
                    aria-label="Save"
                >
                    บันทึก
                </button>
                <button class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                    ปิด
                </button>
            </div><!-- /.modal-footer -->
        </div>
    </div>
</div>
