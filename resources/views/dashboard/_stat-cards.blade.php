<div class="row" ng-init="getStat1()">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3 style="font-size: 28px;">
                    @{{ stat1Cards[0].sum_all | currency:'':0 }}
                    <span style="font-size: 14px;">บาท</span>
                </h3>
                <p><h4>แผนทั้งหมด</h4></p>
            </div>
            <div class="icon">
                <i class="ion ion-connection-bars"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3 style="font-size: 28px;">
                    @{{ '0' | currency:'':0 }}
                    <span style="font-size: 14px;">บาท</span>
                    <!-- <sup style="font-size: 20px">%</sup> -->
                </h3>
                <p><h4>ออกใบสั้งซื้อ</h4></p>
            </div>
            <div class="icon">
                <i class="ion ion-paper-airplane"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3 style="font-size: 28px;">
                    @{{ '0' | currency:'':0 }}
                    <span style="font-size: 14px;">บาท</span>
                </h3>
                <p><h4>ส่งเบิกเงินแล้ว</h4></p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3 style="font-size: 28px;">
                    @{{ '0' | currency:'':0 }}
                    <span style="font-size: 14px;">บาท</span>
                </h3>
                <p><h4>ตั้งหนี้แล้ว</h4></p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div><!-- ./col -->
</div><!-- /.row -->