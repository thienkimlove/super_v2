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
                <a href="{{ route('offers.index') }}" class="btn btn-primary waves-effect waves-light"><span class="m-r-5"><i class="fa fa-list"></i></span> List</a>
            </div>
            <h4 class="page-title">Chi tiết offer</h4>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-sm-12">
                        {!! Form::open(['route' => ['offers.update', $offer->id], 'method' => 'put', 'role' => 'form', 'class' => 'form-horizontal']) !!}
                        @include('layouts.partials.errors')

                        <div class="form-group">
                            <label class="col-md-3 control-label">Name</label>
                            <div class="col-md-9">
                                {!! Form::text('name', $offer->name, ['id' => 'name', 'class' => 'form-control', 'placeholder' => 'Tên Offer']) !!}
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-3 control-label">Network</label>
                            <div class="col-md-9">
                                {!! Form::select('network_id', ['' => '----- Chọn Network -----'] + \App\Site::networkList(), $offer->network_id, ['id' => 'network_id', 'class' => 'select2', 'data-placeholder' => 'Chọn Network...']) !!}
                            </div>

                        </div>


                        <div class="form-group">
                            <label class="col-md-3 control-label">Redirect Link with "#subId" at end of Link</label>
                            <div class="col-md-9">
                                {!! Form::text('redirect_link', $offer->redirect_link, ['id' => 'redirect_link', 'class' => 'form-control', 'placeholder' => 'Redirect link']) !!}
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-3 control-label">Click Rate</label>
                            <div class="col-md-9">
                                {!! Form::text('click_rate', $offer->click_rate, ['id' => 'click_rate', 'class' => 'form-control', 'placeholder' => 'Click Rate']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Allow Geo Locations</label>
                            <div class="col-md-9">
                                {!! Form::text('geo_locations', $offer->geo_locations, ['id' => 'geo_locations', 'class' => 'form-control', 'placeholder' => 'Geo Locations']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Allow Devices</label>
                            <div class="col-md-9">
                                {!! Form::select('allow_devices', ['' => '----- Chọn Devices -----'] + config('devices'), $offer->allow_devices, ['id' => 'allow_devices', 'class' => 'select2', 'data-placeholder' => 'Chọn Devices...']) !!}
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">NetOfferId</label>
                            <div class="col-md-9">
                                {!! Form::text('net_offer_id', $offer->net_offer_id, ['id' => 'net_offer_id', 'class' => 'form-control', 'placeholder' => 'Net Offer Id']) !!}
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-3 control-label">Auto</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{ $offer->auto ? 'Yes' : 'No' }}</p>
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="col-md-3 control-label">Allow MultiLead</label>
                            <div class="col-md-9">
                                {!! Form::checkbox('allow_multi_lead', '1', $offer->allow_multi_lead, ['data-plugin' => 'switchery', 'data-color' => '#81c868']) !!}
                                <span class="lbl"></span>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-3 control-label">Check Click in Network</label>
                            <div class="col-md-9">
                                {!! Form::checkbox('check_click_in_network', '1', $offer->check_click_in_network, ['data-plugin' => 'switchery', 'data-color' => '#81c868']) !!}
                                <span class="lbl"></span>
                            </div>
                        </div>


                        <h4>Auto Clicks</h4>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Number of Virtual Clicks When Have Click</label>
                            <div class="col-md-9">
                                {!! Form::number('number_when_click', $offer->number_when_click, ['id' => 'number_when_click', 'class' => 'form-control', 'placeholder' => 'number of virtual click when have click']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Number of Virtual Clicks When Have Lead</label>
                            <div class="col-md-9">
                                {!! Form::number('number_when_lead', $offer->number_when_lead, ['id' => 'number_when_lead', 'class' => 'form-control', 'placeholder' => 'number of virtual click when have lead']) !!}
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="col-md-3 control-label">Trạng thái</label>
                            <div class="col-md-9">
                                {!! Form::checkbox('status', '1', $offer->status, ['data-plugin' => 'switchery', 'data-color' => '#81c868']) !!}
                                <span class="lbl"></span>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-3 control-label">Ngày tạo</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{ $offer->created_at }}</p>
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