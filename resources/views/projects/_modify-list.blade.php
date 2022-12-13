<table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
    <thead>
        <tr>
            <th style="width: 4%; text-align: center;">ลำดับ</th>
            <th style="width: 15%; text-align: center;">วันที่ขอเปลี่ยนแปลง</th>
            <th>รายละเอียด</th>
            <th style="width: 8%; text-align: center;">ไฟล์แนบ</th>
            <th style="width: 20%; text-align: center;">ผู้ขอ</th>
            <th style="width: 10%; text-align: center;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="(index, payment) in payments">
            <td style="text-align: center;">@{{ index+1 }}</td>
            <td style="text-align: center;">@{{ payment.pay_date | thdate}}</td>
            <td>@{{ payment.net_total }}</td>
            <td style="text-align: center;">
                <i class="fa  fa-file-pdf-o text-success" aria-hidden="true"></i>
            </td>
            <td style="text-align: center;">
                @{{ payment.creator.prefix.prefix_name+payment.creator.person_firstname+ ' ' +payment.creator.person_lastname }}
            </td>
            <td style="text-align: center;">
                <div style="display: flex; justify-content: center; gap: 2px;">
                    <a  href="#"
                        ng-click="showModificationForm($event, project.id, payment)"
                        class="btn btn-warning btn-xs"
                        title="แก้ไขรายการ">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a  href="#"
                        ng-click="deleteModification($event, project.id, payment.id)"
                        class="btn btn-danger btn-xs"
                        title="ลบรายการ">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </td>           
        </tr>
    </tbody>
</table>