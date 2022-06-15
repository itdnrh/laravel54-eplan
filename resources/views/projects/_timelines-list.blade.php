<table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
    <thead>
        <tr>
            <th style="width: 25%; text-align: center;">ส่งงานแผน</th>
            <th style="width: 25%; text-align: center;">ส่งการเงิน</th>
            <th style="width: 25%; text-align: center;">ผอ.อนุมัติ</th>
            <th style="width: 25%; text-align: center;">วันที่ดำเนินโครงการ</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: center;">
                <a
                    href="#"
                    class="btn btn-primary"
                    ng-show="!timeline.sent_stg_date"
                    ng-click="updateTimeline(timeline.id, project.id, 'sent_stg_date')"
                >
                    บันทึกส่งงานแผน
                </a>
                <span ng-show="timeline.sent_stg_date">@{{ timeline.sent_stg_date | thdate }}</span>
            </td>
            <td style="text-align: center;">
                <a
                    href="#"
                    class="btn btn-primary"
                    ng-show="!timeline.sent_fin_date"
                    ng-click="updateTimeline(timeline.id, project.id, 'sent_fin_date')"
                >
                    บันทึกส่งการเงิน
                </a>
                <span ng-show="timeline.sent_fin_date">@{{ timeline.sent_fin_date | thdate }}</span>
            </td>
            <td style="text-align: center;">
                <a
                    href="#"
                    class="btn btn-primary"
                    ng-show="!timeline.approved_date"
                    ng-click="updateTimeline(timeline.id, project.id, 'approved_date')"
                >
                    บันทึก ผอ.อนุมัติ
                </a>
                <span ng-show="timeline.approved_date">@{{ timeline.approved_date | thdate }}</span>
            </td>
            <td style="text-align: center;">
                <a
                    href="#"
                    class="btn btn-primary"
                    ng-show="!timeline.start_date"
                    ng-click="updateTimeline(timeline.id, project.id, 'start_date')"
                >
                    บันทึกดำเนินโครงการ
                </a>
                <span ng-show="timeline.start_date">@{{ timeline.start_date | thdate }}</span>
            </td>       
        </tr>
    </tbody>
</table>