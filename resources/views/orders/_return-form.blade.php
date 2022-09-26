<div class="modal fade" id="return-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เหตุผลการตีกลับ</h5>
            </div>

            <form id="frmReturn" name="frmReturn" novalidate ng-submit="onReturnSupport($event, frmReturn, returnData.support_id);">
                <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}" />

                <div class="modal-body">
                    <div class="row">
                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error': frmReturn.$submitted && frmReturn.reason.$invalid}"
                        >
                            <textarea
                                type="text"
                                id="reason"
                                name="reason"
                                ng-model="returnData.reason"
                                rows="5"
                                class="form-control"
                                required
                            ></textarea>
                            <span class="help-block" ng-show="frmReturn.$submitted && frmReturn.reason.$error.required">
                                กรุณาระบุเหตุผลการตีกลับ
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
