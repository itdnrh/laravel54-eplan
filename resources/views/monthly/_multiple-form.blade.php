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
                        <select
                            id="dtpYear"
                            class="form-control"
                            ng-model="multipleData.year"
                            ng-change="getMultipleData();"
                        >
                            <option value="">ปีงบประมาณ</option>
                            <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                @{{ y }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="">เดือน</label>
                        <input type="text" id="dtpMonth" ng-model="multipleData.month" class="form-control" />
                    </div>
                    <div class="form-group" ng-class="{ 'col-md-6': multipleData.plan_type_id != '1', 'col-md-4': multipleData.plan_type_id == '1' }">
                        <label for="">ประเภทแผน</label>
                        <select
                            id="cboPlanType"
                            class="form-control"
                            ng-model="multipleData.plan_type_id"
                            ng-change="getMultipleData();"
                        >
                            <option value="">ประเภทแผน</option>
                            @foreach($planTypes as $planType)
                                <option value="{{ $planType->id }}">
                                    {{ $planType->plan_type_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" ng-class="{ 'col-md-6': multipleData.plan_type_id != '1', 'col-md-4': multipleData.plan_type_id == '1' }">
                        <label>ในแผน/นอกแผน</label>
                        <select
                            id="cboInPlan"
                            name="cboInPlan"
                            class="form-control"
                            ng-model="cboInPlan"
                            ng-change="getMultipleData();"
                        >
                            <option value="">-- ทั้งหมด --</option>
                            <option value="I">ในแผน</option>
                            <option value="O">นอกแผน</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group" ng-show="multipleData.plan_type_id == '1'">
                        <label>ราคาต่อหน่วย</label>
                        <select
                            id="cboPrice"
                            name="cboPrice"
                            class="form-control"
                            ng-model="cboPrice"
                            ng-change="getMultipleData();"
                        >
                            <option value="">-- เลือก --</option>
                            <option value="1">ราคาตั้งแต่ 10,000 บาทขึ้นไป</option>
                            <option value="2">ราคาน้อยกว่า 10,000 บาท</option>
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
                                <td>@{{ setCategoryName(expense.category_name, multipleData.plan_type_id) }}</td>
                                <td style="text-align: right;">@{{ expense.budget | currency:'':2 }}</td>
                                <td style="text-align: right;">@{{ expense.net_total | currency:'':2 }}</td>
                                <td style="text-align: right;">@{{ expense.remain | currency:'':2 }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div><!-- /.modal-body -->
            <div class="modal-footer" style="padding-bottom: 8px;">
                <button
                    ng-click="multipleStore($event)"
                    ng-show="!multipleData.isExisted"
                    class="btn btn-primary"
                    aria-label="Save"
                >
                    บันทึก
                </button>
                <button
                    ng-click="multipleUpdate($event)"
                    ng-show="multipleData.isExisted"
                    class="btn btn-warning"
                    aria-label="Save"
                >
                    ปรับปรุงข้อมูล
                </button>
                <button class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                    ปิด
                </button>
            </div><!-- /.modal-footer -->
        </div>
    </div>
</div>
