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

    @foreach ($data as $site => $content)
        <div class="row">
            <div class="col-sm">
                <div class="row">
                    <div class="col-lg">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-bar-chart-o fa-fw"></i>{{ ucfirst($site) }}
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th>Today</th>
                                            <th>Month</th>
                                            <th>All</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>{{ round($content['general']['today'], 2) }}</td>
                                            <td>{{ round($content['general']['month'], 2) }}</td>
                                            <td>{{ round($content['general']['total'], 2) }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Today</th>
                                            <th>Week</th>
                                            <th>Month</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($content['user'] as $value)
                                            <tr>
                                                <td>{{$value['username']}}</td>
                                                <td>{{ isset($value['day'])? round($value['day'], 2) : '__' }}</td>
                                                <td>{{ isset($value['week'])? round($value['week'], 2) : '__' }}</td>
                                                <td>{{ isset($value['month'])? round($value['month'], 2) : '__' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.panel-body -->

                        </div>
                    </div>
                </div>

            </div>

        </div>

    @endforeach

    <!-- End row -->
@endsection

@section('scripts')
    <script src="/vendor/ubold/assets/plugins/switchery/js/switchery.min.js"></script>

    <!--C3 Chart-->
    <script type="text/javascript" src="/vendor/ubold/assets/plugins/d3/d3.min.js"></script>
    <script type="text/javascript" src="/vendor/ubold/assets/plugins/c3/c3.min.js"></script>
    {{--<script src="/vendor/ubold/assets/pages/jquery.c3-chart.init.js"></script>--}}

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

@endsection
