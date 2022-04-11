<div class="modal fade" id="po-from" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title">บันทึกออกใบ OP</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="">เลขที่ PO</label>
                            <input type="text" id="po_no" name="po_no" class="form-control" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">วันที่ PO</label>
                            <input type="text" id="po_date" name="po_date" class="form-control" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ยอดเงินทั้งสิ้น</label>
                            <input type="text" id="po_net_total" name="po_net_total" class="form-control" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ผู้บันทัก</label>
                            <input type="text" id="po_user" name="po_user" class="form-control" />
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button
                        ng-click="createPO($event)"
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
