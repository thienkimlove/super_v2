<div class="navbar-custom">
    <?php
        $currentUser = auth('backend')->user();
    ?>
    <div class="container">
        <div id="navigation">
            <!-- Navigation Menu-->
            <ul class="navigation-menu">
                <li class="has-submenu">
                    <a href="/"><i class="md md-dashboard"></i>Trang chủ</a>
                </li>


            @if ($currentUser->isAdmin())

                <li class="has-submenu">
                    <a href="#"><i class="md md-class"></i>Hệ thống</a>
                    <ul class="submenu">
                        <li><a href="{{ url('/users')}}">User</a></li>
                    </ul>
                </li>
                @endif
            </ul>
            <!-- End navigation menu        -->
        </div>
    </div> <!-- end container -->
</div> <!-- end navbar-custom -->
