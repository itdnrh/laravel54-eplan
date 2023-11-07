<div class="box box-info" ng-init="getSummaryServices();">
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
                <th style="width: 15%; text-align: right;">ประมาณการ</th>
                <th style="width: 15%; text-align: right;">ยอดอนุมัติ</th>
                <th style="width: 15%; text-align: right;">รับเอกสาร</th>
                <th style="width: 15%; text-align: right;">ออกใบซื้อ/จ้าง</th>
                <th style="width: 15%; text-align: right;">ส่งเบิกเงิน</th>
                <!-- <th style="width: 15%; text-align: right;">ตั้งหนี้</th> -->
            </tr>
            <tr ng-repeat="(index, service) in services" style="font-size: 12px;">
                <td>@{{ index+1 }}. @{{ service.category_name }}</td>
                <td style="text-align: right;">@{{ service.request | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ asset.plan_approved | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ service.received | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ service.po | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ service.withdraw | currency:'':0 }}</td>
                <!-- <td style="text-align: right;">@{{ service.debt | currency:'':0 }}</td> -->
            </tr><tr>
                <td style="text-align: center;">รวม</td>
                <td style="text-align: right;">@{{ totalService.request | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ totalMaterial.plan_approved | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ totalService.received | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ totalService.po | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ totalService.withdraw | currency:'':0 }}</td>
                <!-- <td style="text-align: right;">@{{ totalService.debt | currency:'':0 }}</td> -->
            </tr>
        </table>
    </div><!-- /.box-body -->

    <!-- Loading (remove the following to stop the loading)-->
    <div ng-show="loading" class="overlay">
        <i class="fa fa-refresh fa-spin"></i>
    </div>
    <!-- end loading -->

</div><!-- /.box -->