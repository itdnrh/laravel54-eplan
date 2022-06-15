<table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
    <thead>
        <tr>
            <th style="width: 4%; text-align: center;">ลำดับ</th>
            <th style="width: 15%; text-align: center;">วันที่เบิกจ่าย</th>
            <th style="text-align: center;">ยอดเงิน</th>
            <th style="width: 10%; text-align: center;">AAR</th>
            <th style="width: 20%; text-align: center;">จนท.การเงิน</th>
            <th style="width: 10%; text-align: center;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="(index, payment) in payments">
            <td style="text-align: center;">@{{ index+1 }}</td>
            <td style="text-align: center;">@{{ payment.pay_date | thdate}}</td>
            <td style="text-align: center;">@{{ payment.net_total | currency:'':2 }}</td>
            <td style="text-align: center;">
                <i class="fa fa-check-square-o text-success" aria-hidden="true" ng-show="payment.have_aar == '1'"></i>
                <i class="fa fa-times text-danger" aria-hidden="true" ng-show="!payment.have_aar"></i>
            </td>
            <td style="text-align: center;">
                @{{ payment.created_user }}
            </td>
            <td style="text-align: center;">
                <div style="display: flex; justify-content: center; gap: 2px;">
                    <a  ng-click="edit(payment.id)"
                        class="btn btn-warning btn-xs"
                        title="แก้ไขรายการ">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form
                        id="frmDelete"
                        method="POST"
                        action="{{ url('/projects/delete') }}"
                    >
                        {{ csrf_field() }}
                        <button
                            type="submit"
                            ng-click="delete($event, payment.id)"
                            class="btn btn-danger btn-xs"
                        >
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>           
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">รวมทั้งสิ้น</td>
            <td style="text-align: center;">@{{ totalPayment | currency:'':2 }}</td>
            <td colspan="3"></td>
        </tr>
    </tbody>
</table>