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

        .hide {
            display: none !important;
        }

        #footer {
            position:fixed;
            left:0px;
            bottom:0px;
            height:30px;
            width:100%;
            background:#999;
        }
    </style>
@endsection

@section('styles')
    <!-- DataTables -->
    <link href="/vendor/ubold/assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    {{--<link href="/vendor/ubold/assets/plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css"/>--}}
    <link href="/vendor/ubold/assets/plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/dataTables.colVis.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="/vendor/ubold/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">

@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">

            <h4 class="page-title">Danh sách Leads</h4>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-sm-12">
                        <form class="form-inline" role="form" id="search-form">

                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">Network</label>
                                {!! Form::select('network_id', ['' => '--- Chọn Network ---'] + \App\Site::networkList(), null, ['class' => 'form-control select2']) !!}
                            </div>


                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">Group</label>
                                {!! Form::select('group_id', ['' => '--- Chọn Group ---'] + \App\Site::groupList(), null, ['class' => 'form-control select2']) !!}
                            </div>


                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">User</label>
                                {!! Form::select('user_id', ['' => '--- Chọn User ---'] + \App\Site::userList(), null, ['class' => 'form-control select2']) !!}
                            </div>


                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">Offer</label>
                                {!! Form::select('offer_id', ['' => '--- Chọn Offer ---'] + \App\Site::offerList(), null, ['class' => 'form-control select2']) !!}
                            </div>

                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">Theo ngày</label>
                                <input class="form-control input-daterange-datepicker" type="text" name="date" value="{{ \Carbon\Carbon::today()->format('d/m/Y') }} - {{ \Carbon\Carbon::today()->format('d/m/Y') }}" placeholder="Theo ngày" style="width: 200px;"/>
                            </div>



                            <button type="submit" class="btn btn-success waves-effect waves-light m-l-15">Tìm kiếm</button>
                        </form>

                        <div class="form-group pull-right">
                            {!! Form::open(['route' => 'network_clicks.export', 'method' => 'get', 'role' => 'form', 'class' => 'form-inline', 'id' => 'export-form']) !!}

                            {{Form::hidden('filter_group_id', null)}}
                            {{Form::hidden('filter_user_id', null)}}
                            {{Form::hidden('filter_network_id', null)}}
                            {{Form::hidden('filter_date', null)}}
                            {{Form::hidden('filter_offer_id', null)}}

                            <button class="btn btn-danger waves-effect waves-light m-t-15" value="export" type="submit" name="export">
                                <i class="fa fa-download"></i>&nbsp; Xuất Excel
                            </button>
                            {!! Form::close() !!}

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <p class="text-muted font-13 m-b-30"></p>

                    <table id="dataTables-leads" class="table table-striped table-bordered table-actions-bar">
                    <thead>
                    <tr>
                        <th>OfferID</th>
                        <th>OfferName</th>
                        <th>ClickRate</th>
                        <th>IP</th>
                        <th>User</th>
                        <th>Created Date</th>
                    </tr>
                    </thead>

                        <tfoot align="right" id="">
                        <tr>
                            <th>Total</th>
                            <th></th>
                            <th></th>
                            <th width="10%" id="total_leads"></th>
                            <th width="10%" id="total_money"></th>
                        </tr>
                        </tfoot>

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
    <script type="text/javascript" src="/js/jquery.number.min.js"></script>

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
            var datatable = $("#dataTables-leads").DataTable({
                searching: false,
                serverSide: true,
                processing: true,
                ajax: {
                    url: '{!! route('network_clicks.dataTables') !!}',
                    data: function (d) {
                        d.network_id = $('select[name=network_id]').val();
                        d.user_id = $('select[name=user_id]').val();
                        d.group_id = $('select[name=group_id]').val();
                        d.offer_id = $('select[name=offer_id]').val();
                        d.date = $('input[name=date]').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'offer_name', name: 'offer_name'},
                    {data: 'offer_click_rate', name: 'offer_click_rate'},
                    {data: 'ip', name: 'ip'},
                    {data: 'username', name: 'username'},
                    {data: 'created_at', name: 'created_at'},

                ],
                order: [[1, 'desc']],

                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api();
                    // Update footer by showing the total with the reference of the column index
                    $( api.column( 0 ).footer() ).html('Total');
                    if (data.length > 0) {
                         $( api.column( 3 ).footer() ).html("Leads : " + data[0].total_leads);
                         $( api.column( 4 ).footer() ).html("Money : " + data[0].total_money);
                    }
                }
            });

            $('#search-form').on('submit', function(e) {
                datatable.draw();
                e.preventDefault();
            });

        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.input-daterange-datepicker').daterangepicker({
            autoUpdateInput: false,
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: 'left',
            drops: 'down',
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-default',
            cancelClass: 'btn-white',
            separator: ' to ',
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: 'Submit',
                cancelLabel: 'Clear',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        });

        $('.input-daterange-datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        });

        $('.input-daterange-datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        $('#export-form').on('submit', function (e) {
            $('input[name=filter_group_id]').val($('select[name=group_id]').val());
            $('input[name=filter_user_id]').val($('select[name=user_id]').val());
            $('input[name=filter_network_id]').val($('select[name=network_id]').val());
            $('input[name=filter_offer_id]').val($('select[name=offer_id]').val());

            $('input[name=filter_date]').val($('input[name=date]').val());

            $(this).submit();
            datatable.draw();
            e.preventDefault();
        });

    </script>


@endsection