<div class="mdk-drawer js-mdk-drawer" id="default-drawer">
    <div class="mdk-drawer__content">
        <div class="sidebar sidebar-dark-pickled-bluewood sidebar-left">
            <!-- Sidebar Content -->

            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('coach.dashboard') }}" class="logo logo-dark sidebar-brand my-3">
                    <span class="logo-lg">
                        @if(academyData()->logo)
                            <img src="{{ Storage::url(academyData()->logo) }}" alt="" height="75">
                        @else
                            LOGO
                        @endif
                    </span>
                </a>
            </div>

            <ul class="sidebar-menu">
                <div class="sidebar-heading">Main</div>
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button"
                           href="{{ route('coach.dashboard') }}">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">dashboard</span>
                            <span class="sidebar-menu-text">Dashboard</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading">Management</div>

                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button"
                           href="{{ route('player-managements.index') }}">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">person</span>
                            <span class="sidebar-menu-text">Players Management</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button"
                           href="{{ route('team-managements.index') }}">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">group</span>
                            <span class="sidebar-menu-text">Teams Management</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-heading">Academy</div>
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button"
                           href="{{ route('skill-assessments.index') }}">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">tune</span>
                            <span class="sidebar-menu-text">Skill Assessments</span>
                        </a>
                    </li>
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
                        <a class="sidebar-menu-button" href="{{ route('training-histories.index') }}">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">class</span>
                            <span class="sidebar-menu-text">Training Histories</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-button" href="{{ route('match-histories.index') }}">
                            <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">class</span>
                            <span class="sidebar-menu-text">Match Histories</span>
                        </a>
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
                                <a class="sidebar-menu-button" href="{{ route('attendance-report.admin-coach-index') }}">
                                    <span class="sidebar-menu-text">Attendance</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a class="sidebar-menu-button" href="{{ route('performance-report.coach-index') }}">
                                    <span class="sidebar-menu-text">Performance</span>
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
                            <span class="sidebar-menu-text">Training Course</span>
                        </a>
                    </li>
                </ul>
            </ul>
        </div>
    </div>
</div>
