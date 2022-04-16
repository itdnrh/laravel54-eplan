<div class="modal fade" id="item-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มรายการสินค้า/บริการ</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="">ประเภทแผน</label>
                            <select
                                type="text"
                                id="plan_type_id"
                                name="plan_type_id"
                                ng-model="newItem.plan_type_id"
                                class="form-control"
                            >
                                <option value="">-- เลือกประเภทแผน --</option>
                                @foreach($planTypes as $planType)
                                    <option value="{{ $planType->id }}">
                                        {{ $planType->plan_type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ประเภทสินค้า/บริการ</label>
                            <select
                                type="text"
                                id="category_id"
                                name="category_id"
                                ng-model="newItem.category_id"
                                class="form-control"
                            >
                                <option value="">-- เลือกประเภทสินค้า/บริการ --</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">กลุ่มสินค้า/บริการ</label>
                            <select
                                type="text"
                                id="group_id"
                                name="group_id"
                                ng-model="newItem.group_id"
                                class="form-control"
                            >
                                <option value="">-- เลือกกลุ่มสินค้า/บริการ --</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ชื่อสินค้า/บริการ</label>
                            <input
                                type="text"
                                id="item_name"
                                name="item_name"
                                ng-model="newItem.item_name"
                                class="form-control"
                            />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ราคา</label>
                            <input
                                type="text"
                                id="price_per_unit"
                                name="price_per_unit"
                                ng-model="newItem.price_per_unit"
                                class="form-control"
                            />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">หน่วยนับ</label>
                            <select
                                type="text"
                                id="category_id"
                                name="category_id"
                                ng-model="newItem.category_id"
                                class="form-control"
                            >
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ใน/นอกคลัง</label>
                            <div style="display: flex; gap: 30px;">
                                <div>
                                    <input type="radio" ng-model="in_stock" ng-value="1" /> ในคลัง 
                                </div>
                                <div>
                                    <input type="radio" ng-model="in_stock" ng-value="0" /> นอกคลัง
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">หมายเหตุ</label>
                            <textarea
                                rows=""
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
                        ng-click="createNewItem($event)"
                        class="btn btn-primary"
                        data-dismiss="modal"
                        aria-label="Save"
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
