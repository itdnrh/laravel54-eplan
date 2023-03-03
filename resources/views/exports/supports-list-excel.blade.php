<div class="box">
    <div class="box-header">
        <div class="row">
            <div class="col-md-6">
                <h3 class="box-title">บันทึกขอสนับสนุน</h3>
            </div>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body">
        <table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
            <thead>
                <tr>
                    <th style="width: 4%; text-align: center;">#</th>
                    <th style="width: 14%; text-align: center;">บันทึกเลขที่</th>
                    <th style="width: 14%; text-align: center;">บันทึกวันที่</th>
                    <th style="width: 8%; text-align: center;">ประเภทแผน</th>
                    <th style="width: 5%; text-align: center;">ปีงบ</th>
                    <th style="width: 18%;">หน่วยงาน</th>
                    <th style="text-align: center;">รายการ</th>
                    <th style="width: 8%; text-align: center;">ยอดขอสนับสนุน</th>
                    <th style="width: 10%; text-align: center;">สถานะ</th>
                    <th style="width: 10%; text-align: center;">วันที่ส่/รับ/ตีกลับเอกสาร</th>
                </tr>
            </thead>
            <tbody>
                <?php $row = 0; ?>
                @foreach($data as $support)
                    <tr>
                        <td style="text-align: center;">{{ ++$row }}</td>
                        <td>{{ $support->doc_no }}</td>
                        <td>{{ $support->doc_date }}</td>
                        <td style="text-align: center;">
                            {{ $support->planType->plan_type_name }}
                        </td>
                        <td style="text-align: center;">{{ $support->year }}</td>
                        <td>
                            {{ $support->depart->depart_name }}
                            @if($support->division)
                                <br>{{ $support->division->ward_name }}
                            @endif
                        </td>
                        <td>
                            @if($support->is_plan_group == '1')
                            <!-- ============================ Plan group ============================ -->
                                @if(count($support->details) > 0)
                                    {{ $support->plan_group_desc }}
                                    จำนวน {{ number_format($support->plan_group_amt) }} {{ $support.details[0].unit.name }}
                                @endif
                            <!-- ============================ End Plan group ============================ -->
                            @else
                                @if(count($support->details) > 0)
                                    <?php $numRow = 0; ?>
                                    @foreach($support->details as $detail)
                                        {{ $detail->plan->plan_no }}-{{ $detail->plan->planItem->item->item_name }}
                                        จำนวน {{ number_format($detail->amount)  }} {{ $detail->unit->name }}
                                        ({{ $detail->plan->in_plan == "I" ? "ในแผน" : "นอกแผน" }})
                                        {{ ++$numRow < count($support->details) ? ", " : "" }}
                                    @endforeach
                                @endif
                            @endif
                        </td>
                        <td style="text-align: center;">
                            {{ number_format($support->total, 2) }}
                        </td>
                        <td style="text-align: center;">
                            @if($support->status == 0)
                                <?php echo "รอดำเนินการ"; ?>
                            @elseif($support->status == 1)
                                <?php echo "ส่งเอกสารแล้ว"; ?>
                            @elseif($support->status == 2)
                                <?php echo "รับเอกสารแล้ว"; ?>
                            @elseif($support->status == 3)
                                <?php echo "ออกใบสั่งซื้อแล้ว"; ?>
                            @elseif($support->status == 4)
                                <?php echo "ตรวจรับแล้ว"; ?>
                            @elseif($support->status == 5)
                                <?php echo "ส่งเบิกเงินแล้ว"; ?>
                            @elseif($support->status == 9)
                                <?php echo "เอกสารถูกตีกลับ"; ?>
                            @elseif($support->status == 99)
                                <?php echo "ยกเลิก"; ?>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($support->status == 1)
                                {{ $support->sent_date }}
                            @elseif($support->status == 2)
                                {{ $support->received_date }}
                            @elseif($support->status == 9)
                                {{ $support->returned_date }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->