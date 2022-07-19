<div class="modal fade" id="support-details" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 65%;">
        <div class="modal-content">
            <form action="">
                <div class="modal-header">
                    <h5 class="modal-title">รายการพัสดุ</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div class="modal-body" style="padding-bottom: 0;">
                    <table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
                        <thead>
                            <tr>
                                <th style="width: 4%; text-align: center;">#</th>
                                <th>รายการ</th>
                                <th style="width: 12%; text-align: center;">จำนวน</th>
                                <th style="width: 12%; text-align: center;">ราคา</th>
                                <th style="width: 12%; text-align: center;">เป็นเงิน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="(index, detail) in items">
                                <td style="text-align: center;">@{{ index+1 }}</td>
                                <td>
                                    @{{ detail.plan.plan_no }} - @{{ detail.plan.plan_item.item.item_name }}
                                    <p style="margin: 0;">- @{{ detail.desc }}</p>
                                </td>
                                <td style="text-align: center;">
                                    <span>@{{ detail.amount | currency:'':0 }}</span>
                                    <span>@{{ detail.unit.name }}</span>
                                </td>
                                <td style="text-align: center;">
                                    @{{ detail.price_per_unit | currency:'':0 }}
                                </td>
                                <td style="text-align: center;">
                                    @{{ detail.sum_price | currency:'':0 }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
