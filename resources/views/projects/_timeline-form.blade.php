<div class="modal fade" id="timeline-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="frmUpdateTimeline" name="frmUpdateTimeline" novalidate ng-submit="updateTimeline($event, frmUpdateTimeline, timeline.id)">
                <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}" />

                <div class="modal-header">
                    <h5 class="modal-title">
                        แก้ไข Timeline
                        <span ng-show="timelineFieldName == 'sent_stg_date'">(ส่งงานแผน)</span>
                        <span ng-show="timelineFieldName == 'sent_fin_date'">(ส่งการเงิน)</span>
                        <span ng-show="timelineFieldName == 'approved_date'">(ผอ.อนุมัติ)</span>
                        <span ng-show="timelineFieldName == 'start_date'">(ดำเนินโครงการ)</span>
                    </h5>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div
                            class="col-md-12 form-group"
                            ng-class="{'has-error has-feedback': frmUpdateTimeline.$submitted && frmUpdateTimeline.dtpDate.$invalid}"
                        >
                            <label for="">วันที่</label>
                            <input
                                type="text"
                                id="dtpDate"
                                name="dtpDate"
                                class="form-control"
                                required
                            />
                            <span class="help-block" ng-show="frmUpdateTimeline.$submitted && frmUpdateTimeline.dtpDate.$error.required">
                                กรุณาเลือกวันที่
                            </span>
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button
                        type="submit"
                        class="btn btn-primary"
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
