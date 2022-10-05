<!-- // TODO: Filtering controls -->
<form id="frmSearch" name="frmSearch" role="form">
    <div class="row">
        <div class="form-group col-md-6">
            <label>ปีงบประมาณ</label>
            <select
                id="cboYear"
                name="cboYear"
                ng-model="cboYear"
                class="form-control"
                ng-change="getSupports()"
            >
                <option value="">-- ทั้งหมด --</option>
                <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                    @{{ y }}
                </option>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label>ประเภทพัสดุ</label>
            <select
                style="margin-right: 1rem;"
                class="form-control"
                ng-model="cboPlanType"
                ng-change="getSupports();"
            >
                <option value="">-- เลือกประเภทพัสดุ --</option>
                @foreach($planTypes as $planType)
                    <option value="{{ $planType->id }}">
                        {{ $planType->plan_type_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>กลุ่มภารกิจ</label>
                <select
                    id="cboFaction"
                    name="cboFaction"
                    ng-model="cboFaction"
                    class="form-control"
                    ng-change="onFactionSelected(cboFaction)"
                >
                    <option value="">-- ทั้งหมด --</option>
                    @foreach($factions as $faction)
                        <option value="{{ $faction->faction_id }}">
                            {{ $faction->faction_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>กลุ่มงาน</label>
                <select
                    id="cboDepart"
                    name="cboDepart"
                    ng-model="cboDepart"
                    class="form-control"
                    ng-change="getSupports();"
                >
                    <option value="">-- ทั้งหมด --</option>
                    <option ng-repeat="dep in forms.departs" value="@{{ dep.depart_id }}">
                        @{{ dep.depart_name }}
                    </option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>เลขที่ใบขอสนับสนุน</label>
                <input
                    type="text"
                    id="txtSupportNo"
                    name="txtSupportNo"
                    ng-model="txtSupportNo"
                    class="form-control"
                    ng-keyup="getSupports();"
                />
            </div>
        </div>
    </div>
</form>
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
        <tr ng-show="supports.length === 0">
            <td colspan="8" style="text-align: center; color: red;">-- ไม่มีรายการ --</td>
        </tr>
        <tr ng-repeat="(index, support) in supports" ng-show="supports.length > 0">
            <td style="text-align: center;">@{{ index+supports_pager.from }}</td>
            <!-- <td style="text-align: center;">@{{ plan.year }}</td> -->
            <td>
                <p style="margin: 0;">เลขที่ @{{ support.doc_no }}</p>
                <p style="margin: 0;">เลขที่ @{{ support.doc_date | thdate }}</p>
                <p class="item__spec-text">
                    (<i class="fa fa-clock-o" aria-hidden="true"></i> ส่งเมื่อ @{{ support.sent_date | thdate }})
                </p>
            </td>
            <td style="text-align: center;">@{{ support.plan_type.plan_type_name }}</td>
            <td>
                <div ng-show="support.is_plan_group">
                    @{{ support.plan_group_desc }}
                    จำนวน <span>@{{ support.details[0].amount | currency:'':0 }}</span>
                    <span>@{{ support.details[0].unit.name }}</span>
                    <a href="#" class="text-danger" ng-show="support.details.length > 1" ng-click="showDetailsList($event, support.details);">
                        <i class="fa fa-tags" aria-hidden="true"></i>
                    </a>
                </div>
                <div ng-show="!support.is_plan_group">
                    <span>@{{ support.details[0].plan.plan_no }} - @{{ support.details[0].plan.plan_item.item.item_name }}</span>
                    <p style="margin: 0; font-size: 12px; color: red;">
                        (@{{ support.details[0].desc }}
                        จำนวน <span>@{{ support.details[0].amount | currency:'':0 }}</span>
                        <span>@{{ support.details[0].unit.name }}</span>
                        ราคา @{{ support.details[0].price_per_unit | currency:'':0 }} บาท) 
                        <a href="#" ng-show="support.details.length > 1" ng-click="showDetailsList($event, support.details);">
                            ... ดูเพิ่ม <i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
                        </a>
                    </p>
                </div>
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
                    ng-click="showReceiveSupportForm($event, support)"
                    class="btn btn-primary btn-xs"
                    title="รับเอกสาร">
                    รับเอกสาร
                </a>
                <a  href="#"
                    ng-click="showReturnSupportForm($event, support)"
                    class="btn btn-danger btn-xs"
                    title="ตีกลับเอกสาร">
                    ตีกลับ
                </a>
            </td>             
        </tr>
    </tbody>
</table>

<div class="row" ng-show="supports_pager.last_page > 1">
    <div class="col-md-4">
        หน้า @{{ supports_pager.current_page }} จาก @{{ supports_pager.last_page }}
    </div>
    <div class="col-md-4" style="text-align: center;">
        จำนวน @{{ supports_pager.total }} รายการ
    </div>
    <div class="col-md-4">
        <ul class="pagination pagination-sm no-margin pull-right">
            <li ng-if="supports_pager.current_page !== 1">
                <a ng-click="getSupportsWithUrl($event, supports_pager.path+ '?page=1', setSupports)" aria-label="Previous">
                    <span aria-hidden="true">First</span>
                </a>
            </li>

            <li ng-class="{'disabled': (supports_pager.current_page==1)}">
                <a ng-click="getSupportsWithUrl($event, supports_pager.prev_page_url, setSupports)" aria-label="Prev">
                    <span aria-hidden="true">Prev</span>
                </a>
            </li>

            <!-- <li ng-if="supports_pager.current_page < supports_pager.last_page && (supports_pager.last_page - supports_pager.current_page) > 10">
                <a href="@{{ supports_pager.url(supports_pager.current_page + 10) }}">
                    ...
                </a>
            </li> -->

            <li ng-class="{'disabled': (supports_pager.current_page==supports_pager.last_page)}">
                <a ng-click="getSupportsWithUrl($event, supports_pager.next_page_url, setSupports)" aria-label="Next">
                    <span aria-hidden="true">Next</span>
                </a>
            </li>

            <li ng-if="supports_pager.current_page !== supports_pager.last_page">
                <a ng-click="getSupportsWithUrl($event, supports_pager.path+ '?page=' +supports_pager.last_page, setSupports)" aria-label="Previous">
                    <span aria-hidden="true">Last</span>
                </a>
            </li>
        </ul>
    </div>
</div>
