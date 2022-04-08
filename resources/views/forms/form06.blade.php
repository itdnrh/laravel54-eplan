<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>ใบลาอุปสมบท</title>
        <link rel="stylesheet" href="{{ asset('/css/pdf.css') }}">
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1 style="margin: 0px;">ใบลาอุปสมบท</h1>
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
                            <p style="margin: 0 0 0 100px;">
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
                            เกิดวันที่
                            <span class="text-val" style="margin-right: 20px;">
                                {{ convDbDateToLongThDate($leave->person->person_birth) }}
                            </span>
                            เข้ารับราชการเมื่อวันที่ 
                            <span class="text-val" style="margin-right: 10px;">
                                {{ convDbDateToLongThDate($leave->person->person_singin) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            ข้าพเจ้า
                            <span style="margin-left: 5px;">
                                [{{ $leave->ordinate->have_ordain == 0 ? ' / ' : '&nbsp;&nbsp;' }}] 
                                ยังไม่เคย
                            </span>
                            <span style="margin-left: 10px;">
                                [{{ $leave->ordinate->have_ordain == 1 ? ' / ' : '&nbsp;&nbsp;' }}] 
                                เคย
                            </span> 
                            อุปสมบท บัดนี้ศรัทธาจะอุปสมบทในพระพุทธศาสนา
                            ณ วัด 
                            <span class="text-val" style="margin-right: 10px;">
                                {{ $leave->ordinate->ordain_temple }}
                            </span>
                            ตั้งอยู่ ณ
                            <span class="text-val" style="margin-right: 10px;">
                                {{ $leave->ordinate->ordain_location }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            กำหนดวันที่
                            <span class="text-val" style="margin-right: 5px;">
                                {{ convDbDateToLongThDate($leave->ordinate->ordain_date) }}
                            </span>
                            และจะจำพรรษาอยู่ ณ วัด 
                            <span class="text-val" style="margin-right: 5px;">
                                {{ $leave->ordinate->hibernate_temple }}
                            </span>
                            ตั้งอยู่ ณ 
                            <span class="text-val">
                                {{ $leave->ordinate->hibernate_location }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            จึงขออนุญาตลาอุปสมบทมีกำหนด <span class="text-val"> {{ $leave->leave_days }} </span>
                            <span style="margin-right: 10px;">วัน</span>
                            ตั้งแต่วันที่
                            <span class="text-val" style="margin-right: 10px;">
                                {{ convDbDateToLongThDate($leave->start_date) }}
                            </span>
                            ถึงวันที่ 
                            <span class="text-val">
                                {{ convDbDateToLongThDate($leave->end_date) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">
                            <p style="margin-top: 10px; margin-left: 40px;">
                                ขอแสดงความนับถือ
                            </p>
                            <p style="margin-left: 0px;">
                                (ลงชื่อ)<span class="dot">......................................................</span>
                            </p>
                            <p style="margin-left: 30px;">
                                ( {{ $leave->person->prefix->prefix_name.$leave->person->person_firstname. ' ' .$leave->person->person_lastname }} )
                            </p>
                            <p style="margin-left: 0px;">
                                ตำแหน่ง <span>{{ $leave->person->position->position_name }}{{ $leave->person->academic ? $leave->person->academic->ac_name : '' }}</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p style="margin-left: 50px; text-decoration: underline;">
                                ความเห็นของผู้บังคับบัญชา
                            </p>
                            <p style="margin-left: 100px;">
                                <span class="dot">...........................................................................................................................</span>
                            </p>
                            <p style="margin-left: 150px;">
                                (ลงชื่อ)<span class="dot">......................................................</span>
                            </p>
                            <p style="margin-left: 180px;">
                                (<span class="dot">......................................................</span>)
                            </p>
                            <p style="margin-left: 150px;">
                                ตำแหน่ง<span class="dot">......................................................</span>
                            </p>
                            <p style="margin-left: 170px;">
                                วันที่<span class="dot">......................................................</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p style="margin-top: 10px; margin-left: 50px;">
                                <span style="text-decoration: underline;">คำสั่ง</span>
                                <span style="margin-left: 20px;">[&nbsp;&nbsp;] อนุญาต</span>
                                <span style="margin-left: 20px;">[&nbsp;&nbsp;] ไม่อนุญาต</span>
                            </p>
                            <p style="margin-left: 150px;">
                                (ลงชื่อ)<span class="dot">......................................................</span>
                            </p>
                            <p style="margin-left: 180px;">
                                (<span class="dot">......................................................</span>)
                            </p>
                            <p style="margin-left: 150px;">
                                ตำแหน่ง<span class="dot">......................................................</span>
                            </p>
                            <p style="margin-left: 170px;">
                                วันที่<span class="dot">......................................................</span>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>