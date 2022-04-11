<div class="row" ng-init="getStatYear()">
    <div class="col-lg-6 col-xs-12">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>
                    @{{ statCards[0].num }}
                    <span style="font-size: 14px;">บาท</span>
                </h3>
                <p><h4>แผนจ้างเหมาบริการ</h4></p>
            </div>
            <div class="icon">
                <i class="ion ion-person"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-6 col-xs-12">
        <div class="small-box bg-gray">
            <div class="inner">
                <h3>
                    @{{ statCards[1].num }}
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