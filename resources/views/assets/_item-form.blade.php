<div class="modal fade" id="item-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มรายการสินค้า/บริการ</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="">ประเภทแผน</label>
                            <input type="text" id="po_no" name="po_no" class="form-control" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ประเภทสินค้า/บริการ</label>
                            <input type="text" id="po_date" name="po_date" class="form-control" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">กลุ่มสินค้า/บริการ</label>
                            <input type="text" id="po_net_total" name="po_net_total" class="form-control" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ชื่อสินค้า/บริการ</label>
                            <input type="text" id="po_user" name="po_user" class="form-control" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ราคา</label>
                            <input type="text" id="po_user" name="po_user" class="form-control" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">หน่วยนับ</label>
                            <input type="text" id="po_user" name="po_user" class="form-control" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">ใน/นอกคลัง</label>
                            <div style="display: flex; gap: 30px;">
                                <div>
                                    <input type="radio" ng-model="in_stock" ng-value="1" /> ในคลัง 
                                </div>
                                <div>
                                    <input type="radio" ng-model="in_stock" ng-value="0" /> นอกคลัง
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">หมายเหตุ</label>
                            <textarea rows="" id="po_user" name="po_user" class="form-control"></textarea>
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
