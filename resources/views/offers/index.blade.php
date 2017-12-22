@extends('layouts.app')

@section('inline_styles')
    <style>
        .select2-container--default {
            width: 250px !important;
        }
        .select2-container--default .select2-results > .select2-results__options {
            max-height: 500px;
            min-height: 400px;
            overflow-y: auto;
        }
    </style>
@endsection

@section('styles')
    <!-- DataTables -->
    <link href="/vendor/ubold/assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/dataTables.colVis.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            @if (auth('backend')->user()->isAdmin())
            <div class="btn-group pull-right m-t-15">
                <a href="/admin/offers/create"><button type="button" class="btn btn-default dropdown-toggle waves-effect" >Tạo mới <span class="m-l-5"><i class="fa fa-plus"></i></span></button></a>
            </div>
            @endif

            <h4 class="page-title">Danh sách Offer</h4>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-sm-12">
                        <form class="form-inline" role="form" id="search-form">


                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">Offer Name</label>
                                <input type="text" class="form-control" placeholder="Tên Offer" name="name"/>
                            </div>

                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">Country</label>
                                <input type="text" class="form-control" placeholder="Country" name="country"/>
                            </div>


                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">Offer Id or Net Offer Id</label>
                                <input type="text" class="form-control" placeholder="Uid" name="uid"/>
                            </div>


                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">Devices</label>
                                {!! Form::select('device', ['' => '--- Chọn Devices ---'] + config('devices'), null, ['class' => 'form-control select2']) !!}
                            </div>


                            @if (auth('backend')->user()->isAdmin())

                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">Network</label>
                                {!! Form::select('network_id', ['' => '--- Chọn Network ---'] + \App\Site::networkList(), null, ['class' => 'form-control select2']) !!}
                            </div>


                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">Auto Or Not</label>
                                {!! Form::select('auto', [1 => 'Auto', 0 => 'Manual'], null, ['class' => 'form-control']) !!}
                            </div>


                                <div class="form-group m-l-10">
                                    <label class="sr-only" for="">Rejected</label>
                                    {!! Form::select('reject', [0 => 'Not Rejected', 1 => 'Rejected'], null, ['class' => 'form-control']) !!}
                                </div>



                                <div class="form-group m-l-10">
                                    <label class="sr-only" for="">Status</label>
                                    {!! Form::select('status', [1 => 'Active', 0 => 'Inactive'], null, ['class' => 'form-control']) !!}
                                </div>

                            @endif



                            <button type="submit" class="btn btn-success waves-effect waves-light m-l-15">Tìm kiếm</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <p class="text-muted font-13 m-b-30"></p>

                    <table id="dataTables-offers" class="table table-striped table-bordered table-actions-bar">
                    <thead>
                    <tr>
                        <th width="20%">Name</th>
                        <th width="5%">Price Per Click</th>
                        <th width="5%">Geo Locations</th>
                        <th width="10%">Allow Devices</th>
                        <th width="10%">Link To Lead</th>
                        <th width="5%">Status</th>
                        <th width="5%">Created Date</th>

                        @if (auth('backend')->user()->isAdmin())

                            <th width="10%">Network OfferID</th>
                            <th width="10%">Network</th>
                            <th width="10%">Action</th>
                            <th width="20%">Test Msg</th>
                        @endif
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>



@endsection

@section('scripts')
    <script src="/vendor/ubold/assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.bootstrap.js"></script>

    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/buttons.bootstrap.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/jszip.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/pdfmake.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/vfs_fonts.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/buttons.html5.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/buttons.print.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.fixedHeader.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.keyTable.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/responsive.bootstrap.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.scroller.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.colVis.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.fixedColumns.min.js"></script>

    <script src="/vendor/ubold/assets/pages/datatables.init.js"></script>
    <script src="/vendor/ubold/assets/plugins/select2/js/select2.full.min.js"></script>
    <script src="/js/handlebars.js"></script>

    <script src="/vendor/ubold/assets/plugins/moment/moment.js"></script>
    <script src="/vendor/ubold/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
