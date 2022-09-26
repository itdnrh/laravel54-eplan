<div class="modal fade" id="receive-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="frmReceive" name="frmReceive" novalidate ng-submit="onReceiveSupport($event, frmReceive, receive.support_id)">
                <input
                    type="hidden"
                    id="support_id"
                    name="support_id"
                    value="@{{ receive.support_id }}"
                    class="form-control"
                />
                <div class="modal-header">
                    <h5 class="modal-title">บันทึกรับใบขอสนับสนุน</h5>
                </div>
                <div class="modal-body">
                    <div class="row" style="padding: 10px 80px;">
                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error has-feedback': frmReceive.$submitted && frmReceive.received_no.$invalid}"
                        >
                            <label for="">เลขที่รับ</label>
                            <input
                                type="text"
                                id="received_no"
                                name="received_no"
                                ng-model="receive.received_no"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmReceive.$submitted && frmReceive.received_no.$error.required">
                                กรุณาระบุเลขที่รับเอกสาร
                            </span>
                        </div>
                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error has-feedback': frmReceive.$submitted && frmReceive.received_date.$invalid}"
                        >
                            <label for="">ลงวันที่</label>
                            <input
                                type="text"
                                id="received_date"
                                name="received_date"
                                ng-model="receive.received_date"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmReceive.$submitted && frmReceive.received_date.$error.required">
                                กรุณาเลือกลงวันที่
                            </span>
                        </div>
                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error has-feedback': frmReceive.$submitted && frmReceive.officer.$invalid}"
                        >
                            <label for="">จทน.พัสดุ (ผู้รับผิดชอบ)</label>
                            <select
                                id="officer"
                                name="officer"
                                ng-model="receive.officer"
                                class="form-control"
                                required
                            >
                                <option value="">-- เลือก จทน.พัสดุ --</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->person_id }}">
                                        {{ $officer->person_firstname }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="help-block" ng-show="frmReceive.$submitted && frmReceive.officer.$error.required">
                                กรุณาเลือก จทน.พัสดุ (ผู้รับผิดชอบ)
                            </span>
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button type="submit" class="btn btn-primary">
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
