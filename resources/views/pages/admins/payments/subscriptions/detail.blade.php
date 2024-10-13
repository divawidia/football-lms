@extends('includes.admins.master')
@section('title')
    Subscription {{ $data['subscription']->product->productName }} of {{ $data['subscription']->user->firstName }} {{ $data['subscription']->user->lastName }}
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('invoices.index') }}" class="nav-link text-70"><i
                                class="material-icons icon--left">keyboard_backspace</i> Back to Subscription lists</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- BEFORE Page Content -->

    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($data['subscription']->user->foto) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-3 mb-md-0 rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex mb-3 mb-md-0 ml-md-4">
                <h2 class="text-white mb-0">{{ $data['subscription']->user->firstName }} {{ $data['subscription']->user->lastName }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">Player
                    - {{ $data['subscription']->user->roles[0]->name }}</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    Action
                    <span class="material-icons ml-3">
                        keyboard_arrow_down
                    </span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    @if($data['subscription']->status == 'scheduled')
                        <form action="" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons text-success">check_circle</span>
                                Mark as Unsubscribed
                            </button>
                        </form>
                    @elseif($data['subscription']->status == 'unsubscribed')
                        <form action="" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons text-danger">check_circle</span>
                                Mark as Scheduled
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- // END BEFORE Page Content -->

    <!-- Page Content -->

    <div class="page-section container page__container">
        <div class="page-separator">
            <div class="page-separator__text">Subscription Details</div>
        </div>

        <div class="card card-sm card-group-row__card">
            <div class="card-body flex-column">
                <div class="d-flex align-items-center">
                    <div class="p-2"><p class="card-title mb-4pt">Subscription Status :</p></div>
                    @if ($data['subscription']->status == 'scheduled')
                        <span class="ml-auto p-2 badge badge-pill badge-success">Scheduled</span>
                    @elseif($$data['subscription']->status == 'unsubscribed')
                        <span class="ml-auto p-2 badge badge-pill badge-danger">Unsubscribed</span>
                    @endif
                </div>
                <div class="d-flex align-items-center border-bottom">
                    <div class="p-2"><p class="card-title mb-4pt">Start Date :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ $data['startDate'] }}</div>
                </div>
                <div class="d-flex align-items-center border-bottom">
                    <div class="p-2"><p class="card-title mb-4pt">Next Due Date :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ $data['nextDueDate'] }}</div>
                </div>
                <div class="d-flex align-items-center border-bottom">
                    <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ $data['createdAt'] }}</div>
                </div>
                <div class="d-flex align-items-center border-bottom">
                    <div class="p-2"><p class="card-title mb-4pt">Last updated at :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ $data['updatedAt'] }}</div>
                </div>
            </div>
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Invoice lists</div>
        </div>

        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="invoicesTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Inovice Number</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Amount Due</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('addon-script')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
    <script>
        $(document).ready(function () {
            $('#invoicesTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->route('subscriptions.invoices', $data['subscription']->id) !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'invoiceNumber', name: 'invoiceNumber'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'ammount', name: 'ammount'},
                    {data: 'dueDate', name: 'dueDate'},
                    {data: 'status', name: 'status'},
                    {data: 'createdAt', name: 'createdAt'},
                    {data: 'updatedAt', name: 'updatedAt'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });
        });
    </script>
@endpush
