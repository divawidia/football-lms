@extends('layouts.master')
@section('title')
    Subscription {{ $data->product->productName }} of {{ $data->user->firstName }} {{ $data->user->lastName }}
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-modal.subscriptions.edit-subscription-tax-modal :taxes="$taxes"/>
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('subscriptions.index') }}" class="nav-link text-70"><i class="material-icons icon--left">keyboard_backspace</i> Back to Subscription lists</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- BEFORE Page Content -->

    <div class="page-section bg-primary">
        <div class="container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($data->user->foto) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-3 mb-md-0 rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex mb-3 mb-md-0 ml-md-4">
                <h2 class="text-white mb-0">{{ $data->user->firstName }} {{ $data->user->lastName }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">Player - {{ $data->user->roles[0]->name }}</p>
            </div>
            <x-buttons.dropdown title="Action" icon="keyboard_arrow_down" btnColor="outline-white" iconMargin="ml-3">
                <x-buttons.basic-button icon="edit" color="white" text="Edit subscription tax" additionalClass="edit-tax" :id="$data->hash" :dropdownItem="true"/>
                @if($data->status == 'Scheduled')
                    <x-buttons.basic-button icon="check_circle" iconColor="danger" color="white" text="Mark as Unsubscribed" additionalClass="unsubscribe" :dropdownItem="true"/>
                @elseif($data->status == 'Unsubscribed')
                    <x-buttons.basic-button icon="check_circle" iconColor="success" color="white" text="Continue Subscription" additionalClass="continueSubs" :dropdownItem="true"/>
                @elseif($data->status == 'Past Due Payment')
                    <x-buttons.basic-button icon="check_circle" iconColor="warning" color="white" text="Renew Subscription" additionalClass="renewSubs" :dropdownItem="true"/>
                @endif
                <x-buttons.basic-button icon="delete" iconColor="danger" color="white" text="Delete players subscription" additionalClass="deleteSubscription" :dropdownItem="true"/>
            </x-buttons.dropdown>
        </div>
    </div>

    <!-- Page Content -->

    <div class="page-section container">
        <div class="page-separator">
            <div class="page-separator__text">Subscription Details</div>
        </div>

        <div class="card">
            <div class="card-body flex-column">
                <div class="d-flex align-items-center">
                    <div class="p-2"><p class="card-title mb-4pt">Subscription Status :</p></div>
                    @if ($data->status == 'Scheduled')
                        <span class="ml-auto p-2 badge badge-pill badge-success">{{ $data->status }}</span>
                    @elseif($data->status == 'Unsubscribed')
                        <span class="ml-auto p-2 badge badge-pill badge-danger">{{ $data->status }}</span>
                    @else
                        <span class="ml-auto p-2 badge badge-pill badge-warning">{{ $data->status }}</span>
                    @endif
                </div>
                <div class="d-flex align-items-center border-bottom">
                    <div class="p-2"><p class="card-title mb-4pt">Start Date :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ convertToDatetime($data->startDate) }}</div>
                </div>
                <div class="d-flex align-items-center border-bottom">
                    <div class="p-2"><p class="card-title mb-4pt">Next Due Date :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ convertToDatetime($data->nextDueDate) }}</div>
                </div>
                <div class="d-flex align-items-center border-bottom">
                    <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ convertToDatetime($data->created_at) }}</div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="p-2"><p class="card-title mb-4pt">Last updated at :</p></div>
                    <div class="ml-auto p-2 text-muted">{{ convertToDatetime($data->updated_at) }}</div>
                </div>
            </div>
        </div>

        <div class="page-separator">
            <div class="page-separator__text">Invoice lists</div>
        </div>

        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <x-table tableId="invoicesTable" :headers="['#', 'Invoice Number', 'Name', 'Email', 'Amount Due', 'Due Date', 'Status', 'Created At', 'Last Updated', 'Action']" />
            </div>
        </div>
    </div>
@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
            $('#invoicesTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->route('subscriptions.invoices', $data->id) !!}',
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
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });

            // unsubscribed subscription
            processWithConfirmation(
                '.unsubscribe',
                "{{ route('subscriptions.set-unsubscribed', $data->hash) }}",
                "{{ route('subscriptions.show', $data->hash) }}",
                'PATCH',
                "Are you sure to mark this subscription as unsubscribed?",
                "Something went wrong when marking as unsubscribed!",
                "{{ csrf_token() }}"
            );

            // continue subscription
            processWithConfirmation(
                '.continueSubs',
                "{{ route('subscriptions.set-scheduled', $data->hash) }}",
                "{{ route('subscriptions.show', $data->hash) }}",
                'PATCH',
                "Are you sure to continue this subscription?",
                "Something went wrong when continue this subscription!",
                "{{ csrf_token() }}"
            );

            // renew subscription
            processWithConfirmation(
                '.renewSubs',
                "{{ route('subscriptions.renew-subscription', $data->hash) }}",
                "{{ route('subscriptions.show', $data->hash) }}",
                'PATCH',
                "Are you sure to renew this subscription?",
                "Something went wrong when renewing this subscription!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                ".deleteSubscription",
                "{{ route('subscriptions.destroy', $data->hash) }}",
                "{{ route('subscriptions.index') }}",
                "DELETE",
                "Are you sure to delete this player's subscription?",
                "Something went wrong when deleting player's subscription!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush
