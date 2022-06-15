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
        <tr>
            <td style="text-align: center;">1</td>
            <td style="text-align: center;">99/99/9999</td>
            <td style="text-align: center;">9,999.00</td>
            <td style="text-align: center;">
                <i class="fa fa-check-square-o text-success" aria-hidden="true" ng-show="plan.approved == 'A'"></i>
                <i class="fa fa-times text-danger" aria-hidden="true" ng-show="!plan.approved"></i>
            </td>
            <td style="text-align: center;">99/99/9999</td>
            <td style="text-align: center;">
                <div style="display: flex; justify-content: center; gap: 2px;">
                    <a  ng-click="edit(project.id)"
                        ng-show="project.status == 0 || (project.status == 1 && {{ Auth::user()->person_id }} == '1300200009261')"
                        class="btn btn-warning btn-xs"
                        title="แก้ไขรายการ">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form
                        id="frmDelete"
                        method="POST"
                        action="{{ url('/projects/delete') }}"
                        ng-show="project.status == 0 || (project.status == 1 && {{ Auth::user()->person_id }} == '1300200009261')"
                    >
                        {{ csrf_field() }}
                        <button
                            type="submit"
                            ng-click="delete($event, project.id)"
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
            <td style="text-align: center;">9,999.00</td>
            <td colspan="3"></td>
        </tr>
    </tbody>
</table>