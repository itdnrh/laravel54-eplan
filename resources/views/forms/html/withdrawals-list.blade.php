<table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
    <thead>
        <tr>
            <th style="width: 4%; text-align: center;">#</th>
            <th style="width: 15%;">หนังสือส่งเบิก</th>
            <th style="width: 5%; text-align: center;">งวดที่</th>
            <th style="width: 15%; text-align: center;">เอกสารส่งมอบงาน</th>
            <th>รายละเอียดใบสั่งซื้อ</th>
            <th style="width: 10%; text-align: center;">ยอดเงิน</th>
            <th style="width: 15%; text-align: center;">สำรองเงินจ่ายโดย</th>
            <th style="width: 8%; text-align: center;">สถานะ</th>
            <th style="width: 10%; text-align: center;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="(index, withdraw) in withdrawals">
            <td style="text-align: center;">@{{ index+pager.from }}</td>
            <td>
                <p style="margin: 0px;">เลขที่: @{{ withdraw.withdraw_no }}</p>
                <p style="margin: 0px;">วันที่: @{{ withdraw.withdraw_date | thdate }}</p>
            </td>
            <td style="text-align: center;">@{{ withdraw.inspection.deliver_seq }}</td>
            <td style="text-align: center;">@{{ withdraw.inspection.deliver_no }}</td>
            <td>
                <h5 style="margin: 0; font-size: 14px;">
                    เลขที่ @{{ withdraw.inspection.order.po_no }}
                    วันที่ @{{ withdraw.inspection.order.po_date | thdate }} 
                </h5>
                <p style="margin: 0;">
                    @{{ withdraw.supplier.supplier_name }}
                </p>
                <!-- <div class="bg-gray disabled" style="padding: 2px 5px; border-radius: 5px;">
                    <p style="margin: 0; text-decoration: underline;">รายการ</p>
                    <ul style="list-style: none; margin: 0px; padding: 0px;">
                        <li ng-repeat="(index, detail) in withdraw.order.details" style="margin: 2px;">
                            @{{ index+1 }}. @{{ detail.item.item_name }}
                        </li>
                    </ul>
                </div> -->
            </td>
            <td style="text-align: right;">
                @{{ withdraw.net_total | currency:'':2 }}
            </td>
            <td style="text-align: center;">
                @{{ withdraw.prepaid.prefix.prefix_name+withdraw.prepaid.person_firstname+ ' ' +withdraw.prepaid.person_lastname }}
            </td>
            <td style="text-align: center;">
                <span class="label label-success" ng-show="withdraw.completed">ส่งเบิกเงินแล้ว</span>
                <span class="label label-danger" ng-show="!withdraw.completed">ยังไม่ได้ส่ง</span>
            </td>
            <td style="text-align: center;">
                <div style="display: flex; justify-content: center; gap: 2px;">
                    <a  href="{{ url('/withdrawals/detail') }}/@{{ withdraw.id }}"
                        class="btn btn-primary btn-xs" 
                        title="รายละเอียด">
                        <i class="fa fa-search"></i>
                    </a>
                    <a  href="{{ url('/withdrawals/edit') }}/@{{ withdraw.id }}"
                        class="btn btn-warning btn-xs"
                        title="แก้ไขรายการ">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form
                        id="frmDelete"
                        method="POST"
                        action="{{ url('/withdrawals/delete') }}"
                    >
                        {{ csrf_field() }}
                        <button
                            type="submit"
                            ng-click="delete($event, withdraw.id)"
                            class="btn btn-danger btn-xs"
                        >
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>             
        </tr>
    </tbody>
</table>