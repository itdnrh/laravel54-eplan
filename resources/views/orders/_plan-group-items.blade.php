<div class="modal fade" id="plan-group-items" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 80%">
        <div class="modal-content">
            <form action="">
                <div class="modal-header">
                    <h5 class="modal-title">รายละเอียดใบขอสนับสนุน</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div class="modal-body" style="padding-bottom: 0;">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%; text-align: center;">#</th>
                                <th>รายการ</th>
                                <!-- <th style="width: 20%; text-align: center;">ประเภท</th> -->
                                <th style="width: 10%; text-align: right;">ราคาต่อหน่วย</th>
                                <th style="width: 8%; text-align: center;">หน่วยนับ</th>
                                <th style="width: 10%; text-align: right;">จำนวน</th>
                                <th style="width: 10%; text-align: right;">รวมเป็นเงิน</th>
                                <th style="width: 6%; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="(index, item) in planGroupItems">
                                <td style="text-align: center;">
                                    @{{ index+1 }}
                                </td>
                                <td>
                                    <p style="margin: 0;">@{{ item.plan_depart }}</p>
                                    <p style="margin: 0;">@{{ item.plan_detail }}</p>
                                    <p style="margin: 0;">@{{ item.desc }}</p>
                                </td>
                                <!-- <td>@{{ item.category.name }}</td> -->
                                <td style="text-align: right;">
                                    @{{ item.price_per_unit | currency:'':2 }}
                                </td>
                                <td style="text-align: center;">
                                    @{{ item.unit.name }}
                                </td>
                                <td style="text-align: right;">
                                    @{{ item.amount | currency:'':2 }}
                                </td>
                                <td style="text-align: right;">
                                    @{{ item.sum_price | currency:'':2 }}
                                </td>
                                <td style="text-align: center;">
                                    <a
                                        href="#"
                                        ng-click="deletePlanGroupItem($event, item)"
                                        class="btn btn-danger btn-xs"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="loading-wrapper" ng-show="supportDetails.length === 0">
                        <!-- Loading (remove the following to stop the loading)-->
                        <div ng-show="loading" class="overlay">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <!-- end loading -->
                    </div>

                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        ปิด
                    </button>
                </div><!-- /.modal-footer -->
            </form>
        </div>
    </div>
</div>
