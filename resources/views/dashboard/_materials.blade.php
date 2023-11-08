<div class="box" ng-init="getSummaryMaterials()">
    <div class="box-header">
        <h3 class="box-title">
            สรุปแผนวัสดุ
            <!-- <span>ประจำเดือน</span> -->
        </h3>
        <!-- <div class="pull-right box-tools">
            <div class="row">
                <div class="form-group col-md-12" style="margin-bottom: 0px;">
                    <input
                        type="text"
                        id="cboMaterialDate"
                        name="cboMaterialDate"
                        class="form-control"
                    />
                </div>
            </div>
        </div> -->
    </div>
    <div class="box-body">
        <table class="table table-striped table-bordered" style="font-size: 12px;" ng-show="!loading">
            <tr>
                <th>ประเภท</th>
                <th style="width: 15%; text-align: right;">ประมาณการ</th>
                <th style="width: 15%; text-align: right;">ยอดอนุมัติ</th>
                <th style="width: 15%; text-align: right;">รับเอกสาร</th>
                <th style="width: 15%; text-align: right;">ออกใบซื้อ/จ้าง</th>
                <th style="width: 15%; text-align: right;">ส่งเบิกเงิน</th>
                <!-- <th style="width: 15%; text-align: right;">ตั้งหนี้</th> -->
            </tr>
            <tr ng-repeat="(index, material) in materials" style="font-size: 12px;">
                <td>@{{ materials_pager.from+index }}. @{{ material.category_name }}</td>
                <td style="text-align: right;">@{{ material.request | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ material.plan_approved | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ material.received | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ material.po | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ material.withdraw | currency:'':0 }}</td>
                <!-- <td style="text-align: right;">@{{ material.debt | currency:'':0 }}</td> -->
            </tr>
            <tr>
                <td style="text-align: center;">รวม</td>
                <td style="text-align: right;">@{{ totalMaterial.request | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ totalMaterial.plan_approved | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ totalMaterial.received | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ totalMaterial.po | currency:'':0 }}</td>
                <td style="text-align: right;">@{{ totalMaterial.withdraw | currency:'':0 }}</td>
                <!-- <td style="text-align: right;">@{{ totalMaterial.debt | currency:'':0 }}</td> -->
            </tr>
        </table>
    </div><!-- /.box-body -->
    <div class="box-footer" ng-show="false">
        <div class="row" ng-show="materials_pager.last_page > 1">
            <div class="col-md-4">
                <span style="margin-top: 5px;" ng-show="materials_pager.last_page > 0">
                    หน้า @{{ materials_pager.current_page }} จาก @{{ materials_pager.last_page }}
                </span>
            </div>
            <div class="col-md-4" style="text-align: center;">
                จำนวน @{{ materials_pager.total }} รายการ
            </div>
            <div class="col-md-4">
                <ul class="pagination pagination-sm no-margin pull-right" ng-show="materials_pager.last_page > 1">
                    <li ng-class="{'disabled': (materials_pager.current_page == 1)}">
                        <a href="#" ng-click="getMaterialsWithUrl($event, materials_pager.path+ '?page=1', setMaterials)" aria-label="Previous">
                            <span aria-hidden="true">First</span>
                        </a>
                    </li>

                    <!-- <li ng-class="{'disabled': (materials_pager.current_page==1)}">
                        <a href="#" ng-click="getMaterialsWithUrl($event, materials_pager.prev_page_url, setMaterials)" aria-label="Prev">
                            <span aria-hidden="true">Prev</span>
                        </a>
                    </li>

                    <li ng-class="{'disabled': (materials_pager.current_page==materials_pager.last_page)}">
                        <a href="#" ng-click="getMaterialsWithUrl($event, materials_pager.next_page_url, setMaterials)" aria-label="Next">
                            <span aria-hidden="true">Next</span>
                        </a>
                    </li> -->

                    <li ng-class="{'disabled': (materials_pager.current_page == materials_pager.last_page)}">
                        <a href="#" ng-click="getMaterialsWithUrl($event, materials_pager.path+ '?page=' +materials_pager.last_page, setMaterials)" aria-label="Previous">
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
