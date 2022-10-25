<div class="modal fade" id="plan-groups-list" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content">
            <form action="">
                <div class="modal-header">
                    <h5 class="modal-title">รายการแผนแบบกลุ่ม</h5>
                </div>
                <div class="modal-body" style="padding-bottom: 0;">
                    <!-- // TODO: Filtering controls -->
                    <div class="box">
                        <div class="box-body">
                            <div style="display: flex; flex-direction: row;">
                                <!-- <input
                                    type="text"
                                    id="txtKeyword"
                                    name="txtKeyword"
                                    class="form-control"
                                    ng-model="txtKeyword"
                                    ng-change="getPlanGroupsList()"
                                /> -->
                            </div>
                        </div><!-- /.box-body -->
                    </div>
                    <!-- // TODO: Filtering controls -->

                    <table class="table table-bordered table-striped table-sm" style="font-size: 14px; margin-bottom: 10px;">
                        <thead>
                            <tr>
                                <th style="width: 3%; text-align: center;">#</th>
                                <!-- <th style="width: 8%; text-align: center;">ปีงบ</th> -->
                                <th>รายการ</th>
                                <th style="width: 10%; text-align: center;">จำนวนที่ขอ</th>
                                <th style="width: 12%; text-align: center;">ยอดงบที่ขอ</th>
                                <th style="width: 6%; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="(index, plan) in planGroups">
                                <td style="text-align: center;">@{{ index+planGroups_pager.from }}</td>
                                <!-- <td style="text-align: center;">@{{ plan.year }}</td> -->
                                <td>
                                    @{{ plan.item_name }}
                                    ราคา <span>@{{ plan.price_per_unit | currency:'':0 }}</span> บาท
                                </td>
                                <td style="text-align: center;">
                                    @{{ plan.amount | currency:'':0 }}
                                    <span>@{{ plan.unit_name }}</span>
                                </td>
                                <td style="text-align: center;">
                                    @{{ plan.sum_price | currency:'':0 }}
                                </td>
                                <td style="text-align: center;">
                                        <a  href="#"
                                            ng-click="onSelectedPlanGroup($event, plan)"
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
                                หน้า @{{ planGroups_pager.current_page }} จาก @{{ planGroups_pager.last_page }} | 
                                จำนวน @{{ planGroups_pager.total }} รายการ
                            </span>
                        </div>
                        <div class="col-md-4">
                            <ul class="pagination pagination-sm no-margin">
                                <li ng-if="planGroups_pager.current_page !== 1">
                                    <a ng-click="getPlanGroupsListWithUrl($event, planGroups_pager.path+ '?page=1', 0, setPlanGroupsList)" aria-label="Previous">
                                        <span aria-hidden="true">First</span>
                                    </a>
                                </li>

                                <li ng-class="{'disabled': (planGroups_pager.current_page==1)}">
                                    <a ng-click="getPlanGroupsListWithUrl($event, planGroups_pager.prev_page_url, 0, setPlanGroupsList)" aria-label="Prev">
                                        <span aria-hidden="true">Prev</span>
                                    </a>
                                </li>

                                <!-- <li ng-if="planGroups_pager.current_page < planGroups_pager.last_page && (planGroups_pager.last_page - planGroups_pager.current_page) > 10">
                                    <a href="@{{ planGroups_pager.url(planGroups_pager.current_page + 10) }}">
                                        ...
                                    </a>
                                </li> -->

                                <li ng-class="{'disabled': (planGroups_pager.current_page==planGroups_pager.last_page)}">
                                    <a ng-click="getPlanGroupsListWithUrl($event, planGroups_pager.next_page_url, 0, setPlanGroupsList)" aria-label="Next">
                                        <span aria-hidden="true">Next</span>
                                    </a>
                                </li>

                                <li ng-if="planGroups_pager.current_page !== planGroups_pager.last_page">
                                    <a ng-click="getPlanGroupsListWithUrl($event, planGroups_pager.path+ '?page=' +planGroups_pager.last_page, 0, setPlanGroupsList)" aria-label="Previous">
                                        <span aria-hidden="true">Last</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger" ng-click="onSelectedPlanGroup($event, null)">
                                ปิด
                            </button>
                        </div>
                    </div>
                </div><!-- /.modal-footer -->
            </form>
        </div>
    </div>
</div>
