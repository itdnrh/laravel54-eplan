<div class="modal fade" id="adjust-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="frmPlanAdjustment" name="frmPlanAdjustment" novalidate ng-submit="adjust($event, frmPlanAdjustment, adjustment.id)">
                <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}" />

                <div class="modal-header">
                    <h5 class="modal-title">ปรับแผน (6 เดือนหลัง)</h5>
                </div>
                <div class="modal-body" style="padding: 20px 80px 10px;">
                    <!-- <div class="row">
                        <div class="col-md-12" style="text-align: center;">
                            <button type="button" class="btn btn-success" ng-click="setAdjustType(1)">
                                ปรับราคา
                            </button>
                            <button type="button" class="btn btn-success" ng-click="setAdjustType(2)">
                                ปรับรายการ
                            </button>
                        </div>
                    </div><br> -->
                    <div class="row" ng-show="adjustment.adjust_type == 1">
                        <div
                            class="form-group col-md-6"
                            ng-class="{'has-error has-feedback': frmPlanAdjustment.$submitted && frmPlanAdjustment.price_per_unit.$invalid}"
                        >
                            <label>ราคาต่อหน่วย : <span class="required-field">*</span></label>
                            <input  type="text"
                                    id="price_per_unit"
                                    name="price_per_unit"
                                    ng-model="adjustment.price_per_unit"
                                    ng-change="calcSumPriceOfAdjustment(adjustment.price_per_unit, adjustment.amount)"
                                    class="form-control"
                                    tabindex="1"
                                    required />
                            <span class="help-block" ng-show="frmPlanAdjustment.$submitted && frmPlanAdjustment.price_per_unit.$error.required">
                                กรุณาระบุราคาต่อหน่วย
                            </span>
                        </div>
                        <div
                            class="form-group col-md-6"
                            ng-class="{'has-error has-feedback': frmPlanAdjustment.$submitted && frmPlanAdjustment.unit_id.$invalid}"
                        >
                            <label>หน่วยนับ : <span class="required-field">*</span></label>
                            <select id="unit_id" 
                                    name="unit_id"
                                    ng-model="adjustment.unit_id"
                                    class="form-control"
                                    tabindex="2"
                                    required>
                                <option value="">-- เลือกหน่วยนับ --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="help-block" ng-show="frmPlanAdjustment.$submitted && frmPlanAdjustment.unit_id.$error.required">
                                กรุณาเลือกหน่วยนับ
                            </span>
                        </div>
                        <div
                            class="form-group col-md-6"
                            ng-class="{'has-error has-feedback': frmPlanAdjustment.$submitted && frmPlanAdjustment.amount.$invalid}"
                        >
                            <label>จำนวนที่ขอ : <span class="required-field">*</span></label>
                            <input  type="text"
                                    id="amount"
                                    name="amount"
                                    ng-model="adjustment.amount"
                                    ng-change="calcSumPriceOfAdjustment(adjustment.price_per_unit, adjustment.amount)"
                                    class="form-control"
                                    tabindex="3"
                                    required />
                            <span class="help-block" ng-show="frmPlanAdjustment.$submitted && frmPlanAdjustment.amount.$error.required">
                                กรุณาระบุจำนวนที่ขอ
                            </span>
                        </div>
                        <div
                            class="form-group col-md-6"
                            ng-class="{'has-error has-feedback': frmPlanAdjustment.$submitted && frmPlanAdjustment.sum_price.$invalid}"
                        >
                            <label>รวมเป็นเงิน : <span class="required-field">*</span></label>
                            <input  type="text"
                                    id="sum_price"
                                    name="sum_price"
                                    ng-model="adjustment.sum_price"
                                    class="form-control"
                                    tabindex="4"
                                    required />
                            <span class="help-block" ng-show="frmPlanAdjustment.$submitted && frmPlanAdjustment.sum_price.$error.required">
                                กรุณาระบุรวมเป็นเงิน
                            </span>
                        </div>
                    </div>
                    <div class="row" ng-show="adjustment.adjust_type == 2">
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': frmPlanAdjustment.$submitted && frmPlanAdjustment.plan_type_id.$invalid}"
                        >
                            <label for="">ประเภทแผน <span class="required-field">*</span></label>
                            <select
                                type="text"
                                id="plan_type_id"
                                name="plan_type_id"
                                ng-model="changeData.plan_type_id"
                                ng-change="onPlanTypeSelected(changeData.plan_type_id)"
                                class="form-control"
                                ng-required="adjustment.adjust_type == 2"
                            >
                                <option value="">-- เลือกประเภทแผน --</option>
                                @foreach($planTypes as $planType)
                                    @if($planType->id != $plan->plan_type_id)
                                        <option value="{{ $planType->id }}">
                                            {{ $planType->plan_type_name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <span class="help-block" ng-show="frmPlanAdjustment.$submitted && frmPlanAdjustment.plan_type_id.$error.required">
                                กรุณาเลือกประเภทแผน
                            </span>
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        บันทึก
                    </button>
                    <button class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        ปิด
                    </button>
                </div><!-- /.modal-footer -->
            </form>
        </div>
    </div>
</div>