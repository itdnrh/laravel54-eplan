<div class="modal fade" id="item-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="frmNewItem">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มรายการสินค้า/บริการ</h5>
                </div>
                <div class="modal-body" style="padding-bottom: 0;">
                    <div class="row">
                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error has-feedback': newItem.error['plan_type_id']}"
                        >
                            <label for="">ประเภทแผน <span class="required-field">*</span></label>
                            <select
                                type="text"
                                id="plan_type_id"
                                name="plan_type_id"
                                ng-model="newItem.plan_type_id"
                                ng-change="onPlanTypeSelected(newItem.plan_type_id)"
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
                            <span class="help-block" ng-show="newItem.error['plan_type_id']">
                                @{{ newItem.error['plan_type_id'] }}
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': newItem.error['category_id']}"
                        >
                            <label for="">ประเภทสินค้า/บริการ <span class="required-field">*</span></label>
                            <select
                                type="text"
                                id="category_id"
                                name="category_id"
                                ng-model="newItem.category_id"
                                class="form-control"
                            >
                                <option value="">-- เลือกประเภทสินค้า/บริการ --</option>
                                <option ng-repeat="category in forms.categories" value="@{{ category.id }}">
                                    @{{ category.name }}
                                </option>
                            </select>
                            <span class="help-block" ng-show="newItem.error['category_id']">
                                @{{ newItem.error['category_id'] }}
                            </span>
                        </div>
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': newItem.error['group_id']}"
                        >
                            <label for="">กลุ่มสินค้า/บริการ</label>
                            <select
                                type="text"
                                id="group_id"
                                name="group_id"
                                ng-model="newItem.group_id"
                                class="form-control"
                            >
                                <option value="">-- เลือกกลุ่มสินค้า/บริการ --</option>
                                <option ng-repeat="group in forms.groups" value="@{{ group.id }}">
                                    @{{ group.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': newItem.error['item_name']}"
                        >
                            <label for="">ชื่อสินค้า/บริการ (ไทย) <span class="required-field">*</span></label>
                            <input
                                type="text"
                                id="item_name"
                                name="item_name"
                                ng-model="newItem.item_name"
                                class="form-control"
                            />
                            <span class="help-block" ng-show="newItem.error['item_name']">
                                @{{ newItem.error['item_name'] }}
                            </span>
                        </div>
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': newItem.error['en_name']}"
                        >
                            <label for="">ชื่อสินค้า/บริการ (อังกฤษ)</label>
                            <input
                                type="text"
                                id="en_name"
                                name="en_name"
                                ng-model="newItem.en_name"
                                class="form-control"
                            />
                            <span class="help-block" ng-show="newItem.error['en_name']">
                                @{{ newItem.error['en_name'] }}
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': newItem.error['price_per_unit']}"
                        >
                            <label for="">ราคา <span class="required-field">*</span></label>
                            <input
                                type="text"
                                id="price_per_unit"
                                name="price_per_unit"
                                ng-model="newItem.price_per_unit"
                                class="form-control"
                            />
                            <span class="help-block" ng-show="newItem.error['price_per_unit']">
                                @{{ newItem.error['price_per_unit'] }}
                            </span>
                        </div>
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': newItem.error['unit_id']}"
                        >
                            <label for="">หน่วยนับ <span class="required-field">*</span></label>
                            <select
                                type="text"
                                id="item_unit_id"
                                name="item_unit_id"
                                ng-model="newItem.unit_id"
                                class="form-control"
                            >
                                <option value="">-- เลือกหน่วยนับ --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="help-block" ng-show="newItem.error['unit_id']">
                                @{{ newItem.error['unit_id'] }}
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-12 alert alert-warning alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <i class="fa fa-warning"></i>
                                กรณีการตั้งงบประมาณเป็นยอดรวม ให้เลือกการตัดยอดตามงบประมาณและเลือกมีรายการย่อย
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label for="">การตัดยอด</label>
                            <div class="form-control" style="display: flex; gap: 30px;">
                                <div>
                                    <input type="radio" ng-model="newItem.calc_method" ng-value="1" disabled/> ตัดยอดตามจำนวน 
                                </div>
                                <div>
                                    <input type="radio" ng-model="newItem.calc_method" ng-value="2"/> ตัดยอดตามงบประมาณ
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">มีรายการย่อย</label>
                            <div class="form-control" style="display: flex; gap: 30px;">
                                <div>
                                    <input type="radio" ng-model="newItem.have_subitem" ng-value="1" /> มีรายการย่อย 
                                </div>
                                <div>
                                    <input type="radio" ng-model="newItem.have_subitem" ng-value="0" /> ไม่มีรายการย่อย
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group" ng-show="isService(newItem.plan_type_id)">
                            <label for="">เป็นรายการ Fix Cost</label>
                            <div class="form-control" style="display: flex; gap: 30px;">
                                <div>
                                    <input type="radio" ng-model="newItem.is_fixcost" ng-value="0" /> ไม่เป็น
                                </div>
                                <div>
                                    <input type="radio" ng-model="newItem.is_fixcost" ng-value="1" /> เป็น
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group" ng-show="isMaterial(newItem.plan_type_id)">
                            <label for="">ใน/นอกคลัง</label>
                            <div class="form-control" style="display: flex; gap: 30px;">
                                <div>
                                    <input
                                        type="radio"
                                        ng-model="newItem.in_stock"
                                        ng-value="1"
                                        ng-disabled="inStock == 0" /> ในคลัง 
                                </div>
                                <div>
                                    <input
                                        type="radio"
                                        ng-model="newItem.in_stock"
                                        ng-value="0"
                                        ng-disabled="inStock == 1" /> นอกคลัง
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="">หมายเหตุ</label>
                            <textarea
                                rows="4"
                                id="remark"
                                name="remark"
                                ng-model="newItem.remark"
                                class="form-control"
                            ></textarea>
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button
                        ng-click="createNewItem($event, onSelectedItem)"
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
