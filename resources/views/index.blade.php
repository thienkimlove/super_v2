@extends('layouts.app')

@section('styles')
    <link href="/vendor/ubold/assets/plugins/c3/c3.min.css" rel="stylesheet" type="text/css"  />
    <link href="/vendor/ubold/assets/plugins/switchery/css/switchery.min.css" rel="stylesheet" />

@endsection

@section('content')
    @php
        $currentUser = auth('backend')->user();
    @endphp
    <div class="row">
        <div class="col-sm-12">
            {{--<h4 class="page-title">HỆ THỐNG CUSTOMER SERVICES</h4>--}}
            <p class="text-muted page-title-alt">Welcome {{ $currentUser->username }}</p>
        </div>

    </div>

    <div class="row">

        <div class="col-lg-3 col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-comments fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{$content['today']}}</div>
                            <div>Money Today</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-tasks fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{$content['month']}}</div>
                            <div>Money This Month</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4">
            <div class="panel panel-yellow">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-shopping-cart fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{$content['total']}}</div>
                            <div>Total</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-6">
            <!-- /.panel -->
            <div class="panel panel-default">

                <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i> Statistic By User Today
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>Money</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($userTotals as $userTotal)
                                <tr>
                                    <td>{{$userTotal['username']}}</td>
                                    <td>{{$userTotal['total']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>

        <div class="col-lg-6">
            <div class="panel panel-default">

                <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i> Statistic By Network Today
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                            <tr>
                                <th>Network</th>
                                <th>Money</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($networkTotals as $networkTotal)
                                <tr>
                                    <td>{{$networkTotal['name']}}</td>
                                    <td>{{$networkTotal['total']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.panel-body -->
            </div>    <!-- /.panel -->
        </div>

    </div>

    @if ($todayOffers)
        <div class="row">
            <div class="col-lg-12">
                <!-- /.panel -->
                <div class="panel panel-default">

                    <div class="panel-heading">
                        <i class="fa fa-bar-chart-o fa-fw"></i> Danh sách offer chạy ngày hôm nay
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>ID</th>
                                            <th>Clicks</th>
                                            <th>Lead</th>
                                            <th>CR</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($todayOffers as $offer)
                                            <tr>
                                                <td>{{$offer['offer_name']}}</td>
                                                <td>{{$offer['offer_id']}}</td>
                                                <td>{{$offer['site_click'] }}</td>
                                                <td>{{$offer['net_lead']}}</td>
                                                <td>{{ $offer['site_cr'] }}</td>
                                                <td>{{ $offer['offer_price'] }}</td>
                                                <td>{{ $offer['offer_total'] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.col-lg-4 (nested) -->

                            <!-- /.col-lg-8 (nested) -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.panel-body -->
                </div>            <!-- /.panel -->

                <!-- /.panel -->
            </div>
        </div>
    @endif

    @if ($yesterdayOffers)
        <div class="row">
            <div class="col-lg-12">
                <!-- /.panel -->
                <div class="panel panel-default">

                    <div class="panel-heading">
                        <i class="fa fa-bar-chart-o fa-fw"></i> Danh sách offer chạy ngày hôm qua
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>ID</th>
                                            <th>Clicks</th>
                                            <th>Lead</th>
                                            <th>CR</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($yesterdayOffers as $offer)
                                            <tr>
                                                <td>{{$offer['offer_name']}}</td>
                                                <td>{{$offer['offer_id']}}</td>
                                                <td>{{$offer['site_click'] }}</td>
                                                <td>{{$offer['net_lead']}}</td>
                                                <td>{{ $offer['site_cr'] }}</td>
                                                <td>{{ $offer['offer_price'] }}</td>
                                                <td>{{ $offer['offer_total'] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.col-lg-4 (nested) -->

                            <!-- /.col-lg-8 (nested) -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.panel-body -->
                </div>            <!-- /.panel -->

                <!-- /.panel -->
            </div>
        </div>
    @endif

    @if ($weekOffers)
        <div class="row">
            <div class="col-lg-12">
                <!-- /.panel -->
                <div class="panel panel-default">

                    <div class="panel-heading">
                        <i class="fa fa-bar-chart-o fa-fw"></i> Danh sách offer chạy tuần này
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>ID</th>
                                            <th>Clicks</th>
                                            <th>Lead</th>
                                            <th>CR</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($weekOffers as $offer)
                                            <tr>
                                                <td>{{$offer['offer_name']}}</td>
                                                <td>{{$offer['offer_id']}}</td>
                                                <td>{{$offer['site_click'] }}</td>
                                                <td>{{$offer['net_lead']}}</td>
                                                <td>{{ $offer['site_cr'] }}</td>
                                                <td>{{ $offer['offer_price'] }}</td>
                                                <td>{{ $offer['offer_total'] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.col-lg-4 (nested) -->

                            <!-- /.col-lg-8 (nested) -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.panel-body -->
                </div>            <!-- /.panel -->

                <!-- /.panel -->
            </div>
        </div>
    @endif

    @if (!$currentUser->isAdmin())
        <div class="row">
            <!-- /.col-lg-8 -->
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-bell fa-fw"></i> Your Recent Lead
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="list-group">
                            @foreach ($userRecent as $recent)
                                <a class="list-group-item" href="#">
                                    <b>You</b> lead offer <b>{{$recent->name}}</b> with IP <b>{{$recent->ip}}</b>
                                    <span class="pull-right text-muted small">
                                    <em>{{$recent->created_at}}</em>
                                </span>
                                </a>
                            @endforeach
                        </div>
                        <!-- /.list-group -->
                        <a class="btn btn-default btn-block" href="{{url('admin/offers')}}">View All Offers</a>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-4 -->
        </div>
    @endif

    <div class="row" id="site-recent-lead">

    </div>

    <!-- End row -->
@endsection

@section('scripts')
    <script src="/vendor/ubold/assets/plugins/switchery/js/switchery.min.js"></script>

    <!--C3 Chart-->
    <script type="text/javascript" src="/vendor/ubold/assets/plugins/d3/d3.min.js"></script>
    <script type="text/javascript" src="/vendor/ubold/assets/plugins/c3/c3.min.js"></script>
    {{--<script src="/vendor/ubold/assets/pages/jquery.c3-chart.init.js"></script>--}}

    <script>
        $(document).ready(function(){
            setInterval(function(){
              //  $('#site-recent-lead').html('<img width="200" align="center" height="auto" src="/image/loading.gif" />');
                $.getJSON('/admin/recentLead', function(response){
                    $('#site-recent-lead').html(response.html);
                });
            }, 3000);
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

@endsection
