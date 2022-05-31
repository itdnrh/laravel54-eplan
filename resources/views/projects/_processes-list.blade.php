<table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
    <thead>
        <tr>
            <th style="width: 20%; text-align: center;">ส่งงานแผน</th>
            <th style="width: 20%; text-align: center;">ส่งการเงิน</th>
            <th style="width: 20%; text-align: center;">ผอ.อนุมัติ</th>
            <th style="width: 20%; text-align: center;">วันที่ดำเนินโครงการ</th>
            <th style="width: 20%; text-align: center;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: center;">99/99/9999</td>
            <td style="text-align: center;">99/99/9999</td>
            <td style="text-align: center;">99/99/9999</td>
            <td style="text-align: center;">99/99/9999</td>
            <td style="text-align: center;">
                <i class="fa fa-check-square-o text-success" aria-hidden="true" ng-show="project.approved == 'A'"></i>
                <i class="fa fa-times text-danger" aria-hidden="true" ng-show="!project.approved"></i>
            </td>           
        </tr>
    </tbody>
</table>