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
                                @if(strlen($project->depart->depart_name) < 120)
                                    <div class="content__header-text" style="width: 87%;">
                                @else
                                    <div class="content__header-text" style="width: 88%; font-size: 18.5px;">
                                @endif
                                    <span style="margin: 0 0 0 2px;">
                                        {{ $project->owner_depart != 37 ? $project->depart->depart_name : 'กลุ่มงานการพยาบาลด้านการควบคุมและป้องกันการติดเชื้อฯ' }}
                                    </span>
                                    <span style="margin: 0 0 0 2px;">โรงพยาบาลเทพรัตน์นครราชสีมา</span>
                                    <span style="margin: 0 0 0 2px;">
                                        โทร {{ thainumDigit($project->depart->tel_no) }}
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%;">
                            <div class="content-header">
                                <span class="content__header-topic">ที่</span>
                                <div class="content__header-text" style="width: 95%;">
                                    <span style="margin: 0 5px;">
                                        {{ thainumDigit($project->depart->memo_no.'/') }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="content-header">
                                <span class="content__header-topic">วันที่</span>
                                <div class="content__header-text" style="width: 88%;">
                                    <span style="margin: 0 10px;">
                                        {{ '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.thainumDigit(convDbDateToLongThMonth(date('Y-m-d'))) }}
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
                                    <span style="margin-left: 5px;">ขออนุมัติดำเนินโครงการ</span>
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
                            <?php
                                $faction = '';
                                $department = '';
                                if($project->depart->faction_id == '7') {
                                    $faction .= ' กลุ่มภารกิจด้านพัฒนาระบบบริการฯ';
                                } else {
                                    $faction .= $project->depart->faction->faction_name;
                                }

                                $department = $project->depart->depart_name.' ';
                                $departNameLen = strlen($department.' '.$faction);
                            ?>

                            <div class="memo-paragraph with-expanded">
                                ตามที่ <span class="{{ ($departNameLen > 220) ? 'with-expanded-3x' : '' }}">{{ $department.$faction }}</span>
                                ได้รับอนุมัติให้จัดทำ {{ thainumDigit($project->project_name) }}                                
                                รหัสโครงการ 
                                @if(!empty($project->project_no))
                                    <span style="margin: 0;">{{ thainumDigit($project->project_no) }}</span>
                                @else
                                    <span class="dot">............................</span>
                                @endif
                                งบประมาณสนับสนุนจาก <span>{{ thainumDigit($project->budgetSrc->name) }}โรงพยาบาลฯ</span>
                                ปีงบประมาณ <span>{{ thainumDigit($project->year) }}</span>
                                จำนวน <span>{{ thainumDigit(number_format($project->total_budget, 2)) }}</span> บาท
                                <span>({{ $project->total_budget_str }})</span>
                                โดยเป็นโครงการระดับ <span>{{ $project->projectType->name }}</span> นั้น
                                <!-- โดยมีวัตถุประสงค์เพื่อ <span>{{ thainumDigit($project->remark) }}</span> นั้น -->
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="memo-paragraph with-expanded">
                                ในการนี้ <span class="{{ ($departNameLen > 200 && $departNameLen <= 220) ? 'with-compressed-2x' : ($departNameLen > 220 ? 'with-expanded-2x' : '') }}">{{ $department.$faction }}</span>
                                จึงขออนุมัติดำเนิน{{ thainumDigit($project->project_name) }}
                                @if(strlen($project->project_name) > 250 || strlen($project->project_name) <= 130)
                                    {{ 'ตามเอกสารที่แนบมาพร้อมนี้' }}
                                @elseif(strlen($project->project_name) > 220 && strlen($project->project_name) <= 250)
                                    {{ 'ตามเอกสารที่แนบมา พร้อมนี้' }}
                                @elseif(strlen($project->project_name) > 200 && strlen($project->project_name) <= 220)
                                    {{ 'ตามเอกสาร ที่แนบมาพร้อมนี้' }}
                                @elseif(strlen($project->project_name) > 130 && strlen($project->project_name) <= 170)
                                    {{ 'ตามเอกสารที่แนบ มาพร้อมนี้' }}
                                @else
                                    {{ 'ตามเอกสาร ที่แนบ มาพร้อมนี้' }}
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="memo-paragraph" style="padding-top: 10px;">
                                จึงเรียนมาเพื่อโปรดทราบและพิจารณาอนุมัติ
                            </div>
                        </td>
                    </tr>

                    <!-- =========================== Owner =========================== -->
                    <!-- <tr>
                        <td></td>
                        <td colspan="3" style="text-align: center; padding: 5px;">
                            <div class="signature">
                                <p style="margin: 30px 0 0;">
                                    ( {{ $project->owner->prefix->prefix_name.$project->owner->person_firstname. ' ' .$project->owner->person_lastname }} )
                                </p>
                                <p style="margin: 0;">
                                    <span>{{ $project->owner->position->position_name }}{{ $project->owner->academic ? $project->owner->academic->ac_name : '' }}</span>
                                </p>
                                <p style="margin: 0;">
                                    ผู้รับผิดชอบโครงการ
                                </p>
                            </div>
                        </td>
                    </tr> -->
                    
                    <!-- =========================== Head of depart =========================== -->
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
                                    หัวหน้า{{ $project->owner_depart != 37 ? $project->depart->depart_name : 'กลุ่มงานการพยาบาลด้านการควบคุมและป้องกันการติดเชื้อฯ' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- =========================== Head of faction =========================== -->
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
                                @if($project->depart->faction_id == '7')
                                    <p style="margin: 0;">
                                        หัวหน้ากลุ่มภารกิจด้านพัฒนาระบบบริการและ
                                    </p>
                                    <p style="margin: 0;">
                                        สนับสนุนบริการสุขภาพ
                                    </p>
                                @else
                                    <p style="margin: 0;">
                                        หัวหน้า{{ $project->depart->faction->faction_name }}
                                    </p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    
                    <!-- =========================== Director =========================== -->
                    <tr>
                        <td></td>
                        <td colspan="3" style="text-align: center; padding: 5px;">
                            <p class="approval-checkbox">
                                <span style="margin: 0;">[&nbsp;&nbsp;] อนุมัติ</span>
                                <span style="margin: 20px;">[&nbsp;&nbsp;] ไม่อนุมัติ</span>
                            </p>
                            <div class="signature">
                                <p style="margin: 30px 0 0;">
                                    ( นายชวศักดิ์  กนกกันฑพงษ์ )
                                </p>
                                <p style="margin: 0;">
                                    ผู้อำนวยการโรงพยาบาลเทพรัตน์นครราชสีมา
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <p class="print-options">พิมพ์จากระบบ E-Plan เมื่อ {{ date('Y-m-d H:i:s') }}</p>
        </div>
    </body>
</html>