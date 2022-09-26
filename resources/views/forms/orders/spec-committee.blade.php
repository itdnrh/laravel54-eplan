<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>บันทึกขอสนับสนุน</title>
        <link rel="stylesheet" href="{{ asset('/css/pdf.css') }}">
    </head>
    <body>
        <div class="memo-container">
            <div class="memo-header">
                <div class="logo-krut">
                    <img src="{{ asset('/img/krut.jpg') }}" alt="krut" />
                </div>
                <h2>บันทึกข้อความ</h2>
            </div>
            <div class="content">
                <table class="layout">
                    <tr>
                        <td colspan="4">
                            <div class="content-header">
                                <span class="content__header-topic">ส่วนราชการ</span>
                                <div class="content__header-text" style="width: 77%; margin-left: 70px;">
                                    <span style="margin: 0 5px;">กลุ่มงานพัสดุ</span>
                                    <span style="margin: 0 5px;">โรงพยาบาลเทพรัตน์นครราชสีมา</span>
                                    โทร <span style="margin: 0 5px;">{{ thainumDigit('9608') }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%;">
                            <div class="content-header">
                                <span class="content__header-topic">ที่</span>
                                <div class="content__header-text" style="width: 90%; margin-left: 12px;">
                                    <span style="margin: 0 5px;">{{ thainumDigit('นม 0032.201.2/') }}</span>
                                </div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="content-header">
                                <span class="content__header-topic">วันที่</span>
                                <div class="content__header-text" style="width: 70%; margin-left: 28px;">
                                    <span style="margin: 0 10px;">
                                        <!-- {{ '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.thainumDigit($support->doc_date) }} -->
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ thainumDigit(convDbDateToLongThMonth(date('Y-m-d'))) }}
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="content-header">
                                <span class="content__header-topic" style="top: 0;">เรื่อง</span>
                                <div class="content__header-text" style="width: 85%; margin-left: 28px;">
                                    <span style="margin-left: 5px;">
                                        ขออนุมัติแต่งตั้งผู้รับผิดชอบจัดทำรายละเอียดคุณลักษณะเฉพาะของพัสดุและราคากลาง
                                    </span>
                                </div>
                            </div>
                            <div style="margin: 5px 0; padding: 0;">
                                <span style="font-size: 20px;">เรียน</span>
                                <span style="margin-left: 5px;">ผู้ว่าราชการจังหวัดนครราชสีมา</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph-content with-compressed with-expanded">
                                <span class="memo-paragraph-topic-inline">๑.ต้นเรื่อง</span>
                                ด้วย กลุ่มงานพัสดุ โรงพยาบาลเทพรัตน์นครราชสีมา มีความประสงค์จะดำเนินการ
                                จัดซื้อ<span>{{ $support->category->name }}</span> โดยวิธีเฉพาะเจาะจง เพื่อใช้ในการปฏิบัติงานของเจ้าหน้าที่
                                จึงขออนุมัติจัดซื้อ <span>{{ $support->category->name }}</span>
                                จำนวน <span>{{ thainumDigit(count($support->details)) }}</span> รายการ
                                จำนวนเงิน <span>{{ thainumDigit(number_format($support->total, 2)) }} บาท</span>
                                (<span>{{ baht_text(number_format($support->total, 2)) }}</span>)
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph-topic">
                                ๒. ข้อกฎหมาย
                            </p>
                            <p class="memo-paragraph-content with-compressed with-expanded">
                                ๒.๑ ระเบียบกระทรวงการคลังว่าด้วยการจัดซื้อจัดจ้างและการบริหารพัสดุภาครัฐ พ.ศ. ๒๕๖๐
                                ข้อ ๒๑ ในการจัดซื้อจัดจ้างที่มิใช่การจ้างก่อสร้าง ให้หัวหน้าหน่วยงานของรัฐแต่งตั้งคณะกรรมการขึ้นมา คณะหนึ่ง
                                หรือจะให้เจ้าหน้าที่ หรือบุคคลใดบุคคลหนึ่งรับผิดชอบในการจัดทำร่างขอบเขตของงานหรือราย-
                                ละเอียดคุณลักษณะเฉพาะของพัสดุที่จะซื้อหรือจ้าง รวมทั้งกำหนดหลักเกณฑ์พิจารณาคัดเลือกข้อเสนอด้วย และ
                                ข้อ ๒๑ วรรคสี่ องค์ประกอบ ระยะเวลาการพิจารณา และประชุมของคณะกรรมการตามวรรคหนึ่ง
                                และวรรคสอง ให้เป็นไปตามที่หัวหน้าหน่วยงานของรัฐกำหนดตามความจำเป็นและเหมาะสม
                            </p>
                            <p class="memo-paragraph-content with-compressed with-expanded">
                                ๒.๒ คำสั่งจังหวัดนครราชสีมา ที่  ลงวันที่ การมอบอำนวจของผู้ว่าราชการจังหวัดนครราชสีมา
                                ให้ผู้อำนวยการโรงพยาบาลทั่วไปโดยให้มีอำนาจภายในวงเงินครั้งหนึ่งไม่เกิน ๑๐,๐๐๐,๐๐๐ บาท
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="memo-paragraph-content with-compressed with-expanded">
                                <span class="memo-paragraph-topic-inline">๓.ข้อพิจารณา</span>
                                ในการนี้ โรงพยาบาลเทพรัตน์นครราชสีมา พิจารณาแล้ว จึงขออนุมัติแต่งตั้ง
                                
                                @if (count($committees) == 1)
                                    <!-- // คณะกรรมการ 1 คน -->
                                    <span>
                                        <span>{{ $committees[0]->person->prefix->prefix_name.$committees[0]->person->person_firstname.' '.$committees[0]->person->person_lastname }}</span>
                                        <span>ตำแหน่ง {{ $committees[0]->person->position->position_name }}{{ $committees[0]->person->academic ? $committees[0]->person->academic->ac_name : '' }}</span>
                                    </span>
                                    เป็นผู้รับผิดชอบจัดทำรายละเอียด คุณลักษณะเฉพาะและราคากลางการซื้อ <span>{{ $support->category->name }}</span>
                                    จำนวน <span>{{ thainumDigit(count($support->details)) }} รายการ</span>
                                    เพื่อใช้ในการปฏิบัติงานของเจ้าหน้าที่ โดยมีหน้าที่จัดทำรายละเอียดคุณลักษณะเฉพาะของพัสดุรวมทั้งกำหนดหลักเกณฑ์พิจารณาคัดเลือก ข้อเสนอด้วย</span>
                                @else
                                    <!-- // คณะกรรมการมากกว่า 1 คน -->
                                    <p>
                                        <span>{{ $committee->person->prefix->prefix_name.$committee->person->person_firstname.' '.$committee->person->person_lastname }}</span>
                                        <span>ตำแหน่ง {{ $committee->person->position->position_name }}{{ $committee->person->academic ? $committee->person->academic->ac_name : '' }}</span>
                                        เป็นผู้รับผิดชอบจัดทำรายละเอียดคุณลักษณะเฉพาะและราคากลางการซื้อ<span>{{ $support->category->name }}
                                        จำนวน <span>{{ thainumDigit(count($details)) }} รายการ</span>
                                        เพื่อใช้ในการปฏิบัติงานของเจ้าหน้าที่โดยมีหน้าที่จัดทำรายละเอียดคุณลักษณะเฉพาะของพัสดุ รวมทั้งกำหนด หลักเกณฑ์พิจารณาคัดเลือกข้อเสนอด้วย
                                    </p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="memo-paragraph-content with-compressed with-expanded">
                                <span class="memo-paragraph-topic-inline">๔.ข้อเสนอ</span>
                                จึงเรียนมาเพื่อโปรดพิจารณา หากเห็นชอบแล้วขอได้โปรดอนุมัติแต่งตั้งผู้จัดทำ
                                รายละเอียดคุณลักษณะเฉพาะและราคากลาง รวมทั้งหลักเกณฑ์การพิจารณาคัดเลือกข้อเสนอตามรายชื่อที่เสนอ
                                ในข้อ ๓ ต่อไป
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="signature-approver" style="top: 93%;">
                                <p style="margin: 10px 0 20px 0; font-weight: bold;">
                                    <span style="margin: 0;">ชอบ/อนุมัติ</span>
                                </p>
                                <p style="margin: 40px 0 0;">
                                    ( นายชวศักดิ์  กนกกัณฑพงษ์ )
                                </p>
                                <p style="margin: 0;">
                                    ผู้อำนวยการโรงพยาบาลเทพรัตน์นครราชสีมา
                                </p>
                                <p style="margin: 0;">
                                    ปฏิบัติราขการแทน ผู้ว่าราชการจังหวัดนครราชสีมา
                                </p>
                            </div>
                        </td>
                        <td colspan="2" style="text-align: center; padding: 5px;">
                            <div class="signature">
                                <p style="margin: 30px 0 0;">
                                    ( {{ $support->officer->prefix->prefix_name.$support->officer->person_firstname. ' ' .$support->officer->person_lastname }} )
                                </p>
                                <!-- <p style="margin: 0;">
                                    <span>{{ $headOfDepart->position->position_name }}{{ $headOfDepart->academic ? $headOfDepart->academic->ac_name : '' }}</span>
                                </p> -->
                                <p style="margin: 0;">
                                    เจ้าหน้าที่พัสดุ
                                </p>
                            </div>
                            <div class="signature">
                                <p style="margin: 30px 0 0;">
                                    ( {{ $headOfFaction->prefix->prefix_name.$headOfFaction->person_firstname. ' ' .$headOfFaction->person_lastname }} )
                                </p>
                                <p style="margin: 0;">
                                    <span>{{ $headOfFaction->position->position_name }}{{ $headOfFaction->academic ? $headOfFaction->academic->ac_name : '' }}</span>
                                </p>
                                <!-- <p style="margin: 0;">
                                    หัวหน้าเจ้าหน้าที่
                                </p> -->
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>