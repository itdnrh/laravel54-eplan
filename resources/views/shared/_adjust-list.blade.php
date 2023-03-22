<div class="nav-tabs-custom" style="margin-bottom: 0; background-color: #EFEFEF;">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#settings" data-toggle="tab">
                <i class="fa fa-sliders"></i>
                ข้อมูลการปรับแผน (ุ6 เดือนหลัง)
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="active tab-pane" id="settings">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 50%;">รายละเอียดก่อนปรับ</th>
                        <th style="width: 50%;">รายละเอียดการปรับ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="(index, adjust) in plan.adjustments">
                        <td style="color: gray;">
                            <p ng-show="adjust.in_plan == 'I'" class="label label-success">
                                ในแผน 
                            </p>
                            <p ng-show="adjust.in_plan == 'O'" class="label label-danger">
                                นอกแผน
                            </p>
                            <p style="margin: 0;">ราคาต่อหน่วย: @{{ adjust.old_price_per_unit | currency:'':2 }} บาท</p>
                            <p style="margin: 0;">จำนวนที่ขอ: @{{ adjust.old_amount }} @{{ adjust.unit.name }}</p>
                            <p style="margin: 0;">รวมเป็นเงิน: @{{ adjust.old_sum_price | currency:'':2 }} บาท</p>
                        </td>
                        <td style="color: gray;">
                            <p ng-show="plan.in_plan == 'I'" class="label label-success">
                                ในแผน 
                            </p>
                            <p ng-show="plan.in_plan == 'O'" class="label label-danger">
                                นอกแผน
                            </p>
                            <p style="margin: 0;">ราคาต่อหน่วย: @{{ plan.price_per_unit | currency:'':2 }} บาท</p>
                            <p style="margin: 0;">จำนวนที่ขอ: @{{ plan.amount }} @{{ plan.unit.name }}</p>
                            <p style="margin: 0;">รวมเป็นเงิน: @{{ plan.sum_price | currency:'':2 }} บาท</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
