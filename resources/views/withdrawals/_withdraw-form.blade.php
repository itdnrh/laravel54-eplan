<div class="modal fade" id="withdraw-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="">
                <div class="modal-header">
                    <h5 class="modal-title">บันทึกส่งเบิกเงิน</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div class="modal-body" style="padding-bottom: 0;">
                    <div class="row">
                        <div
                            class="form-group col-md-12"
                            ng-class="{'has-error has-feedback': errors.withdraw_no}"
                        >
                            <label for="">เลขที่หนังสือส่งเบิกเงิน</label>
                            <input
                                type="text"
                                id="withdraw_no"
                                name="withdraw_no"
                                ng-model="withdraw_no"
                                class="form-control"
                            />
                            <span class="help-block" ng-show="errors.withdraw_no">
                                @{{ errors.withdraw_no[0] }}
                            </span>
                        </div>
                        <div
                            class="form-group col-md-12"
                            ng-class="{'has-error has-feedback': errors.withdraw_date}"
                        >
                            <label for="">วันที่หนังสือส่งเบิกเงิน</label>
                            <input
                                type="text"
                                id="withdraw_date"
                                name="withdraw_date"
                                ng-model="withdraw_date"
                                class="form-control"
                            />
                            <span class="help-block" ng-show="errors.withdraw_date">
                                @{{ errors.withdraw_date[0] }}
                            </span>
                        </div>
                    </div>

                    <!-- Loading (remove the following to stop the loading)-->
                    <div style="width: 100%; height: 50px; text-align: center;">
                        <div ng-show="loading" class="overlay">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                    <!-- end loading -->

                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <div class="row">
                        <div class="col-md-12">
                            <button
                                type="button"
                                class="btn btn-primary"
                                ng-click="withdraw($event)"
                            >
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
