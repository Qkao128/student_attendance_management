<div id="top-bar">
    <div class="row align-items-center">
        <div class="col ps-3 d-lg-none">
            <div role="button">
                <i class="fa-solid fa-bars fs-3" onclick="toggleMobileNavigation()"></i>
            </div>
        </div>

        <div class="col ps-3 d-lg-block d-none">
            <h3 class="fw-bold mb-0" style="margin-left: -5px;">Student Attendance Management</h2>
        </div>


        <div class="col-auto">
            <div class="circle-img-sm-wrap rounded-circle border" role="button" data-bs-toggle="dropdown">

                <img src="{{ auth()->user()->profile_image ? asset('storage/profile_image/' . auth()->user()->profile_image) : asset('img/default-teacher-avatar.png') }}"
                    onerror="this.onerror=null;this.src='{{ asset('img/default-teacher-avatar.png') }}'">
            </div>

            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-user-tie"></i> Account managment
                        </div>
                    </a>
                </li>


                <li>
                    <a class="dropdown-item" href="">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-gear"></i> Setting
                        </div>
                    </a>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <button type="button" class="dropdown-item" onclick="$('#logout-form').submit();">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </div>
                    </button>


                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                    </form>

                </li>
            </ul>
        </div>
    </div>

</div>
