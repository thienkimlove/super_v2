@extends('layouts.app')

@section('styles')
    <link href="/vendor/ubold/assets/plugins/c3/c3.min.css" rel="stylesheet" type="text/css"  />
    <link href="/vendor/ubold/assets/plugins/switchery/css/switchery.min.css" rel="stylesheet" />

@endsection

@section('content')
    @php
        $currentUser = Sentinel::getUser();
    @endphp
    <div class="row">
        <div class="col-sm-12">
            {{--<h4 class="page-title">HỆ THỐNG CUSTOMER SERVICES</h4>--}}
            <p class="text-muted page-title-alt">Welcome {{ $currentUser->name }}</p>
        </div>

    </div>

    @if ($currentUser->department_id)
        @include('fb_add')
    @endif

    @if ($currentUser->isAdmin())
        @include('dashboard._admin')
    @elseif ($currentUser->isManager())

        @include('dashboard._manager')
    @else
        @include('dashboard._staff')
    @endif


    <div id="list-content-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog" style="width:55%;">
            <div class="modal-content">
                <form class="form-horizontal" action="{{ route('contents.updateMapUser') }}" role="form" method="post">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="custom-width-modalLabel">Cài đặt Ads Accounts</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">

                            {{ csrf_field() }}

                            <div class="table-responsive">
                                <table id="datatable" class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Social ID</th>
                                        <th>Social name</th>
                                        <th>Trạng thái</th>
                                        <th>On / Off</th>
                                    </tr>
                                    </thead>


                                    <tbody>

                                    @foreach ($contents as $content)
                                    <tr>
                                        <td>{{ $content->social_id }}</td>
                                        <td>{{ $content->social_name }}</td>
                                        <td>{!! $content->status ? '<i class="ion ion-checkmark-circled text-success"></i>' : '<i class="ion ion-close-circled text-danger"></i>'  !!} </td>
                                        <td> {!! Form::checkbox('status[]', $content->id, ($content->user_id == $currentUser->id) ? 1 : 0, ['data-plugin' => 'switchery', 'data-color' => '#81c868']) !!}<span class="lbl"></span>
                                            <input type="hidden" name="contents[]" value="{{$content->id}}" />
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Cập nhật</button>
                </div>
                </form>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <!-- End row -->
@endsection

@section('scripts')
    <script src="/vendor/ubold/assets/plugins/switchery/js/switchery.min.js"></script>

    <!--C3 Chart-->
    <script type="text/javascript" src="/vendor/ubold/assets/plugins/d3/d3.min.js"></script>
    <script type="text/javascript" src="/vendor/ubold/assets/plugins/c3/c3.min.js"></script>
    {{--<script src="/vendor/ubold/assets/pages/jquery.c3-chart.init.js"></script>--}}

@endsection

@push('inlinescripts')
    <script>
        @if (isset($contents) && $contents)
        $('#list-content-modal').modal({
            show: 'true'
        });
        @endif
    </script>
@endpush