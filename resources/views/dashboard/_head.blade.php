<div class="box" ng-init="getHeadLeaves()">
    <div class="box-header">
        <h3 class="box-title">หัวหน้าลาประจำวัน</h3>
        <div class="pull-right box-tools">
            <div class="row">
                <div class="form-group col-md-12" style="margin-bottom: 0px;">
                    <input
                        type="text"
                        id="cboHeadDate"
                        name="cboHeadDate"
                        class="form-control"
                    />
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-triped" style="margin-bottom: 1rem; ng-show="!loading">
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th>ชื่อ-สกุล</th>
                <th style="width: 30%; text-align: center;">ประเภท</th>
                <th style="width: 30%; text-align: center;">จำนวน (วัน)</th>
            </tr>
            <tr ng-repeat="(index, leave) in headLeaves">
                <td style="text-align: center;">@{{ pager.from+index }}</td>
                <td>@{{ leave.person.person_firstname + ' ' + leave.person.person_lastname }}</td>
                <td style="text-align: center;">@{{ leave.type.name }}</td>
                <td style="text-align: center;">@{{ leave.leave_days }}</td>
            </tr>
        </table>
    </div><!-- /.box-body -->
    <div class="box-footer">
        <div class="row">
            <div class="col-md-4">
                <span style="margin-top: 5px;" ng-show="pager.last_page > 0">
                    หน้า @{{ pager.current_page }} จาก @{{ pager.last_page }}
                </span>
            </div>
            <div class="col-md-4" style="text-align: center;">
                จำนวน @{{ pager.total }} รายการ
            </div>
            <div class="col-md-4">
                <ul class="pagination pagination-sm no-margin pull-right" ng-show="pager.last_page > 1">
                    <li ng-if="pager.current_page !== 1">
                        <a href="#" ng-click="getDataWithURL($event, pager.path+ '?page=1', setHeadLeaves)" aria-label="Previous">
                            <span aria-hidden="true">First</span>
                        </a>
                    </li>
                
                    <li ng-class="{'disabled': (pager.current_page==1)}">
                        <a href="#" ng-click="getDataWithURL($event, pager.prev_page_url, setHeadLeaves)" aria-label="Prev">
                            <span aria-hidden="true">Prev</span>
                        </a>
                    </li>

                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': pager.current_page==i}">
                        <a href="#" ng-click="getDataWithURL($event, pager.path + '?page=' +i, setHeadLeaves)">
                            @{{ i }}
                        </a>
                    </li> -->

                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                        <a href="#" ng-click="pager.path">
                            ...
                        </a>
                    </li> -->

                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                        <a href="#" ng-click="getDataWithURL($event, pager.next_page_url, setHeadLeaves)" aria-label="Next">
                            <span aria-hidden="true">Next</span>
                        </a>
                    </li>

                    <li ng-if="pager.current_page !== pager.last_page">
                        <a href="#" ng-click="getDataWithURL($event, pager.path+ '?page=' +pager.last_page, setHeadLeaves)" aria-label="Previous">
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
