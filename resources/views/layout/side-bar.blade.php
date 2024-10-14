<nav id="sidebar">
    <div id="sidebar-background"></div>


    <div class="sidebar-content" data-simplebar>
        <ul class="sidebar-nav">
            <button id="close-sidebar-btn" type="button" class="btn d-lg-none" onclick="toggleMobileNavigation()">
                <i class="fa-solid fa-circle-xmark fs-3"></i>
            </button>

            <li class="sidebar-logo text-center p-2">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('img/logo.png') }}" />
                </a>
            </li>

            <li class="sidebar-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('dashboard') }}">
                    <div>
                        <i class="fa-solid fa-house"></i>
                    </div>

                    Home
                </a>
            </li>

            <li class="sidebar-item  ">
                <a class="sidebar-link" href="">
                    <div>
                        <i class="fa-solid fa-book-bookmark"></i>
                    </div>

                    Course
                </a>
            </li>



        </ul>
    </div>
</nav>
