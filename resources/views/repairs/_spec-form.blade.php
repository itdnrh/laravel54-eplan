<div class="modal fade" id="spec-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="frmRepairingSpec" name="frmRepairingSpec" novalidate ng-submit="addSpec($event, frmRepairingSpec)">
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

                <div class="modal-header">
                    <h5 class="modal-title">รายละเอียด</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group" ng-class="{ 'has-error has-feedback': frmRepairingSpec.$submitted && frmRepairingSpec.repair_type.$invalid }">
                            <label for="">ประเภทการซ่อม</label>
                            <select
                                id="repair_type"
                                name="repair_type"
                                ng-model="spec.repair_type"
                                class="form-control"
                                required
                            >
                                <option value="">-- เลือกประเภท --</option>
                                <option value="1">ครุภัณฑ์</option>
                                <option value="2">ยานพาหนะ</option>
                                <option value="3">อาคาร/สถานที่</option>
                                <option value="9">อื่นๆ</option>
                            </select>
                            <span class="help-block" ng-show="frmRepairingSpec.$submitted && frmRepairingSpec.repair_type.$error.required">
                                กรุณาเลือกประเภทการซ่อม
                            </span>
                        </div>
                        <div class="col-md-6 form-group" ng-class="{ 'has-error has-feedback': frmRepairingSpec.$submitted && frmRepairingSpec.parcel_no.$invalid }">
                            <label for="">หมายเลขครุภัณฑ์ (ถ้ามี / ถ้าไม่ทราบให้ใส่ -)</label>
                            <input
                                type="text"
                                id="parcel_no"
                                name="parcel_no"
                                ng-model="spec.parcel_no"
                                class="form-control"
                                ng-disabled="spec.repair_type != 1"
                                ng-required="spec.repair_type == 1"
                            />
                            <span class="help-block" ng-show="frmRepairingSpec.$submitted && frmRepairingSpec.parcel_no.$error.required">
                                กรุณาระบุหมายเลขครุภัณฑ์
                            </span>
                        </div>
                        <div class="col-md-6 form-group" ng-class="{ 'has-error has-feedback': frmRepairingSpec.$submitted && frmRepairingSpec.reg_no.$invalid }">
                            <label for="">หมายเลขทะเบียน (รถราชการ)</label>
                            <input
                                type="text"
                                id="reg_no"
                                name="reg_no"
                                ng-model="spec.reg_no"
                                class="form-control"
                                ng-disabled="spec.repair_type != 2"
                                ng-required="spec.repair_type == 2"
                            />
                            <span class="help-block" ng-show="frmRepairingSpec.$submitted && frmRepairingSpec.reg_no.$error.required">
                                กรุณาระบุหมายเลขทะเบียน
                            </span>
                        </div>
                        <div class="col-md-12 form-group" ng-class="{ 'has-error has-feedback': frmRepairingSpec.$submitted && frmRepairingSpec.desc.$invalid }">
                            <label for="">รายการ (ระบุรายการ เช่น เครื่องคอมพิวเตอร์, รถตู้, ตู้เย็น, อาคาร ฯลฯ เป็นต้น)</label>
                            <input
                                type="text"
                                id="desc"
                                name="desc"
                                ng-model="spec.desc"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmRepairingSpec.$submitted && frmRepairingSpec.desc.$error.required">
                                กรุณาระบุรายการ
                            </span>
                        </div>
                        <div class="col-md-12 form-group" ng-class="{ 'has-error has-feedback': frmRepairingSpec.$submitted && frmRepairingSpec.cause.$invalid }">
                            <label for="">รายละเอียดการซ่อม</label>
                            <textarea
                                id="cause"
                                name="cause"
                                rows="5"
                                ng-model="spec.cause"
                                class="form-control"
                                required
                            ></textarea>
                            <span class="help-block" ng-show="frmRepairingSpec.$submitted && frmRepairingSpec.cause.$error.required">
                                กรุณาระบุรายละเอียดการซ่อม
                            </span>
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button type="submit" class="btn btn-primary" aria-label="Save">
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
