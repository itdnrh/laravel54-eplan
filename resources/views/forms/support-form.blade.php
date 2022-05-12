<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>บันทึกขอสนับสนุน</title>
        <link rel="stylesheet" href="{{ asset('/css/pdf.css') }}">
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2 style="margin: 0px;">
                    <img src="{{ asset('/img/krut.jpg') }}"
                        alt="krut"
                        style="height: 65px; position: absolute; top: 12px">
                </h2>
                <h2 style="margin: 0px;">บันทึกข้อความ</h2>
            </div>
            <div class="content">
                <table style="width: 100%; height: 50%">
                    <tr>
                        <td colspan="4">
                            <p style="margin: 0; padding: 0;">
                                <span style="font-size: 22px; font-weight: bold;">ส่วนราชการ</span>
                                <span style="margin: 0 10px;">{{ $support->depart->depart_name }}</span>
                                <span style="margin: 0 10px;">โรงพยาบาลเทพรัตน์นครราชสีมา</span>
                                โทร<span style="margin: 0 5px;">{{ $support->support_place }}</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%;">
                            <p style="margin: 0;">
                                <span style="font-size: 22px; font-weight: bold;">ที่</span>
                                <span style="margin: 0 10px;">{{ $support->doc_no }}</span>
                            </p>
                        </td>
                        <td colspan="2">
                            <p style="margin: 0;">
                                <span style="font-size: 22px; font-weight: bold;">วันที่</span>
                                <span style="margin: 0 10px;">{{ convDbDateToLongThDate($support->doc_date) }}</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div style="margin: 0; padding: 0;">
                                <span style="font-size: 22px; font-weight: bold;">เรื่อง</span>
                                <span>{{ $support->topic }}</span>
                            </div>
                            <div style="margin: 0; padding: 0;">
                                <span style="font-size: 20px;">เรียน</span>
                                <span>ผู้อำนวยการโรงพยาบาลเทพรัตน์นครราชสีมา</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p style="margin: 0 0 0 80px;">
                                ด้วย <span>{{ $support->depart->depart_name }}</span>
                                มีความประสงค์ขอให้ดำเนินการซื้อ/จ้าง ดังนี้
                            </p>

                            <table style="width: 95%;" class="table" border="1">
                                <tr>
                                    <th style="width: 4%; text-align: center;">ลำดับ</th>
                                    <th style="text-align: center;">รายการ</th>
                                    <th style="width: 12%; text-align: center;">จำนวนหน่วย</th>
                                    <th style="width: 12%; text-align: center;">ราคาต่อหน่วย</th>
                                    <th style="width: 12%; text-align: center;">ราคารวม</th>
                                </tr>
                                <?php $row = 0; ?>
                                <?php $total = 0; ?>
                                @foreach($support->details as $detail)
                                    <?php $total += (float)$detail->sum_price; ?>
                                    <tr>
                                        <td style="text-align: center; padding: 0;">{{ ++$row }}</td>
                                        <td style=" padding: 0 5px;">
                                            {{ $detail->plan->planItem->item->item_name }}
                                        </td>
                                        <td style="text-align: center; padding: 0;">
                                            {{ $detail->amount }}
                                        </td>
                                        <td style="text-align: center; padding: 0;">
                                            {{ number_format($detail->price_per_unit) }}
                                        </td>
                                        <td style="text-align: center; padding: 0;">
                                            {{ number_format($detail->sum_price) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td style="text-align: center; font-size: 20px; font-weight: bold;" colspan="4">
                                        รวมเป็นเงิน
                                    </td>
                                    <td style="text-align: center; font-size: 20px; font-weight: bold;">
                                        {{ number_format($total) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            เหตุผลและความจำเป็น
                            <span style="margin: 0 0 0 5px; padding: 0;">
                                {{ $support->reason }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            พร้อมนี้ได้ส่งข้อมูลประกอบการดำเนินการมาด้วย คือ
                            <p style="margin: 0;">
                                ๑. รายชื่อกรรมการกำหนดคุณลักษณะครุภัณฑ์(กรณีจัดซื้อ/จ้างวงเงินไม่เกิน ๑๐๐,๐๐๐ บาท) ***พร้อมแนบสำเนาบัตรประชาชน
                                <ul style="margin: 0; padding: 0; list-style: none;">
                                    <?php $c1 = 1; ?>
                                    @foreach($committees as $committee)
                                        @if($committee->committee_type_id == '1')
                                            <li style="margin: 0 0 0 15px; padding: 0;">
                                                {{ $c1++ }}.
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
                                ๒. รายชื่อกรรมการตรวจรับ ***พร้อมแนบสำเนาบัตรประชาชน
                                <ul style="margin: 0; padding: 0; list-style: none;">
                                    <?php $c2 = 1; ?>
                                    @foreach($committees as $committee)
                                        @if($committee->committee_type_id == '2')
                                            <li style="margin: 0 0 0 15px; padding: 0;">
                                                {{ $c2++ }}.
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
                                ๓.  ชื่อผู้ขาย ข้อมูลร้านค้า/ข้อมูลสินค้า/ราคาสินค้า ตามที่แนบ  จำนวน...............................แผ่น
                            </p>
                            <p style="margin: 0;">
                                ๔.  รายละเอียดคุณลักษณะพัสดุ/แบบแปลน/ใบปริมาณงาน
                            </p>
                            <p style="margin: 0;">
                                ๕.  รายชื่อผู้ประสานงาน
                                <span style="margin: 0;">
                                    ชื่อ-สกุล.........................................................ตำแหน่ง..........................................................โทร.........................
                                </span>
                            </p>
                            <p style="margin: 0 0 0 80px;">
                                จึงเรียนมาเพื่อโปรดดำเนินการ
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p style="margin-left: 50px;">
                                ผู้ขออนุมัติ<span class="dot">......................................................</span>
                            </p>
                            <p style="margin-left: 100px;">
                                ( {{ $headOfDepart->prefix->prefix_name.$headOfDepart->person_firstname. ' ' .$headOfDepart->person_lastname }} )
                            </p>
                            <p style="margin-left: 50px;">
                                ตำแหน่ง <span>{{ $headOfDepart->position->position_name }}{{ $headOfDepart->academic ? $headOfDepart->academic->ac_name : '' }}</span>
                            </p>
                        </td>
                        <td colspan="2">
                            <p style="margin-left: 50px;">
                                หัวหน้ากลุ่มงาน<span class="dot">......................................................</span>
                            </p>
                            <p style="margin-left: 100px;">
                                ( {{ $headOfDepart->prefix->prefix_name.$headOfDepart->person_firstname. ' ' .$headOfDepart->person_lastname }} )
                            </p>
                            <p style="margin-left: 50px;">
                                ตำแหน่ง <span>{{ $headOfDepart->position->position_name }}{{ $headOfDepart->academic ? $headOfDepart->academic->ac_name : '' }}</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">
                            <p style="margin-left: 50px;">
                                หัวหน้ากลุ่มภารกิจ<span class="dot">......................................................</span>
                            </p>
                            <p style="margin-left: 100px;">
                                ( {{ $headOfFaction->prefix->prefix_name.$headOfFaction->person_firstname. ' ' .$headOfFaction->person_lastname }} )
                            </p>
                            <p style="margin-left: 50px;">
                                ตำแหน่ง <span>{{ $headOfFaction->position->position_name }}{{ $headOfFaction->academic ? $headOfFaction->academic->ac_name : '' }}</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">
                            <p style="margin: 0 0 20px 50px;">
                                <span style="margin-left: 20px;">[&nbsp;&nbsp;] อนุมัติ</span>
                                <span style="margin-left: 20px;">[&nbsp;&nbsp;] ไม่อนุมัติ</span>
                            </p>
                            <p style="margin: 0 0 0 100px;">
                                ( นายชวศักดิ์  กนกกัณฑพงษ์ )
                            </p>
                            <p style="margin: 0 0 0 65px;">
                                ผู้อำนวยการโรงพยาบาลเทพรัตน์นครราชสีมา
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>