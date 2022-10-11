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
                            <option value="1">ราคาที่ได้จากการจัดซื้อภายใน 2 ปีงบประมาณ</option>
                            <option value="2">อื่น ๆ</option>
                        </select>
                    </div>
                </div>
                <div class="row">
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
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="">เลขที่เอกสารรายงานขออนุมัติผู้กำหนด Spec</label>
                        <div class="input-group">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default">นม 0033.201.2/</button>
                            </div>
                            <input
                                type="text"
                                id="report_doc_no"
                                name="report_doc_no"
                                ng-model="specCommittee.report_doc_no"
                                class="form-control"
                            />
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="">วันที่เอกสารรายงานขออนุมัติผู้กำหนด Spec</label>
                        <input
                            type="text"
                            id="report_doc_date"
                            name="report_doc_date"
                            ng-model="specCommittee.report_doc_date"
                            class="form-control"
                        />
                    </div>
                </div>
            </div><!-- /.modal-body -->
            <div class="modal-footer" style="padding-bottom: 8px;">
                <button
                    ng-click="onPrintSpecCommittee($event, specCommittee.support_id, specCommittee.is_existed)"
                    class="btn"
                    ng-class="{ 'btn-success': specCommittee.is_existed, 'btn-primary': !specCommittee.is_existed }"
                    aria-label="Save"
                >
                    <i class="fa fa-print" aria-hidden="true" ng-show="specCommittee.is_existed"></i>
                    @{{ specCommittee.is_existed ? 'พิมพ์เอกสาร' : 'บันทึก' }}
                </button>
                <button
                    class="btn btn-danger" 
                    ria-label="Close"
                    ng-click="closeSpecCommitteeForm();"
                >
                    ปิด
                </button>
            </div><!-- /.modal-footer -->
        </div>
    </div>
</div>
