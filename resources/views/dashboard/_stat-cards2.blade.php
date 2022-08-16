<div class="row" ng-init="getStat2()">
    <div class="col-lg-6 col-xs-12">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3 style="font-size: 28px;">
                    @{{ stat2Cards[0].sum_all | currency:'':0 }}
                    <span style="font-size: 14px;">บาท</span>
                </h3>
                <p><h4>แผนครุภัณฑ์</h4></p>
            </div>
            <div class="icon">
                <i class="ion ion-headphone"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-6 col-xs-12">
        <div class="small-box bg-gray">
            <div class="inner">
                <h3 style="font-size: 28px;">
                    @{{ stat2Cards[1].sum_all | currency:'':0 }}
                    <span style="font-size: 14px;">บาท</span>
                    <!-- <sup style="font-size: 20px">%</sup> -->
                </h3>
                <p><h4>แผนวัสดุ</h4></p>
            </div>
            <div class="icon">
                <i class="ion ion-person"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-6 col-xs-12">
        <div class="small-box bg-teal">
            <div class="inner">
                <h3 style="font-size: 28px;">
                    @{{ stat2Cards[2].sum_all | currency:'':0 }}
                    <span style="font-size: 14px;">บาท</span>
                </h3>
                <p><h4>แผนจ้างบริการ</h4></p>
            </div>
            <div class="icon">
                <i class="ion ion-paperclip"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-6 col-xs-12">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3 style="font-size: 28px;">
                    @{{ stat2Cards[3].sum_all | currency:'':0 }}
                    <span style="font-size: 14px;">บาท</span>
                    <!-- <sup style="font-size: 20px">%</sup> -->
                </h3>
                <p><h4>แผนก่อสร้าง</h4></p>
            </div>
            <div class="icon">
                <i class="ion ion-ios-home"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div><!-- ./col -->
</div><!-- /.row -->