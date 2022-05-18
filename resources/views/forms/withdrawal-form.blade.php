<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>บันทึกขอสนับสนุน</title>
        <link rel="stylesheet" href="{{ asset('/css/pdf.css') }}">
    </head>
    <body style="font-size: 16pt;">
        <div class="memo-container">
            <div class="header">
                <p style="margin: 0px;">
                    <img src="{{ asset('/img/krut.jpg') }}" alt="krut" />
                </p>
                <h2>บันทึกข้อความ</h2>
            </div>
            <div class="content">
                <table style="width: 100%;">
                    <tr>
                        <td colspan="4">
                            <div class="content-header">
                                <span class="content__header-topic">ส่วนราชการ</span>
                                <div class="content__header-text" style="width: 77.7%; margin-left: 70px;">
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
                                    <span style="margin: 0 10px;">{{ thainumDigit(convDbDateToLongThDate($withdrawal->withdraw_date)) }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="content-header">
                                <span class="content__header-topic">เรื่อง</span>
                                <div class="content__header-text" style="width: 85%; margin-left: 28px;">
                                    <span style="margin-left: 5px;">ขอส่งเอกสารเบิกจ่ายเงิน</span>
                                </div>
                            </div>
                            <div style="margin: 5px 0; padding: 0;">
                                <span style="font-size: 20px;">เรียน</span>
                                <span style="margin-left: 5px;">ผู้อำนวยการโรงพยาบาลเทพรัตน์นครราชสีมา</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph">
                                ตามบันทึก ที่ <span>{{ thainumDigit($withdrawal->inspection->order->po_app_no) }}</span>
                                ลงวันที่ <span>{{ thainumDigit(convDbDateToLongThDate($withdrawal->inspection->order->po_app_date)) }}</span>
                                จังหวัดนครราชสีมา ได้อนุมัติให้สั่งเป็นเงินทั้งสิ้น <span>{{ thainumDigit(number_format($withdrawal->inspection->order->net_total)) }} บาท</span>
                                (<span>{{ $withdrawal->inspection->order->net_total_str }}</span>)
                                โดยเบิกจ่ายจาก <span style="margin: 0;">{{ $withdrawal->inspection->order->budgetSource->name }}</span>โรงพยาบาลเทพรัตน์นครราชสีมา
                                ปีงบประมาณ <span>{{ thainumDigit($withdrawal->inspection->order->year) }}</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph">
                                <span>{{ $withdrawal->supplier->supplier_name }}</span>
                                ได้ส่งมอบ <span>{{ thainumDigit($withdrawal->inspection->order->desc) }}</span> 
                                และคณะกรรมการตรวจรับพัสดุได้ทำการตรวจรับ
                                ไว้เป็นการถูกต้อง ครบถ้วน และไม่มีค่าปรับแล้ว เมื่อวันที่
                                <span>{{ thainumDigit(convDbDateToLongThDate($withdrawal->inspection->inspect_sdate)) }}</span>
                                ดังรายละเอียดในใบส่งมอบงาน และใบตรวจรับพัสดุที่แนบมาพร้อมนี้
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph">
                                จึงเรียนมาเพื่อโปรดทราบ หากเห็นชอบโปรดอนุมัติให้ส่งเอกสารเบิกจ่ายเงินให้กลุ่มงานการเงินต่อไป
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center; padding-top: 10px;"></td>
                        <td colspan="2" style="text-align: center; padding: 10px;">
                            <p style="margin: 40px 0 0;">
                                ( {{ $headOfDepart->prefix->prefix_name.$headOfDepart->person_firstname. ' ' .$headOfDepart->person_lastname }} )
                            </p>
                            <p style="margin: 0;">
                                <span>{{ $headOfDepart->position->position_name }}{{ $headOfDepart->academic ? $headOfDepart->academic->ac_name : '' }}</span>
                            </p>
                            <p style="margin: 0;">
                                หัวหน้ากลุ่มงานพัสดุ
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2" style="text-align: center; padding: 0;">
                            <p style="margin: 40px 0 0;">
                                ( {{ $headOfFaction->prefix->prefix_name.$headOfFaction->person_firstname. ' ' .$headOfFaction->person_lastname }} )
                            </p>
                            <p style="margin: 0;">
                                <span>{{ $headOfFaction->position->position_name }}{{ $headOfFaction->academic ? $headOfFaction->academic->ac_name : '' }}</span>
                            </p>
                            <p style="margin: 0;">
                                หัวหน้ากลุ่มภารกิจด้านอำนวยการ
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center; padding: 0;">
                            <p style="margin: 10px 0 20px 0; font-weight: bold;">
                                <span style="margin: 0;">ทราบ</span>
                                <span style="margin: 20px 5px;">/&nbsp; อนุมัติ</span>
                            </p>
                            <p style="margin: 0;">
                                ( นายชวศักดิ์  กนกกัณฑพงษ์ )
                            </p>
                            <p style="margin: 0;">
                                ผู้อำนวยการโรงพยาบาลเทพรัตน์นครราชสีมา
                            </p>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>