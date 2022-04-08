<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>ใบลาพักผ่อน</title>
        <link rel="stylesheet" href="{{ asset('/css/pdf.css') }}">
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2 style="margin: 0px; padding: 0px;">ใบลาพักผ่อน</h2>
            </div>
            <div class="content">
                <table style="width: 100%; border-spacing: 0px;">
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
                                วันที่ <span style="margin-left: 10px;">{{ convDbDateToLongThDate($leave->leave_date) }}</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            เรื่อง <span style="margin-left: 10px;">{{ $leave->leave_topic }}</span>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            เรียน <span style="margin-left: 10px;">{{ $leave->leave_to }}</span>
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
                            <span class="text-val" style="margin-right: 2px;">
                                {{ $leave->person->memberOf->depart->depart_name }}
                            </span>
                            <span style="margin-right: 2px;">
                                โรงพยาบาลเทพรัตน์นครราชสีมา
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <span style="margin-right: 10px;">
                                มีวันลาพักผ่อน <span class="dot">......</span> วันทำการ
                            </span>
                            มีสิทธิลาพักผ่อนประจำปีนี้อีก
                            <span style="margin-right: 10px;">
                                <!-- <span class="text-val" style="margin-right: 5px;"> -->
                                    <span class="dot">............</span>
                                <!-- </span> -->
                                วันทำการ
                            </span>
                            <span style="margin-right: 10px;">
                                รวมเป็น
                                <!-- <span class="text-val" style="margin-right: 5px;"> -->
                                    <span class="dot">............</span>
                                <!-- </span> -->
                                วันทำการ
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <span style="margin-right: 10px;">
                                ขอลาพักผ่อนประจำปี
                            </span>
                            ตั้งแต่วันที่
                            <span class="text-val" style="margin-right: 10px;">
                                {{ convDbDateToLongThDate($leave->start_date) }}
                            </span>
                            ถึงวันที่ 
                            <span class="text-val" style="margin-right: 10px;">
                                {{ convDbDateToLongThDate($leave->end_date) }}
                            </span>
                            มีกำหนด <span class="text-val"> {{ $leave->leave_days }} </span> วัน
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            ในระหว่างลาจะติดต่อกับข้าพเจ้าได้ที่
                            <span class="text-val">
                                {{ $leave->leave_contact }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
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
                        <td colspan="4">
                            <p style="margin: 0 0 0 80px;">
                                ในการขออนุญาตลาพักผ่อนประจำปีในครั้งนี้ ข้าพเจ้าขอมอบงานหน้าที่รับผิดชอบให้
                            </p>
                            <p>
                                @if (empty($leave->delegate))
                                    นาย / นาง / นางสาว<span class="dot">......................................................</span>
                                    ตำแหน่ง<span class="dot">..........................................................</span>
                                @else
                                    <span class="text-val" style="margin-right: 10px;">
                                        {{ $leave->delegate->prefix->prefix_name.$leave->delegate->person_firstname. ' ' .$leave->delegate->person_lastname }}
                                    </span>
                                    ตำแหน่ง <span class="text-val" style="margin-right: 10px;">
                                        {{ $leave->delegate->position->position_name }}{{ $leave->delegate->academic ? $leave->delegate->academic->ac_name : '' }}
                                    </span>
                                @endif
                                เป็นผู้ปฏิบัติงานแทน
                                <span style="margin-left: 350px;">
                                    (ลงชื่อ)<span class="dot">......................................................ผู้รับมอบ</span>
                                </span>
                            </p>
                            <p style="margin-left: 350px;">
                                <span style="margin-left: 30px;">
                                    @if (empty($leave->delegate))
                                        (<span class="dot">......................................................</span>)
                                    @else
                                        ( {{ $leave->delegate->prefix->prefix_name.$leave->delegate->person_firstname. ' ' .$leave->delegate->person_lastname }} )
                                    @endif
                                </span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="margin: 0px; padding: 0px;">
                                สถิติการลาในปีงบประมาณนี้ (วันทำการ)
                                <table style="width: 90%;" class="table" border="1">
                                    <tr>
                                        <th style="text-align: center;">ลามาแล้ว</th>
                                        <th style="text-align: center;">ลาครั้งนี้</th>
                                        <th style="text-align: center;">รวมเป็น</th>
                                        <th style="text-align: center;">คงเหลือ</th>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;">
                                            &nbsp;
                                        </td>
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

                            <p style="margin-top: 20px;">
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
                            <div style="margin-top: 0px;">
                                <p style="margin-left: 50px; text-decoration: underline;">
                                    ความเห็นของผู้บังคับบัญชา
                                </p>
                                <p style="margin-left: 50px;">
                                    <span class="dot">......................................................................</span>
                                </p>
                                <p style="margin-top: 10px; margin-left: 50px;">
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
                            <div style="margin-top: 10px;">
                                <p style="margin-left: 50px;">
                                    <span style="text-decoration: underline;">คำสั่ง</span>
                                    <span style="margin-left: 20px;">[&nbsp;&nbsp;] อนุญาต</span>
                                    <span style="margin-left: 20px;">[&nbsp;&nbsp;] ไม่อนุญาต</span>
                                </p>
                                <p style="margin-top: 10px; margin-left: 50px;">
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