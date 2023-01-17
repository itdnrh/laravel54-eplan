<div class="modal fade" id="adjust-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="frmPlanAdjustment" name="frmPlanAdjustment" novalidate ng-submit="change($event, frmPlanAdjustment, changeData.plan_id)">
                <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}" />

                <div class="modal-header">
                    <h5 class="modal-title">ปรับแผน (6 เดือนหลัง)</h5>
                </div>
                <div class="modal-body" style="padding: 0 80px 10px;">
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
                    <div class="row" ng-show="adjustType == 1">
                        <div
                            class="form-group col-md-6"
                            ng-class="{'has-error has-feedback': checkValidate(asset, 'price_per_unit')}"
                        >
                            <label>ราคาต่อหน่วย : <span class="required-field">*</span></label>
                            <input  type="text"
                                    id="price_per_unit"
                                    name="price_per_unit"
                                    ng-model="price_per_unit"
                                    ng-change="calculateSumPrice()"
                                    class="form-control"
                                    tabindex="1" />
                            <span class="help-block" ng-show="checkValidate(asset, 'price_per_unit')">
                                @{{ formError.errors.price_per_unit[0] }}
                            </span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>หน่วย : <span class="required-field">*</span></label>
                            <select id="unit_id" 
                                    name="unit_id"
                                    ng-model="unit_id"
                                    class="form-control"
                                    tabindex="2">
                                <option value="">-- เลือกหน่วย --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="help-block" ng-show="checkValidate(asset, 'unit_id')">
                                @{{ formError.errors.unit_id[0] }}
                            </span>
                        </div>
                        <div
                            class="form-group col-md-6"
                            ng-class="{'has-error has-feedback': checkValidate(asset, 'amount')}"
                        >
                            <label>จำนวนที่ขอ : <span class="required-field">*</span></label>
                            <input  type="text"
                                    id="amount"
                                    name="amount"
                                    ng-model="amount"
                                    ng-change="calculateSumPrice()"
                                    class="form-control"
                                    tabindex="3" />
                            <span class="help-block" ng-show="checkValidate(asset, 'amount')">
                                @{{ formError.errors.amount[0] }}
                            </span>
                        </div>
                        <div
                            class="form-group col-md-6"
                            ng-class="{'has-error has-feedback': checkValidate(asset, 'sum_price')}"
                        >
                            <label>รวมเป็นเงิน : <span class="required-field">*</span></label>
                            <input  type="text"
                                    id="sum_price"
                                    name="sum_price"
                                    ng-model="sum_price"
                                    class="form-control"
                                    tabindex="4" />
                            <span class="help-block" ng-show="checkValidate(asset, 'sum_price')">
                                @{{ formError.errors.sum_price[0] }}
                            </span>
                        </div>
                    </div>
                    <div class="row" ng-show="adjustType == 2">
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': frmPlanAdjustment.from_type.$invalid}"
                        >
                            <label for="">เปลี่ยนจาก</label>
                            <select
                                type="text"
                                id="from_type"
                                name="from_type"
                                ng-model="changeData.from_type"
                                class="form-control"
                                disabled
                            >
                                <option value="">-- เลือกประเภทแผน --</option>
                                @foreach($planTypes as $planType)
                                    <option value="{{ $planType->id }}">
                                        {{ $planType->plan_type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': frmPlanAdjustment.$submitted && frmPlanAdjustment.plan_type_id.$invalid}"
                        >
                            <label for="">เปลี่ยนเป็น <span class="required-field">*</span></label>
                            <select
                                type="text"
                                id="plan_type_id"
                                name="plan_type_id"
                                ng-model="changeData.plan_type_id"
                                ng-change="onPlanTypeSelected(changeData.plan_type_id)"
                                class="form-control"
                                required
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
