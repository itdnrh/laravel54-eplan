<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>บันทึกขอสนับสนุน</title>
        <link rel="stylesheet" href="{{ public_path('/css/pdf.css') }}">
    </head>
    <body>
        <div class="memo-container">
            <div class="memo-header">
                <div class="logo-krut">
                    <img src="{{ public_path('/img/krut.jpg') }}" alt="krut" />
                </div>
                <h2>บันทึกข้อความ</h2>
            </div>
            <div class="content">
                <?php $orderType = in_array($planType->id, [1,2]) ? 'จัดซื้อ' : '' ?>
                <?php $haveRowOvered = 0; ?>
                <table class="layout">
                    <tr>
                        <td colspan="4">
                            <div class="content-header">
                                <span class="content__header-topic">ส่วนราชการ</span>
                                <div class="content__header-text" style="width: 87%;">
                                    <span>{{ thainumDigit($departOfParcel->depart_name) }}</span>
                                    <span>โรงพยาบาลเทพรัตน์นครราชสีมา</span>
                                    <span>โทร {{ thainumDigit('0 4439 5000 ต่อ '.$departOfParcel->tel_no) }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%;">
                            <div class="content-header">
                                <span class="content__header-topic">ที่</span>
                                <div class="content__header-text" style="width: 95%;">
                                    <span>{{ thainumDigit($departOfParcel->memo_no.'/'.$support->spec_doc_no) }}</span>
                                </div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="content-header">
                                <span class="content__header-topic">วันที่</span>
                                <div class="content__header-text" style="width: 88%;">
                                    <span style="margin: 0 10px;">
                                        <!-- {{ '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.thainumDigit($support->doc_date) }} -->
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ thainumDigit(convDbDateToLongThDate($support->spec_doc_date)) }}
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="content-header">
                                <span class="content__header-topic" style="top: 0;">เรื่อง</span>
                                <div class="content__header-text" style="width: 95%;">
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
                            <?php $purchaseMethods = [1 => 'เฉพาะเจาะจง', 2 => 'ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)']; ?>
                            <p class="memo-paragraph-content with-compressed with-expanded">
                                <span class="memo-paragraph-topic-inline">๑.ต้นเรื่อง</span>
                                กลุ่มงานพัสดุ โรงพยาบาลเทพรัตน์นครราชสีมา มีความประสงค์จะดำเนินการ
                                {{$orderType}}<span>{{ $support->order->category->name }}</span>
                                โดยวิธี{{ $purchaseMethods[$support->purchase_method] }} เพื่อใช้ในการปฏิบัติงานของเจ้าหน้าที่
                                จึงขออนุมัติ{{$orderType}}<span>{{ $support->order->category->name }}</span>
                                จำนวน <span>{{ thainumDigit($support->amount) }}</span> รายการ
                                จำนวนเงินทั้งสิ้น <span>{{ thainumDigit(number_format($support->net_total, 2)) }} บาท</span>
                                <span>({{ baht_text($support->net_total) }})</span>

                                @if(
                                    (strlen($orderType.$support->order->category->name) >= 60 && 
                                    (strlen(baht_text($support->net_total)) >= 105 || strlen(baht_text($support->net_total)) >= 135)) ||
                                    strlen($orderType.$support->order->category->name) >= 95
                                )
                                    <?php $haveRowOvered++; ?>
                                @endif
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
                                และที่แก้ไขเพิ่มเติม ข้อ ๒๑ ในการจัดซื้อจัดจ้างที่มิใช่การจ้างก่อสร้าง ให้หัวหน้าหน่วยงานของรัฐแต่งตั้ง
                                คณะกรรมการขึ้นมา  คณะหนึ่ง หรือจะให้เจ้าหน้าที่ หรือบุคคลใดบุคคลหนึ่งรับผิดชอบในการจัดทำร่างขอบเขต ของงาน 
                                หรือ รายละเอียดคุณลักษณะเฉพาะของพัสดุที่จะซื้อหรือจ้าง รวมทั้งกำหนดหลักเกณฑ์พิจารณาคัดเลือก ข้อเสนอด้วย และ
                                ข้อ ๒๑ วรรคสี่ องค์ประกอบ ระยะเวลาการพิจารณา และประชุมของคณะกรรมการ ตามวรรคหนึ่ง
                                และวรรคสอง ให้เป็นไปตามที่หัวหน้าหน่วยงานของรัฐกำหนด ตามความจำเป็นและเหมาะสม
                            </p>                            
                            <p class="memo-paragraph-content with-compressed with-expanded">
                                ๒.๒ คำสั่งจังหวัดนครราชสีมา ที่ <span>{{ thainumDigit($provinceOrders[0]->order_no) }}</span>
                                ลงวันที่&nbsp;&nbsp;&nbsp;&nbsp;<span>{{ thainumDigit(convDbDateToLongThDate($provinceOrders[0]->order_date)) }}</span>
                                เรื่อง การมอบอำนาจการจัดซื้อจัดจ้างและการบริหารพัสดุภาครัฐ
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="memo-paragraph-content with-compressed with-expanded">
                                <span class="memo-paragraph-topic-inline">๓.ข้อพิจารณา</span>
                                โรงพยาบาลเทพรัตน์นครราชสีมา พิจารณาแล้ว จึงขออนุมัติแต่งตั้ง

                                <?php $committeeType = count($committees) > 1 ? 'คณะกรรมการ' : 'ผู้'; ?>
                                <div class="committees-paragraph">
                                    <table style="width: 100%;">
                                        <?php $index = 0;?>
                                        @foreach($committees as $committee)
                                            @if ($support->order->support_id)
                                                <tr>
                                                    <td style="width: 45%;">
                                                        {{ thainumDigit(++$index) }}. {{ $committee->person->prefix->prefix_name.$committee->person->person_firstname.' '.$committee->person->person_lastname }}
                                                    </td>
                                                    <td>ตำแหน่ง {{ $committee->person->position->position_name }}{{ $committee->person->academic ? $committee->person->academic->ac_name : '' }}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td style="width: 45%;">
                                                        {{ thainumDigit(++$index) }}. {{ $committee->prefix->prefix_name.$committee->person_firstname.' '.$committee->person_lastname }}
                                                    </td>
                                                    <td>ตำแหน่ง {{ $committee->position->position_name }}{{ $committee->academic ? $committee->academic->ac_name : '' }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </table>
                                </div>

                                เป็น{{ $committeeType }}รับผิดชอบจัดทำรายละเอียดคุณลักษณะเฉพาะ และราคากลางการ{{$orderType}}<span>{{ $support->order->category->name}}</span>
                                จำนวน <span>{{ thainumDigit($support->amount) }} รายการ</span>
                                เพื่อใช้ในการปฏิบัติงานของเจ้าหน้าที่ โดยมีหน้าที่จัดทำรายละเอียดคุณลักษณะเฉพาะของพัสดุ รวมทั้งกำหนด
                                หลักเกณฑ์พิจารณาคัดเลือกข้อเสนอด้วย</span>
                            </div>
                        </td>
                    </tr>

                    @if(count($committees) > 1 || $haveRowOvered > 0)
                        <tr>
                            <td colspan="4">
                                @if($haveRowOvered > 0)
                                    <div style="height: 160px;"></div>
                                @else
                                    <div style="height: 80px;"></div>
                                @endif

                                <p class="next-paragraph">/๔.ข้อเสนอ...</p>
                            </td>
                        </tr>
                    @else
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
                    @endif

                    @if(count($committees) > 1 || $haveRowOvered > 0)
                        <tr>
                            <td colspan="4">
                                <div class="page-number">- ๒ -</div>
                                <div class="memo-paragraph-content with-compressed with-expanded">
                                    <span class="memo-paragraph-topic-inline">๔.ข้อเสนอ</span>
                                    จึงเรียนมาเพื่อโปรดพิจารณา หากเห็นชอบแล้วขอได้โปรดอนุมัติแต่งตั้งผู้จัดทำ
                                    รายละเอียดคุณลักษณะเฉพาะและราคากลาง รวมทั้งหลักเกณฑ์การพิจารณาคัดเลือกข้อเสนอตามรายชื่อที่เสนอ
                                    ในข้อ ๓ ต่อไป
                                </div>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="2">
                            <div class="signature-approver" style="padding-top: 25px;">
                                <p style="margin: 0; font-weight: bold;">
                                    <span style="margin: 0;">ชอบ/อนุมัติ</span>
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
                        <td colspan="2" style="text-align: center; padding: 0 5px 0 0;">
                            <div class="signature">
                                <p style="margin: 10px 0 0;">
                                    ลงชื่อ<span class="dot">.........................................เจ้าหน้าที่</span>
                                </p>
                                <p style="margin: 0;">
                                    ( {{ $support->order->officer->prefix->prefix_name.$support->order->officer->person_firstname. ' ' .$support->order->officer->person_lastname }} )
                                </p>
                                <p style="margin: 0;">
                                    <span>{{ $support->order->officer->position->position_name }}{{ $support->order->officer->academic ? $support->order->officer->academic->ac_name : '' }}</span>
                                </p>
                            </div>
                            <div class="signature">
                                <p style="margin: 20px 0 0 0px;">
                                    ลงชื่อ<span class="dot">...............................หัวหน้าเจ้าหน้าที่</span>
                                </p>
                                <p style="margin: 0;">
                                    ( {{ $headOfFaction->prefix->prefix_name.$headOfFaction->person_firstname. ' ' .$headOfFaction->person_lastname }} )
                                </p>
                                <p style="margin: 0;">
                                    <span>{{ $headOfFaction->position->position_name }}{{ $headOfFaction->academic ? $headOfFaction->academic->ac_name : '' }}</span>
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
                    <img src="{{ public_path('/img/krut.jpg') }}" alt="krut" />
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
                                    <span>{{ thainumDigit($departOfParcel->depart_name) }}</span>
                                    <span>โรงพยาบาลเทพรัตน์นครราชสีมา</span>
                                    <span>โทร {{ thainumDigit('0 4439 5000 ต่อ '.$departOfParcel->tel_no) }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%;">
                            <div class="content-header">
                                <span class="content__header-topic">ที่</span>
                                <div class="content__header-text" style="width: 95%;">
                                    <span>{{ thainumDigit($departOfParcel->memo_no.'/'.$support->report_doc_no) }}</span>
                                </div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="content-header">
                                <span class="content__header-topic">วันที่</span>
                                <div class="content__header-text" style="width: 89%;">
                                    <span style="margin: 0 10px;">
                                        <!-- {{ '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.thainumDigit($support->doc_date) }} -->
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ thainumDigit(convDbDateToLongThDate($support->report_doc_date)) }}
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="content-header">
                                <span class="content__header-topic" style="top: 0;">เรื่อง</span>
                                <div class="content__header-text" style="width: 95%;">
                                    <span style="margin-left: 5px;">
                                        รายงานผลการกำหนดรายละเอียดคุณลักษณะเฉพาะของพัสดุและราคากลาง
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
                                <span class="memo-paragraph-topic-inline">๑.เรื่องเดิม</span>
                                ตามบันทึกที่ <span>{{ thainumDigit($departOfParcel->memo_no.'/'.$support->spec_doc_no) }}</span>
                                ลงวันที่&nbsp;&nbsp;<span>{{ thainumDigit(convDbDateToLongThDate($support->spec_doc_date)) }}</span>
                                ได้แต่งตั้ง ข้าพเจ้า ผู้มีนามข้างท้ายเป็น{{ $committeeType }}กำหนดรายละเอียดคุณลักษณะเฉพาะและราคากลาง<span class="with-compressed-3x">{{$orderType}}{{ $support->order->category->name}}</span>
                                <span class="with-compressed-3x">สนับสนุนการทำงานของเจ้าหน้าที่ ให้ทำงานได้อย่างมีประสิทธิภาพยิ่งขึ้น นั้น</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p class="memo-paragraph-topic">
                                ๒. ข้อกฎหมาย/ระเบียบ
                            </p>
                            <p class="memo-paragraph-content with-compressed with-expanded">
                                ๒.๑ พระราชบัญญัติการจัดซื้อจัดจ้างและการบริหารพัสดุภาครัฐ พ.ศ. ๒๕๖๐ มาตรา ๔ ใน พระราชบัญญัตินี้
                                "ราคากลาง" หมายความว่า ราคาเพื่อใช้เป็นฐานสำหรับเปรียบเทียบราคาที่ผู้ยื่นข้อเสนอได้ยื่น เสนอไว้
                                ซึ่งสามารถจัดซื้อจัดจ้างได้จริงตามลำดับ (๔) โดยใช้เกณฑ์ราคากลางและคุณลักษณะพื้นฐานการจัดหา
                            </p>
                            <p class="memo-paragraph-content with-compressed with-expanded">
                                ๒.๒ ระเบียบกระทรวงการคลังว่าด้วยการจัดซื้อจัดจ้างและการบริหารพัสดุภาครัฐ พ.ศ. ๒๕๖๐
                                ข้อ ๒๑ ในการจัดซื้อหรือจัดจ้าง ที่มิใช่การจ้างก่อสร้าง ให้หัวหน้าหน่วยงานของรัฐแต่งตั้งคณะกรรมการขึ้นมา คณะหนึ่ง
                                หรือจะให้เจ้าหน้าที่ หรือบุคคลใดบุคคลหนึ่งรับผิดชอบในการจัดทำร่างขอบเขตของงานหรือราย-
                                ละเอียดคุณลักษณะเฉพาะของพัสดุที่จะซื้อหรือจ้าง รวมทั้งกำหนดหลักเกณฑ์พิจารณาคัดเลือกข้อเสนอด้วย
                            </p>
                            <p class="memo-paragraph-content with-compressed with-expanded">
                                <!-- ๒.๓ คำสั่งจังหวัดนครราชสีมา ที่ <span>{{ thainumDigit($provinceOrders[0]->order_no) }}</span>
                                ลงวันที่&nbsp;&nbsp;&nbsp;&nbsp;<span>{{ thainumDigit(convDbDateToLongThDate($provinceOrders[0]->order_date)) }}</span>
                                เรื่อง การแก้ไขการมอบอำนาจของผู้ว่าราชการจังหวัดให้รองผู้ว่าราชการจังหวัด ปลัดจังหวัด หัวหน้าส่วนราชการ
                                ประจำจังหวัดและนายอำเภอ ปฏิบัติราขการแทน และ -->
                                ๒.๓ คำสั่งจังหวัดนครราชสีมา ที่ <span>{{ thainumDigit($provinceOrders[1]->order_no) }}</span>
                                ลงวันที่&nbsp;&nbsp;&nbsp;&nbsp;<span>{{ thainumDigit(convDbDateToLongThDate($provinceOrders[1]->order_date)) }}</span>
                                เรื่อง การมอบอำนาจการจัดซื้อจัดจ้างและการบริหารพัสดุภาครัฐ
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <?php $spacing = strlen(baht_text($support->net_total)) > 80 ? '&nbsp;' : ''; ?>
                            <?php $unspacing = strlen(baht_text($support->net_total)) > 80 ? '' : '&nbsp;'; ?>
                            <?php $sourcePrices = [1 => 'ราคาที่ได้จากการจัดซื้อภายใน 2 ปีงบประมาณ', 2 => 'อื่นๆ']; ?>
                            <div class="memo-paragraph-content with-compressed with-expanded">
                                <span class="memo-paragraph-topic-inline">๓.ข้อพิจารณา</span>
                                บัดนี้ ผู้กำหนดรายละเอียดคุณลักษณะ ได้กำหนดรายละเอียดคุณลักษณะ เฉพาะและราคากลาง{{$orderType}}<span>{{ $support->order->category->name}}</span>
                                จำนวน <span>{{ thainumDigit($support->amount) }} รายการ</span>
                                เป็นเงินทั้งสิ้น <span>{{ thainumDigit(number_format($support->net_total, 2)) }} บาท</span>
                                <span>({{ baht_text($support->net_total) }})</span>
                                โรงพยาบาลเทพรัตน์นครราชสีมา โดยถือปฏิบัติตาม{{ $spacing }}ระเบียบกระทรวงการคลัง{{ $unspacing }}ว่าด้วยการจัดซื้อจัดจ้าง และการบริหารพัสดุภาครัฐ พ.ศ. ๒๕๖๐ และที่แก้ไขเพิ่มเติม
                                ข้อ ๒๑ และได้พิจารณาราคากลาง โดยถือปฏิบัติตามพระราชบัญญัติ การจัดซื้อจัดจ้างและการบริหารพัสดุภาครัฐ พ.ศ. ๒๕๖๐ มาตรา ๔
                                <table style="width: 100%; margin: 10px;" class="table" border="1">
                                    <tr>
                                        <th style="text-align: center;">ลำดับ</th>
                                        <th style="text-align: center;">แหล่งที่มาของราคาอ้างอิง</th>
                                        <th style="text-align: center;">ราคารวม (บาท)</th>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;">๑</td>
                                        <td style="padding: 0 5px 2px;">
                                            {{ thainumDigit($sourcePrices[$support->source_price]) }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ thainumDigit(number_format($support->net_total, 2)) }}
                                        </td>
                                    </tr>
                                </table>
                                <p style="margin: 0;">
                                    ข้าพเจ้าฯ ได้สรุปรายละเอียดคุณลักษณะเฉพาะและราคากลาง<span class="with-compressed-3x">{{$orderType}}{{ $support->order->category->name}}</span>
                                    จำนวน <span>{{ thainumDigit($support->amount) }} รายการ</span>
                                    เป็นเงินทั้งสิ้น <span>{{ thainumDigit(number_format($support->net_total, 2)) }} บาท</span>
                                    <span>({{ baht_text($support->net_total) }})</span>
                                </p>
                            </div>

                            <p class="next-paragraph">/๔. ข้อเสนอ...</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="page-number">- ๒ -</div>
                            <div class="memo-paragraph-content with-compressed with-expanded" style="margin-bottom: 10px;">
                                <span class="memo-paragraph-topic-inline">๔.ข้อเสนอ</span>
                                จึงเรียนมาเพื่อโปรดพิจารณา ให้ความเห็นชอบรายละเอียดคุณลักษณะเฉพาะ และราคากลาง{{$orderType}}<span>{{ $support->order->category->name}}</span>
                                จำนวน <span>{{ thainumDigit($support->amount) }} รายการ</span>
                                เป็นเงินทั้งสิ้น <span>{{ thainumDigit(number_format($support->net_total, 2)) }} บาท</span>
                                <span>({{ baht_text($support->net_total) }})</span> ของโรงพยาบาลเทพรัตน์นครราชสีมา

                                <?php $order = 1; ?>
                                @foreach($committees as $committee)
                                    <?php $position = count($committees) > 1 ? ($order == 1 ? 'ประธานกรรมการ' : 'กรรมการ') : 'ผู้กำหนดรายละเอียดคุณลักษณะ' ?>
                                    <?php $order++?>

                                    <div class="clearfix" style="margin-left: 80px;">
                                        <p style="margin: 20px 0 0;">
                                            ลงชื่อ<span class="dot">.........................................{{ $position }}</span>
                                        </p>
                                        @if ($support->order->support_id)
                                            <div style="width: 60%; text-align: center;">
                                                <p style="margin: 0">
                                                    ( {{ $committee->person->prefix->prefix_name.$committee->person->person_firstname.' '.$committee->person->person_lastname }} )
                                                </p>
                                                <p style="margin: 0">
                                                    <span>{{ $committee->person->position->position_name }}{{ $committee->person->academic ? $committee->person->academic->ac_name : '' }}</span>
                                                </p>
                                            </div>
                                        @else
                                            <div style="width: 60%; text-align: center;">
                                                <p style="margin: 0">
                                                    ( {{ $committee->prefix->prefix_name.$committee->person_firstname.' '.$committee->person_lastname }} )
                                                </p>
                                                <p style="margin: 0">
                                                    <span>{{ $committee->position->position_name }}{{ $committee->academic ? $committee->academic->ac_name : '' }}</span>
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <?php $spacer = count($committees) > 1 ? ' ' : ''; ?>
                            <div class="memo-paragraph-content with-compressed with-expanded">
                                <span>เรียนผู้ว่าราชการจังหวัดนครราชสีมา โรงพยาบาลเทพรัตน์นครราชสีมา ได้พิจารณาแล้ว
                                {{ $committeeType }}กำหนดรายละเอียดคุณลักษณะเฉพาะ ได้ปฏิบัติตามข้อกฏหมาย/ระเบียบ ตามข้อ ๒
                                เห็นควรพิจารณา ให้ความเห็นชอบรายละเอียดคุณลักษณะเฉพาะ ตามที่{{ $committeeType }}กำหนดรายละเอียด{{ $spacer }}คุณลักษณะเฉพาะและราคากลาง เพื่อใช้ในการดำเนินการจัดซื้อในครั้งนี้</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="3" style="text-align: center; padding: 5px;">
                            <div class="signature">
                                <p style="margin: 30px 0 0;">
                                    ลงชื่อ<span class="dot">.........................................เจ้าหน้าที่</span>
                                </p>
                                <p style="margin: 0;">
                                    ( {{ $support->order->officer->prefix->prefix_name.$support->order->officer->person_firstname. ' ' .$support->order->officer->person_lastname }} )
                                </p>
                                <p style="margin: 0;">
                                    เจ้าหน้าที่พัสดุ
                                </p>
                            </div>
                            <div class="signature">
                                <p style="margin: 30px 0 0 50px;">
                                    ลงชื่อ<span class="dot">.........................................หัวหน้าเจ้าหน้าที่</span>
                                </p>
                                <p style="margin: 0;">
                                    ( {{ $headOfFaction->prefix->prefix_name.$headOfFaction->person_firstname. ' ' .$headOfFaction->person_lastname }} )
                                </p>
                                <p style="margin: 0;">
                                    <span>{{ $headOfFaction->position->position_name }}{{ $headOfFaction->academic ? $headOfFaction->academic->ac_name : '' }}</span>
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="3">
                            <div class="signature" style="text-align: center;">
                                <p style="margin: 10px 0 20px 0; font-weight: bold;">
                                    <span style="margin: 0;">ชอบ</span>
                                </p>
                                <p style="margin: 30px 0 0;">
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
    </body>
</html>