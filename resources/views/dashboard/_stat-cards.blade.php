<div class="row" ng-init="getStatYear()">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>
                    @{{ statCards[0].num }}
                    <span style="font-size: 14px;">ครั้ง</span>
                </h3>
                <p><h4>ลาป่วยทั้งหมด</h4></p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>
                    @{{ statCards[1].num }}
                    <span style="font-size: 14px;">ครั้ง</span>
                    <!-- <sup style="font-size: 20px">%</sup> -->
                </h3>
                <p><h4>ลากิจทั้งหมด</h4></p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>
                    @{{ statCards[2].num }}
                    <span style="font-size: 14px;">ครั้ง</span>
                </h3>
                <p><h4>ลาพักผ่อนทั้งหมด</h4></p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3>
                    @{{ '0' }}
                    <span style="font-size: 14px;">ครั้ง</span>
                </h3>
                <p><h4>ลาคลอดทั้งหมด</h4></p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div><!-- ./col -->
</div><!-- /.row -->