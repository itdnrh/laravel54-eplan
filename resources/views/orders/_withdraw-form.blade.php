<div class="modal fade" id="withdraw-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input
                    type="hidden"
                    id="inspect_user"
                    name="inspect_user"
                    value="{{ Auth::user()->person_id }}"
                    class="form-control"
                />
                <input
                    type="hidden"
                    id="po_id"
                    name="po_id"
                    value="@{{ order.id }}"
                    class="form-control"
                />
                <div class="modal-header">
                    <h5 class="modal-title">บันทึกการส่งเบิกเงิน</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="">เลขที่หนังสือส่งเบิกเงิน</label>
                            <input
                                type="text"
                                id="withdraw_no"
                                name="withdraw_no"
                                ng-model="withdrawal.withdraw_no"
                                class="form-control"
                            />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ลงวันที่วันที่</label>
                            <input
                                type="text"
                                id="withdraw_date"
                                name="withdraw_date"
                                ng-model="withdrawal.withdraw_date"
                                class="form-control"
                            />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">งวดงานที่</label>
                            <select
                                id="deliver_seq"
                                name="deliver_seq"
                                ng-model="withdrawal.deliver_seq"
                                class="form-control"
                                ng-change="onDeliverSeqSelected(withdrawal.deliver_seq)"
                            >
                                <option value="">-- เลือกงวดงานที่ --</option>
                                <option ng-repeat="insp in inspections" value="@{{ insp.deliver_seq }}">
                                    @{{ insp.deliver_seq }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">เลขที่เอกสารส่งมอบงาน</label>
                            <input
                                type="text"
                                id="deliver_no"
                                name="deliver_no"
                                ng-model="withdrawal.deliver_no"
                                class="form-control"
                            />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ยอดเงิน</label>
                            <input
                                type="text"
                                id="net_total"
                                name="net_total"
                                value="@{{ withdrawal.net_total | currency:'':2 }}"
                                class="form-control"
                            />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">หมายเหตุ</label>
                            <input
                                type="text"
                                id="remark"
                                name="remark"
                                ng-model="withdrawal.remark"
                                class="form-control"
                            />
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button
                        ng-click="onWithdraw($event)"
                        class="btn btn-primary"
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
