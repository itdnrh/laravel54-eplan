<table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
    <thead>
        <tr>
            <th style="width: 3%; text-align: center;">#</th>
            <th style="width: 8%; text-align: center;">เลขที่แผน</th>
            <!-- <th style="width: 8%; text-align: center;">ปีงบ</th> -->
            <th>รายการ</th>
            <th style="width: 8%; text-align: center;">ราคาต่อหน่วย</th>
            <th style="width: 8%; text-align: center;">รวมเป็นเงิน</th>
            <th style="width: 20%; text-align: center;">หน่วยงาน</th>
            <th style="width: 10%; text-align: center;">วันที่รับเอกสาร</th>
            <th style="width: 10%; text-align: center;">สถานะ</th>
            <th style="width: 6%; text-align: center;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="(index, plan) in plans">
            <td style="text-align: center;">@{{ index+plans_pager.from }}</td>
            <td style="text-align: center;">@{{ plan.plan_no }}</td>
            <!-- <td style="text-align: center;">@{{ plan.year }}</td> -->
            <td>
                <h4 style="margin: 0;">
                    @{{ plan.plan_item.item.category.name }}
                </h4>
                @{{ plan.plan_item.item.item_name }} จำนวน 
                <span>@{{ plan.plan_item.amount | currency:'':0 }}</span>
                <span>@{{ plan.plan_item.unit.name }}</span>
                <a  href="{{ url('/'). '/uploads/' }}@{{ plan_item.attachment }}"
                    class="btn btn-default btn-xs" 
                    title="ไฟล์แนบ"
                    target="_blank"
                    ng-show="plan_item.attachment">
                    <i class="fa fa-paperclip" aria-hidden="true"></i>
                </a>
            </td>
            <td style="text-align: center;">
                @{{ plan.plan_item.price_per_unit | currency:'':0 }}
            </td>
            <td style="text-align: center;">
                @{{ plan.plan_item.sum_price | currency:'':0 }}
            </td>
            <td style="text-align: center;">
                <p style="margin: 0;">@{{ plan.depart.depart_name }}</p>
                <p style="margin: 0;">@{{ plan.division.ward_name }}</p>
            </td>
            <td style="text-align: center;">
                @{{ plan.received_date }}
            </td>
            <td style="text-align: center;">
                <span class="label label-primary" ng-show="plan.status == 0">
                    อยู่ระหว่างดำเนินการ
                </span>
                <span class="label label-info" ng-show="plan.status == 1">
                    ส่งเอกสารแล้ว
                </span>
                <span class="label bg-navy" ng-show="plan.status == 2">
                    รับเอกสารแล้ว
                </span>
                <span class="label label-success" ng-show="plan.status == 3">
                    ออกใบสั้งซื้อแล้ว
                </span>
                <span class="label bg-maroon" ng-show="plan.status == 4">
                    ตรวจรับแล้ว
                </span>
                <span class="label label-warning" ng-show="plan.status == 5">
                    ส่งเบิกเงินแล้ว
                </span>
                <span class="label label-danger" ng-show="plan.status == 6">
                    ตั้งหนี้แล้ว
                </span>
                <span class="label label-default" ng-show="plan.status == 9">
                    ยกเลิก
                </span>
            </td>
            <td style="text-align: center;">
                <div style="display: flex; justify-content: center; gap: 2px;">
                    <form
                        id="frmDelete"
                        method="POST"
                        action="{{ url('/assets/delete') }}"
                        ng-show="plan.status == 2"
                    >
                        {{ csrf_field() }}
                        <button
                            type="submit"
                            ng-click="delete($event, plan.id)"
                            class="btn btn-danger btn-xs"
                        >
                            ยกเลิก
                        </button>
                    </form>
                </div>
            </td>             
        </tr>
    </tbody>
</table>

<div class="row">
    <div class="col-md-4">
        หน้า @{{ plans_pager.current_page }} จาก @{{ plans_pager.last_page }}
    </div>
    <div class="col-md-4" style="text-align: center;">
        จำนวน @{{ plans_pager.total }} รายการ
    </div>
    <div class="col-md-4">
        <ul class="pagination pagination-sm no-margin pull-right" ng-show="plans_pager.last_page > 1">
            <li ng-if="plans_pager.current_page !== 1">
                <a href="#" ng-click="getPlansWithUrl($event, plans_pager.path+ '?page=1', 2, setPlans)" aria-label="Previous">
                    <span aria-hidden="true">First</span>
                </a>
            </li>
        
            <li ng-class="{'disabled': (plans_pager.current_page==1)}">
                <a href="#" ng-click="getPlansWithUrl($event, plans_pager.prev_page_url, 2, setPlans)" aria-label="Prev">
                    <span aria-hidden="true">Prev</span>
                </a>
            </li>

            <!-- <li ng-repeat="i in debtPages" ng-class="{'active': plans_pager.current_page==i}">
                <a href="#" ng-click="getPlansWithUrl(plans_pager.path + '?page=' +i)">
                    @{{ i }}
                </a>
            </li> -->

            <!-- <li ng-if="plans_pager.current_page < plans_pager.last_page && (plans_pager.last_page - plans_pager.current_page) > 10">
                <a href="#" ng-click="plans_pager.path">
                    ...
                </a>
            </li> -->

            <li ng-class="{'disabled': (plans_pager.current_page==plans_pager.last_page)}">
                <a href="#" ng-click="getPlansWithUrl($event, plans_pager.next_page_url, 2, setPlans)" aria-label="Next">
                    <span aria-hidden="true">Next</span>
                </a>
            </li>

            <li ng-if="plans_pager.current_page !== plans_pager.last_page">
                <a href="#" ng-click="getPlansWithUrl($event, plans_pager.path+ '?page=' +plans_pager.last_page, 2, setPlans)" aria-label="Previous">
                    <span aria-hidden="true">Last</span>
                </a>
            </li>
        </ul>
    </div>
</div><!-- /.row -->