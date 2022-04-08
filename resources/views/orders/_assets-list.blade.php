<div class="modal fade" id="assets-list" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="">
                <div class="modal-header">
                    <h5 class="modal-title">รายการครุภัณฑ์</h5>
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
                                    ng-model="cboDepart"
                                    ng-change="onFilterPerson()"
                                >
                                    <option value="">--เลือกกลุ่มงาน--</option>
                                    @foreach($departs as $depart)
                                        <option value="{{ $depart->depart_id }}">{{ $depart->depart_name }}</option>
                                    @endforeach
                                </select>
        
                                <input type="text" ng-model="searchKey" class="form-control" ng-keyup="onFilterPerson()">
                            </div>
                        </div><!-- /.box-body -->
                    </div>
                    <!-- // TODO: Filtering controls -->

                    <table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
                        <thead>
                            <tr>
                                <th style="width: 3%; text-align: center;">#</th>
                                <th style="width: 8%; text-align: center;">ปีงบ</th>
                                <th>รายการ</th>
                                <th style="width: 8%; text-align: center;">ราคาต่อหน่วย</th>
                                <th style="width: 8%; text-align: center;">รวมเป็นเงิน</th>
                                <th style="width: 20%; text-align: center;">หน่วยงาน</th>
                                <th style="width: 10%; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="(index, asset) in assets">
                                <td style="text-align: center;">@{{ index+assets_pager.from }}</td>
                                <td style="text-align: center;">@{{ asset.year }}</td>
                                <td>
                                    <p style="margin: 0;">@{{ asset.category.category_name }}</p>
                                    @{{ asset.desc }} จำนวน 
                                    <span>@{{ asset.amount | currency:'':0 }}</span>
                                    <span>@{{ asset.unit.name }}</span>
                                    <a  href="{{ url('/'). '/uploads/' }}@{{ asset.attachment }}"
                                        class="btn btn-default btn-xs" 
                                        title="ไฟล์แนบ"
                                        target="_blank"
                                        ng-show="asset.attachment">
                                        <i class="fa fa-paperclip" aria-hidden="true"></i>
                                    </a>
                                </td>
                                <td style="text-align: center;">
                                    @{{ asset.price_per_unit | currency:'':0 }}
                                </td>
                                <td style="text-align: center;">
                                    @{{ asset.sum_price | currency:'':0 }}
                                </td>
                                <td style="text-align: center;">
                                    <p style="margin: 0;">@{{ asset.depart.depart_name }}</p>
                                    <p style="margin: 0;">@{{ asset.division.ward_name }}</p>
                                </td>
                                <td style="text-align: center;">
                                        <a  href="#"
                                            ng-click="onSelectedPlan($event, asset)"
                                            class="btn btn-primary btn-xs"
                                            title="เลือก">
                                            เลือก
                                        </a>
                                </td>             
                            </tr>
                        </tbody>
                    </table>

                    <!-- Loading (remove the following to stop the loading)-->
                    <div ng-show="loading" class="overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <!-- end loading -->

                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <div class="row">
                        <div class="col-md-4">
                            <span class="pull-left" style="margin-top: 5px;">
                                หน้า @{{ assets_pager.current_page }} จาก @{{ assets_pager.last_page }} | 
                                จำนวน @{{ assets_pager.total }} รายการ
                            </span>
                        </div>
                        <div class="col-md-4">
                            <ul class="pagination pagination-sm no-margin">
                                <li ng-if="assets_pager.current_page !== 1">
                                    <a ng-click="getDataWithURL($event, assets_pager.path+ '?page=1', setPersons)" aria-label="Previous">
                                        <span aria-hidden="true">First</span>
                                    </a>
                                </li>

                                <li ng-class="{'disabled': (assets_pager.current_page==1)}">
                                    <a ng-click="getDataWithURL($event, assets_pager.prev_page_url, setPersons)" aria-label="Prev">
                                        <span aria-hidden="true">Prev</span>
                                    </a>
                                </li>

                                <!-- <li ng-if="assets_pager.current_page < assets_pager.last_page && (assets_pager.last_page - assets_pager.current_page) > 10">
                                    <a href="@{{ assets_pager.url(assets_pager.current_page + 10) }}">
                                        ...
                                    </a>
                                </li> -->

                                <li ng-class="{'disabled': (assets_pager.current_page==assets_pager.last_page)}">
                                    <a ng-click="getDataWithURL($event, assets_pager.next_page_url, setPersons)" aria-label="Next">
                                        <span aria-hidden="true">Next</span>
                                    </a>
                                </li>

                                <li ng-if="assets_pager.current_page !== assets_pager.last_page">
                                    <a ng-click="getDataWithURL($event, assets_pager.path+ '?page=' +assets_pager.last_page, setPersons)" aria-label="Previous">
                                        <span aria-hidden="true">Last</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger" ng-click="onSelectedPlan($event, null)">
                                ปิด
                            </button>
                        </div>
                    </div>
                </div><!-- /.modal-footer -->
            </form>
        </div>
    </div>
</div>
