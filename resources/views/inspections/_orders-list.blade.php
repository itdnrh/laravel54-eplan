<div class="modal fade" id="orders-list" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content">
            <form action="">
                <div class="modal-header">
                    <h5 class="modal-title">รายการใบสั่งซื้อ</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div class="modal-body" style="padding-bottom: 0;">
                    <!-- // TODO: Filtering controls -->
                    <div class="box">
                        <div class="box-body">
                            <div style="display: flex; flex-direction: row;">
                                <select
                                    style="margin-right: 1rem;"
                                    class="form-control"
                                    ng-model="cboPlanType"
                                    ng-change="onFilterCategories(cboPlanType); getOrder(cboPlanType, 0);"
                                >
                                    <option value="">-- เลือกประเภทแผน --</option>
                                    @foreach($planTypes as $planType)
                                        <option value="{{ $planType->id }}">
                                            {{ $planType->plan_type_name }}
                                        </option>
                                    @endforeach
                                </select>
        
                                <select
                                    style="margin-right: 1rem;"
                                    class="form-control"
                                    ng-model="cboCategory"
                                    ng-change="getOrder(cboPlanType, 0);"
                                >
                                    <option value="">-- เลือกประเภทพัสดุ --</option>
                                    <option ng-repeat="category in forms.categories" value="@{{ category.id }}">
                                            @{{ category.name }}
                                    </option>
                                </select>
                            </div>
                        </div><!-- /.box-body -->
                    </div>
                    <!-- // TODO: Filtering controls -->

                    <table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
                        <thead>
                            <tr>
                                <th style="width: 3%; text-align: center;">#</th>
                                <th style="width: 8%; text-align: center;">ปีงบ</th>
                                <th style="width: 12%; text-align: center;">ใบสั่งซื้อ</th>
                                <th>รายการ</th>
                                <th style="width: 8%; text-align: center;">ยอดสุทธิ</th>
                                <!-- <th style="width: 20%; text-align: center;">หน่วยงาน</th> -->
                                <th style="width: 5%; text-align: center;">สถานะ</th>
                                <th style="width: 10%; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="(index, order) in orders">
                                <td style="text-align: center;">@{{ index+orders_pager.from }}</td>
                                <td style="text-align: center;">@{{ order.year }}</td>
                                <td>
                                    <p style="margin: 0;">เลขที่ @{{ order.po_no }}</p>
                                    <p style="margin: 0;">วันที่ @{{ order.po_date | thdate }}</p>
                                </td>
                                <td>
                                    <h4 style="margin: 0;">@{{ order.supplier.supplier_name }}</h4>
                                    <ul style="margin: 0 5px; padding: 0 10px;">
                                        <li ng-repeat="(index, detail) in order.details">
                                            <p style="margin: 0;">@{{ detail.item.category.name }}</p>
                                            <p style="margin: 0;">
                                                @{{ detail.item.item_name }} จำนวน 
                                                <span>@{{ detail.amount | currency:'':0 }}</span>
                                                <span>@{{ detail.unit.name }}</span>
                                                <span>รวมเป็นเงิน @{{ detail.sum_price | currency:'':2 }} บาท</span>
                                                <!-- <a  href="{{ url('/'). '/uploads/' }}@{{ order.attachment }}"
                                                    class="btn btn-default btn-xs" 
                                                    title="ไฟล์แนบ"
                                                    target="_blank"
                                                    ng-show="order.attachment">
                                                    <i class="fa fa-paperclip" aria-hidden="true"></i>
                                                </a> -->
                                            </p>
                                        </li>
                                    </ul>
                                </td>
                                <td style="text-align: center;">
                                    @{{ order.net_total | currency:'':0 }}
                                </td>
                                <!-- <td style="text-align: center;">
                                    <p style="margin: 0;">@{{ order.depart.depart_name }}</p>
                                    <p style="margin: 0;">@{{ order.division.ward_name }}</p>
                                </td> -->
                                <td>
                                    <span class="label label-primary" ng-show="order.status == 0">
                                        รอดำเนินการ
                                    </span>
                                    <span class="label bg-navy" ng-show="order.status == 1">
                                        ตรวจรับแล้ว
                                    </span>
                                    <span class="label label-success" ng-show="order.status == 2">
                                        ส่งเบิกเงินแล้ว
                                    </span>
                                    <span class="label label-success" ng-show="order.status == 9">
                                        ยกเลิก
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                        <a  href="#"
                                            ng-click="onSelectedOrder($event, order)"
                                            class="btn btn-primary btn-xs"
                                            title="เลือก">
                                            เลือก
                                        </a>
                                </td>             
                            </tr>
                        </tbody>
                    </table>

                    <!-- Loading (remove the following to stop the loading)-->
                    <div style="width: 100%; height: 50px; text-align: center;">
                        <div ng-show="loading" class="overlay">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                    <!-- end loading -->

                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <div class="row">
                        <div class="col-md-4">
                            <span class="pull-left" style="margin-top: 5px;">
                                หน้า @{{ orders_pager.current_page }} จาก @{{ orders_pager.last_page }} | 
                                จำนวน @{{ orders_pager.total }} รายการ
                            </span>
                        </div>
                        <div class="col-md-4">
                            <ul class="pagination pagination-sm no-margin">
                                <li ng-if="orders_pager.current_page !== 1">
                                    <a ng-click="getPlansWithUrl($event, orders_pager.path+ '?page=1', 1, setPlans)" aria-label="Previous">
                                        <span aria-hidden="true">First</span>
                                    </a>
                                </li>

                                <li ng-class="{'disabled': (orders_pager.current_page==1)}">
                                    <a ng-click="getPlansWithUrl($event, orders_pager.prev_page_url, 1, setPlans)" aria-label="Prev">
                                        <span aria-hidden="true">Prev</span>
                                    </a>
                                </li>

                                <!-- <li ng-if="orders_pager.current_page < orders_pager.last_page && (orders_pager.last_page - orders_pager.current_page) > 10">
                                    <a href="@{{ orders_pager.url(orders_pager.current_page + 10) }}">
                                        ...
                                    </a>
                                </li> -->

                                <li ng-class="{'disabled': (orders_pager.current_page==orders_pager.last_page)}">
                                    <a ng-click="getPlansWithUrl($event, orders_pager.next_page_url, 1, setPlans)" aria-label="Next">
                                        <span aria-hidden="true">Next</span>
                                    </a>
                                </li>

                                <li ng-if="orders_pager.current_page !== orders_pager.last_page">
                                    <a ng-click="getPlansWithUrl($event, orders_pager.path+ '?page=' +orders_pager.last_page, 1, setPlans)" aria-label="Previous">
                                        <span aria-hidden="true">Last</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger" ng-click="onSelectedOrder($event, null)">
                                ปิด
                            </button>
                        </div>
                    </div>
                </div><!-- /.modal-footer -->
            </form>
        </div>
    </div>
</div>
