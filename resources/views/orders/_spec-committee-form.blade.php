<div class="modal fade" id="spec-committee-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รายละเอียดเอกสารขออนุมัติผู้กำหนด Spec</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="user" name="user" value="" />

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="">วิธีจัดซื้อจัดจ้าง</label>
                        <select
                            id="purchase_method"
                            name="purchase_method"
                            ng-model="specCommittee.purchase_method"
                            class="form-control"
                        >
                            <option value="1">เฉพาะเจาะจง</option>
                            <option value="2">ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="">แหล่งที่มาของราคาอ้างอิง</label>
                        <select
                            id="source_price"
                            name="source_price"
                            ng-model="specCommittee.source_price"
                            class="form-control"
                        >
                            <option value="1">ราคาที่ได้จากการจัดซื้อภายใน ๒ ปีงบประมาณ</option>
                            <option value="2">อื่น ๆ</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="">เลขที่เอกสารขออนุมัติผู้กำหนด Spec</label>
                        <div class="input-group">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default">นม 0033.201.2/</button>
                            </div>
                            <input
                                type="text"
                                id="spec_doc_no"
                                name="spec_doc_no"
                                ng-model="specCommittee.spec_doc_no"
                                class="form-control"
                            />
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="">วันที่เอกสารขออนุมัติผู้กำหนด Spec</label>
                        <input
                            type="text"
                            id="spec_doc_date"
                            name="spec_doc_date"
                            ng-model="specCommittee.spec_doc_date"
                            class="form-control"
                        />
                    </div>
                </div>
            </div><!-- /.modal-body -->
            <div class="modal-footer" style="padding-bottom: 8px;">
                <button
                    ng-click="onPrintSpecCommittee($event, specCommittee.support_id)"
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
