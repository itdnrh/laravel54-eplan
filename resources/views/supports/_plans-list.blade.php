<div class="modal fade" id="plans-list" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
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
                            <div style="display: flex; gap: 5px;">
                                <input
                                    type="text"
                                    id="txtKeyword"
                                    name="txtKeyword"
                                    class="form-control"
                                    ng-model="txtKeyword"
                                    ng-change="getPlans('0-1')"
                                    placeholder="ค้นหาด้วยชื่อรายการ"
                                    style="width: 50%;"
                                />
                                <select id="cboDepart"
                                        name="cboDepart"
                                        ng-model="cboDepart"
                                        ng-change="getPlans('0-1')"
                                        class="form-control select2" 
                                        style="width: 50%; font-size: 12px;">
                                    <option value="">-- กลุ่มงานทั้งหมด --</option>
                                    @foreach($departs as $depart)
                                        <option value="{{ $depart->depart_id }}">
                                            {{ $depart->depart_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div><!-- /.box-body -->
                    </div>
                    <!-- // TODO: Filtering controls -->

                    <table class="table table-bordered table-striped table-sm" style="font-size: 14px; margin-bottom: 10px;">
                        <thead>
                            <tr>
                                <th style="width: 3%; text-align: center;">#</th>
                                <!-- <th style="width: 8%; text-align: center;">ปีงบ</th> -->
                                <th style="width: 8%; text-align: center;">เลขที่แผน</th>
                                <th>รายการ</th>
                                <th style="width: 8%; text-align: center;">จำนวนที่ขอ</th>
                                <th style="width: 8%; text-align: center;">ยอดงบที่ขอ</th>
                                <th style="width: 8%; text-align: center;">จน.คงเหลือ</th>
                                <th style="width: 8%; text-align: center;">ยอดงบคงเหลือ</th>
                                <th style="width: 20%; text-align: center;">หน่วยงานผู้ขอ</th>
                                <!-- <th style="width: 5%; text-align: center;">สถานะ</th> -->
                                <th style="width: 6%; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="(index, plan) in plans">
                                <td style="text-align: center;">@{{ index+plans_pager.from }}</td>
                                <!-- <td style="text-align: center;">@{{ plan.year }}</td> -->
                                <td style="text-align: center;">@{{ plan.plan_no }}</td>
                                <td>
                                    <h5 style="margin: 0; font-weight: bold;">
                                        @{{ plan.plan_item.item.category.name }}
                                    </h5>
                                    @{{ plan.plan_item.item.item_name }}
                                    <span>ราคา @{{ plan.plan_item.price_per_unit | currency:'':0 }} บาท</span>
                                    <span class="label label-success label-xs" ng-show="plan.in_plan == 'I'">ในแผน</span>
                                    <span class="label label-danger label-xs" ng-show="plan.in_plan == 'O'">นอกแผน</span>
                                </td>
                                <td style="text-align: center;">
                                    <p ng-show="plan.plan_item.calc_method == 1">
                                        @{{ plan.plan_item.amount | currency:'':1 }} 
                                        <span>@{{ plan.plan_item.unit.name }}</span>
                                    </p>
                                    <p ng-show="plan.plan_item.calc_method == 2">-</p>
                                </td>
                                <td style="text-align: center;">
                                    @{{ plan.plan_item.sum_price | currency:'':2 }}
                                </td>
                                <td style="text-align: center;">
                                    <p ng-show="plan.plan_item.calc_method == 1">
                                        @{{ plan.plan_item.remain_amount | currency:'':1 }} 
                                        <span>@{{ plan.plan_item.unit.name }}</span>
                                    </p>
                                    <p ng-show="plan.plan_item.calc_method == 2">-</p>
                                </td>
                                <td style="text-align: center;">
                                    @{{ plan.plan_item.remain_budget | currency:'':2 }}
                                </td>
                                <td style="text-align: center;">
                                    <p style="margin: 0;">@{{ plan.depart.depart_name }}</p>
                                    <p style="margin: 0;">@{{ plan.division.ward_name }}</p>
                                </td>
                                <!-- <td style="text-align: center;">@{{ plan.status }}</td> -->
                                <td style="text-align: center;">
                                        <a  href="#"
                                            ng-click="onSelectedPlan($event, plan)"
                                            ng-show="!isSelected(plan.id)"
                                            class="btn btn-primary btn-xs"
                                            title="เลือก">
                                            เลือก
                                        </a>
                                </td>             
                            </tr>
                        </tbody>
                    </table>

                    <!-- Loading (remove the following to stop the loading)-->
                    <div style="width: 100%; height: 50px; text-align: center;" ng-show="loading">
                        <div class="overlay">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                    <!-- end loading -->

                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <div class="row">
                        <div class="col-md-4">
                            <span class="pull-left" style="margin-top: 5px;">
                                หน้า @{{ plans_pager.current_page }} จาก @{{ plans_pager.last_page }} | 
                                จำนวน @{{ plans_pager.total }} รายการ
                            </span>
                        </div>
                        <div class="col-md-4">
                            <ul class="pagination pagination-sm no-margin">
                                <li ng-if="plans_pager.current_page !== 1">
                                    <a ng-click="getPlansWithUrl($event, plans_pager.path+ '?page=1', '0-1', setPlans)" aria-label="Previous">
                                        <span aria-hidden="true">First</span>
                                    </a>
                                </li>

                                <li ng-class="{'disabled': (plans_pager.current_page==1)}">
                                    <a ng-click="getPlansWithUrl($event, plans_pager.prev_page_url, '0-1', setPlans)" aria-label="Prev">
                                        <span aria-hidden="true">Prev</span>
                                    </a>
                                </li>

                                <!-- <li ng-if="plans_pager.current_page < plans_pager.last_page && (plans_pager.last_page - plans_pager.current_page) > 10">
                                    <a href="@{{ plans_pager.url(plans_pager.current_page + 10) }}">
                                        ...
                                    </a>
                                </li> -->

                                <li ng-class="{'disabled': (plans_pager.current_page==plans_pager.last_page)}">
                                    <a ng-click="getPlansWithUrl($event, plans_pager.next_page_url, '0-1', setPlans)" aria-label="Next">
                                        <span aria-hidden="true">Next</span>
                                    </a>
                                </li>

                                <li ng-if="plans_pager.current_page !== plans_pager.last_page">
                                    <a ng-click="getPlansWithUrl($event, plans_pager.path+ '?page=' +plans_pager.last_page, '0-1', setPlans)" aria-label="Previous">
                                        <span aria-hidden="true">Last</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger" ng-click="onSelectedPlan($event, null)">
                                ปิด
                            </button>
                        </div>
                    </div>
                </div><!-- /.modal-footer -->
            </form>
        </div>
    </div>
</div>
