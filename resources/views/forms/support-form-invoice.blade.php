<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>บันทึกขอสนับสนุน</title>
        <link rel="stylesheet" href="{{ asset('/css/pdf.css') }}">
    </head>
    <body>
        <div class="memo-container-narrow">
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
                                        @if($invoicedetail->depart_id == 37)
                                            <!-- {{ 'กลุ่มงานการพยาบาลด้านการควบคุมและป้องกันการติดเชื้อฯ' }} -->
                                            {{ str_replace('กลุ่มงาน', 'กง.', 'กลุ่มงานการพยาบาลด้านการควบคุมและป้องกันการติดเชื้อฯ') }}
                                        @elseif(in_array($invoicedetail->depart_id, [66,68]))
                                            <!-- {{ $invoicedetail->depart_name }} -->
                                            {{ str_replace('กลุ่มงาน', 'กง.', $invoicedetail->depart_name) }}
                                        @else
                                            <!-- {{ $invoicedetail->depart_name }} -->
                                            {{ str_replace('กลุ่มงาน', 'กง.', $invoicedetail->depart_name) }}
                                        @endif
                                        @if($invoicedetail->faction_id == '1')
                                            กรภ.ด้านอำนวยการ 
                                        @elseif($invoicedetail->faction_id == '2')
                                            กรภ.ทุติยภูมิและตติยภูมิ
                                        @elseif($invoicedetail->faction_id == '3')
                                            กรภ.ด้านบริการปฐมภูมิ
                                        @elseif($invoicedetail->faction_id == '7')
                                            กรภ.พรส.
                                        @elseif($invoicedetail->faction_id == '5')
                                            กรภ.ด้านการพยาบาล
                                        @else
                                            {{$invoicedetail->faction_name}}
                                        @endif
                                    </span>
                                    <!-- <span style="margin: 0 1px;"> {{$invoicedetail->faction_name}}</span> -->
                                    <span style="margin: 0 1px;">โรงพยาบาลเทพรัตน์นครราชสีมา</span>
                                    <span style="margin: 0 1px;">
                                        @if(in_array($invoicedetail->depart_id, [66,68]))
                                            โทร {{ thainumDigit($invoicedetail->tel_no) }}
                                        @else
                                            โทร {{ thainumDigit($invoicedetail->tel_no) }}
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
                                    <span style="margin: 0 5px;">{{ thainumDigit($invoicedetail->doc_no) }}</span>
                                </div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="content-header">
                                <span class="content__header-topic">วันที่</span>
                                <div class="content__header-text" style="width: 88%;">
                                    <span style="margin: 0 10px;">
                                        {{ $invoicedetail->doc_date == ''
                                                ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.thainumDigit(convDbDateToLongThMonth(date('Y-m-d')))
                                                : thainumDigit(convDbDateToLongThDate($invoicedetail->doc_date))
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
                                    <span>ขออนุมัติดำเนินการตามแผนเงินบำรุงโรงพยาบาล ปีงบประมาณ ๒๕๖๘</span>
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
                                ตามที่ <span>{{ $invoicedetail->depart_name }} {{ in_array($invoicedetail->depart_id, [66,68]) ? '('.$invoicedetail->ward_name.')' : '' }}</span>
                                ได้รับอนุมัติให้ 
                                ดำเนินงานตามแผนเงินบำรุงโรงพยาบาล ปีงบประมาณ ๒๕๖๘ หมวด <span style="margin: 0 5px;">{{ thainumDigit($invoicedetail->invoice_item_name) }}</span>
                                รายการ <span style="margin: 0 5px;">{{ thainumDigit($invoicedetail->invoice_detail_name) }}</span>
                                งบประมาณทั้งสิ้น {{ thainumDigit(number_format($invoicedetail->sum_price,2)) }} ({{baht_text($invoicedetail->sum_price)}})
                                เพื่อ {{ thainumDigit($invoicedetail->ivd_reason) }} 
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph-content">
                                <span>{{ $invoicedetail->depart_name }} {{ in_array($invoicedetail->depart_id, [66,68]) ? '('.$invoicedetail->ward_name.')' : '' }}</span>
                                จึงขออนุมัติจ่ายเงินค่าบริการตามแผนเงินบำรุง โรงพยาบาล ปีงบประมาณ ๒๕๖๘ รายการดังกล่าว ประจำเดือน {{ thainumDigit(convDbMonthIdToLonkThMonth($invoicedetail->ivd_month)) }} 
                                เป็นจำนวนเงินทั้งสิ้น {{ thainumDigit(number_format($invoicedetail->ivd_use_price,2)) }} ({{baht_text($invoicedetail->ivd_use_price)}})
                                {{ thainumDigit($invoicedetail->ivd_detail) }}  รายละเอียดตามเอกสารที่แนบเรียนมาพร้อมนี้
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                             <p style="margin: 0 0 0 80px;">
                                จึงเรียนมาเพื่อพิจารณา หากเห็นชอบโปรดอนุมัติ
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                    <td></td>
                    <td></td>
                        <td colspan="2" style="text-align: center; padding-top: 50px;">
                            <p style="margin: 0;">
                                หัวหน้ากลุ่มงาน<span class="dot">......................................................</span>
                            </p>
                            <p style="margin: 0;">
                                ( {{ $headOfDepart->prefix->prefix_name.$headOfDepart->person_firstname. ' ' .$headOfDepart->person_lastname }} )
                            </p>
                            <p style="margin: 0;">
                                <span>{{$headOfDepartPosition->full_position}}</span>
                                <!-- <span>{{ $headOfDepart->position->position_name }}{{ $headOfDepart->academic ? $headOfDepart->academic->ac_name : '' }}</span> -->
                            </p>
                        </td>
                    </tr>

                    <tr>
                    @if($invoicedetail->faction_id == '2')
                    <td colspan="2" style="text-align: center; padding-top: 50px;">
                    <p style="margin: 0 0 37px 0;"><span>เห็นควรอนุมัติ</span></p>
                            <p style="margin: 0;">
                                @if(empty($invoicedetail->head_of_faction))
                                    <span class="dot">......................................................</span>
                                @else
                                    <span class="dot">......................................................</span>
                                @endif
                            </p>
                            <p style="margin: 0;">
                                ( {{ $headOfFaction->prefix->prefix_name.$headOfFaction->person_firstname. ' ' .$headOfFaction->person_lastname }} )
                            </p>
                            <p style="margin: 0;">
                                <span>ประธานองค์กรแพทย์ รักษาราชการแทน</span>
                                <span>หัวหน้ากลุ่มภารกิจด้านบริการทุติยภูมิและตติยภูมิ</span>
                                <!-- <span>{{ $headOfFactionPosition->full_position }}</span> -->
                                <!-- <span>{{ $headOfFaction->position->position_name }}{{ $headOfFaction->academic ? $headOfFaction->academic->ac_name : '' }}</span> -->
                            </p>
                        </td>
                    @else
                    <td colspan="2" style="text-align: center; padding-top: 50px;">
                        <p style="margin: 0 0 37px 0;"><span>เห็นควรอนุมัติ</span></p>
                            <p style="margin: 0;">
                                @if(empty($invoicedetail->head_of_faction))
                                    หัวหน้ากลุ่มภารกิจ<span class="dot">......................................................</span>
                                @else
                                    <span class="dot">......................................................</span>
                                @endif
                            </p>
                            <p style="margin: 0 0 0 60px;">
                                ( {{ $headOfFaction->prefix->prefix_name.$headOfFaction->person_firstname. ' ' .$headOfFaction->person_lastname }} )
                            </p>
                            <p style="margin: 0 0 0 60px;">
                                <span>{{ $headOfFactionPosition->full_position }}</span>
                                <!-- <span>{{ $headOfFaction->position->position_name }}{{ $headOfFaction->academic ? $headOfFaction->academic->ac_name : '' }}</span> -->
                            </p>
                        </td>
                    @endif
                       
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                         <td></td>
                         <td></td>
                        <td colspan="2" style="text-align: center; padding-top: 50px;">
                        <p style="margin: 0 20px 37px 0;">
                        <!-- <span style="margin: 0 0 0 20px;">[&nbsp;&nbsp;] อนุมัติ</span>
                        <span style="margin: 20px;">[&nbsp;&nbsp;] ไม่อนุมัติ</span> -->
                        อนุมัติ
                    </p>
                    <p style="margin: 0;">
                        ( นายชวศักดิ์  กนกกันฑพงษ์ )
                    </p>
                    <p style="margin: 0;">
                        ผู้อำนวยการโรงพยาบาลเทพรัตน์นครราชสีมา
                    </p>
                         
        
                        </td>
                    </tr>

                </table>

                <!-- <div style="text-align: center; position: absolute;">
                    <p style="margin: 0 20px 37px 0;">
                        อนุมัติ
                    </p>
                    <p style="margin: 0;">
                        ( นายชวศักดิ์  กนกกันฑพงษ์ )
                    </p>
                    <p style="margin: 0;">
                        ผู้อำนวยการโรงพยาบาลเทพรัตน์นครราชสีมา
                    </p>
                </div> -->
            </div>
            <p class="print-options-left"><b>สำเนาเรียน</b> กลุ่มงานยุทธศาสตร์และแผนงานโครงการ, กลุ่มงานบัญชี, กลุ่มงานการเงิน</p>
            <p class="print-options">พิมพ์จากระบบ E-Plan เมื่อ {{ date('Y-m-d H:i:s') }}</p>
        </div>
    </body>
</html>