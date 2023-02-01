<div class="modal fade" id="details-list" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
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
                                <th style="width: 12%; text-align: center;">ราคาต่อหน่วย</th>
                                <th style="width: 8%; text-align: center;">หน่วยนับ</th>
                                <th style="width: 8%; text-align: center;">จำนวน</th>
                                <th style="width: 12%; text-align: center;">รวมเป็นเงิน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- ============================ Plan group ============================ -->
                            <tr ng-show="supportDetails.is_plan_group">
                                <td style="text-align: center">@{{ index+1 }}</td>
                                <td>
                                    @{{ supportDetails.plan_group_desc }}
                                    <span class="badge badge-danger">
                                        <i class="fa fa-tags" aria-hidden="true"></i>
                                        Groups
                                    </span>
                                    <ul style="list-style-type: none; margin: 0; padding: 0 0 0 10px; font-size: 12px;">
                                        <li ng-repeat="(index, detail) in supportDetails.details" style="margin: 0; padding: 0;">
                                            -<span ng-show="!isRenderWardInsteadDepart(detail.plan.depart.depart_id)">
                                                @{{ detail.plan.depart.depart_name }}
                                            </span><span ng-show="isRenderWardInsteadDepart(detail.plan.depart.depart_id)">
                                                @{{ detail.plan.division.ward_name }}
                                            </span>
                                            @{{ currencyToNumber(detail.amount) | currency:'':0 }}
                                            @{{ detail.unit_name }}
                                        </li>
                                    </ul>
                                </td>
                                <td style="text-align: center">
                                    @{{ supportDetails.details[0].price_per_unit | currency:'':2 }}
                                </td>
                                <td style="text-align: center">
                                    @{{ supportDetails.details[0].unit.name }}
                                </td>
                                <td style="text-align: center">
                                    @{{ supportDetails.plan_group_amt | currency:'':0 }}
                                </td>
                                <td style="text-align: center">
                                    @{{ supportDetails.total | currency:'':2 }}
                                </td>
                            </tr>
                            <!-- ============================ End Plan group ============================ -->
                            <tr ng-repeat="(index, detail) in supportDetails.details" ng-show="!supportDetails.is_plan_group">
                                <td style="text-align: center;">
                                    @{{ index+1 }}
                                </td>
                                <td>
                                    @{{ detail.plan.plan_item.item.item_name }}
                                    <p>@{{ detail.desc }}</p>
                                </td>
                                <td style="text-align: center;">
                                    @{{ detail.price_per_unit | currency:'':2 }}
                                </td>
                                <td style="text-align: center;">
                                    @{{ detail.unit.name }}
                                </td>
                                <td style="text-align: center;">
                                    @{{ detail.amount | currency:'':2 }}
                                </td>
                                <td style="text-align: center;">
                                    @{{ detail.sum_price | currency:'':2 }}
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
