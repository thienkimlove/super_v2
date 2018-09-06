@extends('layouts.app')

@section('styles')
    <!-- Plugins css-->
    <link href="/vendor/ubold/assets/plugins/bootstrap-tagsinput/css/bootstrap-tagsinput.css" rel="stylesheet" />
    <link href="/vendor/ubold/assets/plugins/switchery/css/switchery.min.css" rel="stylesheet" />
    <link href="/vendor/ubold/assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
    <link href="/vendor/ubold/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="/vendor/ubold/assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="/vendor/ubold/assets/plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-t-15">
                <a href="{{ route('users.index') }}" class="btn btn-primary waves-effect waves-light"><span class="m-r-5"><i class="fa fa-list"></i></span> List</a>
            </div>
            <h4 class="page-title">Chi tiết Network</h4>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-sm-12">
                        {!! Form::open(['route' => ['networks.update', $network->id], 'method' => 'put', 'role' => 'form', 'class' => 'form-horizontal']) !!}
                        @include('layouts.partials.errors')

                        <div class="form-group">
                            <label class="col-md-3 control-label">Name</label>
                            <div class="col-md-9">
                                {!! Form::text('name', $network->name, ['id' => 'name', 'class' => 'form-control', 'placeholder' => 'Tên Network', 'required' => 'required']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">CronURL</label>
                            <div class="col-md-9">
                                {!! Form::text('cron', $network->cron, ['id' => 'cron', 'class' => 'form-control', 'placeholder' => 'cron url']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Postback Prefix</label>
                            <div class="col-md-9">
                                {!! Form::text('prefix', $network->prefix, ['id' => 'prefix', 'class' => 'form-control', 'placeholder' => 'Postback Prefix']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">IDFA Name</label>
                            <div class="col-md-9">
                                {!! Form::text('idfa_name', $network->idfa_name, ['id' => 'idfa_name', 'class' => 'form-control', 'placeholder' => 'IDFA Name']) !!}
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-3 control-label">Chia Giá Offer Cho</label>
                            <div class="col-md-9">
                                {!! Form::number('rate_offer', $network->rate_offer, ['id' => 'rate_offer', 'class' => 'form-control', 'placeholder' => 'Chia Giá Offer']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Number Virtual When have Click</label>
                            <div class="col-md-9">
                                {!! Form::number('virtual_click', $network->virtual_click, ['id' => 'virtual_click', 'class' => 'form-control', 'placeholder' => 'Number Virtual When have Click']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Number Virtual When have Lead</label>
                            <div class="col-md-9">
                                {!! Form::number('virtual_lead', $network->virtual_lead, ['id' => 'virtual_lead', 'class' => 'form-control', 'placeholder' => 'Number Virtual When have Lead']) !!}
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
@endsection

@section('scripts')
    <script src="/vendor/ubold/assets/plugins/bootstrap-tagsinput/js/bootstrap-tagsinput.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/switchery/js/switchery.min.js"></script>
    <script type="text/javascript" src="/vendor/ubold/assets/plugins/multiselect/js/jquery.multi-select.js"></script>
    <script type="text/javascript" src="/vendor/ubold/assets/plugins/jquery-quicksearch/jquery.quicksearch.js"></script>
    <script src="/vendor/ubold/assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script src="/vendor/ubold/assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/vendor/ubold/assets/plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js" type="text/javascript"></script>
    <script src="/vendor/ubold/assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>

    <script type="text/javascript" src="/vendor/ubold/assets/plugins/autocomplete/jquery.mockjax.js"></script>
    <script type="text/javascript" src="/vendor/ubold/assets/plugins/autocomplete/jquery.autocomplete.min.js"></script>
    <script type="text/javascript" src="/vendor/ubold/assets/plugins/autocomplete/countries.js"></script>
    <script type="text/javascript" src="/vendor/ubold/assets/pages/autocomplete.js"></script>

    {{--<script type="text/javascript" src="/vendor/ubold/assets/pages/jquery.form-advanced.init.js"></script>--}}
@endsection

@section('inline_scripts')
    <script>
        (function($){
            $('.select2').select2();

        })(jQuery);
    </script>

@endsection