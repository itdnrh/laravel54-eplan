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

                <?php $orderType = ''; ?>
                @if($withdrawal->inspection->order->orderType->id == '1')
                    <?php $orderType = 'จัดซื้อ'; ?>
                @elseif($withdrawal->inspection->order->orderType->id == '2')
                    <?php $orderType = 'จัด'; ?>
                @else
                    <?php $orderType = 'สัญญา'; ?>
                @endif

                <table class="layout">
                    <tr>
                        <td colspan="4">
                            <div class="content-header">
                                <span class="content__header-topic">ส่วนราชการ</span>
                                <div class="content__header-text" style="width: 87%;">
                                    <!-- <span style="margin: 0 5px;">{{ thainumDigit($departOfParcel->depart_name) }}</span>
                                    <span style="margin: 0 5px;">โรงพยาบาลเทพรัตน์นครราชสีมา</span>
                                    โทร <span style="margin: 0 5px;">{{ thainumDigit('0 4439 5000 ต่อ '.$departOfParcel->tel_no) }}</span> -->
                                    <span>{{ thainumDigit($departOfParcel->depart_name) }}
                                        
                                        @if($departOfParcel->faction_id == '1')
                                            กลุ่มภารกิจด้านอำนวยการ 
                                        @elseif($departOfParcel->faction_id == '2')
                                            กลุ่มภารกิจด้านบริการทุติยภูมิและตติยภูมิ
                                        @elseif($departOfParcel->faction_id == '3')
                                            กลุ่มภารกิจด้านบริการปฐมภูมิ
                                        @elseif($departOfParcel->faction_id == '7')
                                            กรภ.พรส.
                                        @elseif($departOfParcel->faction_id == '5')
                                        กลุ่มภารกิจด้านการพยาบาล
                                        @else
                                            {{$departOfParcel->faction_id}}
                                        @endif
                                        โรงพยาบาลเทพรัตน์นครราชสีมา
                                    <span>โทร. {{ thainumDigit($departOfParcel->tel_no) }}</span>

                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%;">
                            <div class="content-header">
                                <span class="content__header-topic">ที่</span>
                                <div class="content__header-text" style="width: 95%;">
                                    <span style="margin: 0 5px;">{{ thainumDigit($departOfParcel->memo_no.'/')  }}</span>
                                </div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="content-header">
                                <span class="content__header-topic">วันที่</span>
                                <div class="content__header-text" style="width: 88%;">
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
                                <div class="content__header-text" style="width: 94%;">
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
                                จังหวัดนครราชสีมา โดยโรงพยาบาลเทพรัตน์นครราชสีมา ได้ตกลง{{ $orderType }}<span>{{ $planType->plan_type_name }}</span>
                                จำนวน <span>{{ thainumDigit(count($withdrawal->inspection->order->details)) }}</span> รายการ
                                เป็นเงินทั้งสิ้น <span>{{ thainumDigit(number_format($withdrawal->net_total, 2)) }} บาท</span>
                                (<span>{{ baht_text($withdrawal->net_total) }}</span>) กับ <span>{{ $withdrawal->supplier->supplier_name }}</span>
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
                                เป็นเงินทั้งสิ้น <span>{{ thainumDigit(number_format($withdrawal->net_total, 2)) }} บาท</span>
                                <span>({{ baht_text($withdrawal->net_total) }})</span>
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
                                    ( {{ $officer->prefix->prefix_name.$officer->person_firstname. ' ' .$officer->person_lastname }} )
                                </p>
                                <p style="margin: 0;">
                                    <span>{{ $officer->position->position_name }}{{ $officer->academic ? $officer->academic->ac_name : '' }}</span>
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
                                    ( นายชวศักดิ์  กนกกันฑพงษ์ )
                                </p>
                                <p style="margin: 0;">
                                    ผู้อำนวยการโรงพยาบาลเทพรัตน์นครราชสีมา
                                </p>
                                <p style="margin: 0;">
                                    ปฏิบัติราชการแทน ผู้ว่าราชการจังหวัดนครราชสีมา
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="page-break"></div>

        <!-- ==================================== PAGE 2 ==================================== -->
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
                                <div class="content__header-text" style="width: 87%;">
                                    <!-- <span style="margin: 0 5px;">{{ thainumDigit($departOfParcel->depart_name) }}</span>
                                    <span style="margin: 0 5px;">โรงพยาบาลเทพรัตน์นครราชสีมา</span>
                                    โทร <span style="margin: 0 5px;">{{ thainumDigit('0 4439 5000 ต่อ '.$departOfParcel->tel_no) }}</span> -->
                                    <span>{{ thainumDigit($departOfParcel->depart_name) }}
                        
                                        @if($departOfParcel->faction_id == '1')
                                            กลุ่มภารกิจด้านอำนวยการ 
                                        @elseif($departOfParcel->faction_id == '2')
                                            กลุ่มภารกิจด้านบริการทุติยภูมิและตติยภูมิ
                                        @elseif($departOfParcel->faction_id == '3')
                                            กลุ่มภารกิจด้านบริการปฐมภูมิ
                                        @elseif($departOfParcel->faction_id == '7')
                                            กรภ.พรส.
                                        @elseif($departOfParcel->faction_id == '5')
                                        กลุ่มภารกิจด้านการพยาบาล
                                        @else
                                            {{$departOfParcel->faction_id}}
                                        @endif
                                        โรงพยาบาลเทพรัตน์นครราชสีมา
                                    <span>โทร. {{ thainumDigit($departOfParcel->tel_no) }}</span>

                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%;">
                            <div class="content-header">
                                <span class="content__header-topic">ที่</span>
                                <div class="content__header-text" style="width: 95%;">
                                    <span style="margin: 0 5px;">{{ thainumDigit($departOfParcel->memo_no.'/')  }}</span>
                                </div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="content-header">
                                <span class="content__header-topic">วันที่</span>
                                <div class="content__header-text" style="width: 88%;">
                                    <span style="margin: 0 10px;">
                                        {{ $withdrawal->completed == '1' ? thainumDigit(convDbDateToLongThDate($withdrawal->withdraw_date)) : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.thainumDigit($withdrawal->withdraw_month) }}
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
                            <p class="memo-paragraph with-expanded">
                                ตามบันทึก ที่ <span>{{ thainumDigit($withdrawal->inspection->order->po_app_no) }}</span>
                                ลงวันที่ <span>{{ thainumDigit(convDbDateToLongThDate($withdrawal->inspection->order->po_app_date)) }}</span>
                                จังหวัดนครราชสีมา ได้อนุมัติให้{{ $orderType }}{{ $withdrawal->inspection->order->category->name }}
                                เป็นเงินทั้งสิ้น {{ thainumDigit(number_format($withdrawal->net_total, 2)) }} บาท
                                <span>({{ baht_text($withdrawal->net_total) }})</span>
                                โดยเบิกจ่ายจาก{{ $withdrawal->inspection->order->budgetSource->name }} โรงพยาบาลเทพรัตน์นครราชสีมา
                                ปีงบประมาณ <span>{{ thainumDigit($withdrawal->inspection->order->year) }}</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph with-expanded">
                                {{ $withdrawal->supplier->supplier_name }}
                                ได้ส่งมอบ{{ $planType->plan_type_name }}
                                จำนวน <span>{{ thainumDigit(count($withdrawal->inspection->order->details)) }}</span> รายการ
                                และคณะกรรมการตรวจรับพัสดุ ได้ทำการตรวจรับไว้เป็นการถูกต้อง ครบถ้วน และไม่มีค่าปรับแล้ว เมื่อ
                                <span>วันที่ {{ thainumDigit(convDbDateToLongThDate($withdrawal->inspection->inspect_sdate)) }}</span>
                                ดังรายละเอียดในใบส่งมอบงาน และใบตรวจรับพัสดุที่แนบมาพร้อมนี้
                                @if($withdrawal->prepaid_person != '')
                                    โดย <span>{{ $withdrawal->prepaid->prefix->prefix_name.$withdrawal->prepaid->person_firstname. ' ' .$withdrawal->prepaid->person_lastname }}</span>
                                    ตำแหน่ง {{ $withdrawal->prepaid->position->position_name }}{{ $withdrawal->prepaid->academic ? $withdrawal->prepaid->academic->ac_name : '' }}
                                    ได้สำรองเงินจ่ายไปก่อนแล้ว
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph with-compressed with-expanded">
                                จึงเรียนมาเพื่อโปรดพิจารณา <span>หากเห็นชอบขอได้โปรดให้กลุ่มงานพัสดุส่งเอกสารเบิกจ่ายให้</span>
                                <span>กลุ่มงานการเงิน ต่อไป</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="3" style="text-align: center; padding: 5px;">
                            <div class="signature">
                                <p style="margin: 40px 0 0;">
                                    ( {{ $headOfDepart->prefix->prefix_name.$headOfDepart->person_firstname. ' ' .$headOfDepart->person_lastname }} )
                                </p>
                                <p style="margin: 0;">
                                    <span>{{ $headOfDepart->position->position_name }}{{ $headOfDepart->academic ? $headOfDepart->academic->ac_name : '' }}</span>
                                </p>
                                <p style="margin: 0;">
                                    หัวหน้ากลุ่มงานพัสดุ
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
                                    หัวหน้ากลุ่มภารกิจด้านอำนวยการ
                                </p>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center; padding: 0;">
                            <p style="margin: 10px 0 20px 0; font-weight: bold;">
                                <span style="margin: 0;">ทราบ</span>
                                <span style="margin: 20px 5px;">/&nbsp; อนุมัติ</span>
                            </p>
                            <p style="margin: 40px 0 0;">
                                ( นายชวศักดิ์  กนกกันฑพงษ์ )
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
        <div class="page-break"></div>

        <!-- ==================================== PAGE 3 ==================================== -->
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
                                <div class="content__header-text" style="width: 87%;">
                                    <!-- <span style="margin: 0 5px;">{{ thainumDigit($departOfParcel->depart_name) }}</span>
                                    <span style="margin: 0 5px;">โรงพยาบาลเทพรัตน์นครราชสีมา</span>
                                    โทร <span style="margin: 0 5px;">{{ thainumDigit('0 4439 5000 ต่อ '.$departOfParcel->tel_no) }}</span> -->
                                    <span>{{ thainumDigit($departOfParcel->depart_name) }}
                        
                                        @if($departOfParcel->faction_id == '1')
                                            กลุ่มภารกิจด้านอำนวยการ 
                                        @elseif($departOfParcel->faction_id == '2')
                                            กลุ่มภารกิจด้านบริการทุติยภูมิและตติยภูมิ
                                        @elseif($departOfParcel->faction_id == '3')
                                            กลุ่มภารกิจด้านบริการปฐมภูมิ
                                        @elseif($departOfParcel->faction_id == '7')
                                            กรภ.พรส.
                                        @elseif($departOfParcel->faction_id == '5')
                                        กลุ่มภารกิจด้านการพยาบาล
                                        @else
                                            {{$departOfParcel->faction_id}}
                                        @endif
                                        โรงพยาบาลเทพรัตน์นครราชสีมา
                                    <span>โทร. {{ thainumDigit($departOfParcel->tel_no) }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%;">
                            <div class="content-header">
                                <span class="content__header-topic">ที่</span>
                                <div class="content__header-text" style="width: 95%;">
                                    <span style="margin: 0 5px;">{{ thainumDigit($departOfParcel->memo_no.'/')  }}</span>
                                </div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="content-header">
                                <span class="content__header-topic">วันที่</span>
                                <div class="content__header-text" style="width: 88%;">
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
                                <div class="content__header-text" style="width: 94%;">
                                    <span style="margin-left: 5px;">รายงานผลการพิจารณารายละเอียดวิธีการและขั้นตอนการจัดซื้อจัดจ้าง</span>
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
                            <p class="memo-paragraph-topic">
                                ๑. เรื่องเดิม
                            </p>
                            <p class="memo-paragraph-content with-compressed with-expanded">
                                ตามบันทึก ที่ <span>{{ thainumDigit($withdrawal->inspection->order->po_req_no) }}</span>
                                ลงวันที่ <span>{{ thainumDigit(convDbDateToLongThDate($withdrawal->inspection->order->po_req_date)) }}</span>
                                จังหวัดนครราชสีมา ได้เห็นชอบให้{{ $orderType }}<span>{{ $planType->plan_type_name }}</span>
                                จำนวน <span>{{ thainumDigit(count($withdrawal->inspection->order->details)) }}</span> รายการ
                                กับ <span>{{ $withdrawal->supplier->supplier_name }}</span>
                                จำนวนเงิน <span>{{ thainumDigit(number_format($withdrawal->net_total, 2)) }} บาท</span>
                                <span>({{ baht_text($withdrawal->net_total) }})</span>
                                โดยเบิกจ่ายจาก <span style="margin: 0;">{{ $withdrawal->inspection->order->budgetSource->name }}</span>โรงพยาบาลเทพรัตน์นครราชสีมา
                                ปีงบประมาณ <span>{{ thainumDigit($withdrawal->inspection->order->year) }}</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph-topic">
                                ๒. ข้อกฎหมาย/ระเบียบ
                            </p>
                            <p class="memo-paragraph-content with-compressed with-expanded">
                                <span>ระเบียบกระทรวงการคลังว่าด้วยการจัดซื้อจัดจ้างและการบริหารพัสดุภาครัฐ พ.ศ. ๒๕๖๐ และที่แก้ไขเพิ่มเติม</span> 
                                <span>ข้อ ๑๖ เมื่อสิ้นสุดกระบวนการจัดซื้อจัดจ้างในแต่ละโครงการ ให้หน่วยงานของรัฐจัดให้มีการ</span>
                                <span>บันทึกรายงาน พิจารณารายละเอียด วิธีการและขั้นตอนการจัดซื้อจัดจ้างพร้อมทั้งเอกสารหลักฐานประกอบ</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph-topic">
                                ๓. ข้อพิจารณา
                            </p>
                            <p class="memo-paragraph-content with-expanded">
                                <span>เพื่อให้การดำเนินการเป็นไปตามระเบียบกระทรวงการคลังว่าด้วยการจัดซื้อจัดจ้างและการ </span>
                                <span>บริหารพัสดุภาครัฐ พ.ศ. ๒๕๖๐ และที่แก้ไขเพิ่มเติม ข้อ ๑๖ จึงขอรายงานผล การพิจารณารายละเอียด</span>
                                <span>วิธีการและขั้นตอนการจัดซื้อจัดจ้าง{{ $planType->plan_type_name }}</span>
                                จำนวน <span>{{ thainumDigit(count($withdrawal->inspection->order->details)) }}</span> รายการ
                                พร้อมทั้งหลักฐานประกอบ ตามรายการดังต่อไปนี้
                                <p class="memo-paragraph-content">๑) บันทึกรายงานขอจ้าง</p>
                                <p class="memo-paragraph-content">๒) <span>{{ $withdrawal->inspection->deliver_bill }}</span></p>
                                <p class="memo-paragraph-content">๓) ใบตรวจรับพัสดุ</p>
                                <p class="memo-paragraph-content">๔) บันทึกรายงานการตรวจรับพัสดุ</p>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph-topic">
                                ๔. ข้อเสนอ
                            </p>
                            <p class="memo-paragraph-content with-compressed with-expanded">
                                จึงเรียนมาเพื่อโปรดทราบ
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="3" style="text-align: center; padding: 5px;">
                            <div class="signature">
                                <p style="margin: 10px 0 0;">
                                    ( {{ $officer->prefix->prefix_name.$officer->person_firstname. ' ' .$officer->person_lastname }} )
                                </p>
                                <p style="margin: 0;">
                                    <span>{{ $officer->position->position_name }}{{ $officer->academic ? $officer->academic->ac_name : '' }}</span>
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
                            <div class="signature-approver" style="top: 88%">
                                <p style="margin: 10px 0 20px 0; font-weight: bold;">
                                    <span style="margin: 0;">ทราบ</span>
                                </p>
                                <p style="margin: 40px 0 0;">
                                    ( นายชวศักดิ์  กนกกันฑพงษ์ )
                                </p>
                                <p style="margin: 0;">
                                    ผู้อำนวยการโรงพยาบาลเทพรัตน์นครราชสีมา
                                </p>
                                <p style="margin: 0;">
                                    ปฏิบัติราชการแทน ผู้ว่าราชการจังหวัดนครราชสีมา
                                </p>
                            </div>
                            <div class="signature">
                                <p style="margin: 20px 0 0;">
                                    ( {{ $headOfFaction->prefix->prefix_name.$headOfFaction->person_firstname. ' ' .$headOfFaction->person_lastname }} )
                                </p>
                                <p style="margin: 0;">
                                    <span>{{ $headOfFaction->position->position_name }}{{ $headOfFaction->academic ? $headOfFaction->academic->ac_name : '' }}</span>
                                </p>
                                <p style="margin: 0;">
                                    หัวหน้าเจ้าหน้าที่
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>