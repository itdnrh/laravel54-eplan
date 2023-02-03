<div class="modal fade" id="support-from" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title">บันทึกส่งเอกสารไปพัสดุ</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="">เลขที่บันทึกขอสนับสนุน</label>
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-default">@{{ support.doc_prefix }}/</button>
                                </div>
                                <input
                                    type="text"
                                    id="doc_no"
                                    name="doc_no"
                                    ng-model="support.doc_no"
                                    class="form-control"
                                />
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ลงวันที่</label>
                            <input
                                type="text"
                                id="doc_date"
                                name="doc_date"
                                ng-model="support.doc_date"
                                class="form-control"
                            />
                        </div>
                        <!-- <div class="col-md-6 form-group">
                            <label for="">วันที่ส่งเอกสาร</label>
                            <input type="text" id="sent_date" name="sent_date" class="form-control" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ผู้ส่งเอกสาร</label>
                            <input type="text" id="sent_user" name="sent_user" class="form-control" />
                        </div> -->
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button
                        ng-click="send($event, planType, planId)"
                        class="btn btn-primary"
                        data-dismiss="modal"
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
