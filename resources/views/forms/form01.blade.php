<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>ใบลาป่วย ลาคลอด ลากิจส่วนตัว</title>
        <link rel="stylesheet" href="{{ asset('/css/pdf.css') }}">
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1 style="margin: 0px;">ใบลาป่วย ลาคลอด ลากิจส่วนตัว</h1>
            </div>
            <div class="content">
                <table style="width: 100%;">
                    <tr style="height: 20px">
                        <td style="width: 5%;"></td>
                        <td style="width: 30%;"></td>
                        <td style="width: 65%;" colspan="2">
                            <p style="margin: 0 0 0 100px;">
                                เขียนที่ <span style="margin: 0px;">{{ $places[$leave->leave_place] }}</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 10%;"></td>
                        <td style="width: 30%;"></td>
                        <td style="width: 30%;" colspan="2">
                            <p style="margin: 0 0 0 20px;">
                                วันที่ <span>{{ convDbDateToLongThDate($leave->leave_date) }}</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            เรื่อง <span>{{ $leave->leave_topic }}</span>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            เรียน <span>{{ $leave->leave_to }}</span>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p style="margin: 0 0 0 80px;">
                                ข้าพเจ้า 
                                <span class="text-val" style="margin-right: 50px;">
                                    {{ $leave->person->prefix->prefix_name.$leave->person->person_firstname. ' ' .$leave->person->person_lastname }}
                                </span>
                                ตำแหน่ง
                                <span class="text-val">
                                    {{ $leave->person->position->position_name }}{{ $leave->person->academic ? $leave->person->academic->ac_name : '' }}
                                </span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            สังกัด 
                            <span class="text-val" style="margin-right: 10px;">
                                {{ $leave->person->memberOf->depart->depart_name }}
                            </span>
                            โรงพยาบาลเทพรัตน์นครราชสีมา
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <span>
                                {{ $leave->leave_topic }}
                            </span>

                            @if ($leave->leave_type <> '4')
                                เนื่องจาก
                                <span class="text-val" style="margin-right: 10px;">
                                    {{ $leave->leave_reason }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            ตั้งแต่วันที่
                            <span class="text-val" style="margin-right: 50px;">
                                {{ convDbDateToLongThDate($leave->start_date) }}
                            </span>
                            ถึงวันที่ 
                            <span class="text-val" style="margin-right: 50px;">
                                {{ convDbDateToLongThDate($leave->end_date) }}
                            </span>
                            มีกำหนด <span class="text-val"> {{ $leave->leave_days }} </span> วัน
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            ข้าพเจ้าได้
                            @if(!empty($last))
                                <span class="text-val" style="margin-right: 10px;">
                                    {{ $last->type->name }}
                                </span>
                            @else
                                <span class="dot">...................</span>
                            @endif
                            ครั้งสุดท้ายตั้งแต่วันที่
                            @if(!empty($last))
                                <span class="text-val" style="margin-right: 10px;">
                                    {{ convDbDateToLongThDate($last->start_date) }}
                                </span>
                            @else
                                <span class="dot">.................................</span>
                            @endif
                            ถึงวันที่
                            @if(!empty($last))
                                <span class="text-val" style="margin-right: 10px;">
                                    {{ convDbDateToLongThDate($last->end_date) }}
                                </span>
                            @else
                                <span class="dot">.................................</span>
                            @endif
                            มีกำหนด
                            @if(!empty($last))
                                <span class="text-val"> {{ $last->leave_days }} </span>
                            @else
                                <span class="dot">.........</span>
                            @endif
                            วัน
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            ในระหว่างลาจะติดต่อกับข้าพเจ้าได้ที่
                            <span class="text-val">{{ $leave->leave_contact }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p style="margin-top: 10px;">
                                @if (empty($leave->delegate))
                                    โดยมอบหมายงานให้<span class="dot">......................................................</span>
                                @else
                                    โดยมอบหมายงานให้ <span class="text-val">{{ $leave->delegate->prefix->prefix_name.$leave->delegate->person_firstname. ' ' .$leave->delegate->person_lastname }}</span>
                                @endif
                            </p>
                            <p>
                                @if (empty($leave->delegate))
                                    ตำแหน่ง<span class="dot">.........................................................................</span>
                                @else
                                    ตำแหน่ง <span class="text-val">{{ $leave->delegate->position->position_name }}{{ $leave->delegate->academic ? $leave->delegate->academic->ac_name : '' }}</span>
                                @endif
                            </p>
                            <p>
                                (ลงชื่อ)<span class="dot">......................................................</span>ผู้รับมอบงาน
                            </p>
                        </td>
                        <td colspan="2">
                            <p style="margin-left: 50px;">
                                (ลงชื่อ)<span class="dot">......................................................</span>
                            </p>
                            <p style="margin-left: 100px;">
                                ( {{ $leave->person->prefix->prefix_name.$leave->person->person_firstname. ' ' .$leave->person->person_lastname }} )
                            </p>
                            <p style="margin-left: 50px;">
                                ตำแหน่ง <span>{{ $leave->person->position->position_name }}{{ $leave->person->academic ? $leave->person->academic->ac_name : '' }}</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="leave-stat">
                                สถิติการลาในปีงบประมาณนี้ (วันทำการ)
                                <table style="width: 90%;" class="table" border="1">
                                    <tr>
                                        <th style="text-align: center;">ประเภทการลา</th>
                                        <th style="text-align: center;">ลามาแล้ว</th>
                                        <th style="text-align: center;">ลาครั้งนี้</th>
                                        <th style="text-align: center;">รวมเป็น</th>
                                    </tr>
                                    <tr>
                                        <td>ป่วย</td>
                                        <td style="text-align: center;">
                                            &nbsp;
                                        </td>
                                        <td style="text-align: center;">
                                            &nbsp;
                                        </td>
                                        <td style="text-align: center;">
                                            &nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>กิจส่วนตัว</td>
                                        <td style="text-align: center;">
                                            &nbsp;
                                        </td>
                                        <td style="text-align: center;">
                                            &nbsp;
                                        </td>
                                        <td style="text-align: center;">
                                            &nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>คลอดบุตร</td>
                                        <td style="text-align: center;">
                                            &nbsp;
                                        </td>
                                        <td style="text-align: center;">
                                            &nbsp;
                                        </td>
                                        <td style="text-align: center;">
                                            &nbsp;
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td colspan="2">
                            <div style="margin-top: 20px;">
                                <p style="margin-left: 50px; text-decoration: underline;">
                                    ความเห็นของผู้บังคับบัญชา
                                </p>
                                <p style="margin-left: 50px;">
                                    <span class="dot">......................................................................</span>
                                </p>
                                <p style="margin-left: 50px;">
                                    (ลงชื่อ)<span class="dot">......................................................</span>
                                </p>
                                <p style="margin-left: 80px;">
                                    (<span class="dot">......................................................</span>)
                                </p>
                                <p style="margin-left: 50px;">
                                    ตำแหน่ง<span class="dot">......................................................</span>
                                </p>
                                <p style="margin-left: 70px;">
                                    วันที่<span class="dot">......................................................</span>
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p>
                                (ลงชื่อ)<span class="dot">......................................................</span>ผู้ตรวจสอบ
                            </p>
                            <p>
                                ตำแหน่ง<span class="dot">......................................................</span>
                            </p>
                            <p style="margin-left: 15px;">
                                วันที่<span class="dot">......................................................</span>
                            </p>
                        </td>
                        <td colspan="2">
                            <div style="margin-top: 10px;">
                                <p style="margin-left: 50px;">
                                    <span style="text-decoration: underline;">คำสั่ง</span>
                                    <span style="margin-left: 20px;">[&nbsp;&nbsp;] อนุญาต</span>
                                    <span style="margin-left: 20px;">[&nbsp;&nbsp;] ไม่อนุญาต</span>
                                </p>
                                <p style="margin-left: 50px;">
                                    (ลงชื่อ)<span class="dot">......................................................</span>
                                </p>
                                <p style="margin-left: 80px;">
                                    (<span class="dot">......................................................</span>)
                                </p>
                                <p style="margin-left: 50px;">
                                    ตำแหน่ง<span class="dot">......................................................</span>
                                </p>
                                <p style="margin-left: 70px;">
                                    วันที่<span class="dot">......................................................</span>
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>