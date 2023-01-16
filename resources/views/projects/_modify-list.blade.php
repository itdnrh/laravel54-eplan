<table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
    <thead>
        <tr>
            <th style="width: 4%; text-align: center;">ลำดับ</th>
            <th style="width: 10%; text-align: center;">วันที่ขอ</th>
            <th style="width: 18%; text-align: center;">ประเภท</th>
            <th>รายละเอียด</th>
            <th style="width: 8%; text-align: center;">ไฟล์แนบ</th>
            <th style="width: 6%; text-align: center;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="(index, modification) in modifications">
            <td style="text-align: center;">@{{ index+1 }}</td>
            <td style="text-align: center;">@{{ modification.doc_date | thdate }}</td>
            <td style="text-align: center;">
                <span ng-show="modification.modify_type_id == 1">ปรับเปลี่ยนวันที่ดำเนินการ</span>
                <span ng-show="modification.modify_type_id == 2">ปรับเปลี่ยนไตรมาส</span>
                <span ng-show="modification.modify_type_id == 3">ปรับเปลี่ยนวิทยากร</span>
                <span ng-show="modification.modify_type_id == 4">ปรับเปลี่ยนกิจกรรมดำเนินการ</span>
                <span ng-show="modification.modify_type_id == 5">ปรับเปลี่ยนงบประมาณ</span>
            </td>
            <td>@{{ modification.desc }}</td>
            <td style="text-align: center;">
                <a
                    href="{{ url('/uploads/projects') }}/@{{ modification.attachment }}"
                    ng-show="modification.attachment"
                >
                    <i class="fa  fa-file-pdf-o text-success" aria-hidden="true"></i>
                </a>
            </td>
            <td style="text-align: center;">
                <div style="display: flex; justify-content: center; gap: 2px;">
                    <a  href="#"
                        ng-click="showModificationForm($event, project.id, modification)"
                        class="btn btn-warning btn-xs"
                        title="แก้ไขรายการ">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a  href="#"
                        ng-click="deleteModification($event, project.id, modification.id)"
                        class="btn btn-danger btn-xs"
                        title="ลบรายการ">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </td>           
        </tr>
    </tbody>
</table>