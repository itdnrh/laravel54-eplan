<div class="modal fade" id="moveForm" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="">
                <input type="hidden" ng-model="moving.in_out" />

                <div class="modal-header">
                    <h5 class="modal-title">ฟอร์มย้ายไปหน่วยงานอื่นภายใน รพ.</h5>
                </div>
                <div class="modal-body" style="padding-bottom: 0;">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="">เลขที่คำสั่ง</label>
                            <input
                                type="text"
                                id="move_doc_no"
                                class="form-control mr-2"
                                ng-model="moving.move_doc_no"
                            />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">วันที่คำสั่ง</label>
                            <input
                                type="text"
                                id="move_doc_date"
                                class="form-control mr-2"
                                ng-model="moving.move_doc_date"
                            />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">วันที่ย้าย</label>
                            <input
                                type="text"
                                id="move_date"
                                class="form-control mr-2"
                                ng-model="moving.move_date"
                            />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">หน้าที่</label>
                            <select
                                class="form-control mr-2"
                                ng-model="moving.move_duty"
                            >
                                <option value="">-- หน้าที่ --</option>
                                @foreach($duties as $duty)
                                    <option value="{{ $duty->duty_id }}">
                                        {{ $duty->duty_name }}    
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="">ย้าย@{{ moving.in_out === 'O' ? 'ไป' : 'เข้า' }}</label>
                            <div style="display: flex; flex-direction: row;">
                                <select
                                    class="form-control mr-2"
                                    ng-model="moving.move_faction"
                                    ng-change="onFactionSelected(moving.move_faction)"
                                >
                                    <option value="">-- กลุ่มภารกิจ --</option>
                                    @foreach($factions as $faction)
                                        <option value="{{ $faction->faction_id }}">
                                            {{ $faction->faction_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <select
                                    class="form-control mr-2"
                                    ng-model="moving.move_depart"
                                    ng-change="onDepartSelected(moving.move_depart)"
                                >
                                    <option value="">-- กลุ่มงาน --</option>
                                    <option ng-repeat="dep in forms.departs" value="@{{ dep.depart_id }}">
                                        @{{ dep.depart_name }}
                                    </option>
                                </select>
                                <select class="form-control" ng-model="moving.move_division">
                                    <option value="">-- งาน --</option>
                                    <option ng-repeat="div in forms.divisions" value="@{{ div.ward_id }}">
                                        @{{ div.ward_name }}    
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="">เหตุผลการย้าย</label>
                            <textarea
                                rows="5"
                                id="move_reason"
                                class="form-control mr-2"
                                ng-model="moving.move_reason"
                            ></textarea>
                        </div>
                    </div>
                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button class="btn btn-primary" ng-click="move($event)">ย้าย</button>
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
		$('#move_date').datepicker({
			autoclose: true,
			language: 'th',
			format: 'dd/mm/yyyy',
			thaiyear: true
		}).datepicker('update', new Date());

        $('#move_doc_date').datepicker({
			autoclose: true,
			language: 'th',
			format: 'dd/mm/yyyy',
			thaiyear: true
		}).datepicker('update', new Date());
	});
</script>