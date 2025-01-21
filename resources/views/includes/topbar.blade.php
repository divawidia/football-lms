<div class="navbar navbar-expand pr-0 navbar-light border-bottom" id="default-navbar" data-primary>

    <!-- Navbar Toggler -->

    <button class="navbar-toggler w-auto mr-16pt d-block rounded-0 ml-3"
            type="button"
            data-toggle="sidebar">
        <span class="material-icons">short_text</span>
    </button>


    <!-- Navbar Brand -->

    <a href="{{ checkRoleDashboardRoute() }}" class="navbar-brand mr-16pt d-lg-none">
            <span class="avatar avatar-sm navbar-brand-icon mr-0 mr-lg-8pt">
                <span class="avatar-title rounded bg-primary">
                    @if(academyData()->logo)
                        <img src="{{ Storage::url(academyData()->logo) }}" alt="logo" class="img-fluid" height="75">
                    @else
                        LOGO
                    @endif
                </span>
            </span>
    </a>

    <div class="flex"></div>

    <!-- Navbar Menu -->

    <div class="nav navbar-nav flex-nowrap d-flex mr-16pt">
        <!-- Notifications dropdown -->
        <div class="nav-item ml-16pt dropdown dropdown-notifications dropdown-xs-down-full"
             data-toggle="tooltip"
             title="Notifications"
             data-placement="bottom"
             data-boundary="window">
            <button class="nav-link btn-flush dropdown-toggle"
                    type="button"
                    data-toggle="dropdown"
                    data-caret="false">
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <i class="material-icons">notifications_active</i>
                    <span class="badge badge-pill badge-danger">{{auth()->user()->unreadNotifications->count()}}</span>
                @else
                    <i class="material-icons">notifications_none</i>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-right" id="container">
                    <div class="dropdown-header d-flex justify-content-center py-3 border-bottom">
                        <strong class="h5">System notifications</strong>
                        <div class="ml-auto">
                            <a href="{{ route('notifications.markAllAsRead') }}" class="btn btn-sm btn-primary">Mark All as Read</a>
                        </div>
                    </div>
                    <div class="list-group list-group-flush mb-0">
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            @foreach (auth()->user()->unreadNotifications as $notification)
                                <a href="{{ $notification->data['redirectRoute'] }}" class="list-group-item list-group-item-action unread-notification" id="{{ $notification->id }}">
                                    <span class="d-flex align-items-center mb-1">
                                        <small class="text-black-50">{{ $notification->created_at->diffForHumans() }}</small>
                                        <span class="ml-auto unread-indicator bg-primary"></span>
                                    </span>
                                    <span class="d-flex">
                                        <span class="flex d-flex flex-column">
                                            @if(array_key_exists('title', $notification->data))
                                                <strong class="text-black-100 text-capitalize">{{ $notification->data['title'] }}</strong>
                                            @endif
                                            @if(array_key_exists('data', $notification->data))
                                            <span class="text-black-70">{{ $notification->data['data'] }}</span>
                                            @else
                                                <span class="text-black-70">{{ $notification->data['message'] }}</span>
                                            @endif
                                        </span>
                                    </span>
                                </a>
                            @endforeach
                        @else
                            <a href="#" class="list-group-item list-group-item-action">
                                <span class="d-flex">
                                    <span class="flex d-flex flex-column">
                                        <span class="text-black-70">No notifications at this time</span>
                                    </span>
                                </span>
                            </a>
                        @endif
                    </div>
            </div>
        </div>
        <!-- // END Notifications dropdown -->
        <div class="nav-item dropdown">
            <a href="#"
               class="nav-link d-flex align-items-center dropdown-toggle"
               data-toggle="dropdown"
               data-caret="false">
                <span class="avatar avatar-sm mr-8pt2">
                    <img class="rounded-circle header-profile-user img-object-fit-cover" height="45" width="45"
                         src="{{ Storage::url(Auth::user()->foto) }}" alt="profile-pic"/>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header"><strong>Account</strong></div>
                <a class="dropdown-item" href="{{ route('edit-account.edit') }}">Edit Account</a>
                <a class="dropdown-item" href="{{ route('reset-password.edit') }}">Reset Password</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('addon-script')
    <script>
        $(document).on('click', '.unread-notification', function() {
            let notificationId = $(this).attr('id');
            $.ajax({
                url: `{{ route('notifications.markAsRead', ':id') }}`.replace(':id', notificationId),
                method: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        console.log(response.message);
                    }
                },
                error: function(xhr) {
                    console.error('Failed to mark notification as read');
                }
            });
        });
    </script>
@endpush
