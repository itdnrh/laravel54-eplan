<div class="box box-primary" ng-init="getSummaryConstructs();">
    <div class="box-header">
        <h3 class="box-title">
            สรุปแผนก่อสร้าง
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
        <table class="table table-striped table-bordered">
            <tr>
                <th>ประเภท</th>
                <th style="width: 15%; text-align: center;">ประมาณการ</th>
                <th style="width: 15%; text-align: center;">ส่งขอสนับสนุน</th>
                <th style="width: 15%; text-align: center;">ส่งเบิกเงิน</th>
                <th style="width: 15%; text-align: center;">ตั้งหนี้</th>
            </tr>
            <tr ng-repeat="(index, construct) in constructs" style="font-size: 12px;">
                <td>@{{ index+1 }}. @{{ construct.category_name }}</td>
                <td style="text-align: right;">@{{ construct.request | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ construct.po | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ construct.withdraw | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ construct.debt | currency:'':0 }}</td>
            </tr>
        </table>
    </div><!-- /.box-body -->

    <!-- Loading (remove the following to stop the loading)-->
    <div ng-show="loading" class="overlay">
        <i class="fa fa-refresh fa-spin"></i>
    </div>
    <!-- end loading -->

</div><!-- /.box -->