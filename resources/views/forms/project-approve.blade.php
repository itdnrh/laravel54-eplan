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
                                        {{ '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.thainumDigit($withdrawal->withdraw_month) }}
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
                                    <span style="margin-left: 5px;">รายงานผลการตรวจรับ</span>
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
                            <p class="memo-paragraph with-expanded">
                                จังหวัดนครราชสีมา โดยโรงพยาบาลเทพรัตน์นครราชสีมา ได้ตกลง<span>{{ $planType->plan_type_name }}</span>
                                จำนวน <span>{{ thainumDigit(count($withdrawal->inspection->order->details)) }}</span> รายการ
                                เป็นเงินทั้งสิ้น <span>{{ thainumDigit(number_format($withdrawal->inspection->order->net_total)) }} บาท</span>
                                (<span>{{ $withdrawal->inspection->order->net_total_str }}</span>) กับ <span>{{ $withdrawal->supplier->supplier_name }}</span>
                                โดยเบิกจ่ายจาก <span style="margin: 0;">{{ $withdrawal->inspection->order->budgetSource->name }}</span>โรงพยาบาลเทพรัตน์นครราชสีมา
                                ปีงบประมาณ <span>{{ thainumDigit($withdrawal->inspection->order->year) }}</span>
                                ตามรายละเอียดใน<span>{{ $withdrawal->inspection->order->orderType->name }}</span>
                                เลขที่ <span>{{ thainumDigit($withdrawal->inspection->order->po_no) }}</span>
                                ลงวันที่ <span>{{ thainumDigit(convDbDateToLongThDate($withdrawal->inspection->order->po_date)) }}</span> นั้น
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph with-expanded">
                                บัดนี้ <span>{{ $withdrawal->supplier->supplier_name }}</span>
                                ได้ดำเนินการส่งมอบ<span>{{ $planType->plan_type_name }}</span>
                                จำนวน <span>{{ thainumDigit(count($withdrawal->inspection->order->details)) }}</span> รายการ
                                เป็นเงินทั้งสิ้น <span>{{ thainumDigit(number_format($withdrawal->inspection->order->net_total)) }} บาท</span>
                                (<span>{{ $withdrawal->inspection->order->net_total_str }}</span>)
                                ดังกล่าวเรียบร้อยแล้ว และคณะกรรมการตรวจรับพัสดุได้ทำการตรวจรับ
                                ไว้เป็นการถูกต้อง ครบถ้วน และไม่มีค่าปรับ ตาม<span>{{ $withdrawal->inspection->order->orderType->name }}</span>
                                เมื่อวันที่
                                <span>{{ thainumDigit(convDbDateToLongThDate($withdrawal->inspection->inspect_sdate)) }}</span>
                                ดังรายละเอียดในใบส่งมอบงาน และใบรายงานการตรวจรับพัสดุที่แนบมาพร้อมนี้
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph">
                                จึงเรียนมาเพื่อโปรดทราบ
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="3" style="text-align: center; padding: 5px;">
                            <div class="signature">
                                <p style="margin: 30px 0 0;">
                                    ( {{ $headOfDepart->prefix->prefix_name.$headOfDepart->person_firstname. ' ' .$headOfDepart->person_lastname }} )
                                </p>
                                <p style="margin: 0;">
                                    <span>{{ $headOfDepart->position->position_name }}{{ $headOfDepart->academic ? $headOfDepart->academic->ac_name : '' }}</span>
                                </p>
                                <p style="margin: 0;">
                                    เจ้าหน้าที่
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="3" style="text-align: center; padding: 5px;">
                            <div class="signature">
                                <p style="margin: 40px 0 0;">
                                    ( {{ $headOfFaction->prefix->prefix_name.$headOfFaction->person_firstname. ' ' .$headOfFaction->person_lastname }} )
                                </p>
                                <p style="margin: 0;">
                                    <span>{{ $headOfFaction->position->position_name }}{{ $headOfFaction->academic ? $headOfFaction->academic->ac_name : '' }}</span>
                                </p>
                                <p style="margin: 0;">
                                    หัวหน้าเจ้าหน้าที่
                                </p>
                            </div>
                            <div class="signature-approver" style="top: 85%">
                                <p style="margin: 10px 0 20px 0; font-weight: bold;">
                                    <span style="margin: 0;">ทราบ</span>
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
                    </tr>
                </table>
            </div>
        </div>
        <div class="page-break"></div>
    </body>
</html>