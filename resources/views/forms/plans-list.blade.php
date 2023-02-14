<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>รายการแผนคำขอประจำปี</title>
        <link rel="stylesheet" href="{{ asset('/css/pdf.css') }}">
    </head>
    <body>
        <?php $row = 0; ?>
        <?php $total = 0; ?>
        <div class="list-container">
            <div class="header" style="margin-top: 20px;">
                <h2 style="margin: 0">รายการแผนคำขอประจำปี</h2>
                <h3 style="margin: 0">
                    ประเภท {{ $planType->plan_type_name.$inStock }}
                    จำนวน {{ count($plans) }} รายการ
                </h3>
            </div>

            <div class="table-container">
                <table style="width: 100%;" class="table" border="1">
                    <tr style="font-size: 16px;">
                        <th style="width: 4%; text-align: center;">ลำดับ</th>
                        <th style="width: 10%; text-align: center;">ประเภท</th>
                        <th style="text-align: center;">รายการ</th>
                        <th style="width: 15%; text-align: center;">หน่วยงาน</th>
                        <th style="width: 5%; text-align: center;">จำนวน</th>
                        <th style="width: 8%; text-align: center;">ราคาต่อหน่วย</th>
                        <th style="width: 10%; text-align: center;">ราคารวม</th>
                    </tr>

                    @foreach($plans as $plan)
                        <tr>
                            <td style="text-align: center;">{{ ++$row }}</td>
                            <td style="text-align: center;">
                                <p>{{ $plan->planItem->item->category->name }}</p>
                            </td>
                            <td>
                                <div style="padding: 0 5px;">
                                    {{ $plan->plan_no }}-{{ $plan->planItem->item->item_name }}
                                </div>
                            </td>
                            <td>
                                <div style="padding: 0 5px;">
                                    {{ $plan->depart->depart_name }}

                                    @if($plan->division)
                                        <span>/{{ $plan->division->ward_name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align: center;">
                                {{ number_format($plan->planItem->amount) }}
                            </td>
                            <td style="text-align: right;">
                                <p>{{ number_format($plan->planItem->price_per_unit, 2) }}</p>
                            </td>
                            <td style="text-align: right;">
                                <p>{{ number_format($plan->planItem->sum_price, 2) }}</p>
                            </td>
                        </tr>

                        <?php $total += $plan->planItem->sum_price; ?>
                    @endforeach

                    <tr>
                        <td style="text-align: center; font-weight: bold;" colspan="6">
                            รวมเป็นเงินทั้งสิ้น
                        </td>
                        <td style="text-align: right;">
                            <p>{{ number_format($total, 2) }}</p>
                        </td>
                    </tr>
                </table>
            </div>

            <p class="print-options">พิมพ์จากระบบ E-Plan เมื่อ {{ date('Y-m-d H:i:s') }}</p>
        </div>
    </body>
</html>