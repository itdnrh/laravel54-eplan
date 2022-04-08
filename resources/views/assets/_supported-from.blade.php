<div class="modal fade" id="supported-from" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ url('/assets/') }}">
                <div class="modal-header">
                    <h5 class="modal-title">บันทึกขอสนับสนุน</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="">เลขที่บันทึกขอสนับสนุน</label>
                            <input type="text" class="form-control" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ลงวันที่</label>
                            <input type="text" class="form-control" />
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button class="btn btn-primary" data-dismiss="modal" aria-label="Save">
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
