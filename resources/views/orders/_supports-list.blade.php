<!-- // TODO: Filtering controls -->
<!-- <div class="box" style="margin-top: 10px;">
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
    </div>
</div> -->
<!-- // TODO: Filtering controls -->

<table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
    <thead>
        <tr>
            <th style="width: 3%; text-align: center;">#</th>
            <!-- <th style="width: 8%; text-align: center;">ปีงบ</th> -->
            <th style="width: 15%; text-align: center;">เอกสาร</th>
            <th style="width: 8%; text-align: center;">ประเภทพัสดุ</th>
            <th>รายการ</th>
            <th style="width: 8%; text-align: center;">ยอดเงิน</th>
            <th style="width: 20%; text-align: center;">หน่วยงาน</th>
            <th style="width: 10%; text-align: center;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr ng-show="supportsToReceives.length === 0">
            <td colspan="8" style="text-align: center; color: red;">-- ไม่มีรายการ --</td>
        </tr>
        <tr ng-repeat="(index, support) in supportsToReceives" ng-show="supportsToReceives.length > 0">
            <td style="text-align: center;">@{{ index+supportsToReceives_pager.from }}</td>
            <!-- <td style="text-align: center;">@{{ plan.year }}</td> -->
            <td>
                <p style="margin: 0;">เลขที่ @{{ support.doc_no }}</p>
                <p style="margin: 0;">เลขที่ @{{ support.doc_date | thdate }}</p>
                <p style="margin: 0; font-size: 12px;">
                    (<i class="fa fa-clock-o" aria-hidden="true"></i> ส่งเมื่อ @{{ support.sent_date | thdate }})
                </p>
            </td>
            <td style="text-align: center;">@{{ support.plan_type.plan_type_name }}</td>
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
                <a  href="#"
                    ng-click="onReceiveSupport($event, support)"
                    class="btn btn-primary btn-xs"
                    title="รับเอกสาร">
                    รับเอกสาร
                </a>
            </td>             
        </tr>
    </tbody>
</table>

@include('orders._receive-form')

<div class="row" ng-show="supportsToReceives_pager.last_page > 1">
    <div class="col-md-4">
        หน้า @{{ supportsToReceives_pager.current_page }} จาก @{{ supportsToReceives_pager.last_page }}
    </div>
    <div class="col-md-4" style="text-align: center;">
        จำนวน @{{ supportsToReceives_pager.total }} รายการ
    </div>
    <div class="col-md-4">
        <ul class="pagination pagination-sm no-margin pull-right">
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
</div>
