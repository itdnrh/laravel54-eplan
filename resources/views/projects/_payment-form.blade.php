<div class="modal fade" id="payment-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="frmPayment" name="frmPayment" novalidate ng-submit="onSubmitPayment($event, frmPayment, payment.id)">
                <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}" />

                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มเพิ่มรายการเบิกจ่าย</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': frmPayment.$submitted && frmPayment.received_date.$invalid}"
                        >
                            <label for="">วันที่รับเอกสาร</label>
                            <input
                                type="text"
                                id="received_date"
                                name="received_date"
                                ng-model="payment.received_date"
                                ng-change="onPlanTypeSelected(payment.received_date)"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmPayment.$submitted && frmPayment.received_date.$error.required">
                                กรุณาเลือกวันที่รับเอกสาร
                            </span>
                        </div>
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': frmPayment.$submitted && frmPayment.pay_date.$invalid}"
                        >
                            <label for="">วันที่เบิกจ่าย</label>
                            <input
                                type="text"
                                id="pay_date"
                                name="pay_date"
                                ng-model="payment.pay_date"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmPayment.$submitted && frmPayment.pay_date.$error.required">
                                กรุณาเลือกวันที่เบิกจ่าย
                            </span>
                        </div>
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': frmPayment.$submitted && frmPayment.net_total.$invalid}"
                        >
                            <label for="">ยอดเบิกจ่าย</label>
                            <input
                                type="text"
                                id="net_total"
                                name="net_total"
                                ng-model="payment.net_total"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmPayment.$submitted && frmPayment.net_total.$error.required">
                                กรุณาระบุยอดเบิกจ่าย
                            </span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">มี AAR</label>
                            <div style="display: flex; gap: 30px;">
                                <div>
                                    <input type="radio" ng-model="payment.have_aar" ng-value="0" /> ไม่มี 
                                </div>
                                <div>
                                    <input type="radio" ng-model="payment.have_aar" ng-value="1" /> มี
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="">หมายเหตุ</label>
                            <textarea
                                rows=""
                                id="remark"
                                name="remark"
                                ng-model="payment.remark"
                                class="form-control"
                            ></textarea>
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button type="submit" class="btn btn-primary" ng-hide="payment.id">
                        บันทึก
                    </button>
                    <button type="submit" class="btn btn-warning" ng-show="payment.id">
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