@endsection

@section('inline_scripts')
    <script type="text/javascript">
        $('.select2').select2();

        $(function () {
            var datatable = $("#dataTables-offers").DataTable({
                searching: false,
                serverSide: true,
                processing: true,
                ajax: {
                    url: '{!! route('offers.dataTables') !!}',
                    data: function (d) {
                        d.name = $('input[name=name]').val();
                        d.country = $('input[name=country]').val();
                        d.uid = $('input[name=uid]').val();
                        d.device = $('select[name=device]').val();
                        d.network_id = $('select[name=network_id]').val();
                        d.auto = $('select[name=auto]').val();
                        d.status = $('select[name=status]').val();
                        d.reject = $('select[name=reject]').val();
                    }
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'click_rate', name: 'click_rate'},
                    {data: 'geo_locations', name: 'geo_locations'},
                    {data: 'allow_devices', name: 'allow_devices'},
                    {data: 'redirect_link_for_user', name: 'redirect_link_for_user'},
                    {data: 'status', name: 'status'},
                    {data: 'created_at', name: 'created_at'},

                    @if (auth('backend')->user()->isAdmin())

                    {data: 'net_offer_id', name: 'net_offer_id'},
                  /*  {data: 'redirect_link', name: 'redirect_link'},
                    {data: 'allow_multi_lead', name: 'allow_multi_lead'},
                    {data: 'check_click_in_network', name: 'check_click_in_network'},
                    {data: 'virtual_click', name: 'virtual_click'},*/
                    {data: 'network_name', name: 'network_name'},

                    {data: 'action', name: 'action', orderable: false, searchable: false},
                    {data: 'process', name: 'process'}
                    @endif
                ],
                order: [[5, 'desc']]
            });

            $('#search-form').on('submit', function(e) {
                datatable.draw();
                e.preventDefault();
            });


            datatable.on('click', '[id^="btn-reject-"]', function (e) {
                e.preventDefault();

                var url = $(this).data('url');

                swal({
                    title: "Bạn có muốn reject offer nay?",
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Reject!"
                }).then(function () {
                    $.ajax({
                        url : url,
                        type : 'GET',
                        beforeSend: function (xhr) {
                            var token = $('meta[name="csrf_token"]').attr('content');
                            if (token) {
                                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                            }
                        }
                    }).always(function (data) {
                        window.location.reload();
                    });
                });
            });

            datatable.on('click', '[id^="btn-accept-"]', function (e) {
                e.preventDefault();

                var url = $(this).data('url');

                swal({
                    title: "Bạn có muốn accept offer nay?",
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Accept!"
                }).then(function () {
                    $.ajax({
                        url : url,
                        type : 'GET',
                        beforeSend: function (xhr) {
                            var token = $('meta[name="csrf_token"]').attr('content');
                            if (token) {
                                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                            }
                        }
                    }).always(function (data) {
                        window.location.reload();
                    });
                });
            });

            datatable.on('click', '[id^="btn-test-"]', function (e) {
                e.preventDefault();

                var url = $(this).data('url');

                var offer_id = $(this).data('offer');

                $('div#test_status_' + offer_id).html('<img width="50" align="center" height="auto" src="/image/loading.gif" />');

                $.ajax({
                    url : url,
                    type : 'GET',
                    beforeSend: function (xhr) {
                        var token = $('meta[name="csrf_token"]').attr('content');
                        if (token) {
                            return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        }
                    }
                }).always(function (res) {
                    if (res.status === true) {
                        $('div#test_status_' + offer_id).html('<span>' + res.msg + '</span><br/><span><img src="/test/' + offer_id + '_last.png" width="auto" height="100" /></span><br/><span><a target="_blank" href="/test/' + offer_id + '_last.html">Debug Html</a></span>');
                    } else {
                        $('div#test_status_' + offer_id).html(res.msg);
                    }
                });
            });
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    </script>


@endsection