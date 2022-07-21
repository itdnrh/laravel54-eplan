<div class="modal fade" id="leaveForm" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="">
                <div class="modal-header">
                    <h5 class="modal-title">ฟอร์มออก</h5>
                </div>
                <div class="modal-body" style="padding-bottom: 0;">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="">เลขที่คำสั่ง</label>
                            <input
                                type="text"
                                id="leave_doc_no"
                                class="form-control mr-2"
                                ng-model="leaving.leave_doc_no"
                            />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">วันที่คำสั่ง</label>
                            <input
                                type="text"
                                id="leave_doc_date"
                                class="form-control mr-2"
                                ng-model="leaving.leave_doc_date"
                            />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">วันที่ออก</label>
                            <input
                                type="text"
                                id="leave_date"
                                class="form-control mr-2"
                                ng-model="leaving.leave_date"
                            />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">สาเหตุ</label>
                            <select
                                class="form-control mr-2"
                                ng-model="leaving.leave_type"
                            >
                                <option value="">-- สาเหตุ --</option>
                                <option value="1">เกษียณอายุราชการ</option>
                                <option value="2">ลาออก</option>
                                <option value="3">ถูกให้ออก</option>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="">เหตุผลการออก</label>
                            <textarea
                                rows="5"
                                id="leave_reason"
                                class="form-control mr-2"
                                ng-model="leaving.leave_reason"
                            ></textarea>
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button class="btn btn-primary" ng-click="leave($event)">บันทึก</button>
                    <button class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        ปิด
                    </button>
                </div><!-- /.modal-footer -->
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(function() {
		$('#leave_date').datepicker({
			autoclose: true,
			language: 'th',
			format: 'dd/mm/yyyy',
			thaiyear: true
		}).datepicker('update', new Date());

        $('#leave_doc_date').datepicker({
			autoclose: true,
			language: 'th',
			format: 'dd/mm/yyyy',
			thaiyear: true
		}).datepicker('update', new Date());
	});
</script>