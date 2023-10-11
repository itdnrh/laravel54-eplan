<div class="modal fade" id="return-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="frmReturn" name="frmReturn" novalidate ng-submit="onReturnSupport($event, frmReturn, returnData.support_id)">
                <input
                    type="hidden"
                    id="support_id"
                    name="support_id"
                    value="@{{ returnData.support_id }}"
                    class="form-control"
                />
                <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}" />
                <div class="modal-header">
                    <h5 class="modal-title"> <i class="glyphicon glyphicon-refresh"></i> ตีกลับเอกสาร</h5>
                </div>
                <div class="modal-body">
                    <div class="row" style="padding: 10px 80px;">
                        <!-- <div class="col-md-12 form-group">
                          <label class="radio-inline">
                            <input type="radio" name="plan_approved_status" ng-model="receive.plan_approved_status" id="plan_approved_status1" value="approved" checked>อนุมัติ
                          </label>
                          <label class="radio-inline">
                            <input type="radio" name="plan_approved_status" ng-model="receive.plan_approved_status" id="plan_approved_status2" value="wait_approved">ไม่อนุมัติ
                          </label>
                        </div> -->

                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error has-feedback': frmReturn.$submitted && frmReturn.plan_approved_date.$invalid}"
                        >
                            <label for="">วันที่ตีกลับ :</label>
                            <input
                                type="text"
                                id="plan_bounced_date"
                                name="plan_bounced_date"
                                ng-model="returnData.plan_bounced_date"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmReturn.$submitted && frmReturn.plan_approved_date.$error.required">
                                กรุณาเลือกลงวันที่
                            </span>
                        </div>

                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error has-feedback': frmReturn.$submitted && frmReturn.plan_bounced_date.$invalid}"
                        >
                            <label for="">เหตุผลการตีกลับเอกสาร :</label>
                            <input
                                type="text"
                                id="plan_bounced_note"
                                name="plan_bounced_note"
                                ng-model="returnData.plan_bounced_note"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmReturn.$submitted && frmReturn.plan_bounced_note.$error.required">
                                กรุณาระบุเหมายเหตุ
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
