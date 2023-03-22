<div class="box">
    <div class="box-header">
        <h3 style="margin: 0;">รายการแผน{{ $options['plan_type_name'] }}</h3>
        <h4 style="margin: 0;">ประจำปีงบประมาณ {{ $options['year'] }}</h4>
    </div><!-- /.box-header -->
    <div class="box-body">
        <table class="table table-bordered table-striped" style="font-size: 12px;">
            <thead>
                <tr>
                    <th style="width: 3%; text-align: center;">#</th>
                    <th style="width: 8%; text-align: center;">เลขที่แผน</th>
                    <!-- <th style="width: 8%; text-align: center;">ปีงบ</th> -->
                    <th>รายการ</th>
                    <th style="width: 8%; text-align: center;">ประเภทครุภัณฑ์</th>
                    <th style="width: 8%; text-align: center;">ราคาต่อหน่วย</th>
                    <th style="width: 8%; text-align: center;">จำนวนที่ขอ</th>
                    <th style="width: 8%; text-align: center;">หน่วยนับ</th>
                    <th style="width: 8%; text-align: center;">ยอดงบที่ขอ</th>
                    <th style="width: 8%; text-align: center;">ยอดงบคงเหลือ</th>
                    <th style="width: 4%; text-align: center;">เดือนที่ขอ</th>
                    <th style="width: 4%; text-align: center;">ไตรมาส</th>
                    <th style="width: 4%; text-align: center;">ในแผน</th>
                    @if ($options['plan_type_id'] == '1')
                        <th style="width: 4%; text-align: center;">สาเหตุที่ขอ</th>
                    @endif
                    <th style="width: 10%; text-align: center;">เหตุผลความจำเป็น</th>
                    <th style="width: 10%; text-align: center;">กลุ่มภารกิจ</th>
                    <th style="width: 10%; text-align: center;">กลุ่มงาน</th>
                    <th style="width: 10%; text-align: center;">งาน</th>
                    <!-- <th style="width: 5%; text-align: center;">อนุมัติ</th> -->
                    <th style="width: 10%; text-align: center;">สถานะ</th>
                </tr>
            </thead>
            <tbody>

                <?php $cx = 0; ?>
                @foreach($data as $plan)

                    <tr>
                        <td style="text-align: center;">{{ ++$cx }}</td>
                        <td style="text-align: center;">{{ $plan->plan_no }}</td>
                        <!-- <td style="text-align: center;">{{ $plan->year }}</td> -->
                        <td>{{ $plan->planItem->item ? $plan->planItem->item->item_name : '' }}</td>
                        <td>{{ $plan->planItem->item ? $plan->planItem->item->category->name : '' }}</td>
                        <td style="text-align: center;">
                            {{ number_format($plan->planItem->price_per_unit) }}
                        </td>
                        <td style="text-align: center;">
                            {{ number_format($plan->planItem->amount) }}
                        </td>
                        <td style="text-align: center;">
                            {{ $plan->planItem->unit->name }}
                        </td>
                        <td style="text-align: center;">
                            {{ number_format($plan->planItem->sum_price, 2) }}
                        </td>
                        <td style="text-align: center;">
                            {{ number_format($plan->planItem->remain_budget, 2) }}
                        </td>
                        <td style="text-align: center;">
                            {{ getShortMonth($plan->start_month) }}
                        </td>
                        <td style="text-align: center;">
                            @if(in_array($plan->start_month, ['10','11','12']))
                                {{ 'Q1' }}
                            @elseif(in_array($plan->start_month, ['01','02','03']))
                                {{ 'Q2' }}
                            @elseif(in_array($plan->start_month, ['04','05','06']))
                                {{ 'Q3' }}
                            @elseif(in_array($plan->start_month, ['07','08','09']))
                                {{ 'Q4' }}
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($plan->in_plan == 'I')
                                {{ 'ในแผน' }}
                            @else
                                {{ 'นอกแผน' }}
                            @endif
                        </td>
                        @if ($options['plan_type_id'] == '1')
                            <td style="text-align: center;">
                                @if($plan->planItem->request_cause == 'N')
                                    {{ 'ขอใหม่' }}
                                @elseif($plan->planItem->request_cause == 'R')
                                    {{ 'ทดแทน' }}
                                @elseif($plan->planItem->request_cause == 'E')
                                    {{ 'ขยายงาน' }}
                                @endif
                            </td>
                        @endif
                        <td style="text-align: center;">
                            {{ $plan->reason }}
                        </td>
                        <td style="text-align: center;">
                            @if($plan->depart->faction_id == '1')
                                {{ 'อำนวยการ' }}
                            @elseif($plan->depart->faction_id == '2')
                                {{ 'ทุติยภูมิ/ตติยภูมิ' }}
                            @elseif($plan->depart->faction_id == '3')
                                {{ 'ปฐมภูมิ' }}
                            @elseif($plan->depart->faction_id == '7')
                                {{ 'พรส' }}
                            @elseif($plan->depart->faction_id == '5')
                                {{ 'พยาบาล' }}
                            @elseif($plan->depart->faction_id == '13')
                                {{ 'ยุทธศาสตร์' }}
                            @endif
                        </td>
                        <td style="text-align: center;">
                            {{ $plan->depart->depart_name }}
                        </td>
                        <td style="text-align: center;">
                            @if($plan->division)
                                {{ $plan->division->ward_name }}
                            @endif
                        </td>
                        <!-- <td style="text-align: center;">
                            {{ $plan->approved }}
                        </td> -->
                        <td style="text-align: center;">
                            @if($plan->status == '0')
                                {{ 'รอดำเนินการ' }}
                            @elseif($plan->status == '1')
                                {{ 'ดำเนินการแล้วบางส่วน' }}
                            @elseif($plan->status == '2')
                                {{ 'ดำเนินการครบแล้ว' }}
                            @elseif($plan->status == '9')
                                {{ 'อยู่ระหว่างการจัดซื้อ' }}
                            @elseif($plan->status == '99')
                                {{ 'ยกเลิก' }}
                            @endif
                        </td>
                    </tr>

                @endforeach

            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
