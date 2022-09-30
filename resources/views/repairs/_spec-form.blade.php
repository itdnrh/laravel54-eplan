<div class="modal fade" id="spec-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รายละเอียด</h5>
            </div>
            <div class="modal-body">
                <input
                    type="hidden"
                    id="item_id"
                    name="item_id"
                    ng-model="newItem.item_id"
                />
                <input
                    type="hidden"
                    id="plan_no"
                    name="plan_no"
                    style="text-align: center"
                    ng-model="newItem.plan_no"
                />

                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="">ประเภทการซ่อม</label>
                        <select
                            id="type"
                            name="type"
                            ng-model="spec.type"
                            class="form-control"
                        >
                            <option value="">-- เลือกประเภท --</option>
                            <option value="1">ครุภัณฑ์</option>
                            <option value="2">ยานพาหนะ</option>
                            <option value="3">อาคาร/สถานที่</option>
                            <option value="9">อื่นๆ</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="">หมายเลขครุภัณฑ์ (ถ้ามี)</label>
                        <input
                            type="text"
                            id="parcel_no"
                            name="parcel_no"
                            ng-model="spec.parcel_no"
                            class="form-control"
                            ng-disabled="spec.type != 1"
                        />
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="">หมายเลขทะเบียน (รถราชการ)</label>
                        <input
                            type="text"
                            id="reg_no"
                            name="reg_no"
                            ng-model="spec.reg_no"
                            class="form-control"
                            ng-disabled="spec.type != 2"
                        />
                    </div>
                    <div class="col-md-12 form-group">
                        <label for="">รายการ (ระบุรายการ เช่น เครื่องคอมพิวเตอร์, รถตู้, ตู้เย็น, อาคาร ฯลฯ เป็นต้น)</label>
                        <input
                            type="text"
                            id="desc"
                            name="desc"
                            ng-model="spec.desc"
                            class="form-control"
                        />
                    </div>
                    <div class="col-md-12 form-group">
                        <label for="">รายละเอียดการซ่อม</label>
                        <textarea
                            id="cause"
                            name="cause"
                            rows="5"
                            ng-model="spec.cause"
                            class="form-control"
                        ></textarea>
                    </div>
                </div>
            </div><!-- /.modal-body -->
            <div class="modal-footer" style="padding-bottom: 8px;">
                <button
                    ng-click="addSpec($event)"
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
