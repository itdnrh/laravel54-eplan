<div class="modal fade" id="supports-receive" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content">
            <form action="">
                <div class="modal-header">
                    <h5 class="modal-title">รายการแผน</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div class="modal-body" style="padding-bottom: 0;">
                    <!-- // TODO: Filtering controls -->
                    <div class="box">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <select
                                        style="margin-right: 1rem;"
                                        class="form-control"
                                        ng-model="cboPlanType"
                                        ng-change="onFilterCategories(cboPlanType); getSupportsToReceive();"
                                    >
                                        <option value="">-- เลือกประเภทแผน --</option>
                                        @foreach($planTypes as $planType)
                                            <option value="{{ $planType->id }}">
                                                {{ $planType->plan_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select
                                        style="margin-right: 1rem;"
                                        class="form-control"
                                        ng-model="cboCategory"
                                        ng-change="getSupportsToReceive();"
                                    >
                                        <option value="">-- เลือกประเภทพัสดุ --</option>
                                        <option ng-repeat="category in forms.categories" value="@{{ category.id }}">
                                            @{{ category.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 5px;">
                                <div class="col-md-6">
                                    <select
                                        id="cboFaction"
                                        name="cboFaction"
                                        ng-model="cboFaction"
                                        class="form-control"
                                        ng-change="onFactionSelected(cboFaction)"
                                    >
                                        <option value="">-- กลุ่มภารกิจ --</option>
                                        @foreach($factions as $faction)

                                            <option value="{{ $faction->faction_id }}">
                                                {{ $faction->faction_name }}
                                            </option>

                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select
                                        id="cboDepart"
                                        name="cboDepart"
                                        ng-model="cboDepart"
                                        ng-change="getSupportsToReceive()"
                                        class="form-control"
                                    >
                                        <option value="">-- กลุ่มงาน --</option>
                                        <option ng-repeat="dep in forms.departs" value="@{{ dep.depart_id }}">
                                            @{{ dep.depart_name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </div>
                    <!-- // TODO: Filtering controls -->

                    <table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
                        <thead>
                            <tr>
                                <th style="width: 3%; text-align: center;">#</th>
                                <!-- <th style="width: 8%; text-align: center;">ปีงบ</th> -->
                                <th style="width: 12%; text-align: center;">เลขที่เอกสาร</th>
                                <th style="width: 8%; text-align: center;">วันที่เอกสาร</th>
                                <th style="width: 12%; text-align: center;">ประเภทพัสดุ</th>
                                <th>รายการ</th>
                                <th style="width: 8%; text-align: center;">ยอดเงิน</th>
                                <th style="width: 20%; text-align: center;">หน่วยงาน</th>
                                <th style="width: 10%; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="(index, support) in supportsToReceives">
                                <td style="text-align: center;">@{{ index+supportsToReceives_pager.from }}</td>
                                <!-- <td style="text-align: center;">@{{ plan.year }}</td> -->
                                <td style="text-align: center;">@{{ support.doc_no }}</td>
                                <td style="text-align: center;">@{{ support.doc_date | thdate }}</td>
                                <td style="text-align: center;">@{{ support.plan_type.plan_type_name }}</td>
                                <td>
                                    <p style="margin: 0;">@{{ support.plan.plan_no }}</p>
                                    <ul style="margin: 0 5px; padding: 0 10px;">
                                        <li ng-repeat="(index, detail) in support.details">
                                            <span>@{{ detail.plan.plan_item.item.item_name }} จำนวน </span>
                                            <span>@{{ detail.plan.plan_item.amount | currency:'':0 }}</span>
                                            <span>@{{ detail.plan.plan_item.unit.name }}</span>
                                        </li>
                                    </ul>
                                    <!-- <a  href="{{ url('/'). '/uploads/' }}@{{ plan.attachment }}"
                                        class="btn btn-default btn-xs" 
                                        title="ไฟล์แนบ"
                                        target="_blank"
                                        ng-show="plan.attachment">
                                        <i class="fa fa-paperclip" aria-hidden="true"></i>
                                    </a> -->
                                </td>
                                <td style="text-align: center;">
                                    @{{ support.total | currency:'':0 }}
                                </td>
                                <td style="text-align: center;">
                                    <p style="margin: 0;">@{{ support.depart.depart_name }}</p>
                                    <p style="margin: 0;">@{{ support.division.ward_name }}</p>
                                </td>
                                <td style="text-align: center;">
                                    <a  href="#"
                                        ng-click="onReceived($event, support)"
                                        class="btn btn-primary btn-xs"
                                        title="รับเอกสาร">
                                        รับเอกสาร
                                    </a>
                                </td>             
                            </tr>
                        </tbody>
                    </table>

                    <!-- Loading (remove the following to stop the loading)-->
                    <div style="width: 100%; height: 50px; text-align: center;" ng-show="supportsToReceives.length == 0">
                        <div ng-show="loading" class="overlay">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                    <!-- end loading -->

                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <div class="row">
                        <div class="col-md-4">
                            <span class="pull-left" style="margin-top: 5px;">
                                หน้า @{{ supportsToReceives_pager.current_page }} จาก @{{ supportsToReceives_pager.last_page }} | 
                                จำนวน @{{ supportsToReceives_pager.total }} รายการ
                            </span>
                        </div>
                        <div class="col-md-4">
                            <ul class="pagination pagination-sm no-margin">
                                <li ng-if="supportsToReceives_pager.current_page !== 1">
                                    <a ng-click="getPlansToReceiveWithUrl($event, supportsToReceives_pager.path+ '?page=1', setPlansToReceive)" aria-label="Previous">
                                        <span aria-hidden="true">First</span>
                                    </a>
                                </li>

                                <li ng-class="{'disabled': (supportsToReceives_pager.current_page==1)}">
                                    <a ng-click="getPlansToReceiveWithUrl($event, supportsToReceives_pager.prev_page_url, setPlansToReceive)" aria-label="Prev">
                                        <span aria-hidden="true">Prev</span>
                                    </a>
                                </li>

                                <!-- <li ng-if="supportsToReceives_pager.current_page < supportsToReceives_pager.last_page && (supportsToReceives_pager.last_page - supportsToReceives_pager.current_page) > 10">
                                    <a href="@{{ supportsToReceives_pager.url(supportsToReceives_pager.current_page + 10) }}">
                                        ...
                                    </a>
                                </li> -->

                                <li ng-class="{'disabled': (supportsToReceives_pager.current_page==supportsToReceives_pager.last_page)}">
                                    <a ng-click="getPlansToReceiveWithUrl($event, supportsToReceives_pager.next_page_url, setPlansToReceive)" aria-label="Next">
                                        <span aria-hidden="true">Next</span>
                                    </a>
                                </li>

                                <li ng-if="supportsToReceives_pager.current_page !== supportsToReceives_pager.last_page">
                                    <a ng-click="getPlansToReceiveWithUrl($event, supportsToReceives_pager.path+ '?page=' +supportsToReceives_pager.last_page, setPlansToReceive)" aria-label="Previous">
                                        <span aria-hidden="true">Last</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                                ปิด
                            </button>
                        </div>
                    </div>
                </div><!-- /.modal-footer -->
            </form>
        </div>
    </div>
</div>
