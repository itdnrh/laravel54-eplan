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
                <table style="width: 100%;">
                    <tr>
                        <td colspan="4">
                            <div class="content-header">
                                <span class="content__header-topic">ส่วนราชการ</span>
                                <div class="content__header-text" style="width: 77%; margin-left: 70px;">
                                    <span style="margin: 0 5px;">{{ $support->depart->depart_name }}</span>
                                    <span style="margin: 0 5px;">โรงพยาบาลเทพรัตน์นครราชสีมา</span>
                                    โทร <span style="margin: 0 5px;">{{ thainumDigit($support->depart->tel_no) }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%;">
                            <div class="content-header">
                                <span class="content__header-topic">ที่</span>
                                <div class="content__header-text" style="width: 90%; margin-left: 12px;">
                                    <span style="margin: 0 5px;">{{ thainumDigit($support->doc_no) }}</span>
                                </div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="content-header">
                                <span class="content__header-topic">วันที่</span>
                                <div class="content__header-text" style="width: 70%; margin-left: 28px;">
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
                                <div class="content__header-text" style="width: 85%; margin-left: 28px;">
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
                            <p style="margin: 5px 0 0 80px;">
                                ด้วย <span>{{ $support->depart->depart_name }}</span>
                                มีความประสงค์ขอให้ดำเนินการซื้อ/จ้าง ดังนี้
                            </p>

                            <table style="width: 95%;" class="table" border="1">
                                <tr style="font-size: 16px;">
                                    <th style="width: 5%; text-align: center; padding: 0;">ลำดับ</th>
                                    <th style="text-align: center; padding: 0;">รายการ</th>
                                    <th style="width: 12%; text-align: center; padding: 0;">จำนวนหน่วย</th>
                                    <th style="width: 12%; text-align: center; padding: 0;">ราคาต่อหน่วย</th>
                                    <th style="width: 12%; text-align: center; padding: 0;">ราคารวม</th>
                                </tr>
                                <?php $row = 0; ?>
                                <?php $total = 0; ?>
                                @foreach($support->details as $detail)
                                    <?php $total += (float)$detail->sum_price; ?>
                                    <tr>
                                        <td style="text-align: center; padding: 0;">{{ ++$row }}</td>
                                        <td style=" padding: 0 5px;">
                                            {{ thainumDigit($detail->plan->planItem->item->item_name) }}
                                        </td>
                                        <td style="text-align: center; padding: 0;">
                                            {{ thainumDigit($detail->amount) }}
                                        </td>
                                        <td style="text-align: center; padding: 0;">
                                            {{ thainumDigit(number_format($detail->price_per_unit)) }}
                                        </td>
                                        <td style="text-align: center; padding: 0;">
                                            {{ thainumDigit(number_format($detail->sum_price)) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td style="text-align: center; font-size: 20px; font-weight: bold;" colspan="4">
                                        รวมเป็นเงิน
                                    </td>
                                    <td style="text-align: center; font-size: 20px; font-weight: bold;">
                                        {{ thainumDigit(number_format($total)) }}
                                    </td>
                                </tr>
                            </table>
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
                            <p style="margin: 0;">
                                1. รายชื่อกรรมการกำหนดคุณลักษณะครุภัณฑ์(กรณีจัดซื้อ/จ้างวงเงินไม่เกิน ๑๐๐,๐๐๐ บาท) ***พร้อมแนบสำเนาบัตรประชาชน
                                <ul class="committee-lists">
                                    <?php $c1 = 1; ?>
                                    @foreach($committees as $committee)
                                        @if($committee->committee_type_id == '1')
                                            <li class="committee-list">
                                                1.{{ $c1++ }}
                                                {{ $committee->person->prefix->prefix_name.$committee->person->person_firstname.' '.$committee->person->person_lastname }}
                                                <span style="margin: 0 0 0 5px; padding: 0;">
                                                    ตำแหน่ง {{ $committee->person->position->position_name }}{{ $committee->person->academic ? $committee->person->academic->ac_name : '' }}
                                                </span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </p>
                            <p style="margin: 0;">
                                2. รายชื่อกรรมการตรวจรับ ***พร้อมแนบสำเนาบัตรประชาชน
                                <ul class="committee-lists">
                                    <?php $c2 = 1; ?>
                                    @foreach($committees as $committee)
                                        @if($committee->committee_type_id == '2')
                                            <li class="committee-list">
                                                2.{{ $c2++ }}
                                                {{ $committee->person->prefix->prefix_name.$committee->person->person_firstname.' '.$committee->person->person_lastname }}
                                                <span style="margin: 0 0 0 5px; padding: 0;">
                                                    ตำแหน่ง {{ $committee->person->position->position_name }}{{ $committee->person->academic ? $committee->person->academic->ac_name : '' }}
                                                </span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </p>
                            <p style="margin: 0;">
                                3.  ชื่อผู้ขาย ข้อมูลร้านค้า/ข้อมูลสินค้า/ราคาสินค้า ตามที่แนบ  จำนวน...............................แผ่น
                            </p>
                            <p style="margin: 0;">
                                4.  รายละเอียดคุณลักษณะพัสดุ/แบบแปลน/ใบปริมาณงาน
                            </p>
                            <p style="margin: 0 0 10px;">
                                5.  รายชื่อผู้ประสานงาน
                                <span style="margin: 0;">
                                    ชื่อ-สกุล <span class="text-val-dot p10">{{ $contact->prefix->prefix_name.$contact->person_firstname.' '.$contact->person_lastname }}</span>
                                    ตำแหน่ง <span class="text-val-dot p10">{{ $contact->position->position_name }}{{ $contact->academic ? $contact->academic->ac_name : '' }}</span> 
                                    โทร <span class="text-val-dot p10">{{ thainumDigit($contact->person_tel) }}</span>
                                </span>
                            </p>
                            <p style="margin: 0 0 0 80px;">
                                จึงเรียนมาเพื่อโปรดดำเนินการ
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center; padding-top: 10px;">
                            <p style="margin: 0;">
                                ผู้ขออนุมัติ<span class="dot">......................................................</span>
                            </p>
                            <p style="margin: 0;">
                                ( {{ $contact->prefix->prefix_name.$contact->person_firstname.' '.$contact->person_lastname }} )
                            </p>
                            <p style="margin: 0;">
                                <span>{{ $contact->position->position_name }}{{ $contact->academic ? $contact->academic->ac_name : '' }}</span>
                            </p>
                        </td>
                        <td colspan="2" style="text-align: center; padding: 10px;">
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
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2" style="text-align: center; padding: 0;">
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
                        <td colspan="2"></td>
                        <td colspan="2" style="text-align: center; padding: 0;">
                            <p style="margin: 10px 0 20px 0;">
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
        </div>
    </body>
</html>