<div class="box box-danger" ng-init="getSummaryServices();">
    <div class="box-header">
        <h3 class="box-title">
            สรุปแผนจ้างบริการ
            <!-- <span>ประจำเดือน</span> -->
        </h3>
        <!-- <div class="pull-right box-tools">
            <div class="row">
                <div class="form-group col-md-12" style="margin-bottom: 0px;">
                    <input
                        type="text"
                        id="cboAssetDate"
                        name="cboAssetDate"
                        class="form-control"
                    />
                </div>
            </div>
        </div> -->
    </div>
    <div class="box-body">
        <table class="table table-striped table-bordered" style="font-size: 12px;">
            <tr>
                <th>ประเภท</th>
                <th style="width: 15%; text-align: center;">ประมาณการ</th>
                <th style="width: 15%; text-align: center;">ส่งขอสนับสนุน</th>
                <th style="width: 15%; text-align: center;">ส่งเบิกเงิน</th>
                <th style="width: 15%; text-align: center;">ตั้งหนี้</th>
            </tr>
            <tr ng-repeat="(index, service) in services" style="font-size: 12px;">
                <td>@{{ index+1 }}. @{{ service.category_name }}</td>
                <td style="text-align: right;">@{{ service.request | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ service.po | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ service.withdraw | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ service.debt | currency:'':0 }}</td>
            </tr>
        </table>
    </div><!-- /.box-body -->

    <!-- Loading (remove the following to stop the loading)-->
    <div ng-show="loading" class="overlay">
        <i class="fa fa-refresh fa-spin"></i>
    </div>
    <!-- end loading -->

</div><!-- /.box -->