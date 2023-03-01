<div class="modal fade" id="multiple-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รายละเอียดข้อมูลควบคุมกำกับติดตามจาก E-Plan</h5>
            </div>
            <form  id="frmMultiple" name="frmMultiple" novalidate ng-submit="multipleStore($event, frmMultiple);">
                <div class="modal-body">
                    <div class="row">
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error': frmMultiple.$submitted && frmMultiple.dtpYear.$invalid}"
                        >
                            <label for="">ปีงบประมาณ</label>
                            <select
                                id="dtpYear"
                                name="dtpYear"
                                class="form-control"
                                ng-model="multipleData.year"
                                ng-change="getMultipleData();"
                                required
                            >
                                <option value="">-- ปีงบประมาณ --</option>
                                <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                    @{{ y }}
                                </option>
                            </select>
                            <span class="help-block" ng-show="frmMultiple.$submitted && frmMultiple.dtpYear.$error.required">
                                กรุณาเลือกปีงบประมาณ
                            </span>
                        </div>
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error': frmMultiple.$submitted && frmMultiple.dtpMonth.$invalid}"
                        >
                            <label for="">เดือน</label>
                            <input
                                type="text"
                                id="dtpMonth"
                                name="dtpMonth"
                                ng-model="multipleData.month"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmMultiple.$submitted && frmMultiple.dtpMonth.$error.required">
                                กรุณาเลือกเดือน
                            </span>
                        </div>
                        <div
                            class="form-group"
                            ng-class="{
                                'col-md-6': multipleData.plan_type_id != '1',
                                'col-md-4': multipleData.plan_type_id == '1',
                                'has-error': frmMultiple.$submitted && frmMultiple.cboPlanType.$invalid
                            }"
                        >
                            <label for="">ประเภทแผน</label>
                            <select
                                id="cboPlanType"
                                name="cboPlanType"
                                class="form-control"
                                ng-model="multipleData.plan_type_id"
                                ng-change="getMultipleData();"
                                required
                            >
                                <option value="">-- ประเภทแผน --</option>
                                @foreach($planTypes as $planType)
                                    <option value="{{ $planType->id }}">
                                        {{ $planType->plan_type_name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="help-block" ng-show="frmMultiple.$submitted && frmMultiple.cboPlanType.$error.required">
                                กรุณาเลือกเดือน
                            </span>
                        </div>
                        <div
                            class="form-group"
                            ng-class="{
                                'col-md-6': multipleData.plan_type_id != '1',
                                'col-md-4': multipleData.plan_type_id == '1',
                                'has-error': frmMultiple.$submitted && frmMultiple.cboInPlan.$invalid
                            }"
                        >
                            <label>ในแผน/นอกแผน</label>
                            <select
                                id="cboInPlan"
                                name="cboInPlan"
                                class="form-control"
                                ng-model="cboInPlan"
                                ng-change="getMultipleData();"
                                required
                            >
                                <option value="">-- ทั้งหมด --</option>
                                <option value="I">ในแผน</option>
                                <option value="O">นอกแผน</option>
                            </select>
                            <span class="help-block" ng-show="frmMultiple.$submitted && frmMultiple.cboInPlan.$error.required">
                                กรุณาเลือกในแผน/นอกแผน
                            </span>
                        </div>
                        <div
                            class="col-md-4 form-group"
                            ng-class="{'has-error': frmMultiple.$submitted && frmMultiple.cboPrice.$invalid}"
                            ng-show="multipleData.plan_type_id == '1'"
                        >
                            <label>ราคาต่อหน่วย</label>
                            <select
                                id="cboPrice"
                                name="cboPrice"
                                class="form-control"
                                ng-model="cboPrice"
                                ng-change="getMultipleData();"
                                ng-required="multipleData.plan_type_id == 1"
                            >
                                <option value="">-- เลือก --</option>
                                <option value="1">ราคาตั้งแต่ 10,000 บาทขึ้นไป</option>
                                <option value="2">ราคาน้อยกว่า 10,000 บาท (ครุภัณฑ์ต่ำกว่าเกณฑ์)</option>
                            </select>
                            <span class="help-block" ng-show="frmMultiple.$submitted && frmMultiple.cboPrice.$error.required">
                                กรุณาเลือกราคาต่อหน่วย
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered" style="margin-bottom: 0;">
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
                <div class="modal-footer" style="padding: 10px;">
                    <div class="row">
                        <div class="col-md-6">
                            <button
                                type="submit"
                                ng-show="!multipleData.isExisted"
                                class="btn btn-primary pull-left"
                                aria-label="Save"
                            >
                                <i class="fa fa-save"></i>
                                บันทึก
                            </button>
                            <button
                                type="submit"
                                ng-show="multipleData.isExisted"
                                class="btn btn-warning pull-left"
                                aria-label="Save"
                            >
                                <i class="fa fa-pencil-square-o"></i>
                                ปรับปรุงข้อมูล
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                                ปิด
                            </button>
                        </div>
                    </div>
                </div><!-- /.modal-footer -->
            </form>
        </div>
    </div>
</div>
