<nav id="sidebar">
    <div id="sidebar-background"></div>


    <div class="sidebar-content">
        <ul class="sidebar-nav">
            <button id="close-sidebar-btn" type="button" class="btn d-lg-none" onclick="toggleMobileNavigation()">
                <i class="fa-solid fa-circle-xmark fs-3"></i>
            </button>

            <li class="text-muted sidebar-header mb-3">
                Dashboard
            </li>

            <li class="sidebar-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('dashboard') }}">
                    <div><img src="{{ asset('img/dashboard.png') }}" data-original="{{ asset('img/dashboard.png') }}"
                            data-hover="{{ asset('img/dashboard-white.png') }}" id="dashboard-icon"></div>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="m-0">
            @php
                use App\Enums\UserType;
            @endphp

            @hasrole('Monitor')
                <!-- Monitor 角色不顯示任何內容 -->
            @else
                <li
                    class="text-muted sidebar-header  {{ Auth::user()->hasRole(UserType::SuperAdmin()->key) ? 'my-3' : 'my-2' }}">
                    @hasrole('SuperAdmin')
                        Course and
                    @endhasrole
                    Class
                </li>

                @hasrole('SuperAdmin')
                    <li class="sidebar-item {{ Request::routeIs('course.*') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('course.index') }}">
                            <span class="pc-micon">
                                <i class="fa-solid fa-book-bookmark"></i>
                            </span>
                            Manage Course
                        </a>
                    </li>
                @endhasrole

                @hasanyrole('SuperAdmin|Admin')
                    <li
                        class="sidebar-item {{ Request::routeIs('class.*') ? 'active' : '' }} {{ Request::routeIs('student.*') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('class.index') }}">
                            <span class="pc-micon">
                                <i class="fa-solid fa-users-rectangle"></i>
                            </span>
                            Manage Class
                        </a>
                    </li>
                @endhasanyrole

                <hr class="m-0">
            @endhasrole
            <li class="text-muted sidebar-header my-3">
                Attendance
            </li>

            <li class="sidebar-item {{ Request::routeIs('attendance.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('attendance.index') }}">
                    <span class="pc-micon">
                        <i class="fa-solid fa-user-check"></i>
                    </span>
                    Manage Attendance

                </a>
            </li>

            <li class="sidebar-item {{ Request::routeIs('attendance_statistics.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('attendance_statistics.index') }}">
                    <span class="pc-micon" style="margin-left: -2px !important;">
                        <i class="fa-solid fa-chart-column"></i>
                    </span>
                    Attendance Statistics

                </a>
            </li>

            <li class="sidebar-item {{ Request::routeIs('holiday.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('holiday.index') }}">
                    <span class="pc-micon" style="margin-left: -2px !important;">
                        <i class="fa-solid fa-calendar-days"></i>
                    </span>
                    Manage Holidays

                </a>
            </li>

            <hr class="m-0">

            <li class="text-muted sidebar-header my-3">
                Other
            </li>

            <li class="sidebar-item {{ Request::routeIs('user.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('user.index') }}">
                    <span class="pc-micon" style="margin-top: -2px !important;margin-left: -4px !important;">
                        <i class="fa-solid fa-user-tie"></i>
                    </span>
                    Manage Account

                </a>
            </li>


            <li class="sidebar-item">
                <a class="sidebar-link" style="margin-left: 1px" onclick="$('#logout-form').submit();">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </div>
                </a>


                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                </form>

            </li>


        </ul>
    </div>
</nav>
