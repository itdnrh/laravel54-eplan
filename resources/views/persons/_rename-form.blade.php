<div class="modal fade" id="renameForm" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="">
                <div class="modal-header">
                    <h5 class="modal-title">ฟอร์มเปลี่ยนชื่อ-สกุล</h5>
                </div>
                <div class="modal-body" style="padding-bottom: 0;">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="">เลขที่เอกสารอ้างอิง (ถ้ามี)</label>
                            <input
                                type="text"
                                id="doc_no"
                                ng-model="renaming.doc_no"
                                class="form-control mr-2"
                                placeholder="ระบุเลขที่เอกสารอ้างอิง..."
                                />
                            </div>
                        <div class="form-group col-md-6">
                            <label for="">วันที่เอกสารอ้างอิง (ถ้ามี)</label>
                            <input
                                type="text"
                                id="doc_date"
                                class="form-control mr-2"
                                ng-model="renaming.doc_date"
                                placeholder="ระบุวันที่เอกสารอ้างอิง..."
                            />
                        </div>
                        <div class="form-group col-md-12">
                            <label for="">ชื่อ-สกุลเดิม</label>
                            <input
                                type="text"
                                id="old_name"
                                name="old_name"
                                class="form-control"
                                ng-model="renaming.old_fullname"
                                placeholder="ชื่อ-สกุลเดิม"
                            />
                        </div>
                        <div class="form-group col-md-12">
                            <label for="">ชื่อ-สกุลใหม่</label>
                            <div style="display: flex;">
                                <select
                                    id="new_prefix"
                                    class="form-control"
                                    ng-model="renaming.new_prefix"
                                    style="width: 20%;"
                                >
                                    <option value="">คำนำหน้า</option>
                                    @foreach($prefixes as $prefix)
                                        <option value="{{ $prefix->prefix_id }}">
                                            {{ $prefix->prefix_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input
                                    type="text"
                                    id="new_firstname"
                                    class="form-control"
                                    ng-model="renaming.new_firstname"
                                    value="{{ $person->person_firstname }}"
                                    placeholder="ชื่อ"
                                    style="width: 40%;"
                                />
                                <input
                                    type="text"
                                    id="new_lastname"
                                    class="form-control"
                                    ng-model="renaming.new_lastname"
                                    value="{{ $person->person_lastname }}"
                                    placeholder="สกุล"
                                    style="width: 40%;"
                                />
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="">เหตุผลการเปลี่ยน</label>
                            <textarea
                                rows="5"
                                id="reason"
                                class="form-control mr-2"
                                ng-model="renaming.reason"
                            ></textarea>
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button class="btn btn-primary" ng-click="rename($event, person.person_id)">เปลี่ยน</button>
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
        $('#doc_date').datepicker({
			autoclose: true,
			language: 'th',
			format: 'dd/mm/yyyy',
			thaiyear: true
		}).datepicker('update', new Date());
	});
</script>