<div class="navbar-custom">
    <?php
        $currentUser = auth('backend')->user();
    ?>
    <div class="container">
        <div id="navigation">
            <!-- Navigation Menu-->
            <ul class="navigation-menu">
                <li class="has-submenu">
                    <a href="/admin"><i class="md md-dashboard"></i>Trang chủ</a>
                </li>


                <li class="has-submenu">
                    <a href="/admin/offers"><i class="md md-dashboard"></i>Offers</a>
                </li>


            @if ($currentUser->isAdmin())

                <li class="has-submenu">
                    <a href="#"><i class="md md-class"></i>Hệ thống</a>
                    <ul class="submenu">
                        <li><a href="{{ url('/admin/users')}}">User</a></li>
                        <li><a href="{{ url('/admin/networks')}}">Network</a></li>
                        <li><a href="{{ url('/admin/groups')}}">Group</a></li>
                    </ul>
                </li>

                    <li class="has-submenu">
                        <a href="#"><i class="md md-class"></i>Tool</a>
                        <ul class="submenu">

                            <li><a href="{{ url('/admin/network_clicks')}}">Thống kê</a></li>

                           {{-- <li><a href="{{ url('/admin/clearOldOffer')}}">Clear Old Offer Not have Lead</a></li>--}}
                            <li><a href="{{ url('/admin/correctLead')}}">Correct Leads</a></li>
                        </ul>
                    </li>

                @endif
            </ul>
            <!-- End navigation menu        -->
        </div>
    </div> <!-- end container -->
</div> <!-- end navbar-custom -->
