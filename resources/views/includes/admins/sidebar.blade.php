<div class="mdk-drawer js-mdk-drawer" id="default-drawer">
    <div class="mdk-drawer__content">
        <div class="sidebar sidebar-dark-pickled-bluewood sidebar-left" data-perfect-scrollbar>
            <!-- Sidebar Content -->

            <div class="d-flex align-items-center navbar-height">
                <form action="index.html" class="search-form search-form--black mx-16pt pr-0 pl-16pt">
                    <input type="text" class="form-control pl-0" placeholder="Search">
                    <button class="btn" type="submit"><i class="material-icons">search</i></button>
                </form>
            </div>

            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark sidebar-brand my-3">
{{--                    <span class="logo-sm">--}}
{{--                        LOGO--}}
{{--                        <img src="{{ URL::asset('img/logo-sm.png') }}" alt="" height="30">--}}
{{--                    </span>--}}
                    <span class="logo-lg">
                        LOGO
{{--                        <img src="{{ URL::asset('img/logo-2.png') }}" alt="" height="75">--}}
                    </span>
                </a>
            </div>

            <ul class="sidebar-menu">
                <div class="sidebar-heading">Main</div>
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button"
                           href="{{ route('admin.dashboard') }}">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">dashboard</span>
                            <span class="sidebar-menu-text">Dashboard</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading">Management</div>

                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button"
                           href="{{ route('admin-managements.index') }}">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">person</span>
                            <span class="sidebar-menu-text">Admins Management</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button"
                           href="{{ route('player-managements.index') }}">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">person</span>
                            <span class="sidebar-menu-text">Players Management</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button"
                           href="{{ route('coach-managements.index') }}">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">person</span>
                            <span class="sidebar-menu-text">Coaches Management</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button"
                           href="{{ route('team-managements.index') }}">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">group</span>
                            <span class="sidebar-menu-text">Teams Management</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button"
                           href="{{ route('competition-managements.index') }}">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">emoji_events</span>
                            <span class="sidebar-menu-text">Competitions</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading">Academy</div>
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button" data-toggle="collapse" href="#scheduleMenu" role="button" aria-expanded="false" aria-controls="scheduleMenu">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">event</span>
                            Schedule
                            <span class="ml-auto sidebar-menu-toggle-icon"></span>
                        </a>
                        <ul class="collapse sm-indent" id="scheduleMenu">
                            <li class="sidebar-menu-item">
                                <a class="sidebar-menu-button" href="{{ route('training-schedules.index') }}">
                                    <span class="sidebar-menu-text">Training</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a class="sidebar-menu-button" href="{{ route('match-schedules.index') }}">
                                    <span class="sidebar-menu-text">Match</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button" data-toggle="collapse" href="#reportsMenu">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">description</span>
                            Reports
                            <span class="ml-auto sidebar-menu-toggle-icon"></span>
                        </a>
                        <ul class="collapse sm-indent"
                            id="reportsMenu">
                            <li class="sidebar-menu-item">
                                <a class="sidebar-menu-button" href="{{ route('attendance-report.index') }}">
                                    <span class="sidebar-menu-text">Attendance</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a class="sidebar-menu-button" href="{{ route('performance-report.index') }}">
                                    <span class="sidebar-menu-text">Performance</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a class="sidebar-menu-button" href="">
                                    <span class="sidebar-menu-text">Financial</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button" href="{{ route('leaderboards.index') }}">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">bar_chart</span>
                            <span class="sidebar-menu-text">Leaderboards</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button" href="{{ route('training-videos.index') }}">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">video_library</span>
                            <span class="sidebar-menu-text">Training Video</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading">Payments</div>
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button" href="">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">inventory</span>
                            <span class="sidebar-menu-text">Products</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button" href="">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">receipt</span>
                            <span class="sidebar-menu-text">Invoices</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button" href="">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">card_membership</span>
                            <span class="sidebar-menu-text">Subscriptions</span>
                        </a>
                    </li>
                </ul>
            </ul>
        </div>
    </div>
</div>
