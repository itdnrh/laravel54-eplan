<div class="modal fade" id="withdraw-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="frmWithdraw" name="frmWithdraw" novalidate ng-submit="withdraw($event, frmWithdraw)">
                <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}" />

                <div class="modal-header">
                    <h5 class="modal-title">บันทึกส่งเบิกเงิน</h5>
                </div>
                <div class="modal-body" style="padding-bottom: 0;">
                    <div class="row">
                        <div
                            class="form-group col-md-6"
                            ng-class="{'has-error has-feedback': frmWithdraw.$submitted && frmWithdraw.withdraw_no.$invalid}"
                        >
                        
                            <label for="">เลขที่หนังสือส่งเบิกเงิน</label>
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-default">@{{ withdrawal.doc_prefix }}/</button>
                                </div>
                                <input
                                    type="text"
                                    id="withdraw_no"
                                    name="withdraw_no"
                                    ng-model="withdrawal.withdraw_no"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <span class="help-block" ng-show="frmWithdraw.$submitted && frmWithdraw.withdraw_no.$error.required">
                                กรุณาระบุเลขที่หนังสือส่งเบิกเงิน
                            </span>
                        </div>
                        <div
                            class="form-group col-md-6"
                            ng-class="{'has-error has-feedback': frmWithdraw.$submitted && frmWithdraw.withdraw_date.$invalid}"
                        >
                            <label for="">วันที่หนังสือส่งเบิกเงิน</label>
                            <input
                                type="text"
                                id="withdraw_date"
                                name="withdraw_date"
                                ng-model="withdrawal.withdraw_date"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmWithdraw.$submitted && frmWithdraw.withdraw_date.$error.required">
                                กรุณาเลือกวันที่หนังสือส่งเบิกเงิน
                            </span>
                        </div>
                    </div>

                    <!-- Loading (remove the following to stop the loading)-->
                    <div ng-show="loading" style="width: 100%; height: 50px; text-align: center;">
                        <div class="overlay">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                    <!-- end loading -->

                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                บันทึก
                            </button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">
                                ปิด
                            </button>
                        </div>
                    </div>
                </div><!-- /.modal-footer -->
            </form>
        </div>
    </div>
</div>
