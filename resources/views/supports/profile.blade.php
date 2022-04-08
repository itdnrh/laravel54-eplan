@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ข้อมูลส่วนตัว
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">ข้อมูลส่วนตัว</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" ng-controller="historyCtrl">
        <div class="row">
            <div class="col-md-3">
                <?php $userPosition = Auth::user()->academic ? Auth::user()->position->position_name.Auth::user()->academic->ac_name : Auth::user()->position->position_name ?>
                <div class="box">
                    <div class="box-body box-profile">
                        <?php $userAvatarUrl = (Auth::user()->person_photo != '') ? "http://192.168.20.4:3839/ps/PhotoPersonal/" .Auth::user()->person_photo : asset('img/user2-160x160.jpg'); ?>
                        <img class="profile-user-img img-responsive img-circle" src="{{ $userAvatarUrl }}" alt="User profile picture">

                        <h3 class="profile-username text-center">
                            {{ Auth::user()->person_firstname. ' ' .Auth::user()->person_lastname }}
                        </h3>

                        <p class="text-muted text-center">
                            {{ $userPosition }}
                        </p>

                        <!-- <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Followers</b> <a class="pull-right">1,322</a>
                            </li>
                            <li class="list-group-item">
                                <b>Following</b> <a class="pull-right">543</a>
                            </li>
                            <li class="list-group-item">
                                <b>Friends</b> <a class="pull-right">13,287</a>
                            </li>
                        </ul> -->

                        <!-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> -->
                    </div><!-- /.box-body -->
                </div><!-- /.box -->

                <!-- About Me Box -->
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">เกี่ยวกับตัวฉัน</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <strong><i class="fa fa-book margin-r-5"></i> การศึกษา</strong>
                        <p class="text-muted" style="text-indent: 20px; border: 1px dotted grey; padding: 5px;">
                            ระดับ {{ $educationLevels[$educations->edu_level] }}
                            สาขา {{ $educations->edu_course }}
                            จาก {{ $educations->edu_house }}
                            เมื่อปี {{ $educations->edu_year }}
                        </p>

                        <hr>

                        <strong><i class="fa fa-map-marker margin-r-5"></i> ที่อยู่</strong>
                        <p class="text-muted" style="text-indent: 20px; border: 1px dotted grey;">
                            -
                        </p>

                        <!-- <hr> -->

                        <!-- <strong><i class="fa fa-pencil margin-r-5"></i> Skills</strong>
                        <p style="text-indent: 20px; border: 1px dotted grey;">
                            <span class="label label-danger">UI Design</span>
                            <span class="label label-success">Coding</span>
                            <span class="label label-info">Javascript</span>
                            <span class="label label-warning">PHP</span>
                            <span class="label label-primary">Node.js</span>
                        </p> -->

                        <!-- <hr> -->

                        <!-- <strong><i class="fa fa-file-text-o margin-r-5"></i> Notes</strong>
                        <p style="text-indent: 20px; border: 1px dotted grey;">
                            -
                        </p> -->
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->

            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#settings" data-toggle="tab">แก้ไขข้อมูล</a></li>
                        <!-- <li><a href="#activity" data-toggle="tab">Activity</a></li>
                        <li><a href="#timeline" data-toggle="tab">Timeline</a></li> -->
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="settings">
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <label for="inputName" class="col-sm-2 control-label">ชื่อ-สกุล</label>

                                    <div class="col-sm-10">
                                        <input
                                            type="email"
                                            class="form-control"
                                            id="inputName"
                                            value="{{ $personInfo->prefix->prefix_name.Auth::user()->person_firstname. ' ' .Auth::user()->person_lastname }}"
                                            placeholder="Name"
                                        />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail" class="col-sm-2 control-label">อีเมล</label>

                                    <div class="col-sm-10">
                                        <input
                                            type="email"
                                            class="form-control"
                                            id="inputEmail"
                                            value="{{ Auth::user()->person_email }}"
                                            placeholder="Email"
                                        />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputName" class="col-sm-2 control-label">โทรศัพท์</label>

                                    <div class="col-sm-10">
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="inputName"
                                            value="{{ Auth::user()->person_tel }}"
                                            placeholder="Name"
                                        />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputSkills" class="col-sm-2 control-label">ตำแหน่ง</label>

                                    <div class="col-sm-10">
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="inputSkills"
                                            value="{{ $userPosition }}"
                                            placeholder="ตำแหน่ง"
                                        />
                                    </div>
                                </div>
                                <?php
                                    $userDuty = $personInfo->memberOf && $personInfo->memberOf->duty
                                                ? $personInfo->memberOf->duty->duty_name
                                                : '';
                                    if (
                                        $personInfo->memberOf && $personInfo->memberOf->duty 
                                        && ($personInfo->memberOf->duty->duty_id <> '1' && $personInfo->memberOf->duty->duty_id <> '6')
                                    ) {
                                        $userDepart = $personInfo->memberOf && $personInfo->memberOf->depart
                                                        ? $personInfo->memberOf->depart->depart_name. ' (' .$userDuty. ')'
                                                        : '';
                                    } else {
                                        $userDepart = $userDuty;
                                    }
                                ?>
                                <div class="form-group">
                                    <label for="inputSkills" class="col-sm-2 control-label">สังกัด</label>
                                    <div class="col-sm-10">
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="inputSkills"
                                            value="{{ $userDepart }}"
                                            placeholder="ตำแหน่ง"
                                        />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputSkills" class="col-sm-2 control-label">วันที่บรรจุ</label>

                                    <div class="col-sm-10">
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="inputSkills"
                                            value="{{ convDbDateToThDate(Auth::user()->person_singin) }}"
                                            placeholder="วันที่บรรจุ"
                                        />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputExperience" class="col-sm-2 control-label">หมายเหตุ</label>

                                    <div class="col-sm-10">
                                        <textarea
                                            rows="5"
                                            class="form-control"
                                            id="inputExperience"
                                            placeholder="หมายเหตุ"
                                        >
                                        {{ Auth::user()->remark }}
                                    </textarea>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                                            </label>
                                        </div>
                                    </div>
                                </div> -->
                                <!-- <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-danger">Submit</button>
                                    </div>
                                </div> -->
                            </form>
                        </div><!-- /.tab-pane -->

                        <div class="tab-pane" id="activity">
                            <!-- Post -->
                            <div class="post">
                            <div class="user-block">
                                <img class="img-circle img-bordered-sm" src="{{ asset('/img/user3-128x128.jpg') }}" alt="user image">
                                <span class="username">
                                    <a href="#">Jonathan Burke Jr.</a>
                                    <a href="#" class="pull-right btn-box-tool"><i class="fa fa-times"></i></a>
                                </span>
                                <span class="description">Shared publicly - 7:30 PM today</span>
                            </div>
                            <!-- /.user-block -->
                            <p>
                                Lorem ipsum represents a long-held tradition for designers,
                                typographers and the like. Some people hate it and argue for
                                its demise, but others ignore the hate as they create awesome
                                tools to help create filler text for everyone from bacon lovers
                                to Charlie Sheen fans.
                            </p>
                            <ul class="list-inline">
                                <li>
                                    <a href="#" class="link-black text-sm">
                                        <i class="fa fa-share margin-r-5"></i> Share
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="link-black text-sm">
                                        <i class="fa fa-thumbs-o-up margin-r-5"></i> Like
                                    </a>
                                </li>
                                <li class="pull-right">
                                    <a href="#" class="link-black text-sm">
                                        <i class="fa fa-comments-o margin-r-5"></i> Comments
                                        (5)
                                    </a>
                                </li>
                            </ul>

                            <input class="form-control input-sm" type="text" placeholder="Type a comment">
                            </div>
                            <!-- /.post -->

                            <!-- Post -->
                            <div class="post clearfix">
                            <div class="user-block">
                                <img class="img-circle img-bordered-sm" src="{{ asset('/img/user3-128x128.jpg') }}" alt="User Image">
                                    <span class="username">
                                        <a href="#">Sarah Ross</a>
                                        <a href="#" class="pull-right btn-box-tool"><i class="fa fa-times"></i></a>
                                    </span>
                                <span class="description">Sent you a message - 3 days ago</span>
                            </div><!-- /.user-block -->
                            <p>
                                Lorem ipsum represents a long-held tradition for designers,
                                typographers and the like. Some people hate it and argue for
                                its demise, but others ignore the hate as they create awesome
                                tools to help create filler text for everyone from bacon lovers
                                to Charlie Sheen fans.
                            </p>

                            <form class="form-horizontal">
                                <div class="form-group margin-bottom-none">
                                    <div class="col-sm-9">
                                        <input class="form-control input-sm" placeholder="Response">
                                    </div>
                                    <div class="col-sm-3">
                                        <button type="submit" class="btn btn-danger pull-right btn-block btn-sm">Send</button>
                                    </div>
                                </div>
                            </form>
                            </div>
                            <!-- /.post -->

                            <!-- Post -->
                            <div class="post">
                            <div class="user-block">
                                <img class="img-circle img-bordered-sm" src="{{ asset('/img/user3-128x128.jpg') }}" alt="User Image">
                                <span class="username">
                                    <a href="#">Adam Jones</a>
                                    <a href="#" class="pull-right btn-box-tool"><i class="fa fa-times"></i></a>
                                </span>
                                <span class="description">Posted 5 photos - 5 days ago</span>
                            </div><!-- /.user-block -->
                            <div class="row margin-bottom">
                                <div class="col-sm-6">
                                    <img class="img-responsive" src="{{ asset('/img/photo1.png') }}" alt="Photo">
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <img class="img-responsive" src="{{ asset('/img/photo2.png') }}" alt="Photo">
                                            <br>
                                            <img class="img-responsive" src="{{ asset('/img/photo3.jpg') }}" alt="Photo">
                                        </div><!-- /.col -->
                                        <div class="col-sm-6">
                                            <img class="img-responsive" src="{{ asset('/img/photo4.jpg') }}" alt="Photo">
                                            <br>
                                            <img class="img-responsive" src="{{ asset('/img/photo1.png') }}" alt="Photo">
                                        </div><!-- /.col -->
                                    </div><!-- /.row -->
                                </div><!-- /.col -->
                            </div><!-- /.row -->

                            <ul class="list-inline">
                                <li>
                                    <a href="#" class="link-black text-sm">
                                        <i class="fa fa-share margin-r-5"></i> Share
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="link-black text-sm">
                                        <i class="fa fa-thumbs-o-up margin-r-5"></i> Like
                                    </a>
                                </li>
                                <li class="pull-right">
                                    <a href="#" class="link-black text-sm">
                                        <i class="fa fa-comments-o margin-r-5"></i> Comments
                                        (5)
                                    </a>
                                </li>
                            </ul>

                            <input class="form-control input-sm" type="text" placeholder="Type a comment">
                            </div><!-- /.post -->
                        </div><!-- /.tab-pane -->

                        <div class="tab-pane" id="timeline">
                            <!-- The timeline -->
                            <ul class="timeline timeline-inverse">
                                <!-- timeline time label -->
                                <li class="time-label">
                                        <span class="bg-red">
                                        10 Feb. 2014
                                        </span>
                                </li>
                                <!-- /.timeline-label -->
                                <!-- timeline item -->
                                <li>
                                    <i class="fa fa-envelope bg-blue"></i>

                                    <div class="timeline-item">
                                        <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>

                                        <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>

                                        <div class="timeline-body">
                                            Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                                            weebly ning heekya handango imeem plugg dopplr jibjab, movity
                                            jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                                            quora plaxo ideeli hulu weebly balihoo...
                                        </div>
                                        <div class="timeline-footer">
                                            <a class="btn btn-primary btn-xs">Read more</a>
                                            <a class="btn btn-danger btn-xs">Delete</a>
                                        </div>
                                    </div>
                                </li>
                                <!-- END timeline item -->
                                <!-- timeline item -->
                                <li>
                                    <i class="fa fa-user bg-aqua"></i>

                                    <div class="timeline-item">
                                        <span class="time"><i class="fa fa-clock-o"></i> 5 mins ago</span>

                                        <h3 class="timeline-header no-border">
                                            <a href="#">Sarah Young</a> accepted your friend request
                                        </h3>
                                    </div>
                                </li>
                                <!-- END timeline item -->
                                <!-- timeline item -->
                                <li>
                                    <i class="fa fa-comments bg-yellow"></i>

                                    <div class="timeline-item">
                                        <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>

                                        <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>

                                        <div class="timeline-body">
                                            Take me to your leader!
                                            Switzerland is small and neutral!
                                            We are more like Germany, ambitious and misunderstood!
                                        </div>
                                        <div class="timeline-footer">
                                            <a class="btn btn-warning btn-flat btn-xs">View comment</a>
                                        </div>
                                    </div>
                                </li>
                                <!-- END timeline item -->
                                <!-- timeline time label -->
                                <li class="time-label">
                                    <span class="bg-green">
                                        3 Jan. 2014
                                    </span>
                                </li>
                                <!-- /.timeline-label -->
                                <!-- timeline item -->
                                <li>
                                    <i class="fa fa-camera bg-purple"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fa fa-clock-o"></i> 2 days ago</span>

                                        <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>

                                        <div class="timeline-body">
                                            <img src="http://placehold.it/150x100" alt="..." class="margin">
                                            <img src="http://placehold.it/150x100" alt="..." class="margin">
                                            <img src="http://placehold.it/150x100" alt="..." class="margin">
                                            <img src="http://placehold.it/150x100" alt="..." class="margin">
                                        </div>
                                    </div>
                                </li><!-- END timeline item -->
                                <li>
                                    <i class="fa fa-clock-o bg-gray"></i>
                                </li>
                            </ul>
                        </div><!-- /.tab-pane -->
                    </div><!-- /.tab-content -->
                </div><!-- /.nav-tabs-custom -->
            </div><!-- /.col -->

        </div><!-- /.row -->
    </section>

<script>
    $(function () {
        
    });
</script>

@endsection