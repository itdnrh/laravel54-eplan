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
        <table class="table table-striped table-bordered">
            <tr>
                <th>ประเภท</th>
                <th style="width: 15%; text-align: center;">ประมาณการ</th>
                <th style="width: 15%; text-align: center;">ส่งเอกสาร</th>
                <th style="width: 15%; text-align: center;">ออกใบสั่งซื้อ</th>
                <th style="width: 15%; text-align: center;">ส่งเบิกเงิน</th>
                <th style="width: 15%; text-align: center;">ตั้งหนี้</th>
            </tr>
            <tr ng-repeat="(index, asset) in assets" style="font-size: 12px;">
                <td>@{{ index+1 }}. @{{ asset.category_name }}</td>
                <td style="text-align: right;">@{{ asset.budget | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ asset.sent | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ asset.po | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ asset.withdraw | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ asset.debt | currency:'':0 }}</td>
            </tr>
        </table>
    </div><!-- /.box-body -->
    <div class="box-footer">
        <div class="row" ng-show="false">
            <div class="col-md-4">
                <span style="margin-top: 5px;" ng-show="departPager.last_page > 0">
                    หน้า @{{ departPager.current_page }} จาก @{{ departPager.last_page }}
                </span>
            </div>
            <div class="col-md-4" style="text-align: center;">
                จำนวนทั้งสิ้น @{{ departTotal }} บาท
            </div>
            <div class="col-md-4">
                <ul class="pagination pagination-sm no-margin pull-right" ng-show="departPager.last_page > 1">
                    <li ng-if="departPager.current_page !== 1">
                        <a href="#" ng-click="getDataWithURL($event, departPager.path+ '?page=1', setDepartLeaves)" aria-label="Previous">
                            <span aria-hidden="true">First</span>
                        </a>
                    </li>
                
                    <li ng-class="{'disabled': (departPager.current_page==1)}">
                        <a href="#" ng-click="getDataWithURL($event, departPager.prev_page_url, setDepartLeaves)" aria-label="Prev">
                            <span aria-hidden="true">Prev</span>
                        </a>
                    </li>

                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': departPager.current_page==i}">
                        <a href="#" ng-click="getDataWithURL($event, departPager.path + '?page=' +i, setDepartLeaves)">
                            @{{ i }}
                        </a>
                    </li> -->

                    <!-- <li ng-if="departPager.current_page < departPager.last_page && (departPager.last_page - departPager.current_page) > 10">
                        <a href="#" ng-click="departPager.path">
                            ...
                        </a>
                    </li> -->

                    <li ng-class="{'disabled': (departPager.current_page==departPager.last_page)}">
                        <a href="#" ng-click="getDataWithURL($event, departPager.next_page_url, setDepartLeaves)" aria-label="Next">
                            <span aria-hidden="true">Next</span>
                        </a>
                    </li>

                    <li ng-if="departPager.current_page !== departPager.last_page">
                        <a href="#" ng-click="getDataWithURL($event, departPager.path+ '?page=' +departPager.last_page, setDepartLeaves)" aria-label="Previous">
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