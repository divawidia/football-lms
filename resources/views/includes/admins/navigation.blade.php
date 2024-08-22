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

            <a href="{{ route('dashboard') }}"
               class="sidebar-brand ">
                <span class="avatar avatar-xl sidebar-brand-icon h-auto">
                    <span class="avatar-title rounded bg-primary">
{{--                        <img src="../../public/images/illustration/student/128/white.svg"--}}
{{--                           class="img-fluid"--}}
{{--                           alt="logo" />--}}
                    </span>
                </span>
                <span>Logo</span>
            </a>

            <div class="sidebar-heading">Main</div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button"
                       href="index.html">
                        <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">dashboard</span>
                        <span class="sidebar-menu-text">Dashboard</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-heading">Management</div>

            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button"
                       href="instructor-dashboard.html">
                        <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">person</span>
                        <span class="sidebar-menu-text">Admins Management</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button"
                       href="instructor-courses.html">
                        <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">person</span>
                        <span class="sidebar-menu-text">Players Management</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button"
                       href="instructor-quizzes.html">
                        <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">person</span>
                        <span class="sidebar-menu-text">Coaches Management</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button"
                       href="instructor-earnings.html">
                        <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">group</span>
                        <span class="sidebar-menu-text">Teams Management</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-heading">Academy</div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button js-sidebar-collapse" data-toggle="collapse"
                       href="#enterprise_menu">
                        <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">event</span>
                        Schedule
                        <span class="ml-auto sidebar-menu-toggle-icon"></span>
                    </a>
                    <ul class="sidebar-submenu collapse sm-indent"
                        id="enterprise_menu">
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button"
                               href="erp-dashboard.html">
                                <span class="sidebar-menu-text">Training</span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button"
                               href="crm-dashboard.html">
                                <span class="sidebar-menu-text">Match</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button"
                       data-toggle="collapse"
                       href="#productivity_menu">
                        <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">description</span>
                        Reports
                        <span class="ml-auto sidebar-menu-toggle-icon"></span>
                    </a>
                    <ul class="sidebar-submenu collapse sm-indent"
                        id="productivity_menu">
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button"
                               href="projects.html">
                                <span class="sidebar-menu-text">Attendance</span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button"
                               href="tasks-board.html">
                                <span class="sidebar-menu-text">Performance</span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button"
                               href="tasks-list.html">
                                <span class="sidebar-menu-text">Financial</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button"
                       href="instructor-dashboard.html">
                        <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">bar_chart</span>
                        <span class="sidebar-menu-text">Leaderboards</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button"
                       href="instructor-dashboard.html">
                        <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">video_library</span>
                        <span class="sidebar-menu-text">Training Video</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-heading">Payments</div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button"
                       href="instructor-dashboard.html">
                        <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">inventory</span>
                        <span class="sidebar-menu-text">Products</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button"
                       href="instructor-dashboard.html">
                        <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">receipt</span>
                        <span class="sidebar-menu-text">Invoices</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button"
                       href="instructor-dashboard.html">
                        <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">card_membership</span>
                        <span class="sidebar-menu-text">Subscriptions</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
