<table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
    <thead>
        <tr>
            <th style="width: 3%; text-align: center;">#</th>
            <th style="width: 6%; text-align: center;">เลขที่รับ</th>
            <th style="width: 15%; text-align: center;">เอกสาร</th>
            <th>รายการ</th>
            <th style="width: 8%; text-align: center;">ยอดเงิน</th>
            <th style="width: 20%; text-align: center;">หน่วยงาน</th>
            <th style="width: 10%; text-align: center;">สถานะ</th>
            <th style="width: 6%; text-align: center;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="(index, support) in supports">
            <td style="text-align: center;">@{{ index+supports_pager.from }}</td>
            <td style="text-align: center;">@{{ support.received_no }}</td>
            <td>
                <p style="margin: 0;">เลขที่ @{{ support.doc_no }}</p>
                <p style="margin: 0;">เลขที่ @{{ support.doc_date | thdate }}</p>
            </td>
            <td>
                <ul style="margin: 0 5px; padding: 0 10px;">
                    <li ng-repeat="(index, detail) in support.details">
                        <div ng-show="detail.plan.plan_item.calc_method == 1">
                            <span>@{{ detail.plan.plan_no }}</span>
                            <span>@{{ detail.plan.plan_item.item.item_name }} จำนวน </span>
                            <span>@{{ detail.amount | currency:'':0 }}</span>
                            <span>@{{ detail.unit.name }}</span>
                        </div>
                        <div ng-show="detail.plan.plan_item.calc_method == 2">
                            <span>@{{ detail.plan.plan_no }}</span>
                            <span>@{{ detail.plan.plan_item.item.item_name }}</span>
                            <p style="color: red; font-size: 12px;">
                                - @{{ detail.desc }} จำนวน
                                <span>@{{ detail.amount | currency:'':0 }}</span>
                                <span>@{{ detail.unit.name }}</span>
                            </p>
                        </div>
                    </li>
                </ul>
            </td>
            <td style="text-align: center;">
                @{{ support.total | currency:'':0 }}
            </td>
            <td style="text-align: center;">
                <p style="margin: 0;">@{{ support.depart.depart_name }}</p>
                <p style="margin: 0;">@{{ support.division.ward_name }}</p>
            </td>
            <td style="text-align: center;">
                <span class="label label-primary" ng-show="support.status == 0">
                    รอดำเนินการ
                </span>
                <span class="label label-info" ng-show="support.status == 1">
                    ส่งเอกสารแล้ว
                </span>
                <span class="label bg-navy" ng-show="support.status == 2">
                    รับเอกสารแล้ว
                </span>
                <span class="label label-default" ng-show="support.status == 9">
                    ยกเลิก
                </span>
                <p style="margin: 0; font-size: 12px;" ng-show="support.status == 2">
                    (<i class="fa fa-clock-o" aria-hidden="true"></i> @{{ support.received_date | thdate }})
                </p>
            </td>
            <td style="text-align: center;">
                <div style="display: flex; justify-content: center; gap: 2px;">
                    <form
                        id="frmDelete"
                        method="POST"
                        action="{{ url('/assets/delete') }}"
                        ng-show="support.status == 2"
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
        หน้า @{{ supports_pager.current_page }} จาก @{{ supports_pager.last_page }}
    </div>
    <div class="col-md-4" style="text-align: center;">
        จำนวน @{{ supports_pager.total }} รายการ
    </div>
    <div class="col-md-4">
        <ul class="pagination pagination-sm no-margin pull-right" ng-show="supports_pager.last_page > 1">
            <li ng-if="supports_pager.current_page !== 1">
                <a href="#" ng-click="getSupportWithUrl($event, supports_pager.path+ '?page=1', 2, setSupports)" aria-label="Previous">
                    <span aria-hidden="true">First</span>
                </a>
            </li>
        
            <li ng-class="{'disabled': (supports_pager.current_page==1)}">
                <a href="#" ng-click="getSupportWithUrl($event, supports_pager.prev_page_url, 2, setSupports)" aria-label="Prev">
                    <span aria-hidden="true">Prev</span>
                </a>
            </li>

            <!-- <li ng-repeat="i in debtPages" ng-class="{'active': supports_pager.current_page==i}">
                <a href="#" ng-click="getSupportWithUrl(supports_pager.path + '?page=' +i)">
                    @{{ i }}
                </a>
            </li> -->

            <!-- <li ng-if="supports_pager.current_page < supports_pager.last_page && (supports_pager.last_page - supports_pager.current_page) > 10">
                <a href="#" ng-click="supports_pager.path">
                    ...
                </a>
            </li> -->

            <li ng-class="{'disabled': (supports_pager.current_page==supports_pager.last_page)}">
                <a href="#" ng-click="getSupportWithUrl($event, supports_pager.next_page_url, 2, setSupports)" aria-label="Next">
                    <span aria-hidden="true">Next</span>
                </a>
            </li>

            <li ng-if="supports_pager.current_page !== supports_pager.last_page">
                <a href="#" ng-click="getSupportWithUrl($event, supports_pager.path+ '?page=' +supports_pager.last_page, 2, setSupports)" aria-label="Previous">
                    <span aria-hidden="true">Last</span>
                </a>
            </li>
        </ul>
    </div>
</div><!-- /.row -->