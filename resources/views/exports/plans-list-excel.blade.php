<div class="box">
    <div class="box-header">
        <h3 style="margin: 0;">รายการแผน{{ $options['type'] }}</h3>
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
                    <th style="width: 8%; text-align: center;">ราคารวม</th>
                    <th style="width: 4%; text-align: center;">ในแผน</th>
                    <th style="width: 20%; text-align: center;">เหตุผลความจำเป็น</th>
                    <th style="width: 20%; text-align: center;">หน่วยงาน</th>
                    <th style="width: 5%; text-align: center;">อนุมัติ</th>
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
                            {{ number_format($plan->planItem->sum_price) }}
                        </td>
                        <td style="text-align: center;">
                            @if($plan->in_plan == 'I')
                                {{ 'ในแผน' }}
                            @else
                                {{ 'นอกแผน' }}
                            @endif
                        </td>
                        <td style="text-align: center;">
                            {{ $plan->reason }}
                        </td>
                        <td style="text-align: center;">
                            {{ $plan->depart->depart_name }}
                            @if($plan->division)
                                <p style="margin: 0;">/{{ $plan->division->ward_name }}</p>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            {{ $plan->approved }}
                        </td>
                        <td style="text-align: center;">
                            @if($plan->status == '0')
                                {{ 'รอดำเนินการ' }}
                            @elseif($plan->status == '1')
                                {{ 'ดำเนินการแล้วบางส่วน' }}
                            @elseif($plan->status == '2')
                                {{ 'ดำเนินการครบแล้ว' }}
                            @elseif($plan->status == '9')
                                {{ 'ยกเลิก' }}
                            @endif
                        </td>
                    </tr>

                @endforeach

            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
