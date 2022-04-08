<div class="modal fade" id="cancel-approval-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ url('/cancellations/approve') }}" method="POST">
                <input type="hidden" id="_id" name="_id" value="@{{ leave.cancellation[0].id }}" />
                <input type="hidden" id="leave_id" name="leave_id" value="@{{ leave.id }}" />
                {{ csrf_field() }}

                <div class="modal-header">
                    <h5 class="modal-title">อนุมัติยกเลิกวันลา</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div class="modal-body" style="padding-bottom: 0;">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <h4 style="margin: 0px 5px auto;">
                                <input type="radio" name="approved" value="3" />
                                <span style="margin-right: 10px;">อนุญาต</span>
                                <input type="radio" name="approved" value="4" />
                                <span style="margin-right: 10px;">ไม่อนุญาต</span>
                            </h4>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="">ความเห็นในการลงนาม</label>
                            <textarea id="comment" name="comment" cols="3" class="form-control"></textarea>
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
