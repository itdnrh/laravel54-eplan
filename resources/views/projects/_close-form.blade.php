<div class="modal fade" id="close-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="frmClose" name="frmClose" novalidate ng-submit="onCloseProject($event, frmClose, project.id)">
                <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}" />

                <div class="modal-header">
                    <h5 class="modal-title">บันทึกปิดโครงการ</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': frmClose.$submitted && frmClose.total_actual.$invalid}"
                        >
                            <label for="">ยอดดำเนินการจริง</label>
                            <input
                                type="text"
                                id="total_actual"
                                name="total_actual"
                                ng-model="project.total_actual"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmClose.$submitted && frmClose.total_actual.$error.required">
                                กรุณาระบุยอดดำเนินการจริง
                            </span>
                        </div>
                        <div
                            class="col-md-6 form-group"
                            ng-class="{'has-error has-feedback': frmClose.$submitted && frmClose.closed_date.$invalid}"
                        >
                            <label for="">วันที่ปิดโครงการ</label>
                            <input
                                type="text"
                                id="closed_date"
                                name="closed_date"
                                ng-model="project.closed_date"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmClose.$submitted && frmClose.closed_date.$error.required">
                                กรุณาเลือกวันที่ปิดโครงการ
                            </span>
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer">
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
    $(function () {
        $('#total_actual').inputmask("currency", { "placeholder": "0" });
    });
</script>
