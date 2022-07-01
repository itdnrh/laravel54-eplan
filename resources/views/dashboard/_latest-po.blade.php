<div class="box box-success" ng-init="getLatestOrders();">
    <div class="box-header">
        <h3 class="box-title">
            รายการใบสั่งซื้อล่าสุด
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
            <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 8%; text-align: center;">เลขที่ P/O</th>
                <th style="width: 8%; text-align: center;">วันที่ P/O</th>
                <th style="width: 10%; text-align: center;">ประเภทพัสดุ</th>
                <th>เจ้าหนี้</th>
                <th style="width: 6%; text-align: center;">ปีงบ</th>
                <th style="width: 6%; text-align: center;">จำนวนรายการ</th>
                <th style="width: 10%; text-align: center;">ยอดจัดซื้อ</th>
                <th style="width: 10%; text-align: center;">สถานะ</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="(index, order) in orders">
                <td style="text-align: center;">@{{ index+orders_pager.from }}</td>
                <td style="text-align: center;">@{{ order.po_no }}</td>
                <td style="text-align: center;">@{{ order.po_date | thdate }}</td>
                <td style="text-align: center;">@{{ order.plan_type.plan_type_name }}</td>
                <td>@{{ order.supplier.supplier_name }}</td>
                <td style="text-align: center;">@{{ order.year }}</td>
                <td style="text-align: center;">
                    @{{ order.details.length }}
                    <!-- <a  href="#"
                        ng-click="showOrderDetails(order.details)"
                        class="btn btn-default btn-xs" 
                        title="รายการ">
                        <i class="fa fa-clone"></i>
                    </a> -->
                </td>
                <td style="text-align: center;">@{{ order.net_total | currency:'':0 }}</td>
                <td style="text-align: center;">
                    <span class="label label-primary" ng-show="order.status == 0">
                        อยู่ระหว่างจัดซื้อจัดจ้าง
                    </span>
                    <span class="label label-info" ng-show="order.status == 1">
                        อนุมัติจัดซื้อจัดจ้าง
                    </span>
                    <span class="label bg-maroon" ng-show="order.status == 2">
                        ตรวจรับแล้วบางงวด
                    </span>
                    <span class="label label-success" ng-show="order.status == 3">
                        ตรวจรับทั้งหมดแล้ว
                    </span>
                    <span class="label label-warning" ng-show="order.status == 4">
                        ส่งเบิกเงินแล้ว
                    </span>
                    <span class="label label-danger" ng-show="order.status == 9">
                        ยกเลิก
                    </span>
                </td>
            </tr>
        </tbody>
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