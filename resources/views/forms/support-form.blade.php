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
                                        {{ $support->depart_id != 37 ? $support->depart->depart_name : 'กลุ่มงานการพยาบาลด้านการควบคุมและป้องกันการติดเชื้อฯ' }}
                                    </span>
                                    <span style="margin: 0 5px;">โรงพยาบาลเทพรัตน์นครราชสีมา</span>
                                    <span style="margin: 0 5px;">โทร {{ thainumDigit($support->depart->tel_no) }}</span>
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
                                ด้วย <span>{{ $support->depart->depart_name }}</span>
                                มีความประสงค์ขอให้ดำเนินการซื้อ / จ้าง ดังนี้
                            </p>
                            
                            <div class="table-container">
                                <table style="width: 100%;" class="table" border="1">
                                    <tr style="font-size: 16px;">
                                        <th style="width: 5%; text-align: center;">ลำดับ</th>
                                        <th style="text-align: center;">รายการ</th>
                                        <th style="width: 10%; text-align: center;">จำนวนหน่วย</th>
                                        <th style="width: 15%; text-align: center;">ราคาต่อหน่วย</th>
                                        <th style="width: 15%; text-align: center;">ราคารวม</th>
                                    </tr>

                                    <?php $row = 0; ?>
                                    <?php $total = 0; ?>
                                    <?php $tableHeight = 0; ?>
                                    @if($support->is_plan_group == '1')
                                        <?php $total = (float)$support->total; ?>
                                        <tr style="min-height: 20px;">
                                            <td style="text-align: center;">{{ thainumDigit(++$row) }}</td>
                                            <td>
                                                <?php $tableHeight += 20; ?>
                                                {{ thainumDigit($support->plan_group_desc) }}
                                                @foreach($support->details as $detail)
                                                    <?php $tableHeight += 20; ?>
                                                    <p style="margin: 0; padding: 0; font-size: 14px;">
                                                        - {{ $detail->plan->depart->depart_name }}
                                                        {{ thainumDigit(number_format($detail->amount)) }}
                                                        {{ $detail->plan->planItem->unit->name }}
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
                                    @else
                                        @foreach($support->details as $detail)
                                            <?php $total += (float)$detail->sum_price; ?>
                                            <tr style="min-height: 20px;">
                                                <td style="text-align: center;">{{ thainumDigit(++$row) }}</td>
                                                <td>
                                                    <?php $tableHeight += 20; ?>
                                                    {{ thainumDigit($detail->plan->planItem->item->item_name) }}
                                                    @if($detail->desc != '')
                                                        <?php $tableHeight += 20; ?>
                                                        <p style="margin: 0 0 0 5px;">
                                                            - {{ thainumDigit($detail->desc) }}
                                                        </p>
                                                    @endif
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
                                        @endforeach
                                    @endif
                                    <?php $tableHeight += 20; ?>

                                    <tr>
                                        <td style="text-align: center; font-weight: bold;" colspan="4">
                                            รวมเป็นเงินทั้งสิ้น
                                        </td>
                                        <td style="text-align: right;">
                                            {{ thainumDigit(number_format($total, 2)) }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <span>เหตุผลและความจำเป็น</span>
                            <span style="margin: 0 0 0 5px;" class="text-val-dot">
                                {{ thainumDigit($support->reason) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            พร้อมนี้ได้ส่งข้อมูลประกอบการดำเนินการมาด้วย คือ
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
                            @if(($tableHeight <= 340 && $committeeHeight > 60) || $tableHeight > 340)
                                <div style="height: 20px;"></div>
                                <p class="next-paragraph">/๒. รายชื่อคณะกรรมการ...</p>
                            @endif
                        </td>
                    </tr>
                    @if((float)$total >= 500000)
                        <tr>
                            <td colspan="4">
                                @if(($tableHeight <= 340 && $committeeHeight > 60) || $tableHeight > 340)
                                    <div style="height: 20px;"></div>
                                    <p class="page-number">- ๒ -</p>
                                @endif
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
                            @if(($tableHeight <= 340 && $committeeHeight > 60) || $tableHeight > 340)
                                <div style="height: 20px;"></div>
                                <p class="page-number">- ๒ -</p>
                            @endif
                            <div style="margin: 0;">
                                @if((float)$total >= 500000)
                                    ๓. รายชื่อคณะกรรมการตรวจรับพัสดุ
                                @else
                                    ๒. รายชื่อคณะกรรมการตรวจรับพัสดุ
                                @endif
                                <ul class="committee-lists">
                                    <?php $c2 = 1; ?>
                                    @foreach($committees as $committee)
                                        @if($committee->committee_type_id == '2')
                                            <?php $committeeHeight += 20; ?>
                                            <li class="committee-list">
                                                @if((float)$total >= 500000)
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
                                @if((float)$total >= 500000)
                                    ๔.  ชื่อผู้ขาย ข้อมูลร้านค้า/ข้อมูลสินค้า/ราคาสินค้า ตามที่แนบ  จำนวน............แผ่น
                                @else
                                    ๓.  ชื่อผู้ขาย ข้อมูลร้านค้า/ข้อมูลสินค้า/ราคาสินค้า ตามที่แนบ  จำนวน............แผ่น
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p style="margin: 0;">
                                @if((float)$total >= 500000)
                                    ๕.  รายละเอียดคุณลักษณะเฉพาะพัสดุ/ร่างขอบเขตงาน/แบบแปลน/ใบปริมาณงาน ตามที่แนบ จำนวน............แผ่น
                                @else
                                    ๔.  รายละเอียดคุณลักษณะเฉพาะพัสดุ/ร่างขอบเขตงาน/แบบแปลน/ใบปริมาณงาน ตามที่แนบ จำนวน............แผ่น
                                @endif
                            </p>

                            @if(count($committees) <= 6)
                                @if($tableHeight > 100 && $tableHeight < 180)
                                    <div style="height: 40px;"></div>
                                    <p class="next-paragraph">/๖.  รายชื่อผู้ประสานงาน...</p>
                                @endif

                                @if($tableHeight >= 180 && $tableHeight < 360)
                                    <div style="height: 20px;"></div>
                                    <p class="next-paragraph">/๖.  รายชื่อผู้ประสานงาน...</p>
                                @endif
                            @endif

                            @if(count($committees) > 6)
                                <!-- ประมาณไม่เกิน 2 แถวใหญ่ -->
                                @if($tableHeight <= 100)
                                    <div style="height: {{ 100 - $tableHeight }}px;"></div>
                                    <p class="next-paragraph">/๖.  รายชื่อผู้ประสานงาน...</p>
                                @endif

                                <!-- ประมาณมากกว่า 2 แถวใหญ่ -->
                                @if($tableHeight > 100)
                                    <div style="height: 20px;"></div>
                                    <p class="next-paragraph">/๖.  รายชื่อผู้ประสานงาน...</p>
                                @endif
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            @if(($tableHeight > 100 && $tableHeight < 360) || count($committees) > 6)
                                <div style="height: 20px;"></div>
                                <p class="page-number">- ๒ -</p>
                            @endif
                            <p style="margin: 0 0 10px;">
                                @if((float)$total >= 500000)
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
                                หัวหน้ากลุ่มภารกิจ<span class="dot">......................................................</span>
                            </p>
                            <p style="margin: 0;">
                                ( {{ $headOfFaction->prefix->prefix_name.$headOfFaction->person_firstname. ' ' .$headOfFaction->person_lastname }} )
                            </p>
                            <p style="margin: 0;">
                                <span>{{ $headOfFaction->position->position_name }}{{ $headOfFaction->academic ? $headOfFaction->academic->ac_name : '' }}</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 0;">
                            <p style="margin: 0 0 20px 0;">
                                <span style="margin: 0;">[&nbsp;&nbsp;] อนุมัติ</span>
                                <span style="margin: 20px;">[&nbsp;&nbsp;] ไม่อนุมัติ</span>
                            </p>
                            <p style="margin: 0;">
                                ( นายชวศักดิ์  กนกกัณฑพงษ์ )
                            </p>
                            <p style="margin: 0;">
                                ผู้อำนวยการโรงพยาบาลเทพรัตน์นครราชสีมา
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            <p class="print-options">พิมพ์จากระบบ E-Plan เมื่อ {{ date('Y-m-d H:i:s') }}</p>
        </div>
    </body>
</html>