<div class="modal fade" id="modification-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="frmModification" name="frmModification" novalidate ng-submit="onSubmitModification($event, frmModification, modification.id)">
                <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}" />

                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มรายการขอเปลี่ยนแปลง</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error has-feedback': frmModification.$submitted && frmModification.doc_no.$invalid}"
                        >
                            <label for="">เลขที่บันทึก</label>
                            <input
                                type="text"
                                id="doc_no"
                                name="doc_no"
                                ng-model="modification.doc_no"
                                ng-change="onPlanTypeSelected(payment.doc_no)"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmModification.$submitted && frmModification.doc_no.$error.required">
                                กรุณาระบุเลขที่บันทึก
                            </span>
                        </div>
                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error has-feedback': frmModification.$submitted && frmModification.doc_date.$invalid}"
                        >
                            <label for="">วันที่บันทึก</label>
                            <input
                                type="text"
                                id="doc_date"
                                name="doc_date"
                                ng-model="modification.doc_date"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmModification.$submitted && frmModification.doc_date.$error.required">
                                กรุณาเลือกวันที่บันทึก
                            </span>
                        </div>
                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error has-feedback': frmModification.$submitted && frmModification.modification_type_id.$invalid}"
                        >
                            <label for="">ประเภทการขอ</label>
                            <select
                                id="modification_type_id"
                                name="modification_type_id"
                                ng-model="modification.modification_type_id"
                                class="form-control"
                                required
                            >
                                <option value="">-- เลือกประเภทการขอ --</option>
                                <option value="1">ปรับเปลี่ยนวันที่ดำเนินการ</option>
                                <option value="2">ปรับเปลี่ยนไตรมาส</option>
                                <option value="3">ปรับเปลี่ยนวิทยากร</option>
                                <option value="4">ปรับเปลี่ยนกิจกรรมดำเนินการ</option>
                                <option value="5">ปรับเปลี่ยนงบประมาณ</option>
                            </select>
                            <span class="help-block" ng-show="frmModification.$submitted && frmModification.modification_type_id.$error.required">
                                กรุณาเลือกประเภทการขอ
                            </span>
                        </div>
                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error has-feedback': frmModification.$submitted && frmModification.desc.$invalid}"
                        >
                            <label for="">รายละเอียด</label>
                            <textarea
                                id="desc"
                                name="desc"
                                rows="5"
                                ng-model="modification.desc"
                                class="form-control"
                            ></textarea>
                            <span class="help-block" ng-show="frmModification.$submitted && frmModification.desc.$error.required">
                                กรุณาระบุรายละเอียด
                            </span>
                        </div>
                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error has-feedback': frmModification.$submitted && frmModification.attachment.$invalid}"
                        >
                            <label for="">ไฟล์แนบ (เฉพาะไฟล์ประเภท PDF เท่านั้น)</label>
                            <input
                                type="file"
                                id="attachment"
                                name="attachment"
                            />
                            <span class="help-block" ng-show="frmModification.$submitted && frmModification.attachment.$error.required">
                                กรุณาระบุไฟล์แนบ
                            </span>
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button type="submit" class="btn btn-primary" ng-hide="modification.id">
                        บันทึก
                    </button>
                    <button type="submit" class="btn btn-warning" ng-show="modification.id">
                        บันทึกการเปลี่ยนแปลง
                    </button>
                    <button class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        ปิด
                    </button>
                </div><!-- /.modal-footer -->
            </form>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#net_total').inputmask("currency", { "placeholder": "0" });
    });
</script>