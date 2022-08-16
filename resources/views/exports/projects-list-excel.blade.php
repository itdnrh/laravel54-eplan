<div class="box">
    <div class="box-header">
        <h3 style="margin: 0;">รายการโครงการ</h3>
        <h4 style="margin: 0;">ประจำปีงบประมาณ {{ $options['year'] }}</h4>
    </div><!-- /.box-header -->
    <div class="box-body">
        <table class="table table-bordered table-striped" style="font-size: 12px;">
            <thead>
                <tr>
                    <th style="width: 3%; text-align: center;">#</th>
                    <th style="width: 8%; text-align: center;">เลขที่โครงการ</th>
                    <th style="width: 10%; text-align: center;">ยุทธศาสตร์ที่</th>
                    <th style="width: 20%; text-align: center;">กลยุทธ์</th>
                    <th>ชื่อโครงการ</th>
                    <th style="width: 8%; text-align: center;">งบประมาณ</th>
                    <th style="width: 8%; text-align: center;">แหล่งงบฯ</th>
                    <th style="width: 20%; text-align: center;">ผู้รับผิดชอบ</th>
                    <th style="width: 20%; text-align: center;">หน่วยงาน</th>
                    <th style="width: 5%; text-align: center;">อนุมัติ</th>
                    <th style="width: 10%; text-align: center;">สถานะ</th>
                </tr>
            </thead>
            <tbody>

                <?php $cx = 0; ?>
                @foreach($data as $project)

                    <tr>
                        <td style="text-align: center;">{{ ++$cx }}</td>
                        <td style="text-align: center;">{{ $project->project_no }}</td>
                        <td style="text-align: center;">{{ $project->strategy->strategic_id }}</td>
                        <td>{{ $project->strategy->strategy_name }}</td>
                        <td>{{ $project->project_name }}</td>
                        <td style="text-align: center;">
                            {{ number_format($project->total_budget) }}
                        </td>
                        <td style="text-align: center;">
                            {{ $project->budgetSrc->name }}
                        </td>
                        <td>
                            {{ $project->owner->prefix->prefix_name.$project->owner->person_firstname. ' ' .$project->owner->person_lastname }}
                        </td>
                        <td>
                            {{ $project->depart->depart_name }}
                        </td>
                        <td style="text-align: center;">
                            {{ $project->approved }}
                        </td>
                        <td style="text-align: center;">
                            @if($project->status == '0')
                                {{ 'รอดำเนินการ' }}
                            @elseif($project->status == '1')
                                {{ 'ส่งงานแผนแล้ว' }}
                            @elseif($project->status == '2')
                                {{ 'ส่งการเงินแล้ว' }}
                            @elseif($project->status == '3')
                                {{ 'ผอ.อนุมัติแล้ว' }}
                            @elseif($project->status == '4')
                                {{ 'ดำเนินโครงการแล้ว' }}
                            @elseif($project->status == '9')
                                {{ 'ยกเลิก' }}
                            @endif
                        </td>
                    </tr>

                @endforeach

            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
