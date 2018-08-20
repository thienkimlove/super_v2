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
        <div class="col-md-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-sm-12">
                        {!! Form::open(['route' => ['ssh.tool'], 'method' => 'post', 'role' => 'form', 'class' => 'form-horizontal', 'files'=>true]) !!}

                        <div class="form-group">
                            <label class="col-md-3 control-label">Input SSH File</label>
                            <div class="col-md-9">
                                {!! Form::file('name', null, ['id' => 'name', 'class' => 'form-control', 'placeholder' => 'File', 'required' => 'required']) !!}
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right"></label>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-success waves-effect waves-light">Lưu</button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

@endsection
