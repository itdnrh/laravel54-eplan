<div class="modal fade" id="change-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="frmChangePlanType" name="frmChangePlanType" novalidate ng-submit="change($event, frmChangePlanType, changeData.plan_id)">
                <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}" />

                <div class="modal-header">
                    <h5 class="modal-title">แบบบันทึกเปลี่ยนหมวด</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': frmChangePlanType.from_type.$invalid}"
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
                            ng-class="{'has-error has-feedback': frmChangePlanType.$submitted && frmChangePlanType.plan_type_id.$invalid}"
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
                            <span class="help-block" ng-show="frmChangePlanType.$submitted && frmChangePlanType.plan_type_id.$error.required">
                                กรุณาเลือกประเภทแผน
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': frmChangePlanType.$submitted && frmChangePlanType.category_id.$invalid}"
                        >
                            <label for="">ประเภทสินค้า/บริการ <span class="required-field">*</span></label>
                            <select
                                type="text"
                                id="category_id"
                                name="category_id"
                                ng-model="changeData.category_id"
                                ng-change="onCategorySelected(changeData.category_id)"
                                class="form-control"
                                required
                            >
                                <option value="">-- เลือกประเภทสินค้า/บริการ --</option>
                                <option ng-repeat="category in forms.categories" value="@{{ category.id }}">
                                    @{{ category.name }}
                                </option>
                            </select>
                            <span class="help-block" ng-show="frmChangePlanType.$submitted && frmChangePlanType.category_id.$error.required">
                                กรุณาเลือกประเภทสินค้า/บริการ
                            </span>
                        </div>
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': frmChangePlanType.$submitted && frmChangePlanType.group_id.$invalid}"
                        >
                            <label for="">กลุ่มสินค้า/บริการ</label>
                            <select
                                type="text"
                                id="group_id"
                                name="group_id"
                                ng-model="changeData.group_id"
                                class="form-control"
                            >
                                <option value="">-- เลือกกลุ่มสินค้า/บริการ --</option>
                                <option ng-repeat="group in forms.groups" value="@{{ group.id }}">
                                    @{{ group.name }}
                                </option>
                            </select>
                            <span class="help-block" ng-show="frmChangePlanType.$submitted && frmChangePlanType.group_id.$error.required">
                                กรุณาเลือกกลุ่มสินค้า/บริการ
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
