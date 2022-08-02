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
                            <div style="display: flex; flex-direction: row;">
                                <!-- <select
                                    style="margin-right: 1rem;"
                                    class="form-control"
                                    ng-model="cboPlanType"
                                    ng-change="onFilterCategories(cboPlanType); getPlans(2);"
                                >
                                    <option value="">-- เลือกประเภทแผน --</option>
                                    @foreach($planTypes as $planType)
                                        <option value="{{ $planType->id }}">
                                            {{ $planType->plan_type_name }}
                                        </option>
                                    @endforeach
                                </select> -->
        
                                <select
                                    style="margin-right: 1rem;"
                                    class="form-control"
                                    ng-model="cboCategory"
                                    ng-change="getPlans(2);"
                                >
                                    <option value="">-- เลือกประเภทพัสดุ --</option>
                                    <option ng-repeat="category in forms.categories" value="@{{ category.id }}">
                                            @{{ category.name }}
                                    </option>
                                </select>
                            </div>
                        </div><!-- /.box-body -->
                    </div>
                    <!-- // TODO: Filtering controls -->

                    <table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
                        <thead>
                            <tr>
                                <th style="width: 3%; text-align: center;">#</th>
                                <!-- <th style="width: 8%; text-align: center;">ปีงบ</th> -->
                                <th style="width: 8%; text-align: center;">เลขที่แผน</th>
                                <th>รายการ</th>
                                <th style="width: 10%; text-align: center;">ราคาต่อหน่วย</th>
                                <th style="width: 10%; text-align: center;">จำนวนที่ขอ</th>
                                <th style="width: 10%; text-align: center;">รวมเป็นเงิน</th>
                                <th style="width: 20%; text-align: center;">หน่วยงาน</th>
                                <th style="width: 5%; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="(index, plan) in plans">
                                <td style="text-align: center;">@{{ index+plans_pager.from }}</td>
                                <!-- <td style="text-align: center;">@{{ plan.year }}</td> -->
                                <td style="text-align: center;">@{{ plan.plan.plan_no }}</td>
                                <td>
                                    <p style="margin: 0; font-weight: bold;">
                                        @{{ plan.plan.plan_item.item.category.name }}
                                    </p>
                                    @{{ plan.plan.plan_item.item.item_name }}
                                    <p style="margin: 0; color: blue;">
                                        @{{ plan.desc }}
                                    </p>
                                    <a  href="{{ url('/'). '/uploads/' }}@{{ plan.attachment }}"
                                        class="btn btn-default btn-xs" 
                                        title="ไฟล์แนบ"
                                        target="_blank"
                                        ng-show="plan.attachment">
                                        <i class="fa fa-paperclip" aria-hidden="true"></i>
                                    </a>
                                </td>
                                <td style="text-align: center;">
                                    @{{ plan.price_per_unit | currency:'':0 }}
                                </td>
                                <td style="text-align: center;">
                                    @{{ plan.amount | currency:'':0 }}
                                    <span>@{{ plan.unit.name }}</span>
                                </td>
                                <td style="text-align: center;">
                                    @{{ plan.sum_price | currency:'':0 }}
                                </td>
                                <td style="text-align: center;">
                                    <p style="margin: 0;">@{{ plan.support.depart.depart_name }}</p>
                                    <p style="margin: 0;">@{{ plan.support.division.ward_name }}</p>
                                </td>
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
                    <div style="width: 100%; height: 50px; text-align: center;">
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
                                หน้า @{{ plans_pager.current_page }} จาก @{{ plans_pager.last_page }} | 
                                จำนวน @{{ plans_pager.total }} รายการ
                            </span>
                        </div>
                        <div class="col-md-4">
                            <ul class="pagination pagination-sm no-margin">
                                <li ng-if="plans_pager.current_page !== 1">
                                    <a ng-click="getPlansWithUrl($event, plans_pager.path+ '?page=1', 1, setPlans)" aria-label="Previous">
                                        <span aria-hidden="true">First</span>
                                    </a>
                                </li>

                                <li ng-class="{'disabled': (plans_pager.current_page==1)}">
                                    <a ng-click="getPlansWithUrl($event, plans_pager.prev_page_url, 1, setPlans)" aria-label="Prev">
                                        <span aria-hidden="true">Prev</span>
                                    </a>
                                </li>

                                <!-- <li ng-if="plans_pager.current_page < plans_pager.last_page && (plans_pager.last_page - plans_pager.current_page) > 10">
                                    <a href="@{{ plans_pager.url(plans_pager.current_page + 10) }}">
                                        ...
                                    </a>
                                </li> -->

                                <li ng-class="{'disabled': (plans_pager.current_page==plans_pager.last_page)}">
                                    <a ng-click="getPlansWithUrl($event, plans_pager.next_page_url, 1, setPlans)" aria-label="Next">
                                        <span aria-hidden="true">Next</span>
                                    </a>
                                </li>

                                <li ng-if="plans_pager.current_page !== plans_pager.last_page">
                                    <a ng-click="getPlansWithUrl($event, plans_pager.path+ '?page=' +plans_pager.last_page, 1, setPlans)" aria-label="Previous">
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
