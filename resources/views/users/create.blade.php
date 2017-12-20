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
            <h4 class="page-title">Tạo mới người dùng</h4>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('users.index') }}">Danh sách người dùng</a>
                </li>
                <li class="active">
                    Tạo mới người dùng
                </li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-sm-12">
                        <h4 class="header-title m-t-0"><b>Tạo mới người dùng</b></h4>
                        {!! Form::open(['route' => ['users.store'], 'method' => 'post', 'role' => 'form', 'class' => 'form-horizontal']) !!}
                        @include('layouts.partials.errors')

                        <div class="form-group">
                            <label class="col-md-3 control-label">Tên người dùng</label>
                            <div class="col-md-9">
                                {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control', 'placeholder' => 'Tên người dùng', 'required' => 'required']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Email</label>
                            <div class="col-md-9">
                                {!! Form::text('email', null, ['id' => 'email', 'class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Roles</label>
                            <div class="col-md-9">
                                {!! Form::select('roles[]', Helpers::roleList(), null, ['id' => 'roles', 'class' => 'form-control', 'data-placeholder' => 'Chọn quyền...']) !!}
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Phòng ban</label>
                            <div class="col-md-9">
                                {!! Form::select('department_id', ['' => '----- Chọn phòng ban -----'] + Helpers::departmentList(), null, ['id' => 'department_id', 'class' => 'select2', 'data-placeholder' => 'Chọn phòng ban...']) !!}
                            </div>

                        </div>



                        <div class="form-group">
                            <label class="col-md-3 control-label">Danh sách Tài Khoản Quảng Cáo</label>
                            <div class="col-md-9">
                                {!! Form::select('contents[]',  ['' => '----- Chọn Tài Khoản -----'] + Helpers::contentListForCreate(), null, ['id' => 'contents', 'multiple' => true, 'class' => 'select2 select2-multiple', 'data-placeholder' => 'Choose Ad Accounts...']) !!}
                            </div>

                        </div>



                        <div class="form-group">
                            <label class="col-md-3 control-label">Trạng thái</label>
                            <div class="col-md-9">
                                {!! Form::checkbox('status', '1', 1, ['data-plugin' => 'switchery', 'data-color' => '#81c868']) !!}
                                <span class="lbl"></span>
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
    <script src="/vendor/ubold/assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
    <script src="/vendor/ubold/assets/plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js" type="text/javascript"></script>
    <script src="/vendor/ubold/assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>

    <script type="text/javascript" src="/vendor/ubold/assets/plugins/autocomplete/jquery.mockjax.js"></script>
    <script type="text/javascript" src="/vendor/ubold/assets/plugins/autocomplete/jquery.autocomplete.min.js"></script>
    <script type="text/javascript" src="/vendor/ubold/assets/plugins/autocomplete/countries.js"></script>
    <script type="text/javascript" src="/vendor/ubold/assets/pages/autocomplete.js"></script>

    <script type="text/javascript" src="/vendor/ubold/assets/pages/jquery.form-advanced.init.js"></script>
@endsection

@section('inline_scripts')
<script>
    (function($){
        $('.select2').select2();

        $('#roles').on('change', function(e) {
            if ('Admin' != $("#roles option:selected").text()) {
                $('#department_id').attr('required', true);
            } else {
                $('#department_id').attr('required', false);

            }
        });

        $('#contents').on('change', function(e) {
            if ('Admin' != $("#contents option:selected").text()) {
                $('#contents').attr('required', true);
            } else {
                $('#contents').attr('required', false);

            }
        });

    })(jQuery);
</script>
@endsection