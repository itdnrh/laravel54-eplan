<table class="table table-bordered table-striped" style="font-size: 14px; margin: 10px auto;">
    <thead>
        <tr>
            <th style="width: 5%; text-align: center;">#</th>
            <th>รายละเอียดการลา</th>
            <th style="width: 35%;">ขอยกเลิกวันลา</th>
            <th style="width: 10%; text-align: center;">ปีงบประมาณ</th>
            <th style="width: 10%; text-align: center;">วันที่ลงทะเบียน</th>
            <th style="width: 6%; text-align: center;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="(index, cancel) in cancellations">
            <td style="text-align: center;">@{{ index+cancelPager.from }}</td>
            <td>
                <h4 style="margin: 2px auto;">
                    ขอยกเลิกวัน@{{ cancel.type.name }}
                    @{{ cancel.person.prefix.prefix_name + cancel.person.person_firstname + ' ' + cancel.person.person_lastname }}
                </h4>
                <p style="color: grey; margin: 0px auto;">
                    ระหว่างวันที่ <span>@{{ cancel.start_date | thdate }} - </span>
                    ถึงวันที่ <span>@{{ cancel.end_date | thdate }}</span>
                    จำนวน <span>@{{ cancel.leave_days }}</span> วัน
                    <span ng-show="cancel.end_period != 1">
                        <span ng-show="cancel.end_period == 2">ช่วงเช้า (08.00-12.00น.)</span>
                        <span ng-show="cancel.end_period == 3">ช่วงบ่าย (13.00-16.00น.)</span>
                    </span>
                    <a  href="{{ url('/'). '/uploads/' }}@{{ cancel.attachment }}"
                        title="ไฟล์แนบ"
                        target="_blank"
                        ng-show="cancel.attachment"
                        class="btn btn-default btn-xs"
                    >
                        <i class="fa fa-paperclip" aria-hidden="true"></i>
                    </a>
                </p>
            </td>
            <td>
                <span style="font-weight: bold;">วันที่</span>
                @{{ cancel.cancellation[0].start_date | thdate }} - @{{ cancel.cancellation[0].end_date | thdate }}
                <span style="font-weight: bold;">จำนวน</span> @{{ cancel.cancellation[0].days }} วัน
                <p style="color: grey; margin: 0px auto;">
                    <span style="font-weight: bold;">เนื่องจาก</span> @{{ cancel.cancellation[0].reason }}
                </p>
            </td>
            <td style="text-align: center;">@{{ cancel.year }}</td>
            <td style="text-align: center;">
                <p style="margin: 0px auto;">@{{ cancel.cancellation[0].cancel_date | thdate }}</p>
                <p style="margin: 0px auto;">
                    <span class="label label-warning" ng-show="cancel.status == 5">
                        อยู่ระหว่างการยกเลิก
                    </span>
                </p>
            </td>
            <td style="text-align: center;">
                <span ng-show="cancel.cancellation[0].commented_date != null" style="color: green; font-size: 12px;">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    ลงความเห็นแล้ว
                </span>
                <a  ng-click="showCommentForm(cancel, 2)" 
                    ng-show="(cancel.status!==4 || cancel.status!==3) && cancel.cancellation[0].commented_date == null"
                    class="btn btn-danger btn-sm"
                    title="ลงความเห็นยกเลิกการลา">
                    <i class="fa fa-check" aria-hidden="true"></i>
                    ลงความเห็น
                </a>
            </td>             
        </tr>
    </tbody>
</table>

<div class="row">
    <div class="col-md-4">
        <span style="margin-top: 5px;">
            หน้า @{{ cancelPager.current_page }} จาก @{{ cancelPager.last_page }}
        </span>
    </div>
    <div class="col-md-4" style="text-align: center;">
        จำนวน @{{ cancelPager.total }} รายการ
    </div>
    <div class="col-md-4">
        <ul class="pagination pagination-sm no-margin pull-right" ng-show="cancelPager.last_page > 1">
            <li ng-if="cancelPager.current_page !== 1">
                <a href="#" ng-click="getDataWithURL($event, cancelPager.path+ '?page=1', setLeaves)" aria-label="Previous">
                    <span aria-hidden="true">First</span>
                </a>
            </li>

            <li ng-class="{'disabled': (cancelPager.current_page==1)}">
                <a href="#" ng-click="getDataWithURL($event, cancelPager.prev_page_url, setLeaves)" aria-label="Prev">
                    <span aria-hidden="true">Prev</span>
                </a>
            </li>

            <!-- <li ng-repeat="i in debtPages" ng-class="{'active': cancelPager.current_page==i}">
                <a href="#" ng-click="getDataWithURL($event, cancelPager.path + '?page=' +i, setLeaves)">
                    @{{ i }}
                </a>
            </li> -->

            <!-- <li ng-if="cancelPager.current_page < cancelPager.last_page && (cancelPager.last_page - cancelPager.current_page) > 10">
                <a href="#" ng-click="cancelPager.path">
                    ...
                </a>
            </li> -->

            <li ng-class="{'disabled': (cancelPager.current_page==cancelPager.last_page)}">
                <a href="#" ng-click="getDataWithURL($event, cancelPager.next_page_url, setLeaves)" aria-label="Next">
                    <span aria-hidden="true">Next</span>
                </a>
            </li>

            <li ng-if="cancelPager.current_page !== cancelPager.last_page">
                <a href="#" ng-click="getDataWithURL($event, cancelPager.path+ '?page=' +cancelPager.last_page, setLeaves)" aria-label="Previous">
                    <span aria-hidden="true">Last</span>
                </a>
            </li>
        </ul>
    </div>
</div>