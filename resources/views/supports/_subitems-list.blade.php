<div class="modal fade" id="subitems-list" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 80%">
        <div class="modal-content">
            <form action="">
                <div class="modal-header">
                    <h5 class="modal-title">รายการสินค้า/บริการ</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div class="modal-body" style="padding-bottom: 0;">
                    <!-- // TODO: Filtering controls -->
                    <div class="box">
                        <div class="box-body">
                            <div style="display: flex; gap: 5px;">
                                <select
                                    type="text"
                                    id="cboCategory"
                                    name="cboCategory"
                                    ng-model="cboCategory"
                                    ng-change="handleInputChange('cboCategory', cboCategory); getItems();"
                                    class="form-control"
                                >
                                    <option value="">-- เลือกประเภทสินค้า/บริการ --</option>
                                    <option ng-repeat="category in forms.categories" value="@{{ category.id }}">
                                        @{{ category.name }}
                                    </option>
                                </select>
        
                                <input
                                    type="text"
                                    ng-model="searchKey"
                                    class="form-control"
                                    ng-keyup="handleInputChange('searchKey', searchKey); getItems();"
                                />
                            </div>
                        </div><!-- /.box-body -->
                    </div>
                    <!-- // TODO: Filtering controls -->

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%; text-align: center;">#</th>
                                <th>รายการ</th>
                                <th style="width: 8%; text-align: center;">หน่วยนับ</th>
                                <th style="width: 10%;">ราคาต่อหน่วย</th>
                                <th style="width: 6%; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="(index, item) in items">
                                <td style="text-align: center;">
                                    @{{ items_pager.from + index }}
                                </td>
                                <td>
                                    @{{ item.item_name }}
                                </td>
                                <td style="text-align: center;">
                                    @{{ item.unit.name }}
                                </td>
                                <td style="text-align: center;">
                                    @{{ item.price_per_unit | currency:'':2 }}
                                </td>
                                <td style="text-align: center;">
                                    <a href="#" class="btn btn-primary" ng-click="handleItemSelected($event, item, onSelectedItem)">
                                        เลือก
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="loading-wrapper" ng-show="items.length === 0">
                        <!-- Loading (remove the following to stop the loading)-->
                        <div ng-show="loading" class="overlay">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <!-- end loading -->
                    </div>

                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <div class="row">
                        <div class="col-md-4">
                            <span class="pull-left" style="margin-top: 5px;">
                                หน้า @{{ items_pager.current_page }} จาก @{{ items_pager.last_page }} | 
                                จำนวน @{{ items_pager.total }} รายการ
                            </span>
                        </div>
                        <div class="col-md-4">
                            <ul class="pagination pagination-sm no-margin">
                                <li ng-if="items_pager.current_page !== 1">
                                    <a ng-click="getItemsWithUrl($event, items_pager.path+ '?page=1', setItems)" aria-label="Previous">
                                        <span aria-hidden="true">First</span>
                                    </a>
                                </li>

                                <li ng-class="{'disabled': (items_pager.current_page==1)}">
                                    <a ng-click="getItemsWithUrl($event, items_pager.prev_page_url, setItems)" aria-label="Prev">
                                        <span aria-hidden="true">Prev</span>
                                    </a>
                                </li>

                                <!-- <li ng-if="items_pager.current_page < items_pager.last_page && (items_pager.last_page - items_pager.current_page) > 10">
                                    <a href="@{{ items_pager.url(items_pager.current_page + 10) }}">
                                        ...
                                    </a>
                                </li> -->

                                <li ng-class="{'disabled': (items_pager.current_page==items_pager.last_page)}">
                                    <a ng-click="getItemsWithUrl($event, items_pager.next_page_url, setItems)" aria-label="Next">
                                        <span aria-hidden="true">Next</span>
                                    </a>
                                </li>

                                <li ng-if="items_pager.current_page !== items_pager.last_page">
                                    <a ng-click="getItemsWithUrl($event, items_pager.path+ '?page=' +items_pager.last_page, setItems)" aria-label="Previous">
                                        <span aria-hidden="true">Last</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger" ng-click="handleItemSelected($event, null, onSelectedItem)">
                                ปิด
                            </button>
                        </div>
                    </div>
                </div><!-- /.modal-footer -->
            </form>
        </div>
    </div>
</div>
