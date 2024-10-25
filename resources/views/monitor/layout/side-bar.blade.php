<nav id="sidebar">
    <div id="sidebar-background"></div>


    <div class="sidebar-content" data-simplebar>
        <ul class="sidebar-nav">
            <button id="close-sidebar-btn" type="button" class="btn d-lg-none" onclick="toggleMobileNavigation()">
                <i class="fa-solid fa-circle-xmark fs-3"></i>
            </button>

            <li class="text-muted sidebar-header my-3">
                Attendance
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('dashboard.monitor.index') }}">
                    <span class="pc-micon">
                        <i class="fa-solid fa-user-check"></i>
                    </span>
                    Manage Attendance

                </a>
            </li>

            <hr class="m-0">

            <li class="text-muted sidebar-header my-3">
                Setting
            </li>

            <li class="sidebar-item  ">
                <a class="sidebar-link" href="">
                    <span class="pc-micon" style="margin-top: -2px !important;margin-left: -4px !important;">
                        <i class="fa-solid fa-gear"></i>
                    </span>
                    Setting

                </a>
            </li>

        </ul>
    </div>
</nav>
