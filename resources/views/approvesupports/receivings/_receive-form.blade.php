<div class="modal fade" id="receive-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-md">
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
                    <h5 class="modal-title"> <i class="fa fa-check-square-o"></i> การอนุมัติงบประมาณ</h5>
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
                            ng-class="{'has-error has-feedback': frmReceive.$submitted && frmReceive.plan_approved_date.$invalid}"
                        >
                            <label for="">วันที่อนุมัติ :</label>
                            <input
                                type="text"
                                id="plan_approved_date"
                                name="plan_approved_date"
                                ng-model="receive.plan_approved_date"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmReceive.$submitted && frmReceive.plan_approved_date.$error.required">
                                กรุณาเลือกลงวันที่
                            </span>
                        </div>

                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error has-feedback': frmReceive.$submitted && frmReceive.plan_approved_budget.$invalid}"
                        >
                            <label for="">งบประมาณที่อนุมัติ :</label>
                            <input
                                type="text"
                                id="plan_approved_budget"
                                name="plan_approved_budget"
                                ng-model="receive.plan_approved_budget"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmReceive.$submitted && frmReceive.plan_approved_budget.$error.required">
                                กรุณาระบุงบประมาณที่อนุมัติ
                            </span>
                        </div>

                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error has-feedback': frmReceive.$submitted && frmReceive.plan_approved_note.$invalid}"
                        >
                            <label for="">หมายเหตุ :</label>
                            <input
                                type="text"
                                id="plan_approved_note"
                                name="plan_approved_note"
                                ng-model="receive.plan_approved_note"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmReceive.$submitted && frmReceive.plan_approved_note.$error.required">
                                กรุณาระบุเหมายเหตุ
                            </span>
                        </div>
<!--                        
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
                                        {{ $officer->prefix->prefix_name.$officer->person_firstname. ' ' .$officer->person_lastname }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="help-block" ng-show="frmReceive.$submitted && frmReceive.officer.$error.required">
                                กรุณาเลือก จทน.พัสดุ (ผู้รับผิดชอบ)
                            </span>
                        </div> -->
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

<script>
     $('#plan_approved_budget').inputmask("currency", { "placeholder": "0" });
</script>
