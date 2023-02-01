<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>บันทึกขอสนับสนุน</title>
        <link rel="stylesheet" href="{{ asset('/css/pdf.css') }}">
    </head>
    <body>
        <div class="container">
            <div class="memo-header-narrow">
                <div class="logo-krut">
                    <img src="{{ asset('/img/krut.jpg') }}" alt="krut" />
                </div>
                <h2>บันทึกข้อความ</h2>
            </div>
            <div class="content">
                <?php $committeeHeight = 0; ?>
                <table style="width: 100%;">
                    <tr>
                        <td colspan="4">
                            <div class="content-header">
                                <span class="content__header-topic">ส่วนราชการ</span>
                                <div class="content__header-text" style="width: 87%;">
                                    <span style="margin: 0 5px;">
                                        @if($support->depart_id == 37)
                                            {{ 'กลุ่มงานการพยาบาลด้านการควบคุมและป้องกันการติดเชื้อฯ' }}
                                        @elseif(in_array($support->depart_id, [66,68]))
                                            {{ $support->depart->depart_name }}
                                        @else
                                            {{ $support->depart->depart_name }}
                                        @endif
                                    </span>
                                    <span style="margin: 0 1px;">โรงพยาบาลเทพรัตน์นครราชสีมา</span>
                                    <span style="margin: 0 1px;">
                                        @if(in_array($support->depart_id, [66,68]))
                                            โทร {{ thainumDigit($support->division->tel_no) }}
                                        @else
                                            โทร {{ thainumDigit($support->depart->tel_no) }}
                                        @endif
                                        
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%;">
                            <div class="content-header">
                                <span class="content__header-topic">ที่</span>
                                <div class="content__header-text" style="width: 94%;">
                                    <span style="margin: 0 5px;">{{ thainumDigit($support->doc_no) }}</span>
                                </div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="content-header">
                                <span class="content__header-topic">วันที่</span>
                                <div class="content__header-text" style="width: 88%;">
                                    <span style="margin: 0 10px;">
                                        {{ $support->doc_date == ''
                                                ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.thainumDigit(convDbDateToLongThMonth(date('Y-m-d')))
                                                : thainumDigit(convDbDateToLongThDate($support->doc_date))
                                        }}
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="content-header">
                                <span class="content__header-topic">เรื่อง</span>
                                <div class="content__header-text" style="width: 94%;">
                                    <span>{{ thainumDigit($support->topic) }}</span>
                                </div>
                            </div>
                            <div style="margin: 0; padding: 0;">
                                <span style="font-size: 20px;">เรียน</span>
                                <span>ผู้อำนวยการโรงพยาบาลเทพรัตน์นครราชสีมา</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph-content">
                                ด้วย <span>{{ $support->depart->depart_name }} {{ in_array($support->depart_id, [66,68]) ? '('.$support->division->ward_name.')' : '' }}</span>
                                มีความประสงค์ขอให้ดำเนินการซื้อ / จ้าง ดังนี้
                            </p>
                        </td>
                    </tr>

                    <?php $row = 0; ?>
                    <?php $restRow = 0; ?>
                    <?php $total = 0; ?>
                    <?php $tableHeight = 0; ?>
                    <?php $haveRowOvered = 0; ?>
                    <?php $nextBullet = 0; ?>
                    <?php $page = 1; ?>

                    <!-- ========================================= รายการน้อยกว่า 12 รายการ ===================================== -->
                    @if (count($support->details) < 12 || $support->is_plan_group == 1)
                        <tr>
                            <td colspan="4">
                                <div class="table-container">
                                    <table style="width: 100%;" class="table" border="1">
                                        <tr style="font-size: 16px;">
                                            <th style="width: 5%; text-align: center;">ลำดับ</th>
                                            <th style="text-align: center;">รายการ</th>
                                            <th style="width: 10%; text-align: center;">จำนวนหน่วย</th>
                                            <th style="width: 15%; text-align: center;">ราคาต่อหน่วย</th>
                                            <th style="width: 15%; text-align: center;">ราคารวม</th>
                                        </tr>

                                        @if($support->is_plan_group == 1)

                                            <!-- ========================================= รายการ PLAN GROUP ===================================== -->
                                            <?php $total = (float)$support->total; ?>
                                            <tr style="min-height: 20px;">
                                                <td style="text-align: center;">{{ thainumDigit(++$row) }}</td>
                                                <td>
                                                    <?php $tableHeight += 20; ?>
                                                    {{ thainumDigit($support->plan_group_desc) }}
                                                    @foreach($support->details as $detail)
                                                        <?php $tableHeight += 20; ?>
                                                        <p style="margin: 0; padding: 0; font-size: 14px;">
                                                            - {{ !isRenderWardInsteadDepart($detail->plan->depart_id)
                                                                ? $detail->plan->depart->depart_name
                                                                : $detail->plan->division->ward_name }}

                                                            {{ thainumDigit(number_format($detail->amount)) }}
                                                            {{ $detail->plan->planItem->unit->name }}
                                                            ({{ $detail->plan->in_plan == 'I' ? 'ในแผน' : 'นอกแผน' }})
                                                        </p>
                                                    @endforeach
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ thainumDigit(number_format($support->plan_group_amt)) }}
                                                </td>
                                                <td style="text-align: right;">
                                                    {{ thainumDigit(number_format($support->details[0]->price_per_unit, 2)) }}
                                                </td>
                                                <td style="text-align: right;">
                                                    {{ thainumDigit(number_format($support->total, 2)) }}
                                                </td>
                                            </tr>
                                            <!-- ========================================= End รายการ PLAN GROUP ===================================== -->

                                        @else

                                            @foreach($support->details as $detail)
                                                <?php $total += (float)$detail->sum_price; ?>
                                                <tr style="min-height: 20px;">
                                                    <td style="text-align: center;">{{ thainumDigit(++$row) }}</td>
                                                    <td>
                                                        <?php $tableHeight += 20; ?>
                                                        <div class="support__detail-item">
                                                            @if(strlen(thainumDigit($detail->plan->plan_no).'-'.thainumDigit($detail->plan->planItem->item->item_name)) >= 135)
                                                                <?php $haveRowOvered++; ?>
                                                            @endif

                                                            <span style="margin: 0">
                                                                {{ thainumDigit($detail->plan->plan_no) }}-{{ thainumDigit($detail->plan->planItem->item->item_name) }}
                                                            </span>

                                                            @if($detail->addon_id)
                                                                <p style="margin: 0">
                                                                    (งบนอกแผน {{ thainumDigit(number_format($detail->addon->planItem->sum_price)) }} บาท)
                                                                </p>
                                                            @endif

                                                            @if($detail->desc != '')
                                                                <?php $tableHeight += 20; ?>
                                                                <p class="item__desc-text">
                                                                    - {{ thainumDigit($detail->desc) }}
                                                                </p>
                                                            @else
                                                                @if (count($support->details) >= 4)
                                                                    <p style="margin: 0">&nbsp;</p>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        {{ thainumDigit(number_format($detail->amount)) }}
                                                    </td>
                                                    <td style="text-align: right;">
                                                        {{ thainumDigit(number_format($detail->price_per_unit + $detail->addon->planItem->sum_price, 2)) }}
                                                    </td>
                                                    <td style="text-align: right;">
                                                        {{ thainumDigit(number_format($detail->sum_price, 2)) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif

                                        <?php $tableHeight += 20; ?>
                                        <tr>
                                            <td style="text-align: center; font-weight: bold;" colspan="4">
                                                รวมเป็นเงินทั้งสิ้น
                                            </td>
                                            <td style="text-align: right;">
                                                {{ thainumDigit(number_format($support->total, 2)) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    <!-- ========================================= รายการมากกว่า 12 รายการ ===================================== -->
                    @else
                        <tr>
                            <td colspan="4">
                                <div class="table-container">
                                    <table style="width: 100%;" class="table" border="1">
                                        <tr style="font-size: 16px;">
                                            <th style="width: 5%; text-align: center;">ลำดับ</th>
                                            <th style="text-align: center;">รายการ</th>
                                            <th style="width: 10%; text-align: center;">จำนวนหน่วย</th>
                                            <th style="width: 15%; text-align: center;">ราคาต่อหน่วย</th>
                                            <th style="width: 15%; text-align: center;">ราคารวม</th>
                                        </tr>
                                        @foreach($support->details as $detail)
                                            @if ($row < 12)
                                                <tr style="min-height: 20px;">
                                                    <td style="text-align: center;">{{ thainumDigit(++$row) }}</td>
                                                    <td>
                                                        <?php $tableHeight += 20; ?>
                                                        <div class="support__detail-item">
                                                            {{ thainumDigit($detail->plan->plan_no) }}-{{ thainumDigit($detail->plan->planItem->item->item_name) }}

                                                            @if($detail->desc != '')
                                                                <?php $tableHeight += 20; ?>
                                                                <p class="item__desc-text">
                                                                    - {{ thainumDigit($detail->desc) }}
                                                                </p>
                                                            @else
                                                                @if (count($support->details) >= 4)
                                                                    <p style="margin: 0">&nbsp;</p>
                                                                @endif
                                                            @endif
                                                            </div>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        {{ thainumDigit(number_format($detail->amount)) }}
                                                    </td>
                                                    <td style="text-align: right;">
                                                        {{ thainumDigit(number_format($detail->price_per_unit, 2)) }}
                                                    </td>
                                                    <td style="text-align: right;">
                                                        {{ thainumDigit(number_format($detail->sum_price, 2)) }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </table>
                                </div>

                                <!-- ############################ Pagination ############################ -->
                                <p class="next-paragraph">/{{ thainumDigit(++$row) }}...</p>
                                <!-- ############################ Pagination ############################ -->

                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">

                                <!-- ############################ Pagination ############################ -->
                                <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                <!-- ############################ Pagination ############################ -->

                                <table style="width: 100%;" class="table" border="1">
                                    @foreach($support->details as $detail)
                                        <?php ++$restRow; ?>
                                        @if ($restRow > 12 && $restRow < 28)
                                            <?php $total += (float)$detail->sum_price; ?>
                                            <tr style="min-height: 20px;">
                                                <td style="width: 5%; text-align: center;">{{ thainumDigit($restRow) }}</td>
                                                <td>
                                                    <?php $tableHeight += 20; ?>
                                                    <div class="support__detail-item">
                                                        {{ thainumDigit($detail->plan->plan_no) }}-{{ thainumDigit($detail->plan->planItem->item->item_name) }}
                                                        
                                                        @if($detail->desc != '')
                                                            <?php $tableHeight += 20; ?>
                                                            <p class="item__desc-text">
                                                                - {{ thainumDigit($detail->desc) }}
                                                            </p>
                                                        @else
                                                            @if (count($support->details) >= 4)
                                                                <p style="margin: 0">&nbsp;</p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                                <td style="width: 10%;text-align: center;">
                                                    {{ thainumDigit(number_format($detail->amount)) }}
                                                </td>
                                                <td style="width: 15%;text-align: right;">
                                                    {{ thainumDigit(number_format($detail->price_per_unit, 2)) }}
                                                </td>
                                                <td style="width: 15%;text-align: right;">
                                                    {{ thainumDigit(number_format($detail->sum_price, 2)) }}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    @if (count($support->details) > 12 && count($support->details) <= 25)
                                        <tr>
                                            <td style="text-align: center; font-weight: bold;" colspan="4">
                                                รวมเป็นเงินทั้งสิ้น
                                            </td>
                                            <td style="text-align: right;">
                                                {{ thainumDigit(number_format($support->total, 2)) }}
                                            </td>
                                        </tr>
                                    @endif
                                </table>

                                <!-- ############################ Pagination ############################ -->
                                @if (count($support->details) > 27)
                                    <p class="next-paragraph">/{{ thainumDigit(28) }}...</p>
                                @endif
                                <!-- ############################ End Pagination ############################ -->

                            </td>
                        </tr>

                        <!-- ========================================= รายการมากกว่า 28 รายการขึ้นไป ===================================== -->
                        @if (count($support->details) > 28)
                            <?php $restRow = 0; ?>
                            <tr>
                                <td colspan="4">
                                    <p class="page-number">- {{ thainumDigit(++$page) }} -</p>

                                    <table style="width: 100%;" class="table" border="1">
                                        @foreach($support->details as $detail)
                                            <?php ++$restRow; ?>
                                            @if ($restRow >= 28)
                                                <?php $total += (float)$detail->sum_price; ?>
                                                <tr style="min-height: 20px;">
                                                    <td style="width: 5%; text-align: center;">{{ thainumDigit($restRow) }}</td>
                                                    <td>
                                                        <?php $tableHeight += 20; ?>
                                                        <div class="support__detail-item">
                                                            {{ thainumDigit($detail->plan->plan_no) }}-{{ thainumDigit($detail->plan->planItem->item->item_name) }}

                                                            @if($detail->desc != '')
                                                                <?php $tableHeight += 20; ?>
                                                                <p class="item__desc-text">
                                                                    - {{ thainumDigit($detail->desc) }}
                                                                </p>
                                                            @else
                                                                @if (count($support->details) >= 4)
                                                                    <p style="margin: 0">&nbsp;</p>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td style="width: 10%;text-align: center;">
                                                        {{ thainumDigit(number_format($detail->amount)) }}
                                                    </td>
                                                    <td style="width: 15%;text-align: right;">
                                                        {{ thainumDigit(number_format($detail->price_per_unit, 2)) }}
                                                    </td>
                                                    <td style="width: 15%;text-align: right;">
                                                        {{ thainumDigit(number_format($detail->sum_price, 2)) }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        <tr>
                                            <td style="text-align: center; font-weight: bold;" colspan="4">
                                                รวมเป็นเงินทั้งสิ้น
                                            </td>
                                            <td style="text-align: right;">
                                                {{ thainumDigit(number_format($support->total, 2)) }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        @endif
                        <!-- ========================================= End รายการมากกว่า 28 รายการขึ้นไป ===================================== -->

                    @endif
                    <!-- ========================================= End รายการมากกว่า 12 รายการ ===================================== -->

                    <tr>
                        <td colspan="4">
                            <span>เหตุผลและความจำเป็น</span>
                            <span style="margin: 0 0 0 5px;" class="text-val-dot">
                                {{ thainumDigit($support->reason) }}
                            </span>
                        </td>
                    </tr>

                    <!-- ############################ Pagination ############################ -->
                    @if(count($support->details) > 10 && count($support->details) < 12)
                        <tr>
                            <td colspan="4">
                                <p class="next-paragraph">/พร้อมนี้ได้ส่งข้อมูลประกอบ...</p>
                            </td>
                        </tr>
                    @endif
                    <!-- ############################ End Pagination ############################ -->

                    <tr>
                        <td colspan="4">

                            <!-- ############################ Pagination ############################ -->
                            @if(count($support->details) > 10 && count($support->details) < 12)
                                <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                            @endif
                            <!-- ############################ End Pagination ############################ -->

                            พร้อมนี้ได้ส่งข้อมูลประกอบการดำเนินการมาด้วย คือ

                            <!-- ############################ Pagination ############################ -->
                            @if(count($support->details) == 10)
                                <div style="height: 40px;"></div>
                                <p class="next-paragraph">/๑. รายชื่อคณะกรรมการกำหนด...</p>
                            @endif

                            @if(count($committees) > 2 && count($committees) <= 6)
                                @if(committeeNumber($committees, 1) == 1)

                                @else
                                    @if (count($support->details) > 19 && count($support->details) <= 25)
                                        <div style="height: 40px;"></div>
                                        <p class="next-paragraph">/๑. รายชื่อคณะกรรมการกำหนด...</p>
                                    @endif
                                @endif
                            @endif

                            @if(count($committees) > 6)
                                @if (count($support->details) > 19 && count($support->details) <= 25 && $support->is_plan_group == 1)
                                    <div style="height: 40px;"></div>
                                    <p class="next-paragraph">/๑. รายชื่อคณะกรรมการกำหนด...</p>
                                @endif
                            @endif
                            <!-- ############################ End Pagination ############################ -->

                        </td>
                    </tr>

                    <tr>
                        <td colspan="4">
                            <?php $nextBullet = 1; ?>

                            <!-- ############################ Pagination ############################ -->
                            @if(count($support->details) == 10)
                                <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                            @endif

                            @if(count($committees) > 2 && count($committees) <= 6)
                                @if(committeeNumber($committees, 1) == 1)

                                @else
                                    @if (count($support->details) > 19 && count($support->details) <= 25)
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif
                                @endif
                            @endif

                            @if(count($committees) > 6)
                                @if ((count($support->details) > 19 && count($support->details)) <= 25 && $support->is_plan_group == 1)
                                <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                @endif
                            @endif
                            <!-- ############################ End Pagination ############################ -->

                            <div style="margin: 0;">
                                ๑. รายชื่อคณะกรรมการกำหนดคุณลักษณะเฉพาะวัสดุหรือครุภัณฑ์ (กรณีงานซื้อ)/คณะกรรมการจัดทำร่างขอบเขตงาน (กรณีงานจ้าง)
                                <ul class="committee-lists">
                                    <?php $c1 = 1; ?>
                                    @foreach($committees as $committee)
                                        @if($committee->committee_type_id == '1')
                                            <?php $committeeHeight += 20; ?>
                                            <li class="committee-list">
                                                ๑.{{ thainumDigit($c1++) }}
                                                {{ $committee->person->prefix->prefix_name.$committee->person->person_firstname.' '.$committee->person->person_lastname }}
                                                <span style="margin: 0 0 0 5px; padding: 0;">
                                                    ตำแหน่ง {{ $committee->person->position->position_name }}{{ $committee->person->academic ? $committee->person->academic->ac_name : '' }}
                                                </span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>

                            <!-- ############################ Pagination ############################ -->
                            @if(count($committees) <= 2)
                                @if (count($support->details) == 4 && $haveRowOvered > 4)
                                    <div style="height: 20px;"></div>
                                    <p class="next-paragraph">/๒. รายชื่อคณะกรรมการ...</p>
                                @endif
                            @endif

                            @if(count($committees) > 2 && count($committees) <= 6)
                                @if(committeeNumber($committees, 1) == 1)
                                    @if(count($support->details) > 7 && count($support->details) <= 10)
                                        <div style="height: 40px;"></div>
                                        <p class="next-paragraph">/๒. รายชื่อคณะกรรมการ...</p>
                                    @endif

                                    @if($page == 2 && count($support->details) == 25)
                                        <div style="height: 20px;"></div>
                                        <p class="next-paragraph">/๒. รายชื่อคณะกรรมการ...</p>
                                    @endif
                                @else
                                    @if($haveRowOvered == 0 && (count($support->details) > 4 && count($support->details) <= 7))
                                        <p class="next-paragraph">/๒. รายชื่อคณะกรรมการ...</p>
                                    @endif

                                    @if($page == 1 && (count($support->details) > 7 && count($support->details) <= 10))
                                        <p class="next-paragraph">/๒. รายชื่อคณะกรรมการ...</p>
                                    @endif

                                    @if($page == 2 && (count($support->details) > 19 && count($support->details) <= 25))
                                        <p class="next-paragraph">/๒. รายชื่อคณะกรรมการ...</p>
                                    @endif
                                @endif
                            @endif

                            @if($support->is_plan_group == 1 && count($committees) <= 2)
                                @if(count($support->details) > 19 && count($support->details) <= 25)
                                    <p class="next-paragraph">/๒. รายชื่อคณะกรรมการ...</p>
                                @endif
                            @endif
                            <!-- ############################ End Pagination ############################ -->

                        </td>
                    </tr>

                    @if((float)$support->total >= 500000)
                        <?php $nextBullet = 2; ?>
                        <tr>
                            <td colspan="4">

                                <!-- ############################ Pagination ############################ -->
                                @if($support->is_plan_group != 1)
                                    @if(count($support->details) < 10 && count($support->details) > 7)
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif

                                    @if (count($support->details) > 19 && count($support->details) <= 25)
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif
                                @endif
                                <!-- ############################ End Pagination ############################ -->

                                <div style="margin: 0;">
                                    ๒. รายชื่อคณะกรรมการพิจารณาผลการประกวดราคา
                                    <ul class="committee-lists">
                                        <?php $c3 = 1; ?>
                                        @foreach($committees as $committee)
                                            @if($committee->committee_type_id == '3')
                                                <?php $committeeHeight += 20; ?>
                                                <li class="committee-list">
                                                    ๒.{{ thainumDigit($c3++) }}
                                                    {{ $committee->person->prefix->prefix_name.$committee->person->person_firstname.' '.$committee->person->person_lastname }}
                                                    <span style="margin: 0 0 0 5px; padding: 0;">
                                                        ตำแหน่ง {{ $committee->person->position->position_name }}{{ $committee->person->academic ? $committee->person->academic->ac_name : '' }}
                                                    </span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="4">

                            <!-- ############################ Pagination ############################ -->
                            <!-- ============================== คณะกรรมการรวมไม่เกิน 2 คน ============================== -->
                            @if(count($committees) <= 2)
                                @if (count($support->details) == 4 && $haveRowOvered > 4)
                                        <div style="height: 20px;"></div>
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                @endif
                            @endif

                            <!-- ============================== คณะกรรมการรวมระหว่าง 3-6 คน ============================== -->
                            @if(count($committees) > 2 && count($committees) <= 6)
                                @if(committeeNumber($committees, 1) == 1)
                                    @if(count($support->details) > 7 && count($support->details) <= 10)
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif

                                    @if($page == 2 && count($support->details) == 25)
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif
                                @else
                                    <!-- แบบ 2 หน้า และ รายการในตารางอยู่ระหว่าง 5-7 -->
                                    @if($page == 1 && $haveRowOvered > 0 && (count($support->details) > 4 && count($support->details) <= 7))
                                        <div style="height: 20px;"></div>
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif

                                    <!-- แบบ 2 หน้า และ รายการในตารางอยู่ระหว่าง 8-9 -->
                                    @if($page == 1 && (count($support->details) > 7 && count($support->details) < 10))
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif

                                    @if($page == 2 && (count($support->details) > 19 && count($support->details) <= 25))
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif
                                @endif
                            @endif

                            <!-- ============================== คณะกรรมการรวมมากกว่า 6 คน ============================== -->
                            @if(count($committees) > 6)
                                @if($support->is_plan_group == 1)
                                    <!-- <p class="page-number">- {{ thainumDigit(++$page) }} -</p> -->
                                    
                                @else
                                    
                                @endif
                            @endif
                            <!-- ############################ End Pagination ############################ -->

                            <div style="margin: 0;">
                                @if((float)$support->total >= 500000)
                                    <?php $nextBullet = 3; ?>
                                    ๓. รายชื่อคณะกรรมการตรวจรับพัสดุ
                                @else
                                    <?php $nextBullet = 2; ?>
                                    ๒. รายชื่อคณะกรรมการตรวจรับพัสดุ
                                @endif
                                <ul class="committee-lists">
                                    <?php $c2 = 1; ?>
                                    @foreach($committees as $committee)
                                        @if($committee->committee_type_id == '2')
                                            <?php $committeeHeight += 20; ?>
                                            <li class="committee-list">
                                                @if((float)$support->total >= 500000)
                                                    ๓.{{ thainumDigit($c2++) }}
                                                @else
                                                    ๒.{{ thainumDigit($c2++) }}
                                                @endif

                                                {{ $committee->person->prefix->prefix_name.$committee->person->person_firstname.' '.$committee->person->person_lastname }}
                                                <span style="margin: 0 0 0 5px; padding: 0;">
                                                    ตำแหน่ง {{ $committee->person->position->position_name }}{{ $committee->person->academic ? $committee->person->academic->ac_name : '' }}
                                                </span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p style="margin: 0;">
                                @if((float)$support->total >= 500000)
                                    <?php $nextBullet = 4; ?>
                                    ๔.  ชื่อผู้ขาย ข้อมูลร้านค้า/ข้อมูลสินค้า/ราคาสินค้า ตามที่แนบ  จำนวน............แผ่น
                                @else
                                    <?php $nextBullet = 3; ?>
                                    ๓.  ชื่อผู้ขาย ข้อมูลร้านค้า/ข้อมูลสินค้า/ราคาสินค้า ตามที่แนบ  จำนวน............แผ่น
                                @endif
                            </p>

                            <!-- ############################ Page 2 ############################ -->
                            <!-- ============================== คณะกรรมการรวมระหว่าง 3-6 คน ============================== -->
                            @if(count($committees) > 2 && count($committees) <= 6)
                                @if(committeeNumber($committees, '1') == 1)
                                    @if($page == 1 && count($support->details) > 5 && count($support->details) <= 7)
                                        <div style="height: 80px;"></div>
                                        <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายละเอียดคุณลักษณะ...</p>
                                    @endif
                                @else
                                    @if($page == 1 && count($support->details) >= 7 && count($support->details) < 10)
                                        @if(count($committees) < 4)
                                            <div style="height: 80px;"></div>
                                        @endif
                                        <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายละเอียดคุณลักษณะ...</p>
                                    @endif
                                @endif
                            @endif
                            <!-- ############################ End Page 2 ############################ -->

                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">

                            <!-- ############################ Page 2 ############################ -->
                            <!-- ============================== คณะกรรมการรวมระหว่าง 3-6 คน ============================== -->
                            @if(count($committees) > 2 && count($committees) <= 6)
                                @if(committeeNumber($committees, '1') == 1)
                                    @if(count($support->details) > 5 && count($support->details) < 7)
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif
                                @else
                                    @if($page == 1 && (count($support->details) >= 7 && count($support->details) < 10))
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif
                                @endif
                            @endif
                            <!-- ############################ End Page 2 ############################ -->

                            <p style="margin: 0;">
                                @if((float)$support->total >= 500000)
                                    <?php $nextBullet = 5; ?>
                                    ๕.  รายละเอียดคุณลักษณะเฉพาะพัสดุ/ร่างขอบเขตงาน/แบบแปลน/ใบปริมาณงาน ตามที่แนบ จำนวน............แผ่น
                                @else
                                    <?php $nextBullet = 4; ?>
                                    ๔.  รายละเอียดคุณลักษณะเฉพาะพัสดุ/ร่างขอบเขตงาน/แบบแปลน/ใบปริมาณงาน ตามที่แนบ จำนวน............แผ่น
                                @endif
                            </p>
                        </td>
                    </tr>

                    <!-- ############################ Pagination ############################ -->
                    <tr>
                        <td colspan="4">
                            <!-- ============================== คณะกรรมการรวมไม่เกิน 2 คน ============================== -->
                            @if(count($committees) <= 2)
                                @if($page == 1 && (count($support->details) == 4 && $haveRowOvered > 0))
                                    <div style="height: {{ 200 - ($haveRowOvered * 20) }}px;"></div>
                                    <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายชื่อผู้ประสานงาน...</p>
                                @endif

                                @if($page == 1 && $haveRowOvered == 0 && (count($support->details) > 4 && count($support->details) <= 10))
                                    <div style="height: {{ (10 - count($support->details)) * 20 }}px;"></div>
                                    <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายชื่อผู้ประสานงาน...</p>
                                @endif

                                @if($page == 1 && $haveRowOvered > 0 && (count($support->details) > 4 && count($support->details) <= 10))
                                    <div style="height: {{ (10 - count($support->details)) * 20 }}px;"></div>
                                    <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายชื่อผู้ประสานงาน...</p>
                                @endif
                            @endif

                            <!-- ============================== คณะกรรมการรวมระหว่าง 3-6 คน ============================== -->
                            @if(count($committees) > 2 && count($committees) <= 6)
                                @if(committeeNumber($committees, '1') == 1)
                                    @if($page == 1 && (count($support->details) >= 4 && count($support->details) <= 7))
                                        <div style="height: 80px;"></div>
                                        <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายชื่อผู้ประสานงาน...</p>
                                    @endif
    
                                    @if($page == 2 && (count($support->details) > 19 && count($support->details) <= 23))
                                        <div style="height: {{ (23 - count($support->details)) * 40 }}px;"></div>
                                        <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายชื่อผู้ประสานงาน...</p>
                                    @endif
                                @else
                                    @if($page == 1 && count($support->details) == 4 && $haveRowOvered > 0)
                                        <div style="height: {{ $haveRowOvered * 15 }}px;"></div>
                                        <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายชื่อผู้ประสานงาน...</p>
                                    @endif

                                    @if($page == 1 && $haveRowOvered == 0 && (count($support->details) >= 4 && count($support->details) <= 7))
                                        <div style="height: {{ (7 - count($support->details)) * 30 }}px;"></div>
                                        <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายชื่อผู้ประสานงาน...</p>
                                    @endif

                                    @if($page == 1 && (count($support->details) > 7 && count($support->details) <= 10))
                                        <div style="height: 20px;"></div>
                                        <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายชื่อผู้ประสานงาน...</p>
                                    @endif

                                    @if($page == 2 && (count($support->details) > 17 && count($support->details) <= 19))
                                        <div style="height: 20px;"></div>
                                        <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายชื่อผู้ประสานงาน...</p>
                                    @endif

                                    @if($page == 2 && count($support->details) > 19 && count($support->details) <= 27)
                                        <div style="height: 20px;"></div>
                                        <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายชื่อผู้ประสานงาน...</p>
                                    @endif

                                    @if($page == 3 && count($support->details) > 27)
                                        <div style="height: 20px;"></div>
                                        <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายชื่อผู้ประสานงาน...</p>
                                    @endif
                                @endif
                            @endif

                            <!-- ============================== คณะกรรมการรวมมากกว่า 6 คน ============================== -->
                            @if(count($committees) > 6)
                                @if(count($support->details) <= 2)
                                    <div style="height: {{ 180 - ((count($committees) - 6) * 20) }}px;"></div>
                                    <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายชื่อผู้ประสานงาน...</p>
                                @endif

                                @if($page == 1 && count($support->details) > 2)
                                    <div style="height: 20px;"></div>
                                    <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายชื่อผู้ประสานงาน...</p>
                                @endif

                                @if($page == 1 && count($support->details) > 17)
                                    <div style="height: 20px;"></div>
                                    <p class="next-paragraph">/{{ thainumDigit(++$nextBullet) }}.  รายชื่อผู้ประสานงาน...</p>
                                @endif
                            @endif
                        </td>
                    </tr>
                    <!-- ############################ End Pagination ############################ -->

                    <tr>
                        <td colspan="4">

                            <!-- ############################ Pagination ############################ -->
                            <!-- ============================== คณะกรรมการรวมไม่เกิน 2 คน ============================== -->
                            @if(count($committees) <= 2)
                                @if($page == 1 && ((count($support->details) <= 4 && $haveRowOvered > 2) && (count($support->details) > 4 && count($support->details) <= 10) && $haveRowOvered == 0))
                                    <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                @endif

                                @if($page == 1 && (count($support->details) > 4 && count($support->details) <= 10))
                                    <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                @endif
                            @endif

                            <!-- ============================== คณะกรรมการรวมระหว่าง 3-6 คน ============================== -->
                            @if(count($committees) > 2 && count($committees) <= 6)
                                @if(committeeNumber($committees, '1') == 1)
                                    @if($page == 1 && (count($support->details) >= 4 && count($support->details) <= 7))
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif

                                    @if($page == 2 && (count($support->details) > 19 && count($support->details) <= 27))
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif
                                @else
                                    @if($page == 1 && count($support->details) == 4)
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif

                                    @if ($page == 1 && (count($support->details) > 7 && count($support->details) < 10))
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif
    
                                    @if($page == 2 && (count($support->details) > 17 && count($support->details) <= 19))
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif
    
                                    @if($page == 2 && (count($support->details) > 19 && count($support->details) <= 27))
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif

                                    @if($page == 3 && count($support->details) > 27)
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif
                                @endif
                            @endif

                            <!-- ============================== คณะกรรมการรวมมากกว่า 6 คน ============================== -->
                            @if(count($committees) > 6)
                                @if($support->is_plan_group == 1)
                                    
                                @else
                                    @if($page == 1 && count($support->details) <= 2)
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif

                                    @if($page == 2 && (count($support->details) > 10 && count($support->details) <= 17))
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif

                                    @if($page == 3 && (count($support->details) > 17 && count($support->details) <= 19))
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif

                                    @if($page == 3 && (count($support->details) > 19 && count($support->details) <= 27))
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif

                                    @if($page == 3 && count($support->details) > 27)
                                        <div style="height: 20px;"></div>
                                        <p class="page-number">- {{ thainumDigit(++$page) }} -</p>
                                    @endif
                                @endif
                            @endif
                            <!-- ############################ End Pagination ############################ -->

                            <p style="margin: 0 0 10px;">
                                @if((float)$support->total >= 500000)
                                    ๖.  รายชื่อผู้ประสานงาน
                                @else
                                    ๕.  รายชื่อผู้ประสานงาน
                                @endif
                                <span style="margin: 0;">
                                    ชื่อ-สกุล <span class="text-val-dot p5">{{ $contact->prefix->prefix_name.$contact->person_firstname.' '.$contact->person_lastname }}</span>
                                    ตำแหน่ง <span class="text-val-dot p5">{{ $contact->position->position_name }}{{ $contact->academic ? $contact->academic->ac_name : '' }}</span> 
                                    โทร <span class="text-val-dot p5">{{ thainumDigit($contact->person_tel) }}</span>
                                </span>
                            </p>
                            <p style="margin: 0 0 0 80px;">
                                จึงเรียนมาเพื่อพิจารณา หากเห็นชอบโปรดอนุมัติ
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center; padding-top: 10px;">
                            <p style="margin: 0;">
                                หัวหน้ากลุ่มงาน<span class="dot">......................................................</span>
                            </p>
                            <p style="margin: 0;">
                                ( {{ $headOfDepart->prefix->prefix_name.$headOfDepart->person_firstname. ' ' .$headOfDepart->person_lastname }} )
                            </p>
                            <p style="margin: 0;">
                                <span>{{ $headOfDepart->position->position_name }}{{ $headOfDepart->academic ? $headOfDepart->academic->ac_name : '' }}</span>
                            </p>
                        </td>
                        <td colspan="2" style="text-align: center; padding: 10px;">
                            <p style="margin: 0;">
                                @if(empty($support->head_of_faction))
                                    หัวหน้ากลุ่มภารกิจ<span class="dot">......................................................</span>
                                @else
                                    <span class="dot">......................................................</span>
                                @endif
                            </p>
                            <p style="margin: 0;">
                                ( {{ $headOfFaction->prefix->prefix_name.$headOfFaction->person_firstname. ' ' .$headOfFaction->person_lastname }} )
                            </p>
                            <p style="margin: 0;">
                                <span>{{ $headOfFaction->position->position_name }}{{ $headOfFaction->academic ? $headOfFaction->academic->ac_name : '' }}</span>
                            </p>
                        </td>
                    </tr>
                </table>

                <div style="text-align: center; position: absolute;">
                    <p style="margin: 0 0 20px 0;">
                        <span style="margin: 0;">[&nbsp;&nbsp;] อนุมัติ</span>
                        <span style="margin: 20px;">[&nbsp;&nbsp;] ไม่อนุมัติ</span>
                    </p>
                    <p style="margin: 0;">
                        ( นายชวศักดิ์  กนกกันฑพงษ์ )
                    </p>
                    <p style="margin: 0;">
                        ผู้อำนวยการโรงพยาบาลเทพรัตน์นครราชสีมา
                    </p>
                </div>
            </div>
            <p class="print-options">พิมพ์จากระบบ E-Plan เมื่อ {{ date('Y-m-d H:i:s') }}</p>
        </div>
    </body>
</html>