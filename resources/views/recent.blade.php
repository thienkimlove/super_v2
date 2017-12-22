<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-bell fa-fw"></i>Site Recent Lead
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <div class="list-group">
                @foreach ($siteRecentLead as $recent)
                    <a class="list-group-item" href="#">
                        <b>{{$recent->username}} </b> lead offer <b>{{$recent->name}}</b> with IP <b>{{$recent->network_ip}}</b> - ID=<b>{{$recent->id}} | Time Click: {{$recent->click_at}} || postbackId= {{$recent->postback_id}}</b>
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