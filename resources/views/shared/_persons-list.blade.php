<div class="modal fade" id="persons-list" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="">
                <div class="modal-header">
                    <h5 class="modal-title">รายชื่อบุคลากร</h5>
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
                                    ng-change="getPersons()"
                                >
                                    <option value="">--เลือกกลุ่มงาน--</option>
                                    @foreach($departs as $depart)
                                        <option value="{{ $depart->depart_id }}">{{ $depart->depart_name }}</option>
                                    @endforeach
                                </select>
        
                                <input type="text" ng-model="searchKey" class="form-control" ng-keyup="getPersons()">
                            </div>
                        </div><!-- /.box-body -->
                    </div>
                    <!-- // TODO: Filtering controls -->

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%; text-align: center;">#</th>
                                <th>ชื่อ-สกุล</th>
                                <th style="width: 25%;">ตำแหน่ง</th>
                                <th style="width: 30%;">สังกัด</th>
                                <th style="width: 10%; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="(index, person) in persons">
                                <td style="text-align: center;">
                                    @{{ persons_pager.from + index }}
                                </td>
                                <td>
                                    @{{ person.prefix.prefix_name + person.person_firstname + ' ' + person.person_lastname }}
                                </td>
                                <td>
                                    @{{ person.position.position_name + person.academic.ac_name }}
                                </td>
                                <td>
                                    @{{ person.member_of.depart.depart_name }}
                                </td>
                                <td style="text-align: center;">
                                    <a href="#" class="btn btn-primary" ng-click="onSelectedPerson(selectedMode, person)">
                                        เลือก
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Loading (remove the following to stop the loading)-->
                    <div class="loading-wrapper" ng-show="loading">
                        <div class="overlay">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                    <!-- end loading -->

                </div><!-- /.modal-body -->
                <div class="modal-footer" style="padding-bottom: 8px;">
                    <div class="row">
                        <div class="col-md-4">
                            <span class="pull-left" style="margin-top: 5px;">
                                หน้า @{{ persons_pager.current_page }} จาก @{{ persons_pager.last_page }} | 
                                จำนวน @{{ persons_pager.total }} รายการ
                            </span>
                        </div>
                        <div class="col-md-4">
                            <ul class="pagination pagination-sm no-margin">
                                <li ng-if="persons_pager.current_page !== 1">
                                    <a ng-click="getPersonsWithUrl($event, persons_pager.path+ '?page=1', setPersons)" aria-label="Previous">
                                        <span aria-hidden="true">First</span>
                                    </a>
                                </li>

                                <li ng-class="{'disabled': (persons_pager.current_page==1)}">
                                    <a ng-click="getPersonsWithUrl($event, persons_pager.prev_page_url, setPersons)" aria-label="Prev">
                                        <span aria-hidden="true">Prev</span>
                                    </a>
                                </li>

                                <!-- <li ng-if="persons_pager.current_page < persons_pager.last_page && (persons_pager.last_page - persons_pager.current_page) > 10">
                                    <a href="@{{ persons_pager.url(persons_pager.current_page + 10) }}">
                                        ...
                                    </a>
                                </li> -->

                                <li ng-class="{'disabled': (persons_pager.current_page==persons_pager.last_page)}">
                                    <a ng-click="getPersonsWithUrl($event, persons_pager.next_page_url, setPersons)" aria-label="Next">
                                        <span aria-hidden="true">Next</span>
                                    </a>
                                </li>

                                <li ng-if="persons_pager.current_page !== persons_pager.last_page">
                                    <a ng-click="getPersonsWithUrl($event, persons_pager.path+ '?page=' +persons_pager.last_page, setPersons)" aria-label="Previous">
                                        <span aria-hidden="true">Last</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger" ng-click="onSelectedPerson(selectedMode, null)">
                                ปิด
                            </button>
                        </div>
                    </div>
                </div><!-- /.modal-footer -->
            </form>
        </div>
    </div>
</div>
