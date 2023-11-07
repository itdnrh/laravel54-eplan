<div class="box box-primary" ng-init="getSummaryAssets();">
    <div class="box-header">
        <h3 class="box-title">
            สรุปแผนครุภัณฑ์
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
            <tr ng-repeat="(index, asset) in assets">
                <td>@{{ index+1 }}. @{{ asset.category_name }}</td>
                <td style="text-align: right;">@{{ asset.request | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ asset.plan_approved | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ asset.received | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ asset.po | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ asset.withdraw | currency:'':0 }}</td>
                <!-- <td style="text-align: right;">@{{ asset.debt | currency:'':0 }}</td> -->
            </tr>
            <tr>
                <td style="text-align: center;">รวม</td>
                <td style="text-align: right;">@{{ totalAsset.request | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ totalAsset.plan_approved | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ totalAsset.received | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ totalAsset.po | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ totalAsset.withdraw | currency:'':0 }}</td>
                <!-- <td style="text-align: right;">@{{ totalAsset.debt | currency:'':0 }}</td> -->
            </tr>
        </table>
    </div><!-- /.box-body -->
    <div class="box-footer" ng-show="false">
        <div class="row" ng-show="asseassets_pager.last_page > 1">
            <div class="col-md-4">
                <span style="margin-top: 5px;" ng-show="false">
                    หน้า @{{ assets_pager.current_page }} จาก @{{ assets_pager.last_page }}
                </span>
            </div>
            <div class="col-md-4" style="text-align: center;">
                จำนวน @{{ assets.length }} รายการ
            </div>
            <div class="col-md-4">
                <ul class="pagination pagination-sm no-margin pull-right" ng-show="assets_pager.last_page > 1">
                    <li ng-class="{'disabled': (assets_pager.current_page == 1)}">
                        <a href="#" ng-click="getDataWithURL($event, assets_pager.path+ '?page=1', setDepartLeaves)" aria-label="Previous">
                            <span aria-hidden="true">First</span>
                        </a>
                    </li>
                    <!-- <li ng-class="{'disabled': true}">
                        <a href="#" ng-click="getDataWithURL($event, assets_pager.prev_page_url, setDepartLeaves)" aria-label="Prev">
                            <span aria-hidden="true">Prev</span>
                        </a>
                    </li>
                    <li ng-class="{'disabled': true}">
                        <a href="#" ng-click="getDataWithURL($event, assets_pager.next_page_url, setDepartLeaves)" aria-label="Next">
                            <span aria-hidden="true">Next</span>
                        </a>
                    </li> -->
                    <li ng-class="{'disabled': (assets_pager.current_page == assets_pager.last_page)}">
                        <a href="#" ng-click="getDataWithURL($event, assets_pager.path+ '?page=' +assets_pager.last_page, setDepartLeaves)" aria-label="Previous">
                            <span aria-hidden="true">Last</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div><!-- /.box-footer -->

    <!-- Loading (remove the following to stop the loading)-->
    <div ng-show="loading" class="overlay">
        <i class="fa fa-refresh fa-spin"></i>
    </div>
    <!-- end loading -->

</div><!-- /.box -->