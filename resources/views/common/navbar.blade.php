<div class="navbar-custom">
    <?php
        $currentUser = Sentinel::getUser();
    ?>
    <div class="container">
        <div id="navigation">
            <!-- Navigation Menu-->
            <ul class="navigation-menu">
                <li class="has-submenu">
                    <a href="/"><i class="md md-dashboard"></i>Trang chủ</a>
                </li>

                <li class="has-submenu">
                    <a href="{{ route('reports.index')}}"><i class="md md-view-list"></i>Thống kê</a>
                </li>


            @if ($currentUser->isAdmin() || $currentUser->isManager())

                <li class="has-submenu">
                    <a href="#"><i class="md md-class"></i>Hệ thống</a>
                    <ul class="submenu">
                        <li><a href="{{ url('/users')}}">User</a></li>

                        @if ($currentUser->isAdmin())
                            <li><a href="{{ url('/roles') }}">Role</a></li>
                            <li><a href="{{ url('/permissions') }}">Permission</a></li>
                            <li><a href="{{ route('departments.index') }}">Phòng ban</a></li>

                            <li><a href="{{ route('accounts.index') }}">Social Accounts</a></li>
                            <li><a href="{{ route('contents.index') }}">Ad Accounts</a></li>
                        @endif
                    </ul>
                </li>
                @endif
            </ul>
            <!-- End navigation menu        -->
        </div>
    </div> <!-- end container -->
</div> <!-- end navbar-custom -->
